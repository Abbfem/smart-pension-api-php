<?php

namespace PeoplesPension\Oauth2;

use Illuminate\Support\Facades\Session;
use PeoplesPension\Exceptions\InvalidVariableTypeException;
use PeoplesPension\Exceptions\MissingAccessTokenException;
use League\OAuth2\Client\Token\AccessTokenInterface;

/**
 * Access Token storage and retrieval for People's Pension API.
 * 
 * Handles storage of OAuth2 access tokens in the session.
 */
class AccessToken
{
    public const SESSION_KEY = 'peoples_pension_access_token';
    public const SESSION_KEY_TYPE = 'peoples_pension_access_token_type';
    public const SESSION_REFRESH_KEY = 'peoples_pension_refresh_token';
    public const SESSION_OAUTH_STATE = 'peoples_pension_oauth_state';

    /**
     * Check if an access token exists in the session.
     */
    public static function exists(): bool
    {
        return Session::has(self::SESSION_KEY);
    }

    /**
     * Get the stored access token.
     *
     * @return AccessTokenInterface|string|null
     */
    public static function get(): mixed
    {
        $type = Session::get(self::SESSION_KEY_TYPE);

        if ($type === 'string') {
            return Session::get(self::SESSION_KEY);
        }

        if ($type === 'serialize') {
            $token = Session::get(self::SESSION_KEY);
            return $token ? unserialize($token) : null;
        }

        return null;
    }

    /**
     * Store an access token in the session.
     *
     * @param AccessTokenInterface|string $accessToken
     * @throws InvalidVariableTypeException
     */
    public static function set(mixed $accessToken): void
    {
        if ($accessToken instanceof AccessTokenInterface) {
            Session::put(self::SESSION_KEY_TYPE, 'serialize');
            Session::put(self::SESSION_KEY, serialize($accessToken));
            
            // Store refresh token separately for easy access
            $refreshToken = $accessToken->getRefreshToken();
            if ($refreshToken) {
                Session::put(self::SESSION_REFRESH_KEY, $refreshToken);
            }
            return;
        }

        if (gettype($accessToken) !== 'string') {
            throw new InvalidVariableTypeException(
                'Access token must be string or implement AccessTokenInterface.'
            );
        }

        Session::put(self::SESSION_KEY_TYPE, 'string');
        Session::put(self::SESSION_KEY, $accessToken);
    }

    /**
     * Get the stored refresh token.
     */
    public static function getRefreshToken(): ?string
    {
        return Session::get(self::SESSION_REFRESH_KEY);
    }

    /**
     * Check if the access token has expired.
     *
     * @throws MissingAccessTokenException
     */
    public static function hasExpired(): bool
    {
        $accessToken = self::get();

        if ($accessToken === null) {
            throw new MissingAccessTokenException("Access token doesn't exist.");
        }

        if ($accessToken instanceof AccessTokenInterface) {
            return $accessToken->hasExpired();
        }

        // For string tokens, we can't determine expiry
        return false;
    }

    /**
     * Clear the stored access token.
     */
    public static function clear(): void
    {
        Session::forget(self::SESSION_KEY);
        Session::forget(self::SESSION_KEY_TYPE);
        Session::forget(self::SESSION_REFRESH_KEY);
    }

    /**
     * Store OAuth state for CSRF verification.
     */
    public static function setOAuthState(string $state): void
    {
        Session::put(self::SESSION_OAUTH_STATE, $state);
    }

    /**
     * Get stored OAuth state.
     */
    public static function getOAuthState(): ?string
    {
        return Session::get(self::SESSION_OAUTH_STATE);
    }

    /**
     * Verify and clear OAuth state.
     */
    public static function verifyOAuthState(string $state): bool
    {
        $storedState = self::getOAuthState();
        Session::forget(self::SESSION_OAUTH_STATE);
        
        return $storedState !== null && $storedState === $state;
    }
}
