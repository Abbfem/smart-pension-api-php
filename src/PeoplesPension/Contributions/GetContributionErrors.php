<?php

namespace PeoplesPension\Contributions;

use PeoplesPension\Contributions\Request\GetRequest;
use PeoplesPension\Models\ContributionError;

/**
 * Get contribution errors.
 * 
 * Retrieves any errors that have been found in a previously submitted 
 * set of employee contributions.
 * 
 * GET /contributions/{contributionId}/errors
 * 
 * @see https://developer.peoplespartnership.co.uk/develop/v2/api-reference/
 */
class GetContributionErrors extends GetRequest
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
     * Execute the request and return parsed errors.
     *
     * @return ContributionError[]
     */
    public function getErrors(): array
    {
        $response = $this->fire();
        $errors = [];

        if ($response->isSuccess()) {
            $errorData = $response->getErrors();
            foreach ($errorData as $error) {
                $errors[] = ContributionError::fromArray($error);
            }
        }

        return $errors;
    }

    /**
     * Check if there are any errors.
     */
    public function hasErrors(): bool
    {
        return !empty($this->getErrors());
    }

    /**
     * Get errors grouped by employee unique ID.
     *
     * @return array<string, ContributionError[]>
     */
    public function getErrorsByEmployee(): array
    {
        $errors = $this->getErrors();
        $grouped = [];

        foreach ($errors as $error) {
            $uniqueId = $error->uniqueId ?? 'general';
            if (!isset($grouped[$uniqueId])) {
                $grouped[$uniqueId] = [];
            }
            $grouped[$uniqueId][] = $error;
        }

        return $grouped;
    }

    protected function getContributionsApiPath(): string
    {
        return '/' . $this->contributionId . '/errors';
    }
}
