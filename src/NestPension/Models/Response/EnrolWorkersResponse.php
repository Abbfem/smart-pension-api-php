<?php

declare(strict_types=1);

namespace NestPension\Models\Response;

/**
 * Response from enrol workers operation.
 */
class EnrolWorkersResponse
{
    private string $rawXml;
    private array $parsedData;
    private array $enrolledWorkers = [];
    private array $failedWorkers = [];
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
        // Extract enrolled workers from parsed data
        if (isset($this->parsedData['Workers'])) {
            foreach ($this->parsedData['Workers'] as $worker) {
                if (isset($worker['Status']) && $worker['Status'] === 'Success') {
                    $this->enrolledWorkers[] = $worker;
                } else {
                    $this->failedWorkers[] = $worker;
                }
            }
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
     * Get successfully enrolled workers.
     */
    public function getEnrolledWorkers(): array
    {
        return $this->enrolledWorkers;
    }

    /**
     * Get failed workers.
     */
    public function getFailedWorkers(): array
    {
        return $this->failedWorkers;
    }

    /**
     * Get response messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Get enrolled worker count.
     */
    public function getEnrolledCount(): int
    {
        return count($this->enrolledWorkers);
    }

    /**
     * Get failed worker count.
     */
    public function getFailedCount(): int
    {
        return count($this->failedWorkers);
    }

    /**
     * Check if all workers were enrolled successfully.
     */
    public function isFullySuccessful(): bool
    {
        return count($this->failedWorkers) === 0;
    }

    /**
     * Check if any workers were enrolled.
     */
    public function hasEnrolledWorkers(): bool
    {
        return count($this->enrolledWorkers) > 0;
    }
}
