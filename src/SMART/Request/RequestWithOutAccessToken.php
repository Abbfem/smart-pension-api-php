<?php

namespace SMART\Request;

use SMART\Exceptions\MissingAccessTokenException;
use SMART\Oauth2\AccessToken;
use SMART\Response\Response;
use League\OAuth2\Client\Token\AccessTokenInterface;

abstract class RequestWithOutAccessToken extends Request
{
   
    /**
     * RequestWithAccessToken constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws MissingAccessTokenException
     *
     * @return mixed|Response
     */
    public function fire()
    {
        
        return parent::fire();
    }

    protected function getHeaders(): array
    {
        return array_merge(parent::getHeaders(), [
            'Content-Typ' => 'application/json',
        ]);
    }
}
