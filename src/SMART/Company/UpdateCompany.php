<?php

namespace SMART\Company;

use SMART\Company\Request\PutRequest;
use SMART\Company\Request\NewPostBody;


class UpdateCompany extends PutRequest
{
    /** @var string */
    private $company_id;

    public function __construct(string $company_id, NewPostBody $postBody)
    {
        parent::__construct($postBody);
        $this->company_id = $company_id;
    }

    protected function getCompanyApiPath(): string
    {
        return "/{$this->company_id}";
    }

    
}
