<?php

namespace SMART\Oauth2;

use SMART\Environment\Environment;
use SMART\Request\RequestURL;
use League\OAuth2\Client\Provider\GenericProvider;

class Provider extends GenericProvider
{
    /**
     * Provider constructor.
     *
     * @param string $clientID
     * @param string $clientSecret
     * @param string $callbackURI
     * * @param string $responseType
     */
    public function __construct(string $clientID, string $clientSecret, string $callbackURI, string $responseType = "code")
    {
        $options = array_merge([
            'clientId'     => $clientID,
            'clientSecret' => $clientSecret,
            'redirectUri'  => $callbackURI,
            'response_type' => $responseType,
            // 'grant_type' => 'authorization_code'
        ], $this->optionFromEnvironments());

        parent::__construct($options);
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * @return string Scope separator, defaults to ','
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    private function optionFromEnvironments(): array
    {
        $host = Environment::getInstance()->isLive() ? RequestURL::AUTH_LIVE : RequestURL::AUTH_SANDBOX;

        


        return [
            'urlAuthorize'            => "{$host}/oauth/authorize",
            'urlAccessToken'          => "{$host}/oauth/token",
            'urlResourceOwnerDetails' => "{$host}/oauth/resource",
        ];
    }

    public function redirectToAuthorizationURL(array $scopes)
    {
        $authorizationUrl = $this->getAuthorizationUrl([
            'scope' => $scopes,
        ]);

        header('Location: '.$authorizationUrl);
        exit;
    }

    public function getRedirectAuthorizationURL(array $scopes)
    {
        $authorizationUrl = $this->getAuthorizationUrl([
            'scope' => $scopes,
        ]);
        return $authorizationUrl;
    }
}
