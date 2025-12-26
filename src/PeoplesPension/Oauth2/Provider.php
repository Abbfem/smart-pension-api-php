<?php

namespace PeoplesPension\Oauth2;

use PeoplesPension\Environment\Environment;
use League\OAuth2\Client\Provider\GenericProvider;

/**
 * OAuth2 Provider for The People's Pension API.
 * 
 * Implements OAuth 2.0 Authorization Code Grant Flow.
 * 
 * Sandbox Authorization Server: https://sbconnect.peoplespartnership.co.uk
 * Live Authorization Server: https://connect.peoplespartnership.co.uk
 */
class Provider extends GenericProvider
{
    /** Sandbox authorization server hostname */
    public const AUTH_SANDBOX = 'https://sbconnect.peoplespartnership.co.uk';
    
    /** Live authorization server hostname */
    public const AUTH_LIVE = 'https://connect.peoplespartnership.co.uk';

    /** Default OAuth2 scopes for People's Pension API */
    public const DEFAULT_SCOPES = ['openid', 'profile', 'read', 'write', 'offline_access'];

    /**
     * Provider constructor.
     *
     * @param string $clientID Your client ID from Developer Hub Dashboard
     * @param string $clientSecret Your client secret
     * @param string $redirectUri The redirect URI registered in Developer Hub Dashboard
     */
    public function __construct(string $clientID, string $clientSecret, string $redirectUri)
    {
        $options = array_merge([
            'clientId'     => $clientID,
            'clientSecret' => $clientSecret,
            'redirectUri'  => $redirectUri,
        ], $this->getOptionsFromEnvironment());

        parent::__construct($options);
    }

    /**
     * Returns the string that should be used to separate scopes.
     *
     * @return string Scope separator (space for People's Pension)
     */
    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    /**
     * Get OAuth2 endpoints based on current environment.
     */
    private function getOptionsFromEnvironment(): array
    {
        $host = Environment::getInstance()->isLive() 
            ? self::AUTH_LIVE 
            : self::AUTH_SANDBOX;

        return [
            'urlAuthorize'            => "{$host}/connect/authorize",
            'urlAccessToken'          => "{$host}/connect/token",
            'urlResourceOwnerDetails' => "{$host}/connect/userinfo",
        ];
    }

    /**
     * Get the authorization server hostname based on current environment.
     */
    public static function getAuthorizationServerHost(): string
    {
        return Environment::getInstance()->isLive()
            ? self::AUTH_LIVE
            : self::AUTH_SANDBOX;
    }

    /**
     * Get the token revocation endpoint.
     */
    public static function getRevocationEndpoint(): string
    {
        return self::getAuthorizationServerHost() . '/connect/revocation';
    }

    /**
     * Redirect to authorization URL with default scopes.
     *
     * @param array $scopes Optional custom scopes, defaults to DEFAULT_SCOPES
     */
    public function redirectToAuthorizationURL(array $scopes = []): void
    {
        if (empty($scopes)) {
            $scopes = self::DEFAULT_SCOPES;
        }

        $authorizationUrl = $this->getAuthorizationUrl([
            'scope' => $scopes,
        ]);

        header('Location: ' . $authorizationUrl);
        exit;
    }

    /**
     * Get the authorization URL without redirecting.
     *
     * @param array $scopes Optional custom scopes, defaults to DEFAULT_SCOPES
     * @return string The authorization URL
     */
    public function getRedirectAuthorizationURL(array $scopes = []): string
    {
        if (empty($scopes)) {
            $scopes = self::DEFAULT_SCOPES;
        }

        return $this->getAuthorizationUrl([
            'scope' => $scopes,
        ]);
    }

    /**
     * Get OAuth state for CSRF protection.
     * This should be stored in session before redirecting and verified upon callback.
     */
    public function getOAuthState(): string
    {
        return $this->getState();
    }
}
