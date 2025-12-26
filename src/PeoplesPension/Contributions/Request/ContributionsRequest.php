<?php

namespace PeoplesPension\Contributions\Request;

use PeoplesPension\Request\RequestWithAccessToken;
use PeoplesPension\Request\RequestHeader;
use PeoplesPension\Request\RequestHeaderValue;

/**
 * Base request class for Contributions API endpoints.
 */
abstract class ContributionsRequest extends RequestWithAccessToken
{
    protected function getHeaders(): array
    {
        return array_merge(parent::getHeaders(), [
            RequestHeader::CONTENT_TYPE => RequestHeaderValue::APPLICATION_VND_API_JSON,
        ]);
    }

    protected function getApiPath(): string
    {
        return '/contributions' . $this->getContributionsApiPath();
    }

    /**
     * Get the contributions-specific API path.
     */
    abstract protected function getContributionsApiPath(): string;
}
