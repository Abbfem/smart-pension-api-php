<?php

namespace SMART\Group;

use SMART\Group\Request\DeleteRequest;


class Delete extends DeleteRequest
{
    /** @var string */
    private $group_id;

    /**
     * Delete specific group from a specific company.
     *
     * @param string $company_id Required. The ID of the company. Example: '194'.
     * @param string $group_id Required. The ID of the group. Example: '29'.
     *
     * @return array The group data for the given company and group.
     */

    public function __construct(string $company_id,string $group_id)
    {
        parent::__construct($company_id);
        $this->group_id = $group_id;
    }


    protected function getSubApiPath(): string
    {
        return "/groups/{$this->group_id}";
    }

    
}
