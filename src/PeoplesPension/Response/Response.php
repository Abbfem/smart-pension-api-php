<?php

namespace PeoplesPension\Response;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use PeoplesPension\HTTP\Code;

/**
 * Response wrapper for People's Pension API responses.
 */
class Response
{
    private GuzzleResponse $response;

    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Check if the response is successful (2xx status code).
     */
    public function isSuccess(): bool
    {
        $statusCode = $this->response->getStatusCode();
        return $statusCode >= 200 && $statusCode < 300;
    }

    /**
     * Check if the response indicates the request was accepted for processing.
     */
    public function isAccepted(): bool
    {
        return $this->response->getStatusCode() === Code::ACCEPTED;
    }

    /**
     * Check if there is no content (204).
     */
    public function isNoContent(): bool
    {
        return $this->response->getStatusCode() === Code::NO_CONTENT;
    }

    /**
     * Check if the response is a client error (4xx).
     */
    public function isClientError(): bool
    {
        $statusCode = $this->response->getStatusCode();
        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Check if the response is a server error (5xx).
     */
    public function isServerError(): bool
    {
        $statusCode = $this->response->getStatusCode();
        return $statusCode >= 500 && $statusCode < 600;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get the response body stream.
     */
    public function getBody(): \Psr\Http\Message\StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * Get response body as JSON.
     *
     * @param bool $assoc When true, returns array instead of object
     */
    public function getJson(bool $assoc = false): mixed
    {
        $body = (string) $this->response->getBody();
        if (empty($body)) {
            return $assoc ? [] : null;
        }
        return json_decode($body, $assoc);
    }

    /**
     * Get response body as associative array.
     */
    public function getArray(): array
    {
        return $this->getJson(true) ?? [];
    }

    /**
     * Get the 'data' portion of the JSON API response.
     */
    public function getData(): mixed
    {
        $json = $this->getJson(true);
        return $json['data'] ?? null;
    }

    /**
     * Get the 'errors' portion of the JSON API response.
     */
    public function getErrors(): array
    {
        $json = $this->getJson(true);
        return $json['errors'] ?? [];
    }

    /**
     * Get the 'links' portion of the JSON API response.
     */
    public function getLinks(): array
    {
        $json = $this->getJson(true);
        return $json['links'] ?? [];
    }

    /**
     * Get a specific header value.
     */
    public function getHeader(string $name): array
    {
        return $this->response->getHeader($name);
    }

    /**
     * Get the Location header (useful for 202 Accepted responses).
     */
    public function getLocation(): ?string
    {
        $location = $this->response->getHeader('Location');
        return $location[0] ?? null;
    }

    /**
     * Echo the response body with JSON header.
     */
    public function echoBodyWithJsonHeader(): void
    {
        header('Content-Type: application/json');
        echo (string) $this->getBody();
    }

    /**
     * Get the underlying Guzzle response.
     */
    public function getGuzzleResponse(): GuzzleResponse
    {
        return $this->response;
    }
}
