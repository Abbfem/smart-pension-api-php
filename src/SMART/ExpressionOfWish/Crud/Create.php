<?php

namespace SMART\ExpressionOfWish\Crud;

use SMART\ExpressionOfWish\Request\PostData;
use SMART\ExpressionOfWish\Request\PostRequest;


class Create extends PostRequest
{

    /**
     * Updates employee contribution details for a company.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param bool $follow_local_law Determines if local law should be followed.
     * @param array $beneficiaries List of primary beneficiaries.
     * @param array $contingent_beneficiaries List of contingent beneficiaries.
     * @return array The processed beneficiary data.
     * @throws Exception If required parameters are missing or invalid.
     * @return array The updated employee contribution details.
     */
    
    public function __construct(string $company_id, public $employee_id, PostData $postBody)
    {
        parent::__construct($company_id, $postBody);
    }

    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/expression_of_wish";
    }

    
}
