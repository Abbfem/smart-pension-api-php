<?php

namespace SMART\Nationality;

use SMART\Nationality\Request\GetRequest;



class Countries extends GetRequest
{
   

    /**
     * @return array
     */
    protected function getQueryString(): array
    {

        return [];
    }

   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    protected function getSubApiPath(): string
    {
        return "/nationality";
    }
}
