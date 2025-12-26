<?php

/**
 * OAuth2 Logout - Revoke tokens and clear session.
 * 
 * This example demonstrates how to properly log out by revoking tokens.
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Oauth2\AccessToken;
use PeoplesPension\Oauth2\TokenRevocation;
use League\OAuth2\Client\Token\AccessTokenInterface;

$config = loadConfig();
initializeEnvironment($config);
startSession();

// Get stored tokens before clearing
$accessToken = AccessToken::get();
$refreshToken = AccessToken::getRefreshToken();

// Revoke tokens at authorization server
if ($accessToken || $refreshToken) {
    $revocation = new TokenRevocation($config['client_id'], $config['client_secret']);
    
    $tokenValue = $accessToken instanceof AccessTokenInterface 
        ? $accessToken->getToken() 
        : $accessToken;
    
    $revocation->revokeAllTokens($tokenValue, $refreshToken);
}

// Clear local session
AccessToken::clear();

// Destroy session
session_destroy();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - People's Pension API</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
            text-align: center;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="message">
        <h1>✓ Logged Out</h1>
        <p>Your tokens have been revoked and session cleared.</p>
    </div>
    <p><a href="../index.php">Return to examples</a></p>
</body>
</html>
