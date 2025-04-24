<?php

namespace SMART\Group\Crud;

use SMART\Group\Request\PostData;
use SMART\Group\Request\PostRequest;


class Create extends PostRequest
{

    /**
     * Create new employee for a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '415'.
    * @param PostData $postBody The payment configuration details in JSON format with the following fields:
    *      - 'name' (string) Required. The name of the payment configuration.
    *      - 'employee_percentage' (number) The employee's contribution percentage.
    *      - 'company_percentage' (number) The company's contribution percentage.
    *      - 'payment_frequency' (string) Required. Payment frequency.
    *          Allowed values:
    *              - 'annually'
    *              - 'weekly'
    *              - 'fortnightly'
    *              - 'four_weekly'
    *              - 'monthly'
    *              - 'quarterly'
    *              - 'bi_annually'
    *      - 'include_in_payment_schedule' (boolean) Required. Whether to include this configuration in the payment schedule.
    *          Allowed values:
    *              - true
    *              - false
     *
     */
    public function __construct($company_id,PostData $postBody)
    {
        parent::__construct($company_id,$postBody);
    }

    protected function getSubApiPath(): string
    {
        return '/groups';
    }

    
}
