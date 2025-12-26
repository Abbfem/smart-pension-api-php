<?php

declare(strict_types=1);

namespace NestPension\HTTP;

use GuzzleHttp\Psr7\Request;

/**
 * Builds HTTP requests for NEST API calls.
 */
class RequestBuilder
{
    private Authentication $auth;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Build a GET request.
     */
    public function buildGetRequest(string $url, array $additionalHeaders = []): Request
    {
        $headers = $this->buildHeaders($additionalHeaders);

        return new Request('GET', $url, $headers);
    }

    /**
     * Build a POST request with XML payload.
     */
    public function buildPostRequest(string $url, string $xmlPayload, array $additionalHeaders = []): Request
    {
        $headers = $this->buildHeaders($additionalHeaders, true);

        return new Request('POST', $url, $headers, $xmlPayload);
    }

    /**
     * Build headers for requests.
     */
    private function buildHeaders(array $additionalHeaders = [], bool $includeContentType = false): array
    {
        $headers = $this->auth->getHeaders();

        if ($includeContentType) {
            $headers['Content-Type'] = 'application/xml; charset=UTF-8';
        }

        // Add cache control headers as per NEST API requirements
        $headers['Cache-Control'] = 'no-cache, no-store';
        $headers['Pragma'] = 'no-cache';

        // Merge with additional headers (additional headers override defaults)
        return array_merge($headers, $additionalHeaders);
    }

    /**
     * Extract UID from Location header.
     * Used for async operations that return a Location header with UID.
     */
    public function extractUidFromLocation(string $locationHeader): string
    {
        // Location format: https://ws.nestpensions.org.uk/psp-webservices/employer/v1/.../status/{UID}
        $parts = explode('/', $locationHeader);
        return end($parts);
    }

    /**
     * Build URL with parameters.
     */
    public function buildUrlWithParams(string $baseUrl, array $params): string
    {
        $url = $baseUrl;

        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        return $url;
    }

    /**
     * Validate required parameters are present in URL.
     */
    public function validateUrlParams(string $url, array $requiredParams): void
    {
        foreach ($requiredParams as $param) {
            if (strpos($url, '{' . $param . '}') !== false) {
                throw new \InvalidArgumentException("Required parameter '{$param}' not provided");
            }
        }
    }
}
