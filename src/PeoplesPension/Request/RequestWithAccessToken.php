<?php

namespace PeoplesPension\Request;

use PeoplesPension\Exceptions\MissingAccessTokenException;
use PeoplesPension\Oauth2\AccessToken;
use PeoplesPension\Response\Response;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Base class for requests that require an access token.
 */
abstract class RequestWithAccessToken extends Request
{
    /** @var AccessTokenInterface|string|null */
    protected mixed $accessToken;

    public function __construct()
    {
        parent::__construct();
        $this->accessToken = AccessToken::get();
    }

    /**
     * Execute the request.
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws MissingAccessTokenException
     */
    public function fire(): Response
    {
        if ($this->accessToken === null) {
            throw new MissingAccessTokenException(
                'No access token available. Please authenticate first using the OAuth2 flow.'
            );
        }

        return parent::fire();
    }

    /**
     * Get headers including Authorization.
     */
    protected function getHeaders(): array
    {
        $token = $this->accessToken instanceof AccessTokenInterface
            ? $this->accessToken->getToken()
            : $this->accessToken;

        return array_merge(parent::getHeaders(), [
            RequestHeader::AUTHORIZATION => $this->getAuthorizationHeader($token),
        ]);
    }

    /**
     * Set a custom access token for this request.
     */
    public function setAccessToken(mixed $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }
}
