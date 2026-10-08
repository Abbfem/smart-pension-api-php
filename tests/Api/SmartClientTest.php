<?php

namespace SMART\Test\Api;

use GuzzleHttp\Psr7\Response;
use SMART\Api\Exceptions\ApiException;
use SMART\Api\Exceptions\MissingTokenException;
use SMART\Api\Exceptions\NotFoundException;
use SMART\Api\Exceptions\RateLimitException;
use SMART\Api\Exceptions\UnauthorizedException;
use SMART\Api\Exceptions\ValidationException;
use SMART\Api\SmartClient;
use SMART\Exceptions\InvalidVariableValueException;

class SmartClientTest extends ApiTestCase
{
    private function ok($body = ['data' => []]): Response
    {
        return new Response(200, [], json_encode($body));
    }

    /** @test */
    public function it_uses_the_base_url_of_each_environment()
    {
        foreach (['dev' => 'https://api.dev.autoenrolment.co.uk', 'sandbox' => 'https://api.sandbox.autoenrolment.co.uk', 'live' => 'https://api.autoenrolment.co.uk'] as $env => $url) {
            $client = new SmartClient('t', ['environment' => $env]);
            $this->assertSame($url, $client->getBaseUrl());
        }
    }

    /** @test */
    public function it_rejects_an_invalid_environment()
    {
        $this->expectException(InvalidVariableValueException::class);

        new SmartClient('t', ['environment' => 'nope']);
    }

    /** @test */
    public function it_honours_the_base_url_override()
    {
        $client = $this->makeClient([$this->ok()], 'tok', ['base_url' => 'https://api.proxy.test/']);

        $client->request('GET', '/companies');

        $this->assertSame('https://api.proxy.test/companies', (string) $this->request()->getUri());
        $this->assertSame('https://account-claiming.proxy.test', $client->getBaseUrl('account-claiming'));
    }

    /** @test */
    public function it_sends_the_bearer_token_and_default_accept_header()
    {
        $client = $this->makeClient([$this->ok()], 'secret');

        $client->request('GET', '/companies');

        $this->assertSame('Bearer secret', $this->request()->getHeaderLine('Authorization'));
        $this->assertSame('application/json', $this->request()->getHeaderLine('Accept'));
    }

    /** @test */
    public function it_sends_a_versioned_accept_header()
    {
        $client = $this->makeClient([$this->ok()], 'tok', ['version' => 12]);

        $client->request('GET', '/companies');

        $this->assertSame('application/vnd.autoenrolment.v12+json', $this->request()->getHeaderLine('Accept'));
    }

    /** @test */
    public function it_encodes_json_bodies_and_sends_empty_arrays_as_objects()
    {
        $client = $this->makeClient([$this->ok(), $this->ok()]);

        $client->request('POST', '/x', ['json' => ['a' => 1, 'url' => 'http://x/y']]);
        $client->request('POST', '/x', ['json' => []]);

        $this->assertSame('{"a":1,"url":"http://x/y"}', (string) $this->request(0)->getBody());
        $this->assertSame('application/json', $this->request(0)->getHeaderLine('Content-Type'));
        $this->assertSame('{}', (string) $this->request(1)->getBody());
    }

    /** @test */
    public function it_url_encodes_path_params_and_builds_the_query()
    {
        $client = $this->makeClient([$this->ok(), $this->ok()]);

        $client->contributions()->getContribution(1, 2, 3);
        $client->contributions()->getContribution('a b', 2, '3/4', ['include' => ['x', 'y'], 'filter' => ['s' => 'paid']]);

        $this->assertSame('/companies/1/employees/2/contributions/3', $this->request(0)->getUri()->getPath());
        $this->assertSame('/companies/a%20b/employees/2/contributions/3%2F4', $this->request(1)->getUri()->getPath());
        $this->assertSame('include[]=x&include[]=y&filter[s]=paid', urldecode_brackets($this->request(1)->getUri()->getQuery()));
    }

    /** @test */
    public function it_sends_multipart_bodies()
    {
        $client = $this->makeClient([$this->ok()]);

        $client->companyBranding()->createCompanyBranding(1, ['logo' => fopen('php://memory', 'r'), 'description' => 'x']);

        $request = $this->request();
        $body = (string) $request->getBody();

        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('/companies/1/company_branding', $request->getUri()->getPath());
        $this->assertStringStartsWith('multipart/form-data', $request->getHeaderLine('Content-Type'));
        $this->assertStringContainsString('name="logo"', $body);
        $this->assertStringContainsString('name="description"', $body);
    }

    /** @test */
    public function account_claiming_uses_its_own_host_and_works_without_a_token()
    {
        $client = $this->makeClient([$this->ok(), $this->ok()], null);

        $client->accountClaiming()->getPasswordValidations();

        $this->assertSame('https://account-claiming.sandbox.autoenrolment.co.uk/account-claiming/password-validations', (string) $this->request()->getUri());
        $this->assertFalse($this->request()->hasHeader('Authorization'));

        $authed = $client->withToken('abc');
        $authed->accountClaiming()->getPasswordValidations();

        $this->assertSame('Bearer abc', $this->request(1)->getHeaderLine('Authorization'));
    }

    /** @test */
    public function it_throws_when_a_required_token_is_missing()
    {
        $client = $this->makeClient([$this->ok()], null);

        $this->expectException(MissingTokenException::class);

        $client->contributions()->getContribution(1, 2, 3);
    }

    /** @test */
    public function it_maps_status_codes_to_exceptions()
    {
        $cases = [
            401 => UnauthorizedException::class,
            404 => NotFoundException::class,
            422 => ValidationException::class,
            429 => RateLimitException::class,
            500 => ApiException::class,
        ];

        foreach ($cases as $status => $class) {
            $client = $this->makeClient([new Response($status, [], '{}')]);

            try {
                $client->request('GET', '/x');
                $this->fail("Expected {$class} for {$status}");
            } catch (ApiException $e) {
                $this->assertSame($class, get_class($e));
                $this->assertSame($status, $e->getStatusCode());
                $this->assertSame($status, $e->getCode());
            }
        }
    }

    /** @test */
    public function rate_limit_exceptions_expose_retry_after()
    {
        $client = $this->makeClient([new Response(429, ['Retry-After' => '12'], '{}')]);

        try {
            $client->request('GET', '/x');
            $this->fail('Expected RateLimitException');
        } catch (RateLimitException $e) {
            $this->assertSame(12, $e->getRetryAfter());
        }
    }

    /** @test */
    public function it_normalises_the_three_error_shapes()
    {
        $bodies = [
            ['errors' => ['code' => 'c1', 'title' => 'T1', 'detail' => 'D1']],
            ['errors' => [['code' => 'c1', 'title' => 'T1', 'detail' => 'D1']]],
            ['code' => 'c1', 'title' => 'T1', 'detail' => 'D1'],
        ];

        foreach ($bodies as $body) {
            $client = $this->makeClient([new Response(422, [], json_encode($body))]);

            try {
                $client->request('POST', '/x');
                $this->fail('Expected ValidationException');
            } catch (ValidationException $e) {
                $this->assertSame([['code' => 'c1', 'title' => 'T1', 'detail' => 'D1', 'source' => null]], $e->getErrors());
            }
        }
    }

    /** @test */
    public function it_returns_the_response_when_throw_on_error_is_disabled()
    {
        $client = $this->makeClient([new Response(404, [], '{"code":"nf"}')], 'tok', ['throw_on_error' => false]);

        $response = $client->request('GET', '/x');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertFalse($response->isSuccessful());
    }

    /** @test */
    public function it_retries_once_after_a_401_when_the_token_provider_can_refresh()
    {
        $provider = new class implements \SMART\Api\Auth\TokenProvider {
            public $n = 0;

            public function getToken(): ?string
            {
                return 'token'.++$this->n;
            }

            public function invalidate(): bool
            {
                return true;
            }
        };

        $client = $this->makeClient([new Response(401, [], '{}'), $this->ok()], $provider);

        $client->request('GET', '/x');

        $this->assertCount(2, $this->history);
        $this->assertSame('Bearer token2', $this->request(1)->getHeaderLine('Authorization'));
    }

    /** @test */
    public function it_does_not_retry_a_401_with_a_static_token()
    {
        $client = $this->makeClient([new Response(401, [], '{}'), $this->ok()]);

        $this->expectException(UnauthorizedException::class);

        try {
            $client->request('GET', '/x');
        } finally {
            $this->assertCount(1, $this->history);
        }
    }

    /** @test */
    public function paginate_walks_pages_and_stops_on_a_short_page()
    {
        $client = $this->makeClient([
            $this->ok(['total' => 5, 'limit' => 2, 'offset' => 0, 'data' => [1, 2]]),
            $this->ok(['total' => 5, 'limit' => 2, 'offset' => 2, 'data' => [3, 4]]),
            $this->ok(['total' => 5, 'limit' => 2, 'offset' => 4, 'data' => [5]]),
        ]);

        $items = iterator_to_array($client->paginate(function ($q) use ($client) {
            return $client->request('GET', '/things', ['query' => $q]);
        }, [], 2), false);

        $this->assertSame([1, 2, 3, 4, 5], $items);
        $this->assertCount(3, $this->history);
        $this->assertSame('limit=2&offset=4', $this->request(2)->getUri()->getQuery());
    }

    /** @test */
    public function paginate_stops_when_the_total_is_reached()
    {
        $client = $this->makeClient([$this->ok(['total' => 2, 'data' => [1, 2]])]);

        $items = iterator_to_array($client->paginate(function ($q) use ($client) {
            return $client->request('GET', '/things', ['query' => $q]);
        }, [], 2), false);

        $this->assertSame([1, 2], $items);
        $this->assertCount(1, $this->history);
    }
}

function urldecode_brackets(string $query): string
{
    return str_replace(['%5B', '%5D'], ['[', ']'], $query);
}
