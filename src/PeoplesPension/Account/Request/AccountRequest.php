<?php

namespace PeoplesPension\Account\Request;

use PeoplesPension\Request\RequestWithAccessToken;
use PeoplesPension\Request\RequestHeader;
use PeoplesPension\Request\RequestHeaderValue;

/**
 * Base request class for Account API endpoints.
 */
abstract class AccountRequest extends RequestWithAccessToken
{
    protected function getHeaders(): array
    {
        return array_merge(parent::getHeaders(), [
            RequestHeader::CONTENT_TYPE => RequestHeaderValue::APPLICATION_VND_API_JSON,
        ]);
    }

    protected function getApiPath(): string
    {
        return '/accounts' . $this->getAccountApiPath();
    }

    /**
     * Get the account-specific API path.
     */
    abstract protected function getAccountApiPath(): string;
}
