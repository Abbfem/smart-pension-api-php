<?php

declare(strict_types=1);

namespace NestPension\HTTP;

use NestPension\Environment\Environment;

/**
 * Handles HTTP Basic Authentication for NEST API.
 */
class Authentication
{
    private string $username;
    private string $password;
    private string $providerSoftware;
    private string $providerVersion;

    public function __construct(
        ?string $username = null,
        ?string $password = null,
        ?string $providerSoftware = null,
        ?string $providerVersion = null
    ) {
        $env = Environment::getInstance();
        
        $this->username = $username ?? $env->getUsername();
        $this->password = $password ?? $env->getPassword();
        $this->providerSoftware = $providerSoftware ?? $env->getProviderSoftware();
        $this->providerVersion = $providerVersion ?? $env->getProviderVersion();
    }

    /**
     * Generate Basic Authentication header value.
     */
    public function getAuthorizationHeader(): string
    {
        $credentials = base64_encode($this->username . ':' . $this->password);
        return 'Basic ' . $credentials;
    }

    /**
     * Get provider software header.
     */
    public function getProviderSoftwareHeader(): string
    {
        return $this->providerSoftware;
    }

    /**
     * Get provider version header.
     */
    public function getProviderVersionHeader(): string
    {
        return $this->providerVersion;
    }

    /**
     * Get all authentication headers as array.
     */
    public function getHeaders(): array
    {
        return [
            'Authorization' => $this->getAuthorizationHeader(),
            'X-PROVIDER-SOFTWARE' => $this->getProviderSoftwareHeader(),
            'X-PROVIDER-SOFTWARE-VERSION' => $this->getProviderVersionHeader(),
        ];
    }

    /**
     * Get username (for logging purposes).
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Check if authentication is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->username) 
            && !empty($this->password) 
            && !empty($this->providerSoftware) 
            && !empty($this->providerVersion);
    }
}
