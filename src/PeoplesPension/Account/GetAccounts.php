<?php

namespace PeoplesPension\Account;

use PeoplesPension\Account\Request\GetRequest;
use PeoplesPension\Models\AccountSummary;
use PeoplesPension\Response\Response;

/**
 * Get list of admin accounts.
 * 
 * Retrieves a collection of admin accounts that the authenticated user has access to.
 * 
 * GET /accounts
 * 
 * @see https://developer.peoplespartnership.co.uk/develop/v2/api-reference/
 */
class GetAccounts extends GetRequest
{
    /**
     * Execute the request and return parsed account summaries.
     *
     * @return AccountSummary[]
     */
    public function getAccountSummaries(): array
    {
        $response = $this->fire();
        $accounts = [];

        if ($response->isSuccess()) {
            $data = $response->getData();
            // getData() returns stdClass or array of stdClass, convert to array for iteration
            if (is_object($data)) {
                $data = (array) $data;
            }
            if (is_array($data)) {
                foreach ($data as $item) {
                    $accounts[] = AccountSummary::fromArray((array) $item);
                }
            }
        }

        return $accounts;
    }

    protected function getAccountApiPath(): string
    {
        return '';
    }
}
