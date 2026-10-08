<?php

namespace SMART\Api\Exceptions;

use SMART\Exceptions\SMARTException;
use SMART\Response\Response;

/**
 * Thrown when the Keystone API answers with a non-2xx status code.
 *
 * Keystone returns errors in a few shapes; all of them are normalised by getErrors():
 *   {"errors": {"code": "...", "title": "...", "detail": "..."}}
 *   {"errors": [{"code": "...", "title": "...", "detail": "...", "source": {...}}]}
 *   {"code": "...", "title": "...", "detail": "..."}
 *   {"error": "invalid_client", "error_description": "..."}       (OAuth server)
 */
class ApiException extends SMARTException
{
    /** @var Response */
    private $response;

    /** @var array<int, array{code: mixed, title: ?string, detail: ?string, source: mixed}> */
    private $errors;

    public function __construct(string $message, Response $response, array $errors = [])
    {
        parent::__construct($message, $response->getStatusCode());

        $this->response = $response;
        $this->errors = $errors;
    }

    public static function fromResponse(Response $response, string $method, string $uri): self
    {
        $errors = self::normaliseErrors($response->getArray());
        $status = $response->getStatusCode();

        $summary = $errors
            ? implode('; ', array_filter(array_map(function (array $error) {
                return trim(($error['title'] ?? '').' '.($error['detail'] ? "({$error['detail']})" : ''));
            }, $errors)))
            : $response->getGuzzleResponse()->getReasonPhrase();

        $message = "SMART API {$method} {$uri} failed with HTTP {$status}".($summary ? ": {$summary}" : '');

        switch (true) {
            case $status === 401:
                return new UnauthorizedException($message, $response, $errors);
            case $status === 404:
                return new NotFoundException($message, $response, $errors);
            case $status === 422:
                return new ValidationException($message, $response, $errors);
            case $status === 429:
                return new RateLimitException($message, $response, $errors);
            default:
                return new self($message, $response, $errors);
        }
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Normalised list of errors returned by the API.
     *
     * @return array<int, array{code: mixed, title: ?string, detail: ?string, source: mixed}>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Decoded response body (null for non-JSON bodies such as 500 HTML pages).
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->response->getArray();
    }

    private static function normaliseErrors($body): array
    {
        if (!is_array($body)) {
            return [];
        }

        if (isset($body['error']) && is_string($body['error'])) {
            return [self::error(['code' => $body['error'], 'title' => $body['error_description'] ?? $body['error']])];
        }

        $errors = $body['errors'] ?? (isset($body['title']) || isset($body['code']) ? $body : null);

        if (!is_array($errors)) {
            return is_string($errors) ? [self::error(['title' => $errors])] : [];
        }

        // a single error object
        if (isset($errors['title']) || isset($errors['code']) || isset($errors['detail'])) {
            return [self::error($errors)];
        }

        $normalised = [];

        foreach ($errors as $key => $error) {
            if (is_array($error) && (isset($error['title']) || isset($error['code']) || isset($error['detail']))) {
                $normalised[] = self::error($error);
            } elseif (is_string($key)) {
                // Rails style {"field": ["message", ...]}
                foreach ((array) $error as $message) {
                    $normalised[] = self::error(['title' => "{$key} ".(is_scalar($message) ? $message : json_encode($message)), 'source' => ['pointer' => $key]]);
                }
            } elseif (is_string($error)) {
                $normalised[] = self::error(['title' => $error]);
            }
        }

        return $normalised;
    }

    private static function error(array $error): array
    {
        return [
            'code'   => $error['code'] ?? null,
            'title'  => isset($error['title']) ? (string) $error['title'] : null,
            'detail' => isset($error['detail']) ? (string) $error['detail'] : null,
            'source' => $error['source'] ?? null,
        ];
    }
}
