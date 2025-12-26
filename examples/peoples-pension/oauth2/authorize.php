<?php

/**
 * OAuth2 Authorization - Start the OAuth2 flow.
 * 
 * This example initiates the OAuth2 authorization code flow.
 * The user will be redirected to People's Pension login page.
 */

require_once __DIR__ . '/../helpers.php';

$config = loadConfig();
initializeEnvironment($config);
startSession();

$provider = getProvider($config);

// Get authorization URL
$authorizationUrl = $provider->getRedirectAuthorizationURL();

// Store state for CSRF protection
$_SESSION['peoples_pension_oauth_state'] = $provider->getOAuthState();

// Redirect to authorization URL
header('Location: ' . $authorizationUrl);
exit;
