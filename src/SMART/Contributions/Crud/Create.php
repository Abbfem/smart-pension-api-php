<?php

namespace SMART\Contributions\Crud;

use SMART\Contributions\Request\PostData;
use SMART\Contributions\Request\PostRequest;


class Create extends PostRequest
{

    /**
     * Updates employee contribution details for a company.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param PostData $postBody The contribution details in JSON format with the following fields:
     *      - 'voluntary_amount' (number) Voluntary contribution amount.
     *      - 'starts_on' (string) Contribution start date.
     *      - 'ends_on' (string) Contribution end date.
     *      - 'period_type' (string) Type of period for contribution.
     *      - 'category' (string) Employee category. Allowed values:
     *          - 'No duties'
     *          - 'Entitled workers'
     *          - 'Non-eligible jobholders'
     *          - 'Eligible jobholders'
     *      - 'contribution_type' (string) Allowed value: 'regular'.
     *      - 'pensionable_earnings' (number) Pensionable earnings amount.
     *      - 'gross_qualifying_earnings' (number) Gross qualifying earnings.
     *      - 'second_gross_qualifying_earnings_amount' (number) Second gross qualifying earnings amount.
     *      - 'company_amount' (number) Employer contribution amount.
     *      - 'employee_amount' (number) Employee contribution amount.
     *      - 'employee_percentage' (number) Employee contribution percentage.
     *      - 'company_percentage' (number) Employer contribution percentage.
     *      - 'avc_percentage' (number) Additional voluntary contribution percentage.
     *
     * @return array The updated employee contribution details.
     */
    
    public function __construct(string $company_id, public $employee_id, PostData $postBody)
    {
        parent::__construct($company_id, $postBody);
    }

    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/contributions";
    }

    
}
