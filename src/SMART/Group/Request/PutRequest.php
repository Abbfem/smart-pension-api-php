<?php

namespace SMART\Group\Request;

use SMART\Request\PostBody;
use SMART\Response\Response;
use SMART\Request\RequestMethod;
use SMART\Group\Request\GroupRequest;

abstract class PutRequest extends GroupRequest
{
    /** @var PostBody */
    protected $postBody;

    public function __construct($company_id,PostBody $postBody)
    {
        parent::__construct($company_id);

        $this->postBody = $postBody;
    }

    protected function getMethod(): string
    {
        return RequestMethod::PUT;
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
