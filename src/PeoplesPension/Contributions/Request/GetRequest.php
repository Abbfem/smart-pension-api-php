<?php

namespace PeoplesPension\Contributions\Request;

use PeoplesPension\Request\RequestMethod;

/**
 * Base GET request class for Contributions API endpoints.
 */
abstract class GetRequest extends ContributionsRequest
{
    protected function getMethod(): string
    {
        return RequestMethod::GET;
    }
}
