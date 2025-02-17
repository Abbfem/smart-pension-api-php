<?php

namespace SMART\AvailableWithdrawalOptions;

use SMART\AvailableWithdrawalOptions\Request\GetRequest;

class AvailableWithdrawalOptionsList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted AvailableWithdrawalOptionss data.
     *
     * @param string $company_id company id to fetch it's AvailableWithdrawalOptions list.
     * @param string $employee_id employee id to fetch it's AvailableWithdrawalOptions list.
     *
     */
    
   
    public function __construct(
        string $company_id,
        public string $employee_id,

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
        return "/employees/{$this->employee_id}/available_withdrawal_options";
    }
}
