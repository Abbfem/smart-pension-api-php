<?php

declare(strict_types=1);

namespace NestPension\Models\Response;

/**
 * Response from approve payment operation.
 */
class ApprovePaymentResponse
{
    private string $rawXml;
    private array $parsedData;
    private ?string $paymentReference = null;
    private ?string $status = null;
    private array $messages = [];

    public function __construct(string $rawXml, array $parsedData)
    {
        $this->rawXml = $rawXml;
        $this->parsedData = $parsedData;
        $this->parseResponse();
    }

    /**
     * Parse the response data.
     */
    private function parseResponse(): void
    {
        // Extract payment reference
        if (isset($this->parsedData['PaymentReference'])) {
            $this->paymentReference = $this->parsedData['PaymentReference'];
        }

        // Extract status
        if (isset($this->parsedData['Status'])) {
            $this->status = $this->parsedData['Status'];
        }

        // Extract messages
        if (isset($this->parsedData['Messages'])) {
            $this->messages = $this->parsedData['Messages'];
        }
    }

    /**
     * Get raw XML response.
     */
    public function getRawXml(): string
    {
        return $this->rawXml;
    }

    /**
     * Get parsed response data.
     */
    public function getParsedData(): array
    {
        return $this->parsedData;
    }

    /**
     * Get payment reference.
     */
    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    /**
     * Get status.
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Get response messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Check if payment was approved successfully.
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'Approved' || $this->status === 'Success';
    }
}
