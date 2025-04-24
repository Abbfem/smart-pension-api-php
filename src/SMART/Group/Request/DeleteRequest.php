<?php

namespace SMART\Group\Request;

use SMART\Request\PostBody;
use SMART\Response\Response;
use SMART\Request\RequestMethod;
use SMART\Group\Request\EmployeeRequest;

abstract class DeleteRequest extends EmployeeRequest
{
    

    public function __construct($company_id)
    {
        parent::__construct($company_id);

    }

    protected function getMethod(): string
    {
        return RequestMethod::DELETE;
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
        return parent::fire();
    }

}
