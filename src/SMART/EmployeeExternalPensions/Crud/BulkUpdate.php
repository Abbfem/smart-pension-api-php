<?php

namespace SMART\EmployeeExternalPensions\Crud;

use SMART\EmployeeExternalPensions\Request\PostData;
use SMART\EmployeeExternalPensions\Request\PostRequest;


class BulkUpdate extends PostRequest
{

    /**
     * Updates an employee's external pension details for a company.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param PostData $postBody An array of external pension objects containing:
     *    - 'employee_external_pensions' (array) An array of external pension objects containing:
     *      - 'id' (string) The ID of the external pension record.
     *      - 'name' (string) The name of the external pension.
     *      - 'type' (string) The type of external pension.
     *      - 'amount' (string) The amount associated with the pension.
     *      - '_destroy' (boolean) If true, marks the record for deletion.
     *
     */
    public function __construct($company_id, private string $employee_id, PostData $postBody)
    {
        parent::__construct($company_id,$postBody);
    }

    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/employee_external_pensions/bulk_update";
    }

    
}
