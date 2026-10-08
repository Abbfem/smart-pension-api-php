<?php

namespace SMART\Api\Resources;

use SMART\Api\SmartClient;
use SMART\Response\Response;

/**
 * Base class of the generated resource classes.
 */
abstract class AbstractResource
{
    /** @var SmartClient */
    protected $client;

    public function __construct(SmartClient $client)
    {
        $this->client = $client;
    }

    /**
     * @param string               $method     HTTP method
     * @param string               $path       path template, e.g. /companies/{company_id}/employees/{id}
     * @param array<string, mixed> $pathParams values for the placeholders in $path
     * @param array                $options    see SmartClient::request()
     */
    protected function send(string $method, string $path, array $pathParams = [], array $options = []): Response
    {
        foreach ($pathParams as $name => $value) {
            $path = str_replace('{'.$name.'}', rawurlencode((string) $value), $path);
        }

        return $this->client->request($method, $path, $options);
    }
}
