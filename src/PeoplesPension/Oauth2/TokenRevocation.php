<?php

namespace PeoplesPension\Oauth2;

use GuzzleHttp\Client;
use PeoplesPension\Environment\Environment;

/**
 * Token revocation helper for People's Pension OAuth2.
 * 
 * Used to revoke access tokens and refresh tokens when logging out.
 */
class TokenRevocation
{
    private Client $client;
    private string $clientId;
    private string $clientSecret;

    public function __construct(string $clientId, string $clientSecret)
    {
        $this->client = new Client();
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    /**
     * Revoke an access token.
     */
    public function revokeAccessToken(string $accessToken): bool
    {
        return $this->revokeToken($accessToken, 'access_token');
    }

    /**
     * Revoke a refresh token.
     */
    public function revokeRefreshToken(string $refreshToken): bool
    {
        return $this->revokeToken($refreshToken, 'refresh_token');
    }

    /**
     * Revoke both access and refresh tokens.
     */
    public function revokeAllTokens(?string $accessToken = null, ?string $refreshToken = null): bool
    {
        $success = true;

        if ($accessToken) {
            $success = $this->revokeAccessToken($accessToken) && $success;
        }

        if ($refreshToken) {
            $success = $this->revokeRefreshToken($refreshToken) && $success;
        }

        return $success;
    }

    /**
     * Revoke a token.
     */
    private function revokeToken(string $token, string $tokenTypeHint): bool
    {
        $endpoint = Provider::getRevocationEndpoint();

        try {
            $response = $this->client->post($endpoint, [
                'auth' => [$this->clientId, $this->clientSecret],
                'form_params' => [
                    'token' => $token,
                    'token_type_hint' => $tokenTypeHint,
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            return false;
        }
    }
}
