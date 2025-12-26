<?php

namespace PeoplesPension\Contributions\Request;

use PeoplesPension\Request\RequestMethod;
use PeoplesPension\Request\PostBody;
use PeoplesPension\Response\Response;

/**
 * Base POST request class for Contributions API endpoints.
 */
abstract class PostRequest extends ContributionsRequest
{
    protected PostBody $postBody;

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
     * Execute the request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \PeoplesPension\Exceptions\InvalidPostBodyException
     * @throws \PeoplesPension\Exceptions\MissingAccessTokenException
     */
    public function fire(): Response
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
