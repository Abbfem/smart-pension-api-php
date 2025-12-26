<?php

/**
 * OAuth2 Callback - Handle the OAuth2 callback.
 * 
 * This page handles the callback from People's Pension after user authorization.
 * It exchanges the authorization code for an access token.
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Oauth2\AccessToken;

$config = loadConfig();
initializeEnvironment($config);
startSession();

// Check for errors from authorization server
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    $description = $_GET['error_description'] ?? 'Unknown error';
    die("Authorization error: {$error} - {$description}");
}

// Check for authorization code
if (!isset($_GET['code'])) {
    die('Authorization code not received. Please try authorizing again.');
}

// Verify state parameter to prevent CSRF
$state = $_GET['state'] ?? '';
$storedState = $_SESSION['peoples_pension_oauth_state'] ?? '';

if (empty($state) || $state !== $storedState) {
    die('Invalid state parameter. Possible CSRF attack.');
}

// Clear stored state
unset($_SESSION['peoples_pension_oauth_state']);

// Exchange authorization code for access token
$provider = getProvider($config);

try {
    $accessToken = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);
    
    // Store the access token
    AccessToken::set($accessToken);
    
    echo '<h1>Authorization Successful!</h1>';
    echo '<p>Access token has been stored in session.</p>';
    echo '<p>Token expires at: ' . date('Y-m-d H:i:s', $accessToken->getExpires()) . '</p>';
    echo '<p><a href="../index.php">Return to examples</a></p>';
    
} catch (Exception $e) {
    echo '<h1>Error exchanging code for token</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><a href="authorize.php">Try again</a></p>';
}
