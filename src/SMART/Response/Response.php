<?php

namespace SMART\Response;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use SMART\HTTP\Code;

class Response
{
    /** @var GuzzleResponse */
    private $response;

    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    /**
     * Check if the response is success.
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->response->getStatusCode() == Code::SUCCESS;
    }

    /**
     * Check if the response has any 2xx status code (200, 201, 204...).
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        $status = $this->response->getStatusCode();

        return $status >= 200 && $status < 300;
    }

    /**
     * Get the HTTP status code.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get a response header as a comma separated string.
     *
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * Get the response body.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * Get response body as JSON.
     *
     * @param bool $assoc
     *
     * @return mixed
     */
    public function getJson(bool $assoc = false)
    {
        return json_decode((string) $this->response->getBody(), $assoc);
    }

    /**
     * Get response body as associate array.
     *
     * @return mixed
     */
    public function getArray()
    {
        return $this->getJson(true);
    }

    /**
     * Echo out the response body with json header.
     */
    public function echoBodyWithJsonHeader()
    {
        header('Content-Type: application/json');

        echo (string) $this->getBody();
    }

    /**
     * @return GuzzleResponse
     */
    public function getGuzzleResponse(): GuzzleResponse
    {
        return $this->response;
    }
}
