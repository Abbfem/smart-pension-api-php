<?php

namespace SMART\Contributions\Request;

use SMART\Request\PostBody;
use SMART\Response\Response;
use SMART\Request\RequestMethod;
use SMART\Contributions\Request\ContributionsRequest;

abstract class DeleteRequest extends ContributionsRequest
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
