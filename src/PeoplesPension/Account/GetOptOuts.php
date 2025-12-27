<?php

namespace PeoplesPension\Account;

use PeoplesPension\Account\Request\GetRequest;
use PeoplesPension\Models\OptOut;

/**
 * Get opt-outs for an admin account.
 * 
 * Retrieves details of employees who have opted out of the pension scheme.
 * 
 * GET /accounts/{accountId}/opt-outs
 * 
 * @see https://developer.peoplespartnership.co.uk/develop/v2/api-reference/
 */
class GetOptOuts extends GetRequest
{
    private string $accountId;
    private ?string $startDate;
    private ?string $endDate;

    /**
     * @param string $accountId The admin account's unique identifier
     * @param string|null $startDate Restricts results to opt-outs on or after this date (YYYY-MM-DD)
     * @param string|null $endDate Restricts results to opt-outs before this date (YYYY-MM-DD)
     */
    public function __construct(
        string $accountId,
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        parent::__construct();
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the request and return parsed opt-outs.
     *
     * @return OptOut[]
     */
    public function getOptOuts(): array
    {
        $response = $this->fire();
        $optOuts = [];

        if ($response->isSuccess()) {
            $data = $response->getData();
            // getData() returns stdClass, convert to array for safe access
            if (is_object($data)) {
                $data = (array) $data;
            }
            if ($data && isset($data['attributes'])) {
                $attributes = is_object($data['attributes']) ? (array) $data['attributes'] : $data['attributes'];
                if (isset($attributes['optOuts'])) {
                    foreach ($attributes['optOuts'] as $item) {
                        $optOuts[] = OptOut::fromArray((array) $item);
                    }
                }
            }
        }

        return $optOuts;
    }

    /**
     * Check if there are any opt-outs.
     */
    public function hasOptOuts(): bool
    {
        $response = $this->fire();
        return !$response->isNoContent();
    }

    protected function getQueryString(): array
    {
        $params = [];

        if ($this->startDate !== null) {
            $params['startDate'] = $this->startDate;
        }

        if ($this->endDate !== null) {
            $params['endDate'] = $this->endDate;
        }

        return $params;
    }

    protected function getAccountApiPath(): string
    {
        return '/' . $this->accountId . '/opt-outs';
    }
}
