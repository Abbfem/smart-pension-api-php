<?php

namespace SMART\Company;

use SMART\GovernmentTestScenario\GovernmentTestScenario;
use SMART\HTTP\Header;
use SMART\Request\RequestHeader;
use SMART\Request\RequestHeaderValue;
use SMART\Request\RequestWithAccessToken;

abstract class CompanyRequest extends RequestWithAccessToken
{
    

    public function __construct()
    {
        parent::__construct();

    }

    protected function getApiPath(): string
    {
        return "/companies".$this->getCompanyApiPath();
    }

    protected function getHeaders(): array
    {
        $ownHeaders = [
            RequestHeader::CONTENT_TYPE => RequestHeaderValue::APPLICATION_JSON,
        ];

        return array_merge($ownHeaders, parent::getHeaders());
    }

    
    /**
     * @param string $govTestScenario
     *
     * @throws \SMART\Exceptions\InvalidVariableValueException
     * @throws \ReflectionException
     *
     * @return CompanyRequest
     */
  
   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    abstract protected function getCompanyApiPath(): string;
}
