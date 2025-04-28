<?php

namespace SMART\Oauth2;

use SMART\Exceptions\InvalidVariableTypeException;
use SMART\Exceptions\MissingAccessTokenException;
use League\OAuth2\Client\Token\AccessTokenInterface;

class AccessToken
{
    const SESSION_ADVISER_KEY = 'smart_access_token';
    const SESSION_EMPLOYER_KEY = 'smart_access_token';
    const SESSION_KEY = 'smart_access_token';
    const SESSION_KEY_TYPE = 'smart_access_token_type';

    public static function exists(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * @return AccessTokenInterface|null
     */
    public static function get()
    {
        if(isset($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] == "string"){
            return isset($_SESSION[self::SESSION_KEY]) ? $_SESSION[self::SESSION_KEY] : null;
        }

        if(isset($_SESSION[self::SESSION_KEY]) && $_SESSION[self::SESSION_KEY] == "serialize"){
            return isset($_SESSION[self::SESSION_KEY]) ? unserialize($_SESSION[self::SESSION_KEY]) : null;
        }
        
    }

    /**
     * @param $accessToken
     *
     * @throws InvalidVariableTypeException
     */
    public static function set($accessToken)
    {
        if ($accessToken instanceof AccessTokenInterface) {
            $accessToken = serialize($accessToken);
            $_SESSION[self::SESSION_KEY_TYPE] = "serialize";
        }else{
            $_SESSION[self::SESSION_KEY_TYPE] = "string";
        }

        if (gettype($accessToken) !== 'string') {
            throw new InvalidVariableTypeException('Access token must be string or implement AccessTokenInterface.');
        }


        $_SESSION[self::SESSION_KEY] = $accessToken;
        $_SESSION[self::SESSION_KEY] = $accessToken;
    }

    /**
     * @throws MissingAccessTokenException
     *
     * @return bool
     */
    public static function hasExpired(): bool
    {
        /** @var \League\OAuth2\Client\Token\AccessToken $accessToken */
        $accessToken = self::get();

        if (is_null($accessToken)) {
            throw new MissingAccessTokenException("Access token doesn't exists.");
        }

        return $accessToken->hasExpired();
    }
}
