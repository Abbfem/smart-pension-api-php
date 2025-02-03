<?php

namespace SMART\Contributions;

use SMART\Contributions\Request\DeleteRequest;


class Delete extends DeleteRequest
{
    /** @var string */
    

    /**
     * Delete specific contributions from a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '194'.
     * @param string $employee_id Required. The ID of the employee. Example: '29'.
     * @param string $contribution_id Required. The ID of the contributions. Example: '13'.
     *
     * @return array The contributions data for the given company and contributions.
     */

    public function __construct(string $company_id, public string $employee_id, public string $contribution_id)
    {
        parent::__construct($company_id);
        
    }


    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/contributions/{$this->contribution_id}";
    }

    
}
