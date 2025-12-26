<?php

/**
 * Configuration file for People's Pension API examples.
 * 
 * Copy this file to config.php and fill in your credentials.
 */

return [
    // OAuth2 Client Credentials from Developer Hub Dashboard
    'client_id' => 'your-client-id-from-developer-hub',
    'client_secret' => 'your-client-secret',
    
    // Redirect URI registered in Developer Hub Dashboard
    'redirect_uri' => 'http://localhost:8080/peoples-pension/oauth2/callback.php',
    
    // Environment: 'sandbox' or 'live'
    'environment' => 'sandbox',
    
    // Test account ID (for sandbox testing)
    'test_account_id' => '855969',
];
