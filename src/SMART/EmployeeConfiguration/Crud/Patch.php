<?php

namespace SMART\EmployeeConfiguration\Crud;


use SMART\EmployeeConfiguration\Request\PostData;
use SMART\EmployeeConfiguration\Request\PatchRequest;



class Patch extends PatchRequest
{
    
    /**
     * Updates an employee's status and preferences for a company.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param PostData $postBody The employee's status and preference settings, including:
     *      - 'benefits_out_suspended' (boolean) Whether benefits are suspended.
     *      - 'valuation_under_review' (boolean) Whether the valuation is under review.
     *      - 'disabled_switching' (boolean) Whether switching is disabled.
     *      - 'gone_away' (boolean) Whether the employee is considered "gone away".
     *      - 'blocked_rejoin' (boolean) Whether rejoin is blocked.
     *      - 'subject_to_money_purchase_annual_allowance' (boolean) Whether the employee is subject to money purchase annual allowance.
     *      - 'enable_auto_increase_percentage' (boolean) Whether auto increase percentage is enabled.
     *      - 'investments_locked' (boolean) Whether the investments are locked.
     *      - 'money_purchase_annual_allowance_effective_on' (string) Date when the money purchase annual allowance becomes effective.
     *      - 'preferred_currency' (string) The employee's preferred currency.
     *      - 'redirection_strategy' (string) The redirection strategy.
     *      - 'preferred_locale' (string) The employee's preferred locale.
     *      - 'requires_income_adequacy_page_visit' (boolean) Whether the income adequacy page visit is required.
     *
     * @return array The updated employee status and preferences.
     */



  
     public function __construct(string $company_id, public string $employee_id, PostData $postBody)
    {
        parent::__construct($company_id, $postBody);
        
    }


    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/contributions";
    }

    
}
