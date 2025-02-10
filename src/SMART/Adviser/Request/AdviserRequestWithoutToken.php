<?php

namespace SMART\Adviser\Request;

use SMART\HTTP\Header;
use SMART\Request\RequestHeader;
use SMART\Request\RequestHeaderValue;
use SMART\Request\RequestWithAccessToken;
use SMART\Request\RequestWithOutAccessToken;
use SMART\GovernmentTestScenario\GovernmentTestScenario;

abstract class AdviserRequestWithoutToken extends RequestWithOutAccessToken
{
    

    public function __construct()
    {
        parent::__construct();

    }

    protected function getApiPath(): string
    {
        return "/advisers".$this->getAdviserApiPath();
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
     * @return AdviserRequest
     */
  
   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    abstract protected function getAdviserApiPath(): string;
}
