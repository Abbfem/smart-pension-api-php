<?php

declare(strict_types=1);

namespace NestPension\Models\Response;

/**
 * Response from a status check operation.
 */
class StatusResponse
{
    private int $statusCode;
    private bool $isCompleted;
    private bool $isInProgress;
    private ?string $errorMessage;
    private ?\DateTime $expectedCompletionTime;

    public function __construct(
        int $statusCode,
        bool $isCompleted,
        bool $isInProgress,
        ?string $errorMessage = null,
        ?\DateTime $expectedCompletionTime = null
    ) {
        $this->statusCode = $statusCode;
        $this->isCompleted = $isCompleted;
        $this->isInProgress = $isInProgress;
        $this->errorMessage = $errorMessage;
        $this->expectedCompletionTime = $expectedCompletionTime;
    }

    /**
     * Get HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Check if the operation is completed.
     */
    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    /**
     * Check if the operation is still in progress.
     */
    public function isInProgress(): bool
    {
        return $this->isInProgress;
    }

    /**
     * Check if the operation failed.
     */
    public function isFailed(): bool
    {
        return !$this->isCompleted && !$this->isInProgress;
    }

    /**
     * Get error message (if any).
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Get expected completion time (if provided).
     */
    public function getExpectedCompletionTime(): ?\DateTime
    {
        return $this->expectedCompletionTime;
    }

    /**
     * Get status description.
     */
    public function getStatusDescription(): string
    {
        if ($this->isCompleted) {
            return 'Completed';
        }

        if ($this->isInProgress) {
            return 'In Progress';
        }

        return 'Failed';
    }
}
