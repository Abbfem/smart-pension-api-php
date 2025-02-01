<?php

namespace SMART\Employee\Request;

use SMART\Request\PostBody;
use SMART\Response\Response;
use SMART\Request\RequestMethod;
use SMART\Employee\Request\EmployeeRequest;

abstract class PatchRequest extends EmployeeRequest
{
    /** @var PostBody */
    protected $postBody;

    public function __construct($company_id, PostBody $postBody)
    {
        parent::__construct($company_id);

        $this->postBody = $postBody;
    }

    protected function getMethod(): string
    {
        return RequestMethod::PATCH;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \SMART\Exceptions\InvalidPostBodyException
     * @throws \SMART\Exceptions\MissingAccessTokenException
     *
     * @return mixed|Response
     */
    public function fire()
    {
        $this->postBody->validate();

        return parent::fire();
    }

    protected function getHTTPClientOptions(): array
    {
        return array_merge([
            'json' => $this->postBody->toArray(),
        ], parent::getHTTPClientOptions());
    }
}
