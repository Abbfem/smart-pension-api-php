<?php

namespace PeoplesPension\Account\Request;

use PeoplesPension\Request\RequestMethod;

/**
 * Base GET request class for Account API endpoints.
 */
abstract class GetRequest extends AccountRequest
{
    protected function getURI(): string
    {
        $uri = parent::getURI();
        $queryString = $this->getQueryString();

        if (empty($queryString)) {
            return $uri;
        }

        return $uri . '?' . http_build_query($queryString);
    }

    protected function getMethod(): string
    {
        return RequestMethod::GET;
    }

    /**
     * Get query string parameters.
     */
    protected function getQueryString(): array
    {
        return [];
    }
}
