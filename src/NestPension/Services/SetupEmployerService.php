<?php

declare(strict_types=1);

namespace NestPension\Services;

use NestPension\Exceptions\NestException;
use NestPension\HTTP\Client;
use NestPension\Models\Request\SetupEmployerRequest;
use NestPension\Models\Response\AsyncOperationResponse;
use NestPension\Models\Response\SetupEmployerResponse;
use NestPension\Models\Response\StatusResponse;
use NestPension\Utils\XmlProcessor;
use Psr\Http\Message\ResponseInterface;

/**
 * Service for setting up new employer in NEST pension scheme.
 */
class SetupEmployerService
{
    private Client $client;
    private XmlProcessor $xmlProcessor;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->xmlProcessor = new XmlProcessor();
    }

    /**
     * Setup new employer in NEST pension scheme.
     * 
     * This is an async operation that returns a 202 status with a Location header
     * containing the UID for tracking the operation status.
     * 
     * Note: This endpoint doesn't require empRefNo as it's for setting up new employers.
     */
    public function setupNewEmployer(SetupEmployerRequest $request): AsyncOperationResponse
    {
        $endpoint = "/psp-webservices/employer/v1/setup-new-employer";
        $xmlPayload = $this->convertRequestToXml($request);

        $response = $this->client->post($endpoint, $xmlPayload);

        return $this->handleAsyncResponse($response);
    }

    /**
     * Check the status of a setup employer operation.
     * Note: For new employer setup, status check doesn't require empRefNo.
     */
    public function getSetupEmployerStatus(string $uid): StatusResponse
    {
        $endpoint = "/psp-webservices/employer/v1/setup-new-employer/status/{$uid}";

        $response = $this->client->get($endpoint);

        return $this->parseStatusResponse($response);
    }

    /**
     * Get the final response from a setup employer operation.
     * Only call this after status indicates completion (201 status).
     */
    public function getSetupEmployerResponse(string $uid): SetupEmployerResponse
    {
        $endpoint = "/psp-webservices/employer/v1/setup-new-employer/response/{$uid}";

        $response = $this->client->get($endpoint);

        return $this->parseSetupEmployerResponse($response);
    }

    /**
     * Convenience method to handle the full async workflow.
     * This will poll the status until completion and return the final response.
     */
    public function setupNewEmployerAndWait(
        SetupEmployerRequest $request,
        int $maxWaitTime = 300,
        int $pollInterval = 5
    ): SetupEmployerResponse {
        $asyncResponse = $this->setupNewEmployer($request);

        $startTime = time();

        while ((time() - $startTime) < $maxWaitTime) {
            $statusResponse = $this->getSetupEmployerStatus($asyncResponse->getUid());

            if ($statusResponse->isCompleted()) {
                return $this->getSetupEmployerResponse($asyncResponse->getUid());
            }

            if ($statusResponse->isFailed()) {
                throw new NestException("Setup employer operation failed: " . $statusResponse->getErrorMessage());
            }

            sleep($pollInterval);
        }

        throw new NestException("Setup employer operation timed out after {$maxWaitTime} seconds");
    }

    /**
     * Convert request object to XML payload.
     */
    private function convertRequestToXml(SetupEmployerRequest $request): string
    {
        $data = $request->toArray();

        return $this->xmlProcessor->arrayToXml(
            $data,
            'SetupEmployerRequest',
            XmlProcessor::NAMESPACE_SETUP_EMPLOYER_REQUEST,
            'emp'
        );
    }

    /**
     * Handle async operation response (202 with Location header).
     */
    private function handleAsyncResponse(ResponseInterface $response): AsyncOperationResponse
    {
        if ($response->getStatusCode() !== 202) {
            throw new NestException("Expected 202 status code for async operation, got: " . $response->getStatusCode());
        }

        $locationHeader = $response->getHeader('Location')[0] ?? null;
        if (!$locationHeader) {
            throw new NestException("Missing Location header in async response");
        }

        $uid = $this->client->getRequestBuilder()->extractUidFromLocation($locationHeader);

        return new AsyncOperationResponse($uid, $locationHeader);
    }

    /**
     * Parse status response.
     */
    private function parseStatusResponse(ResponseInterface $response): StatusResponse
    {
        $statusCode = $response->getStatusCode();
        $isCompleted = $statusCode === 201;
        $isInProgress = $statusCode === 200;

        if (!$isCompleted && !$isInProgress) {
            throw new NestException("Unexpected status code: " . $statusCode);
        }

        return new StatusResponse($statusCode, $isCompleted, $isInProgress);
    }

    /**
     * Parse setup employer response.
     */
    private function parseSetupEmployerResponse(ResponseInterface $response): SetupEmployerResponse
    {
        if ($response->getStatusCode() !== 200) {
            throw new NestException("Expected 200 status code for response, got: " . $response->getStatusCode());
        }

        $xmlContent = (string) $response->getBody();
        $simpleXmlElement = $this->xmlProcessor->parseXml($xmlContent);
        $parsedData = $this->xmlProcessor->xmlToArray($simpleXmlElement);

        return new SetupEmployerResponse($xmlContent, $parsedData);
    }
}
