<?php

namespace SMART\Assessments;

use SMART\Assessments\Request\GetRequest;


class GetAssessments extends GetRequest
{
   

    /**
     * Retrieves specific assessments details in a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '194'.
     * @param string $employee_id Required. The ID of the company. Example: '194'.
     * @param string $assessments_id Required. The ID of the assessments. Example: '29'.
     *
     * @return array The assessments data for the given company and Assessments.
     */

    public function __construct(string $company_id, public string $employee_id, public string $assessments_id)
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
        return "/employees/{$this->employee_id}/assessments/{$this->assessments_id}";
    }

    
}
