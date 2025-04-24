<?php

namespace SMART\BenefitCategories\Request;


use SMART\HTTP\Header;
use SMART\Request\RequestHeader;
use SMART\Request\RequestHeaderValue;
use SMART\Request\RequestWithAccessToken;

abstract class BenefitCategoriesRequest extends RequestWithAccessToken
{
    
    /** @var string Company ID */
    protected $company_id;

    public function __construct(string $company_id)
    {
        parent::__construct();
        $this->company_id = $company_id;
    }

    protected function getApiPath(): string
    {
        return "/companies/{$this->company_id}".$this->getSubApiPath();
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
     * @return ContributionsRequest
     */
  
   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    abstract protected function getSubApiPath(): string;
}
