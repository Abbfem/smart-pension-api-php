<?php

namespace SMART\Employee;

use SMART\Employee\Request\GetWithoutIdRequest;


class Retirement extends GetWithoutIdRequest
{
    

    /**
     * @return array
     */
    protected function getQueryString(): array
    {
        return [];
    }

    protected function getSubApiPath(): string
    {
        return "/retirement";
    }

    
}
