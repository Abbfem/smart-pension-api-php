<?php

namespace SMART\BenefitCategories;

use SMART\BenefitCategories\Request\GetRequest;

class BenefitCategoriesList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted Benefits Categories data.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
    
     * @throws \SMART\Exceptions\InvalidDateFormatException
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
        return "/employees/{$this->employee_id}/benefit_categories";
    }
}
