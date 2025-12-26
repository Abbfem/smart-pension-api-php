<?php

declare(strict_types=1);

namespace NestPension\Environment;

use NestPension\Exceptions\InvalidEnvironmentException;

/**
 * Environment singleton for NEST Pension API.
 * Manages sandbox and live environment configurations.
 */
class Environment
{
    public const ALLOWED_ENV = [self::SANDBOX, self::LIVE];

    public const SANDBOX = 'sandbox';
    public const LIVE = 'live';

    /** Base URLs for environments */
    public const SANDBOX_BASE_URL = 'https://ws-test.nestpensions.org.uk';
    public const LIVE_BASE_URL = 'https://ws.nestpensions.org.uk';

    private static ?self $instance = null;

    private string $env;

    /** @var array Default request headers for all requests */
    private array $requestHeaders = [];

    /** @var string Provider software name */
    private string $providerSoftware = '';

    /** @var string Provider software version */
    private string $providerVersion = '';

    /** @var string Username for HTTP Basic Auth */
    private string $username = '';

    /** @var string Password for HTTP Basic Auth */
    private string $password = '';

    private function __construct()
    {
        $this->env = self::SANDBOX;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = new self();
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @throws InvalidEnvironmentException
     */
    public function setEnv(string $env): void
    {
        if (!in_array($env, self::ALLOWED_ENV, true)) {
            throw new InvalidEnvironmentException(
                "Invalid environment: {$env}. Allowed values: " . implode(', ', self::ALLOWED_ENV)
            );
        }

        $this->env = $env;
    }

    public function isSandbox(): bool
    {
        return $this->env === self::SANDBOX;
    }

    public function isLive(): bool
    {
        return $this->env === self::LIVE;
    }

    public function setToSandbox(): void
    {
        $this->env = self::SANDBOX;
    }

    public function setToLive(): void
    {
        $this->env = self::LIVE;
    }

    /**
     * Get the base URL for the current environment.
     */
    public function getBaseUrl(): string
    {
        return $this->isSandbox() ? self::SANDBOX_BASE_URL : self::LIVE_BASE_URL;
    }

    /**
     * Sets default headers to include in all requests.
     */
    public function setDefaultRequestHeaders(array $headers): void
    {
        $this->requestHeaders = $headers;
    }

    /**
     * Returns default request headers.
     */
    public function getDefaultRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    /**
     * Set provider software information (required for NEST API).
     */
    public function setProviderInfo(string $software, string $version): void
    {
        $this->providerSoftware = $software;
        $this->providerVersion = $version;
    }

    public function getProviderSoftware(): string
    {
        return $this->providerSoftware;
    }

    public function getProviderVersion(): string
    {
        return $this->providerVersion;
    }

    /**
     * Set HTTP Basic Auth credentials.
     */
    public function setCredentials(string $username, string $password): void
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Check if credentials are configured.
     */
    public function hasCredentials(): bool
    {
        return !empty($this->username) && !empty($this->password);
    }

    /**
     * Check if provider info is configured.
     */
    public function hasProviderInfo(): bool
    {
        return !empty($this->providerSoftware) && !empty($this->providerVersion);
    }
}
