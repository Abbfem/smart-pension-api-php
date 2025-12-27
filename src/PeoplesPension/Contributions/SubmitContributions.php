<?php

namespace PeoplesPension\Contributions;

use PeoplesPension\Contributions\Request\PostRequest;
use PeoplesPension\Contributions\Request\ContributionsPostBody;
use PeoplesPension\Models\ContributionsStatus;
use PeoplesPension\Response\Response;

/**
 * Submit employee contributions.
 * 
 * Submits details of those employees who have pension contributions 
 * to be made in the given pay reference period.
 * 
 * POST /contributions
 * 
 * @see https://developer.peoplespartnership.co.uk/develop/v2/api-reference/
 */
class SubmitContributions extends PostRequest
{
    public function __construct(ContributionsPostBody $postBody)
    {
        parent::__construct($postBody);
    }

    /**
     * Execute the request and return the contribution status.
     */
    public function submit(): ?ContributionsStatus
    {
        $response = $this->fire();

        if ($response->isAccepted()) {
            $data = $response->getData();
            // getData() returns stdClass, convert to array for safe access
            if (is_object($data)) {
                $data = (array) $data;
            }
            if ($data) {
                return ContributionsStatus::fromArray($data);
            }
        }

        return null;
    }

    /**
     * Get the status URL from the Location header.
     */
    public function getStatusUrl(): ?string
    {
        $response = $this->fire();
        return $response->getLocation();
    }

    protected function getContributionsApiPath(): string
    {
        return '';
    }
}
