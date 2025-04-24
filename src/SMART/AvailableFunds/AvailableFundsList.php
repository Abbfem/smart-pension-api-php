<?php

namespace SMART\AvailableFunds;

use SMART\AvailableFunds\Request\GetRequest;

class AvailableFundsList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted Available Funds data.
     *
     * @param string $company_id company id to fetch it's AvailableFunds list.
     */
    
   
    public function __construct(
        string $company_id

    )
    {
        parent::__construct($company_id);
    }

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
        return "/available_funds";
    }
}
