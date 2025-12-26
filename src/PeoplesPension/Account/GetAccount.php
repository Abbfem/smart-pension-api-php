<?php

namespace PeoplesPension\Account;

use PeoplesPension\Account\Request\GetRequest;
use PeoplesPension\Models\Account;

/**
 * Get a specific admin account.
 * 
 * Retrieves information about an admin account with The People's Pension.
 * 
 * GET /accounts/{accountId}
 * 
 * @see https://developer.peoplespartnership.co.uk/develop/v2/api-reference/
 */
class GetAccount extends GetRequest
{
    private string $accountId;

    /**
     * @param string $accountId The admin account's unique identifier
     */
    public function __construct(string $accountId)
    {
        parent::__construct();
        $this->accountId = $accountId;
    }

    /**
     * Execute the request and return parsed account.
     */
    public function getAccount(): ?Account
    {
        $response = $this->fire();

        if ($response->isSuccess()) {
            $data = $response->getData();
            if ($data) {
                return Account::fromArray((array) $data);
            }
        }

        return null;
    }

    protected function getAccountApiPath(): string
    {
        return '/' . $this->accountId;
    }
}
