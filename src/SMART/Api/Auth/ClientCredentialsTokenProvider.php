<?php

namespace SMART\Api\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use SMART\Api\Exceptions\ApiException;
use SMART\Api\Hosts;
use SMART\Response\Response;

/**
 * Machine-to-machine authentication using the OAuth 2.0 client_credentials grant.
 *
 * Tokens are requested from the identity server (POST /oauth/token with the
 * "Token-Type: jwt" header) and cached until shortly before they expire.
 * Keystone JWTs live for 10 minutes regardless of the "expires_in" value, so the
 * expiry is read from the JWT "exp" claim when available.
 *
 * @see https://developers.autoenrolment.co.uk/smart/yd0a98nlh9e6c-using-client-credentials
 */
class ClientCredentialsTokenProvider implements TokenProvider
{
    /** Seconds before expiry at which the token is treated as expired. */
    private const LEEWAY = 30;

    /** Fallback lifetime when neither the JWT nor the response gives one. */
    private const DEFAULT_TTL = 600;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

    /** @var string space separated scopes, e.g. "read:companies read:employees" */
    private $scope;

    /** @var string */
    private $tokenUrl;

    /** @var ClientInterface */
    private $http;

    /** @var string|null */
    private $token;

    /** @var int|null unix timestamp */
    private $expiresAt;

    /** @var array|null raw token response */
    private $lastResponse;

    /**
     * @param string          $clientId
     * @param string          $clientSecret
     * @param string|string[] $scopes       e.g. [Scope::SMP_COMPANIES, Scope::SMP_EMPLOYEES]
     * @param string          $identityUrl  e.g. https://id.sandbox.autoenrolment.co.uk
     */
    public function __construct(
        string $clientId,
        string $clientSecret,
        $scopes,
        string $identityUrl = 'https://id.sandbox.autoenrolment.co.uk',
        ?ClientInterface $http = null
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->scope = is_array($scopes) ? implode(' ', $scopes) : $scopes;
        $this->tokenUrl = rtrim($identityUrl, '/').'/oauth/token';
        $this->http = $http ?? new Client(['timeout' => 30]);
    }

    /**
     * Builds a provider for one of the Keystone environments (dev, sandbox, live).
     *
     * @param string|string[] $scopes
     */
    public static function forEnvironment(
        string $environment,
        string $clientId,
        string $clientSecret,
        $scopes,
        ?ClientInterface $http = null
    ): self {
        return new self($clientId, $clientSecret, $scopes, Hosts::url($environment, Hosts::IDENTITY), $http);
    }

    /**
     * @throws ApiException when the identity server rejects the credentials
     */
    public function getToken(): ?string
    {
        if ($this->token === null || time() >= $this->expiresAt - self::LEEWAY) {
            $this->fetch();
        }

        return $this->token;
    }

    public function invalidate(): bool
    {
        $this->token = null;
        $this->expiresAt = null;

        return true;
    }

    /**
     * Raw body of the last token response (access_token, token_type, expires_in, scope, created_at).
     */
    public function getLastResponse(): ?array
    {
        return $this->lastResponse;
    }

    public function getExpiresAt(): ?int
    {
        return $this->expiresAt;
    }

    /**
     * @throws ApiException
     */
    private function fetch(): void
    {
        $response = new Response($this->http->request('POST', $this->tokenUrl, [
            'http_errors' => false,
            'headers'     => [
                'Token-Type'   => 'jwt',
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'json' => [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'client_credentials',
                'scope'         => $this->scope,
            ],
        ]));

        $body = $response->getArray();

        if (!$response->isSuccessful() || empty($body['access_token'])) {
            throw ApiException::fromResponse($response, 'POST', $this->tokenUrl);
        }

        $this->lastResponse = $body;
        $this->token = $body['access_token'];
        $this->expiresAt = $this->jwtExpiry($this->token)
            ?? time() + min((int) ($body['expires_in'] ?? self::DEFAULT_TTL), self::DEFAULT_TTL);
    }

    private function jwtExpiry(string $token): ?int
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return null;
        }

        $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

        return isset($payload['exp']) && is_numeric($payload['exp']) ? (int) $payload['exp'] : null;
    }
}
