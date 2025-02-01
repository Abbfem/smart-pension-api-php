<?php

namespace SMART\Employee\Request;

use SMART\Request\RequestMethod;
use SMART\Employee\Request\EmployeeRequest;


abstract class GetRequest extends EmployeeRequest
{
    protected function getURI(): string
    {
        $uri = parent::getURI();

        $queryStringArray = $this->getQueryString();

        if (count($queryStringArray) == 0) {
            return $uri;
        }

        $queryString = http_build_query($queryStringArray);

        return "{$uri}?{$queryString}";
    }

    protected function getMethod(): string
    {
        return RequestMethod::GET;
    }

    /**
     * @return array
     */
    abstract protected function getQueryString(): array;
}
