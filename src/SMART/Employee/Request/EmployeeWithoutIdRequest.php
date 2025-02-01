<?php

namespace SMART\Employee\Request;


use SMART\HTTP\Header;
use SMART\Request\RequestHeader;
use SMART\Request\RequestHeaderValue;
use SMART\Request\RequestWithAccessToken;

abstract class EmployeeWithoutIdRequest extends RequestWithAccessToken
{
    
    
    protected function getApiPath(): string
    {
        return "/employees".$this->getSubApiPath();
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
     * @return EmployeeRequest
     */
  
   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    abstract protected function getSubApiPath(): string;
}
