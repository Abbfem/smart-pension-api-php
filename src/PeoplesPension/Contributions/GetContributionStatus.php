<?php

namespace PeoplesPension\Contributions;

use PeoplesPension\Contributions\Request\GetRequest;
use PeoplesPension\Models\ContributionsStatus;

/**
 * Get contribution status.
 * 
 * Retrieves the processing status of a previously submitted set of employee contributions.
 * 
 * GET /contributions/{contributionId}/status
 * 
 * @see https://developer.peoplespartnership.co.uk/develop/v2/api-reference/
 */
class GetContributionStatus extends GetRequest
{
    private string $contributionId;

    /**
     * @param string $contributionId The unique identifier for the contribution submission
     */
    public function __construct(string $contributionId)
    {
        parent::__construct();
        $this->contributionId = $contributionId;
    }

    /**
     * Execute the request and return parsed status.
     */
    public function getStatus(): ?ContributionsStatus
    {
        $response = $this->fire();

        if ($response->isSuccess()) {
            $data = $response->getData();
            if ($data) {
                return ContributionsStatus::fromArray($data);
            }
        }

        return null;
    }

    /**
     * Check if the contribution has been processed successfully.
     */
    public function isProcessed(): bool
    {
        $status = $this->getStatus();
        return $status !== null && $status->processed;
    }

    /**
     * Check if the contribution has failed validation.
     */
    public function hasFailed(): bool
    {
        $status = $this->getStatus();
        return $status !== null && $status->failed;
    }

    protected function getContributionsApiPath(): string
    {
        return '/' . $this->contributionId . '/status';
    }
}
