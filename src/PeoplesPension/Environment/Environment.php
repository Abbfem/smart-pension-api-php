<?php

namespace PeoplesPension\Environment;

use PeoplesPension\Exceptions\InvalidEnvironmentException;

/**
 * Environment singleton for The People's Pension API.
 * Manages sandbox and live environment configurations.
 */
class Environment
{
    public const ALLOWED_ENV = [self::SANDBOX, self::LIVE];

    public const SANDBOX = 'sandbox';
    public const LIVE = 'live';

    private static ?self $instance = null;

    private string $env;

    /** @var array Default request headers for all requests */
    private array $requestHeaders = [];

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
}
