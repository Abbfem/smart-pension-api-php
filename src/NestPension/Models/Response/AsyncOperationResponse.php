<?php

declare(strict_types=1);

namespace NestPension\Models\Response;

/**
 * Response from an async operation (returned immediately after POST).
 */
class AsyncOperationResponse
{
    private string $uid;
    private string $locationUrl;
    private \DateTime $submittedAt;

    public function __construct(string $uid, string $locationUrl)
    {
        $this->uid = $uid;
        $this->locationUrl = $locationUrl;
        $this->submittedAt = new \DateTime();
    }

    /**
     * Get the unique identifier for this operation.
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Get the location URL for status checking.
     */
    public function getLocationUrl(): string
    {
        return $this->locationUrl;
    }

    /**
     * Get the timestamp when the operation was submitted.
     */
    public function getSubmittedAt(): \DateTime
    {
        return $this->submittedAt;
    }

    /**
     * Build the status check URL for this operation.
     */
    public function getStatusUrl(): string
    {
        return str_replace('/response/', '/status/', $this->locationUrl);
    }

    /**
     * Get time elapsed since submission.
     */
    public function getElapsedSeconds(): int
    {
        $now = new \DateTime();
        return $now->getTimestamp() - $this->submittedAt->getTimestamp();
    }
}
