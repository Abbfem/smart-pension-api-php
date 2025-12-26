<?php

declare(strict_types=1);

namespace NestPension\HTTP;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use NestPension\Environment\Environment;
use NestPension\Exceptions\AuthenticationException;
use NestPension\Exceptions\HttpException;
use NestPension\Exceptions\NestException;
use NestPension\Utils\Logger;
use Psr\Http\Message\ResponseInterface;

/**
 * Main HTTP client for NEST Pension API communication.
 */
class Client
{
    private GuzzleClient $httpClient;
    private Authentication $auth;
    private RequestBuilder $requestBuilder;
    private string $baseUrl;
    private int $timeout;
    private int $maxRetries;
    private Logger $logger;

    public function __construct(
        ?Authentication $auth = null,
        ?int $timeout = 30,
        ?int $maxRetries = 3,
        ?Logger $logger = null
    ) {
        $env = Environment::getInstance();
        
        $this->baseUrl = rtrim($env->getBaseUrl(), '/');
        $this->auth = $auth ?? new Authentication();
        $this->timeout = $timeout ?? 30;
        $this->maxRetries = $maxRetries ?? 3;
        $this->logger = $logger ?? new Logger(false);

        $this->httpClient = new GuzzleClient([
            RequestOptions::TIMEOUT => $this->timeout,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::VERIFY => true,
        ]);

        $this->requestBuilder = new RequestBuilder($this->auth);
    }

    /**
     * Perform a GET request.
     */
    public function get(string $endpoint, array $headers = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);

        $this->logger->info('Making GET request', [
            'url' => $url,
            'headers' => $this->sanitizeHeaders($headers)
        ]);

        $request = $this->requestBuilder->buildGetRequest($url, $headers);

        return $this->executeRequest($request);
    }

    /**
     * Perform a POST request with XML payload.
     */
    public function post(string $endpoint, string $xmlPayload, array $headers = []): ResponseInterface
    {
        $url = $this->buildUrl($endpoint);

        $this->logger->info('Making POST request', [
            'url' => $url,
            'headers' => $this->sanitizeHeaders($headers),
            'payload_length' => strlen($xmlPayload)
        ]);

        $request = $this->requestBuilder->buildPostRequest($url, $xmlPayload, $headers);

        return $this->executeRequest($request);
    }

    /**
     * Get the request builder.
     */
    public function getRequestBuilder(): RequestBuilder
    {
        return $this->requestBuilder;
    }

    /**
     * Execute HTTP request with retry logic.
     */
    private function executeRequest(Request $request): ResponseInterface
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = $this->httpClient->send($request);

                $this->logger->info('Request successful', [
                    'status_code' => $response->getStatusCode(),
                    'attempt' => $attempt
                ]);

                $this->validateResponse($response);

                return $response;
            } catch (GuzzleException $e) {
                $lastException = $e;

                $this->logger->warning('Request failed', [
                    'attempt' => $attempt,
                    'max_attempts' => $this->maxRetries,
                    'error' => $e->getMessage()
                ]);

                if ($attempt < $this->maxRetries) {
                    $this->sleep($attempt);
                }
            }
        }

        throw new HttpException(
            sprintf('Request failed after %d attempts', $this->maxRetries),
            0,
            $lastException
        );
    }

    /**
     * Validate HTTP response.
     */
    private function validateResponse(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        // Handle authentication errors
        if ($statusCode === 401) {
            throw new AuthenticationException('Authentication failed - check credentials');
        }

        // Handle client errors (except 4xx which may be expected)
        if ($statusCode >= 400 && $statusCode < 500 && $statusCode !== 404) {
            $body = (string) $response->getBody();
            throw new HttpException(
                sprintf('Client error (HTTP %d): %s', $statusCode, $this->extractErrorMessage($body)),
                $statusCode
            );
        }

        // Handle server errors
        if ($statusCode >= 500) {
            throw new HttpException(
                sprintf('Server error (HTTP %d)', $statusCode),
                $statusCode
            );
        }
    }

    /**
     * Extract error message from XML response body.
     */
    private function extractErrorMessage(string $body): string
    {
        if (empty($body)) {
            return 'No error details provided';
        }

        try {
            $xml = new \SimpleXMLElement($body);
            $xml->registerXPathNamespace('msg', 'http://www.ws.nestpensions.org.uk/ns/Message');

            $errorMessages = $xml->xpath('//msg:ErrorMessage');
            if (!empty($errorMessages)) {
                return (string) $errorMessages[0];
            }

            return 'Error occurred but message could not be parsed';
        } catch (\Exception $e) {
            return 'Error occurred but response could not be parsed';
        }
    }

    /**
     * Build full URL from endpoint.
     */
    private function buildUrl(string $endpoint): string
    {
        return $this->baseUrl . '/' . ltrim($endpoint, '/');
    }

    /**
     * Sleep with exponential backoff.
     */
    private function sleep(int $attempt): void
    {
        $sleepTime = min(pow(2, $attempt), 30); // Max 30 seconds
        sleep((int)$sleepTime);
    }

    /**
     * Sanitize headers for logging (remove sensitive data).
     */
    private function sanitizeHeaders(array $headers): array
    {
        $sanitized = [];
        $sensitiveHeaders = ['Authorization', 'X-PROVIDER-SOFTWARE', 'X-PROVIDER-SOFTWARE-VERSION'];

        foreach ($headers as $key => $value) {
            if (in_array($key, $sensitiveHeaders, true)) {
                $sanitized[$key] = '***REDACTED***';
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
