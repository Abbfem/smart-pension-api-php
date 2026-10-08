<?php

namespace SMART\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\StreamInterface;
use SMART\Api\Auth\ClientCredentialsTokenProvider;
use SMART\Api\Auth\SessionTokenProvider;
use SMART\Api\Auth\StaticTokenProvider;
use SMART\Api\Auth\TokenProvider;
use SMART\Api\Exceptions\ApiException;
use SMART\Api\Exceptions\MissingTokenException;
use SMART\Api\Resources\ResourceAccessors;
use SMART\Environment\Environment;
use SMART\Response\Response;

/**
 * Entry point for the Smart Pension (Keystone) API.
 *
 *     $smart = new SmartClient($accessToken, ['environment' => 'sandbox']);
 *     $employees = $smart->employees()->listEmployees($companyId, ['limit' => 100])->getArray();
 *
 * Every endpoint of the API is exposed as a method on a resource object
 * (see the accessors in ResourceAccessors and docs/smart/README.md).
 *
 * Options:
 *   environment     dev|sandbox|live      defaults to \SMART\Environment\Environment (sandbox unless set to live)
 *   base_url        string                override the API base URL (e.g. a proxy); other services are derived by swapping "api."
 *   version         int|null              pin the API version (Accept: application/vnd.autoenrolment.v{N}+json); null = latest
 *   headers         array                 extra headers sent with every request
 *   http_client     ClientInterface       custom Guzzle client (proxies, middleware, mocks)
 *   timeout         float                 request timeout in seconds when the default Guzzle client is built (default 30)
 *   throw_on_error  bool                  throw ApiException on non-2xx responses (default true)
 */
class SmartClient
{
    use ResourceAccessors;

    /** Send the bearer token only when one is available. */
    public const AUTH_OPTIONAL = 'optional';

    /** @var TokenProvider|null */
    private $tokens;

    /** @var string */
    private $environment;

    /** @var string|null */
    private $baseUrl;

    /** @var int|null */
    private $version;

    /** @var array */
    private $headers;

    /** @var ClientInterface */
    private $http;

    /** @var bool */
    private $throwOnError;

    /** @var array<string, Resources\AbstractResource> */
    private $resources = [];

    /**
     * @param TokenProvider|AccessTokenInterface|string|null $token
     * @param array                                          $options see class docblock
     *
     * @throws \SMART\Exceptions\InvalidVariableValueException
     */
    public function __construct($token = null, array $options = [])
    {
        $this->tokens = $token instanceof TokenProvider || $token === null ? $token : new StaticTokenProvider($token);
        $this->environment = $options['environment'] ?? Environment::getInstance()->getEnv();
        $this->baseUrl = isset($options['base_url']) ? rtrim($options['base_url'], '/') : null;
        $this->version = $options['version'] ?? null;
        $this->headers = $options['headers'] ?? [];
        $this->http = $options['http_client'] ?? new Client(['timeout' => $options['timeout'] ?? 30]);
        $this->throwOnError = $options['throw_on_error'] ?? true;

        Hosts::assertEnvironment($this->environment);
    }

    /**
     * Uses the token saved by \SMART\Oauth2\AccessToken::set() after the authorization code flow.
     */
    public static function fromSession(array $options = []): self
    {
        return new self(new SessionTokenProvider(), $options);
    }

    /**
     * Machine-to-machine client; tokens are fetched and refreshed automatically.
     *
     * @param string|string[] $scopes e.g. [Scope::SMP_COMPANIES, Scope::SMP_EMPLOYEES]
     */
    public static function withClientCredentials(string $clientId, string $clientSecret, $scopes, array $options = []): self
    {
        $environment = $options['environment'] ?? Environment::getInstance()->getEnv();
        $identityUrl = isset($options['base_url'])
            ? Hosts::swapService($options['base_url'], Hosts::IDENTITY)
            : Hosts::url($environment, Hosts::IDENTITY);

        $provider = new ClientCredentialsTokenProvider(
            $clientId,
            $clientSecret,
            $scopes,
            $identityUrl,
            $options['http_client'] ?? new Client(['timeout' => $options['timeout'] ?? 30])
        );

        return new self($provider, $options);
    }

    /**
     * Sends a request to any Keystone endpoint. All generated resource methods go through here.
     *
     * Options:
     *   query      array   query parameters (see Query::build for the bracket notation)
     *   json       array   JSON body
     *   multipart  array   multipart/form-data body, e.g. ['logo' => fopen('logo.png', 'r'), 'description' => 'x']
     *   headers    array   extra headers for this call
     *   service    string  sub-domain to call (Hosts::API by default, e.g. Hosts::ACCOUNT_CLAIMING)
     *   auth       bool|string  true = token required (default), false = never send it,
     *                           self::AUTH_OPTIONAL = send it only when one is available (public endpoints)
     *
     * @throws ApiException          on non-2xx responses (unless throw_on_error is false)
     * @throws MissingTokenException when auth is required but no token is available
     * @throws \GuzzleHttp\Exception\GuzzleException on transport errors
     */
    public function request(string $method, string $path, array $options = []): Response
    {
        $method = strtoupper($method);
        $uri = $this->getBaseUrl($options['service'] ?? Hosts::API).'/'.ltrim($path, '/');

        if (!empty($options['query'])) {
            $uri .= (strpos($uri, '?') === false ? '?' : '&').Query::build($options['query']);
        }

        $requestOptions = ['http_errors' => false];

        if (array_key_exists('json', $options) && $options['json'] !== null) {
            // an empty array must be sent as {} rather than []
            $requestOptions['body'] = json_encode($options['json'] === [] ? new \stdClass() : $options['json'], JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES);
            $requestOptions['headers']['Content-Type'] = 'application/json';
        } elseif (isset($options['multipart'])) {
            $requestOptions['multipart'] = self::multipart($options['multipart']);
        }

        $auth = $options['auth'] ?? true;
        $response = $this->send($method, $uri, $requestOptions, $options['headers'] ?? [], $auth);

        // a cached client-credentials token may have been revoked or expired early: refresh once
        if ($response->getStatusCode() === 401 && $auth && $this->tokens && self::rewindParts($requestOptions['multipart'] ?? []) && $this->tokens->invalidate()) {
            $response = $this->send($method, $uri, $requestOptions, $options['headers'] ?? [], $auth);
        }

        if ($this->throwOnError && !$response->isSuccessful()) {
            throw ApiException::fromResponse($response, $method, $uri);
        }

        return $response;
    }

    /**
     * Iterates over every item of a paginated list endpoint, fetching pages lazily.
     *
     *     foreach ($smart->paginate(fn ($q) => $smart->employees()->listEmployees($companyId, $q)) as $employee) { ... }
     *
     * @param callable(array): Response $fetch    receives the query (with limit/offset) and returns the page response
     * @param array                     $query    extra query parameters (filter, sort, include...)
     * @param int                       $pageSize 1-100 (Keystone maximum is 100)
     *
     * @return \Generator<int, array>
     */
    public function paginate(callable $fetch, array $query = [], int $pageSize = 100): \Generator
    {
        $pageSize = max(1, min(100, $pageSize));
        $offset = (int) ($query['offset'] ?? 0);

        while (true) {
            $page = $fetch(array_merge($query, ['limit' => $pageSize, 'offset' => $offset]))->getArray();
            $items = self::listItems($page);

            foreach ($items as $item) {
                yield $item;
            }

            $offset += count($items);
            $total = is_array($page) && isset($page['total']) && is_numeric($page['total']) ? (int) $page['total'] : null;

            // A bare array has no paging metadata: treat it as the whole list rather than
            // risk re-fetching the same page from an endpoint that ignores offset.
            $isBareList = !is_array($page) || array_is_list($page);

            if ($isBareList || count($items) === 0 || count($items) < $pageSize || ($total !== null && $offset >= $total)) {
                return;
            }
        }
    }

    /**
     * The items of one list page. Keystone wraps lists as {total, links, <resource>: [...]}
     * (for example "employees", "contributions", "payments", "enrolments"); a few endpoints
     * use "data" or return a bare array.
     *
     * @param mixed $page decoded response body
     *
     * @return array<int, mixed>
     */
    public static function listItems($page): array
    {
        if (!is_array($page)) {
            return [];
        }

        if (array_is_list($page)) {
            return $page;
        }

        if (isset($page['data']) && is_array($page['data'])) {
            return $page['data'];
        }

        foreach ($page as $key => $value) {
            if ($key === 'links' || $key === 'meta' || !is_array($value) || !array_is_list($value)) {
                continue;
            }

            return $value;
        }

        return [];
    }

    /**
     * Returns a (cached) resource instance by class name.
     *
     * @template T of Resources\AbstractResource
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function resource(string $class): Resources\AbstractResource
    {
        return $this->resources[$class] ?? $this->resources[$class] = new $class($this);
    }

    public function getBaseUrl(string $service = Hosts::API): string
    {
        if ($this->baseUrl !== null) {
            return $service === Hosts::API ? $this->baseUrl : Hosts::swapService($this->baseUrl, $service);
        }

        return Hosts::url($this->environment, $service);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function getTokenProvider(): ?TokenProvider
    {
        return $this->tokens;
    }

    public function getHttpClient(): ClientInterface
    {
        return $this->http;
    }

    /**
     * Returns a copy of the client pinned to another API version.
     */
    public function withVersion(?int $version): self
    {
        $clone = clone $this;
        $clone->version = $version;
        $clone->resources = [];

        return $clone;
    }

    /**
     * Returns a copy of the client using another token (e.g. to act as a different customer).
     *
     * @param TokenProvider|AccessTokenInterface|string|null $token
     */
    public function withToken($token): self
    {
        $clone = clone $this;
        $clone->tokens = $token instanceof TokenProvider || $token === null ? $token : new StaticTokenProvider($token);
        $clone->resources = [];

        return $clone;
    }

    /**
     * @param bool|string $auth true, false or self::AUTH_OPTIONAL
     */
    private function send(string $method, string $uri, array $requestOptions, array $headers, $auth): Response
    {
        $defaults = [
            'Accept' => $this->version ? "application/vnd.autoenrolment.v{$this->version}+json" : 'application/json',
        ];

        if ($auth) {
            $token = $this->tokens ? $this->tokens->getToken() : null;

            if (!$token && $auth !== self::AUTH_OPTIONAL) {
                throw new MissingTokenException("No access token available for {$method} {$uri}. Pass a token or TokenProvider to SmartClient.");
            }

            if ($token) {
                $defaults['Authorization'] = "Bearer {$token}";
            }
        }

        $requestOptions['headers'] = array_merge(
            // global defaults (e.g. fraud prevention headers) must not replace Accept/Authorization
            Environment::getInstance()->getDefaultRequestHeaders(),
            $defaults,
            $this->headers,
            $requestOptions['headers'] ?? [],
            $headers
        );

        return new Response($this->http->request($method, $uri, $requestOptions));
    }

    /**
     * Rewinds file contents already consumed by a first attempt so the request can be retried.
     *
     * @return bool false when a part cannot be rewound (the request must not be retried)
     */
    private static function rewindParts(array $parts): bool
    {
        foreach ($parts as $part) {
            $contents = $part['contents'] ?? null;

            if ($contents instanceof StreamInterface) {
                if (!$contents->isSeekable()) {
                    return false;
                }
                $contents->rewind();
            } elseif (is_resource($contents)) {
                if (!stream_get_meta_data($contents)['seekable'] || !rewind($contents)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Converts ['field' => value, 'nested' => ['a' => value]] into Guzzle multipart parts.
     * Values may be scalars, resources, StreamInterface or a Guzzle part array (['contents' => ..., 'filename' => ...]).
     */
    private static function multipart(array $fields, string $prefix = ''): array
    {
        $parts = [];
        $isList = array_keys($fields) === range(0, count($fields) - 1);

        foreach ($fields as $key => $value) {
            $name = $prefix === '' ? (string) $key : ($isList ? "{$prefix}[]" : "{$prefix}[{$key}]");

            if (is_array($value) && array_key_exists('contents', $value)) {
                $parts[] = array_merge(['name' => $name], $value);
            } elseif (is_array($value)) {
                $parts = array_merge($parts, self::multipart($value, $name));
            } elseif ($value !== null) {
                $contents = is_bool($value) ? ($value ? 'true' : 'false') : $value;
                $parts[] = ['name' => $name, 'contents' => is_resource($contents) || $contents instanceof StreamInterface ? $contents : (string) $contents];
            }
        }

        return $parts;
    }
}
