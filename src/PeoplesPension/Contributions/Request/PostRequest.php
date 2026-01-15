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
        // Temporarily set serialize_precision to avoid floating-point precision issues
        // (e.g., 93.55 becoming 93.5499999999...)
        $originalPrecision = ini_get('serialize_precision');
        ini_set('serialize_precision', '14');
        
        $data = $this->formatFloatsRecursive($this->postBody->toArray());
        $json = json_encode($data);
        
        // Restore original precision setting
        ini_set('serialize_precision', $originalPrecision);
        
        return array_merge([
            'body' => $json,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ], parent::getHTTPClientOptions());
    }

    /**
     * Recursively format all float values in an array to avoid precision issues.
     * Uses round() to ensure values are properly rounded to 2 decimal places.
     */
    private function formatFloatsRecursive(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->formatFloatsRecursive($value);
            } elseif (is_float($value)) {
                // Round to 2 decimal places - combined with serialize_precision=14,
                // this ensures clean JSON output like 93.55 instead of 93.549999...
                $data[$key] = round($value, 2);
            }
        }
        return $data;
    }
}
