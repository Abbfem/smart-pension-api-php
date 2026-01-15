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
        // Use custom JSON encoding with recursive float formatting to avoid
        // floating-point precision issues (e.g., 93.55 becoming 93.5499999999...)
        $data = $this->formatFloatsRecursive($this->postBody->toArray());
        
        return array_merge([
            'body' => json_encode($data),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ], parent::getHTTPClientOptions());
    }

    /**
     * Recursively format all float values in an array to avoid precision issues.
     */
    private function formatFloatsRecursive(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->formatFloatsRecursive($value);
            } elseif (is_float($value)) {
                // Format to 2 decimal places and cast back to float
                // This creates a "clean" float that JSON encodes correctly
                $data[$key] = (float) number_format($value, 2, '.', '');
            }
        }
        return $data;
    }
}
