<?php

namespace SMART\Employee;

use SMART\Employee\Request\GetRequest;


class ActivePayrollConfiguration extends GetRequest
{
    /** @var string */
    private $employee_id;

    /**
     * Retrieves specific employee active payroll configuration for a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '194'.
     * @param string $employee_id Required. The ID of the employee. Example: '29'.
     *
     * @return array The employee data for the given company and employee.
     */

    public function __construct(string $company_id,string $employee_id)
    {
        parent::__construct($company_id);
        $this->employee_id = $employee_id;
    }

    /**
     * @return array
     */
    protected function getQueryString(): array
    {
        return [];
    }

    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/active_payroll_configuration";
    }

    
}
