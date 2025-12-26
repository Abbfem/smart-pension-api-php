<?php

namespace PeoplesPension\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PeoplesPension\Environment\Environment;
use PeoplesPension\Response\Response as PeoplesPensionResponse;

/**
 * Base Request class for People's Pension API.
 * 
 * All API requests extend this class.
 */
abstract class Request
{
    /** @var Client Guzzle HTTP client */
    protected Client $client;

    /**
     * Array of additional headers to add in each request.
     */
    protected array $headers = [];

    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false, // Don't throw exceptions on 4xx/5xx responses
        ]);
    }

    /**
     * Execute the request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return PeoplesPensionResponse
     */
    public function fire(): PeoplesPensionResponse
    {
        /** @var Response $response */
        $response = $this->client->request(
            $this->getMethod(), 
            $this->getURI(), 
            $this->getHTTPClientOptions()
        );

        return new PeoplesPensionResponse($response);
    }

    /**
     * Get options for the HTTP client.
     */
    protected function getHTTPClientOptions(): array
    {
        return [
            'headers' => $this->getHeaders(),
        ];
    }

    /**
     * Get the Accept header value.
     * People's Pension API uses JSON API content type.
     */
    protected function getAcceptHeader(): string
    {
        return RequestHeaderValue::APPLICATION_VND_API_JSON;
    }

    /**
     * Get the Content-Type header value.
     */
    protected function getContentTypeHeader(): string
    {
        return RequestHeaderValue::APPLICATION_VND_API_JSON;
    }

    /**
     * Get Authorization header with Bearer token.
     */
    protected function getAuthorizationHeader(string $token): string
    {
        return "Bearer {$token}";
    }

    /**
     * Get all headers for the request.
     */
    protected function getHeaders(): array
    {
        return array_merge(
            // Default headers set in environment
            Environment::getInstance()->getDefaultRequestHeaders(),
            // Headers set for this request
            $this->headers,
            // Standard headers
            [
                RequestHeader::ACCEPT => $this->getAcceptHeader(),
                RequestHeader::CONTENT_TYPE => $this->getContentTypeHeader(),
            ]
        );
    }

    /**
     * Get the full URI for the request.
     */
    protected function getURI(): string
    {
        return $this->getApiBaseUrl() . RequestURL::API_PATH . $this->getApiPath();
    }

    /**
     * Get the API base URL based on environment.
     */
    protected function getApiBaseUrl(): string
    {
        return Environment::getInstance()->isSandbox()
            ? RequestURL::SANDBOX
            : RequestURL::LIVE;
    }

    /**
     * Set the HTTP client (useful for testing).
     */
    public function setClient(Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Add additional header(s).
     *
     * @param string|array $key Header name or array of headers
     * @param string|null $value Header value (if $key is string)
     */
    public function addHeader(string|array $key, ?string $value = null): void
    {
        $this->headers = array_merge(
            $this->headers, 
            is_array($key) ? $key : [$key => $value]
        );
    }

    /**
     * Get the HTTP method for this request.
     */
    abstract protected function getMethod(): string;

    /**
     * Get the API path for this request.
     */
    abstract protected function getApiPath(): string;
}
