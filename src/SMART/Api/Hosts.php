<?php

namespace SMART\Api;

use SMART\Exceptions\InvalidVariableValueException;

/**
 * Resolves Keystone (Smart Pension) host names per environment.
 *
 * Every Keystone service lives on its own sub-domain:
 *   api.*              - the main REST API
 *   id.*               - OAuth 2.0 identity server
 *   account-claiming.* - account claiming API
 */
final class Hosts
{
    public const DEV = 'dev';
    public const SANDBOX = 'sandbox';
    public const LIVE = 'live';

    public const ENVIRONMENTS = [self::DEV, self::SANDBOX, self::LIVE];

    public const API = 'api';
    public const IDENTITY = 'id';
    public const ACCOUNT_CLAIMING = 'account-claiming';

    private const DOMAIN = 'autoenrolment.co.uk';

    /**
     * @throws InvalidVariableValueException
     */
    public static function url(string $environment, string $service = self::API): string
    {
        self::assertEnvironment($environment);

        $middle = $environment === self::LIVE ? '' : "{$environment}.";

        return "https://{$service}.{$middle}".self::DOMAIN;
    }

    /**
     * Swaps the service sub-domain of a custom base URL
     * (e.g. https://api.sandbox.example.com -> https://id.sandbox.example.com).
     */
    public static function swapService(string $baseUrl, string $service): string
    {
        return preg_replace('#^(https?://)api\.#', "\$1{$service}.", rtrim($baseUrl, '/'));
    }

    /**
     * @throws InvalidVariableValueException
     */
    public static function assertEnvironment(string $environment): void
    {
        if (!in_array($environment, self::ENVIRONMENTS, true)) {
            $allowed = implode(', ', self::ENVIRONMENTS);

            throw new InvalidVariableValueException("Invalid environment '{$environment}', allowed values are {$allowed}.");
        }
    }
}
