<?php

namespace SMART\Contributions;

use SMART\Contributions\Request\GetRequest;


class GetContributions extends GetRequest
{
   

    /**
     * Retrieves specific contributions details in a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '194'.
     * @param string $employee_id Required. The ID of the company. Example: '194'.
     * @param string $contribution_id Required. The ID of the contributions. Example: '29'.
     *
     * @return array The contributions data for the given company and contributions.
     */

    public function __construct(string $company_id, public string $employee_id, public string $contribution_id)
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
        return "/employees/{$this->employee_id}/contributions/{$this->contribution_id}";
    }

    
}
