<?php

declare(strict_types=1);

namespace NestPension\Models\Response;

/**
 * Response from setup employer operation.
 */
class SetupEmployerResponse
{
    private string $rawXml;
    private array $parsedData;
    private ?string $employerReferenceNumber = null;
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
        // Extract employer reference number
        if (isset($this->parsedData['EmployerReferenceNumber'])) {
            $this->employerReferenceNumber = $this->parsedData['EmployerReferenceNumber'];
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
     * Get employer reference number.
     */
    public function getEmployerReferenceNumber(): ?string
    {
        return $this->employerReferenceNumber;
    }

    /**
     * Get response messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Check if setup was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->employerReferenceNumber !== null;
    }
}
