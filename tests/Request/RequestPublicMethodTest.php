<?php

namespace SMART\Test\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use SMART\Nationality\Countries;
use SMART\Hello\HelloWorldRequest;
use GuzzleHttp\Handler\MockHandler;

class RequestPublicMethodTest extends TestCase
{
    /** @test */
    public function it_has_correct_accept_header_when_ser_version_and_content_type()
    {
        // Setup mocked client
        $container = [];
        $stack = HandlerStack::create(new MockHandler([
            new Response(200),
        ]));
        $stack->push(Middleware::history($container));
        $mockedClient = new Client(['handler' => $stack]);

        // Call the API
        (new Countries())
            ->setClient($mockedClient)
            ->setServiceVersion('2.0')
            ->setContentType('json')
            ->fire();

        // Asserts
        $this->assertCount(1, $container);

        /** @var Request $guzzleRequest */
        $guzzleRequest = $container[0]['request'];
        $acceptHeader = $guzzleRequest->getHeader('Accept');
        $this->assertCount(1, $acceptHeader);
        $this->assertEquals('application/vnd.smart.2.0+json', $acceptHeader[0]);
    }
}
