<?php

namespace SMART\EmployeeExternalPensions;

use SMART\EmployeeExternalPensions\Request\GetRequest;

class ListEmployeePension extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted employee data.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     *
     * @throws \SMART\Exceptions\InvalidDateFormatException
     */
    
   
    public function __construct(
        string $company_id,
        private string $employee_id,
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
        return "/employees/{$this->employee_id}/employee_external_pensions";
    }
}
