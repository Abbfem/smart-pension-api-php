<?php

declare(strict_types=1);

namespace NestPension\Services;

use NestPension\Exceptions\NestException;
use NestPension\HTTP\Client;
use NestPension\Models\Request\ApprovePaymentRequest;
use NestPension\Models\Response\AsyncOperationResponse;
use NestPension\Models\Response\ApprovePaymentResponse;
use NestPension\Models\Response\StatusResponse;
use NestPension\Utils\XmlProcessor;
use Psr\Http\Message\ResponseInterface;

/**
 * Service for approving payments to NEST pension scheme.
 */
class ApprovePaymentService
{
    private Client $client;
    private XmlProcessor $xmlProcessor;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->xmlProcessor = new XmlProcessor();
    }

    /**
     * Approve payment for NEST pension scheme.
     * 
     * This is an async operation that returns a 202 status with a Location header
     * containing the UID for tracking the operation status.
     */
    public function approvePayment(string $empRefNo, ApprovePaymentRequest $request): AsyncOperationResponse
    {
        $endpoint = "/psp-webservices/employer/v1/{$empRefNo}/approve-payment";
        $xmlPayload = $this->convertRequestToXml($request);

        $response = $this->client->post($endpoint, $xmlPayload);

        return $this->handleAsyncResponse($response);
    }

    /**
     * Check the status of an approve payment operation.
     */
    public function getApprovePaymentStatus(string $empRefNo, string $uid): StatusResponse
    {
        $endpoint = "/psp-webservices/employer/v1/{$empRefNo}/approve-payment/status/{$uid}";

        $response = $this->client->get($endpoint);

        return $this->parseStatusResponse($response);
    }

    /**
     * Get the final response from an approve payment operation.
     * Only call this after status indicates completion (201 status).
     */
    public function getApprovePaymentResponse(string $empRefNo, string $uid): ApprovePaymentResponse
    {
        $endpoint = "/psp-webservices/employer/v1/{$empRefNo}/approve-payment/response/{$uid}";

        $response = $this->client->get($endpoint);

        return $this->parseApprovePaymentResponse($response);
    }

    /**
     * Convenience method to handle the full async workflow.
     * This will poll the status until completion and return the final response.
     */
    public function approvePaymentAndWait(
        string $empRefNo,
        ApprovePaymentRequest $request,
        int $maxWaitTime = 300,
        int $pollInterval = 5
    ): ApprovePaymentResponse {
        $asyncResponse = $this->approvePayment($empRefNo, $request);

        $startTime = time();

        while ((time() - $startTime) < $maxWaitTime) {
            $statusResponse = $this->getApprovePaymentStatus($empRefNo, $asyncResponse->getUid());

            if ($statusResponse->isCompleted()) {
                return $this->getApprovePaymentResponse($empRefNo, $asyncResponse->getUid());
            }

            if ($statusResponse->isFailed()) {
                throw new NestException("Approve payment operation failed: " . $statusResponse->getErrorMessage());
            }

            sleep($pollInterval);
        }

        throw new NestException("Approve payment operation timed out after {$maxWaitTime} seconds");
    }

    /**
     * Convert request object to XML payload.
     */
    private function convertRequestToXml(ApprovePaymentRequest $request): string
    {
        $data = $request->toArray();

        return $this->xmlProcessor->arrayToXml(
            $data,
            'ApprovePaymentRequest',
            XmlProcessor::NAMESPACE_APPROVE_PAYMENT_REQUEST,
            'apr'
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
     * Parse approve payment response.
     */
    private function parseApprovePaymentResponse(ResponseInterface $response): ApprovePaymentResponse
    {
        if ($response->getStatusCode() !== 200) {
            throw new NestException("Expected 200 status code for response, got: " . $response->getStatusCode());
        }

        $xmlContent = (string) $response->getBody();
        $simpleXmlElement = $this->xmlProcessor->parseXml($xmlContent);
        $parsedData = $this->xmlProcessor->xmlToArray($simpleXmlElement);

        return new ApprovePaymentResponse($xmlContent, $parsedData);
    }
}
