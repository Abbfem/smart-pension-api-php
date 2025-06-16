<?php
namespace SMART\ExpressionOfWish\Crud;


use SMART\ExpressionOfWish\Request\PostData;
use SMART\ExpressionOfWish\Request\PatchRequest;



class Patch extends PatchRequest
{
    
    /**
     * Updates a specific employee contribution record for a company.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param bool $follow_local_law Determines if local law should be followed.
     * @param array $beneficiaries List of primary beneficiaries.
     * @param array $contingent_beneficiaries List of contingent beneficiaries.
     *
     * @param PostData $postBody The contribution details in JSON format with any of the following fields:
     *      - 'gross_qualifying_earnings' (number) Gross qualifying earnings.
     *      - 'second_gross_qualifying_earnings_amount' (number) Second gross qualifying earnings amount.
     *      - 'pensionable_earnings' (number) Pensionable earnings amount.
     *      - 'company_amount' (number) Employer contribution amount.
     *      - 'company_percentage' (number) Employer contribution percentage.
     *      - 'employee_amount' (number) Employee contribution amount.
     *      - 'employee_percentage' (number) Employee contribution percentage.
     *      - 'avc_percentage' (number) Additional voluntary contribution percentage.
     *      - 'voluntary_amount' (number) Voluntary contribution amount.
     *
     * @return array The updated employee ExpressionOfWish details.
     */



  
     public function __construct(string $company_id, public string $employee_id, PostData $postBody)
    {
        parent::__construct($company_id, $postBody);
        
    }


    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/expression_of_wish}";
    }

    
}
