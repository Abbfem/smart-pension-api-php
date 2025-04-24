<?php

namespace SMART\AvailableWithdrawalOptions\Request;

use SMART\Request\RequestMethod;
use SMART\AvailableWithdrawalOptions\Request\AvailableWithdrawalOptionsRequest;


abstract class GetRequest extends AvailableWithdrawalOptionsRequest
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
