<?php

declare(strict_types=1);

namespace NestPension\Models\Response;

/**
 * Response from update contributions operation.
 */
class UpdateContributionsResponse
{
    private string $rawXml;
    private array $parsedData;
    private array $processedContributions = [];
    private array $failedContributions = [];
    private ?float $totalAmount = null;
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
        // Extract processed contributions from parsed data
        if (isset($this->parsedData['Contributions'])) {
            foreach ($this->parsedData['Contributions'] as $contribution) {
                if (isset($contribution['Status']) && $contribution['Status'] === 'Success') {
                    $this->processedContributions[] = $contribution;
                } else {
                    $this->failedContributions[] = $contribution;
                }
            }
        }

        // Extract total amount
        if (isset($this->parsedData['TotalAmount'])) {
            $this->totalAmount = (float)$this->parsedData['TotalAmount'];
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
     * Get successfully processed contributions.
     */
    public function getProcessedContributions(): array
    {
        return $this->processedContributions;
    }

    /**
     * Get failed contributions.
     */
    public function getFailedContributions(): array
    {
        return $this->failedContributions;
    }

    /**
     * Get total contribution amount.
     */
    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    /**
     * Get response messages.
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Get processed contribution count.
     */
    public function getProcessedCount(): int
    {
        return count($this->processedContributions);
    }

    /**
     * Get failed contribution count.
     */
    public function getFailedCount(): int
    {
        return count($this->failedContributions);
    }

    /**
     * Check if all contributions were processed successfully.
     */
    public function isFullySuccessful(): bool
    {
        return count($this->failedContributions) === 0;
    }
}
