<?php

namespace SMART\Company;

use SMART\Company\NewPostBody;
use SMART\Company\PatchRequest;



class PatchCompany extends PatchRequest
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
