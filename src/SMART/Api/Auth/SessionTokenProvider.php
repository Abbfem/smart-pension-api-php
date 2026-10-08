<?php

namespace SMART\Api\Auth;

use League\OAuth2\Client\Token\AccessTokenInterface;
use SMART\Oauth2\AccessToken;

/**
 * Reads the token stored in the session by \SMART\Oauth2\AccessToken::set()
 * (the authorization code flow used by the examples and legacy request classes).
 */
class SessionTokenProvider implements TokenProvider
{
    public function getToken(): ?string
    {
        $token = AccessToken::get();

        if ($token instanceof AccessTokenInterface) {
            return $token->getToken();
        }

        return $token ?: null;
    }

    public function invalidate(): bool
    {
        return false;
    }
}
