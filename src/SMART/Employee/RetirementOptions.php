<?php

namespace SMART\Employee;

use SMART\Employee\Request\GetWithoutIdRequest;


class RetirementOptions extends GetWithoutIdRequest
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
        return "/retirement_options";
    }

    
}
