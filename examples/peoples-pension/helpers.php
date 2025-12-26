<?php

/**
 * Helper functions for People's Pension API examples.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use PeoplesPension\Environment\Environment;
use PeoplesPension\Oauth2\AccessToken;
use PeoplesPension\Oauth2\Provider;

/**
 * Load configuration.
 */
function loadConfig(): array
{
    $configFile = __DIR__ . '/config.php';
    
    if (!file_exists($configFile)) {
        die('Please copy config.example.php to config.php and fill in your credentials.');
    }
    
    return require $configFile;
}

/**
 * Initialize environment from config.
 */
function initializeEnvironment(array $config): void
{
    $env = Environment::getInstance();
    
    if ($config['environment'] === 'live') {
        $env->setToLive();
    } else {
        $env->setToSandbox();
    }
}

/**
 * Get OAuth2 provider.
 */
function getProvider(array $config): Provider
{
    return new Provider(
        $config['client_id'],
        $config['client_secret'],
        $config['redirect_uri']
    );
}

/**
 * Start session for token storage.
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is authenticated.
 */
function isAuthenticated(): bool
{
    startSession();
    return AccessToken::exists() && !AccessToken::hasExpired();
}

/**
 * Output JSON response.
 */
function jsonResponse(mixed $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit;
}

/**
 * Format date for display.
 */
function formatDate(?string $date): string
{
    if ($date === null) {
        return 'N/A';
    }
    
    return date('d M Y', strtotime($date));
}

/**
 * Format currency for display.
 */
function formatCurrency(float $amount): string
{
    return '£' . number_format($amount, 2);
}
