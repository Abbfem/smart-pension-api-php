<?php

namespace SMART\Company\Request;

use SMART\Request\PostBody;
use SMART\Response\Response;
use SMART\Request\RequestMethod;
use SMART\Company\Request\CompanyRequest;

abstract class PostRequest extends CompanyRequest
{
    /** @var PostBody */
    protected $postBody;

    public function __construct(PostBody $postBody)
    {
        parent::__construct();

        $this->postBody = $postBody;
    }

    protected function getMethod(): string
    {
        return RequestMethod::POST;
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
