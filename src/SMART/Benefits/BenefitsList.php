<?php

namespace SMART\Benefits;

use SMART\Benefits\Request\GetRequest;

class BenefitsList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted benefitss data.
     *
     * @param string $company_id company id to fetch it's benefits list.
     * @param string $employee_id employee id to fetch it's benefits list.
     * @param string $id employee id to fetch it's benefits list.
     *
     */
    
   
    public function __construct(
        string $company_id,
        public string $employee_id,
        public string $id,

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
        return "/employees/{$this->employee_id}/benefits/{$this->id}";
    }
}
