<?php

namespace SMART\Company;

use SMART\Company\Request\GetRequest;


class GetCompany extends GetRequest
{
    /** @var string */
    private $company_id;

    public function __construct(string $company_id)
    {
        
        $this->company_id = $company_id;
    }

    /**
     * @return array
     */
    protected function getQueryString(): array
    {
        return [];
    }

    protected function getCompanyApiPath(): string
    {
        return "/{$this->company_id}";
    }

    
}
