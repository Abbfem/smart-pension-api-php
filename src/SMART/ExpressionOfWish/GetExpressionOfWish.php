<?php

namespace SMART\ExpressionOfWish;

use SMART\ExpressionOfWish\Request\GetRequest;


class GetExpressionOfWish extends GetRequest
{
   

    /**
     * Retrieves specific ExpressionOfWish details in a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '194'.
     * @param string $employee_id Required. The ID of the company. Example: '194'.
     *
     * @return array The contributions data for the given company and contributions.
     */

    public function __construct(string $company_id, public string $employee_id)
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

    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/expression_of_wish}";
    }

    
}
