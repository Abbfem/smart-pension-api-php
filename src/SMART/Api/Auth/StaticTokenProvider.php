<?php

namespace SMART\Api\Auth;

use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Uses a token you already hold (string or league/oauth2-client AccessToken).
 */
class StaticTokenProvider implements TokenProvider
{
    /** @var string|null */
    private $token;

    /**
     * @param string|AccessTokenInterface|null $token
     */
    public function __construct($token)
    {
        $this->token = $token instanceof AccessTokenInterface ? $token->getToken() : $token;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function invalidate(): bool
    {
        return false;
    }
}
