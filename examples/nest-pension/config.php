<?php

/**
 * NEST Pension Configuration Example
 * 
 * This file demonstrates how to configure the NEST Pension API client.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use NestPension\Environment\Environment;

// Get the singleton environment instance
$env = Environment::getInstance();

// Set to sandbox mode (default)
$env->setToSandbox();

// Or set to live mode when ready
// $env->setToLive();

// Configure credentials (HTTP Basic Auth)
// These credentials are provided by NEST when you register as a PSP
$env->setCredentials(
    'your_username',  // NEST API username
    'your_password'   // NEST API password
);

// Configure provider information (required for all requests)
$env->setProviderInfo(
    'Your Software Name',  // The name of your payroll software
    '1.0.0'                // The version of your software
);

// Optionally set default request headers
$env->setDefaultRequestHeaders([
    'X-Custom-Header' => 'custom-value',
]);

// Environment URLs
// Sandbox: https://ws-test.nestpensions.org.uk
// Live: https://ws.nestpensions.org.uk

echo "Environment: " . $env->getEnv() . "\n";
echo "Base URL: " . $env->getBaseUrl() . "\n";
echo "Credentials configured: " . ($env->hasCredentials() ? 'Yes' : 'No') . "\n";
echo "Provider info configured: " . ($env->hasProviderInfo() ? 'Yes' : 'No') . "\n";
