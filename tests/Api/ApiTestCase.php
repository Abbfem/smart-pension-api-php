<?php

namespace SMART\Test\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use SMART\Api\SmartClient;
use SMART\Environment\Environment;

abstract class ApiTestCase extends TestCase
{
    /** @var array<int, array{request: RequestInterface}> */
    protected $history = [];

    protected function setUp(): void
    {
        parent::setUp();

        Environment::reset();
        $this->history = [];
    }

    /**
     * Guzzle client answering with the given responses and recording the requests in $this->history.
     */
    protected function mockHttp(array $responses): Client
    {
        $stack = HandlerStack::create(new MockHandler($responses));
        $stack->push(Middleware::history($this->history));

        return new Client(['handler' => $stack]);
    }

    protected function makeClient(array $responses, $token = 'tok', array $options = []): SmartClient
    {
        return new SmartClient($token, $options + [
            'environment' => 'sandbox',
            'http_client' => $this->mockHttp($responses),
        ]);
    }

    protected function request(int $index = 0): RequestInterface
    {
        return $this->history[$index]['request'];
    }

    protected function unsignedJwt(int $exp): string
    {
        $encode = function (array $data) {
            return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
        };

        return $encode(['alg' => 'none']).'.'.$encode(['exp' => $exp]).'.sig';
    }
}
