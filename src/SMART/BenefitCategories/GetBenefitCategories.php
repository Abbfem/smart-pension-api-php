<?php

namespace SMART\BenefitCategories;

use SMART\BenefitCategories\Request\GetRequest;


class GetBenefitCategories extends GetRequest
{
   

    /**
     * Retrieves specific Benefit Categories details in a specific company.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param string $benefit_rule_set_id Required. The ID of the benefit rule set.
     *
     * @return array The contributions data for the given company and contributions.
     */

    public function __construct(string $company_id, public string $employee_id, public string $benefit_rule_set_id)
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
        return "/employees/{$this->employee_id}/benefit_categories/{$this->benefit_rule_set_id}";
    }

    
}
