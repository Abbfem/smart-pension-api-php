<?php

namespace SMART\Company;

use SMART\Company\NewPostBody;
use SMART\Company\PostRequest;


class CreateCompany extends PostRequest
{
    public function __construct(NewPostBody $postBody)
    {
        parent::__construct($postBody);
    }

    protected function getCompanyApiPath(): string
    {
        return '';
    }

    
}
