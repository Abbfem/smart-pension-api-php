<?php

namespace SMART\Test\Api;

use GuzzleHttp\Psr7\Response;
use SMART\Api\Auth\ClientCredentialsTokenProvider;
use SMART\Api\Exceptions\ApiException;
use SMART\Api\SmartClient;

class ClientCredentialsTokenProviderTest extends ApiTestCase
{
    private function tokenResponse(string $token, int $expiresIn = 600): Response
    {
        return new Response(200, [], json_encode(['access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => $expiresIn]));
    }

    private function ok(): Response
    {
        return new Response(200, [], '{"data":[]}');
    }

    /** @test */
    public function it_posts_the_client_credentials_grant_to_the_identity_server()
    {
        $provider = new ClientCredentialsTokenProvider('id', 'secret', ['read:companies', 'read:employees'], 'https://id.example.test/', $this->mockHttp([$this->tokenResponse('abc')]));

        $this->assertSame('abc', $provider->getToken());

        $request = $this->request();
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://id.example.test/oauth/token', (string) $request->getUri());
        $this->assertSame('jwt', $request->getHeaderLine('Token-Type'));
        $this->assertSame([
            'client_id'     => 'id',
            'client_secret' => 'secret',
            'grant_type'    => 'client_credentials',
            'scope'         => 'read:companies read:employees',
        ], json_decode((string) $request->getBody(), true));
    }

    /** @test */
    public function it_caches_the_token_across_api_calls()
    {
        $http = $this->mockHttp([$this->tokenResponse('abc'), $this->ok(), $this->ok()]);
        $client = SmartClient::withClientCredentials('id', 'secret', 'read:companies', ['environment' => 'sandbox', 'http_client' => $http]);

        $client->request('GET', '/companies');
        $client->request('GET', '/companies');

        $this->assertCount(3, $this->history);
        $this->assertSame('https://id.sandbox.autoenrolment.co.uk/oauth/token', (string) $this->request(0)->getUri());
        $this->assertSame('Bearer abc', $this->request(1)->getHeaderLine('Authorization'));
        $this->assertSame('Bearer abc', $this->request(2)->getHeaderLine('Authorization'));
    }

    /** @test */
    public function it_uses_the_jwt_exp_claim_for_expiry()
    {
        $exp = time() + 3600;
        $jwt = $this->unsignedJwt($exp);
        // expires_in is tiny but the JWT says otherwise, so the token must still be cached
        $provider = new ClientCredentialsTokenProvider('id', 'secret', 's', 'https://id.example.test', $this->mockHttp([$this->tokenResponse($jwt, 1)]));

        $this->assertSame($jwt, $provider->getToken());
        $this->assertSame($jwt, $provider->getToken());
        $this->assertSame($exp, $provider->getExpiresAt());
        $this->assertCount(1, $this->history);
    }

    /** @test */
    public function it_refetches_an_expired_token()
    {
        $old = $this->unsignedJwt(time() - 10);
        $provider = new ClientCredentialsTokenProvider('id', 'secret', 's', 'https://id.example.test', $this->mockHttp([$this->tokenResponse($old), $this->tokenResponse('new')]));

        $provider->getToken();

        $this->assertSame('new', $provider->getToken());
        $this->assertCount(2, $this->history);
    }

    /** @test */
    public function the_client_retries_once_after_a_401_with_a_new_token()
    {
        $http = $this->mockHttp([
            $this->tokenResponse('one'),
            new Response(401, [], '{}'),
            $this->tokenResponse('two'),
            $this->ok(),
        ]);
        $client = SmartClient::withClientCredentials('id', 'secret', 's', ['environment' => 'sandbox', 'http_client' => $http]);

        $client->request('GET', '/companies');

        $this->assertCount(4, $this->history);
        $this->assertSame('Bearer one', $this->request(1)->getHeaderLine('Authorization'));
        $this->assertSame('Bearer two', $this->request(3)->getHeaderLine('Authorization'));
    }

    /** @test */
    public function a_failed_token_request_throws_an_api_exception()
    {
        $provider = new ClientCredentialsTokenProvider('id', 'bad', 's', 'https://id.example.test', $this->mockHttp([
            new Response(401, [], '{"error":"invalid_client","error_description":"Bad credentials"}'),
        ]));

        try {
            $provider->getToken();
            $this->fail('Expected ApiException');
        } catch (ApiException $e) {
            $this->assertSame(401, $e->getStatusCode());
            $this->assertSame('invalid_client', $e->getErrors()[0]['code']);
        }
    }
}
