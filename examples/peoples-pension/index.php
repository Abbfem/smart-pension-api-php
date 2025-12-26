<?php

/**
 * People's Pension API Examples - Index/Dashboard
 * 
 * This page provides links to all example endpoints.
 */

require_once __DIR__ . '/helpers.php';

$config = loadConfig();
initializeEnvironment($config);
startSession();

$isAuthenticated = false;
try {
    $isAuthenticated = isAuthenticated();
} catch (Exception $e) {
    // Not authenticated
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>People's Pension API Examples</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        h1, h2 {
            color: #333;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .status {
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .status.authenticated {
            background-color: #d4edda;
            color: #155724;
        }
        .status.not-authenticated {
            background-color: #f8d7da;
            color: #721c24;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        li:last-child {
            border-bottom: none;
        }
        .env-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            background-color: #ffc107;
            color: #000;
        }
        .env-badge.live {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <h1>People's Pension API Examples</h1>
    
    <div class="card">
        <div class="status <?= $isAuthenticated ? 'authenticated' : 'not-authenticated' ?>">
            <?php if ($isAuthenticated): ?>
                ✓ Authenticated - You can access the API
            <?php else: ?>
                ✗ Not authenticated - Please authenticate first
            <?php endif; ?>
        </div>
        
        <p>
            Environment: 
            <span class="env-badge <?= $config['environment'] ?>">
                <?= strtoupper($config['environment']) ?>
            </span>
        </p>
    </div>
    
    <div class="card">
        <h2>Authentication</h2>
        <ul>
            <li><a href="oauth2/authorize.php">Authorize (Start OAuth2 Flow)</a></li>
            <li><a href="oauth2/callback.php">OAuth2 Callback (automatic)</a></li>
            <li><a href="oauth2/logout.php">Logout</a></li>
        </ul>
    </div>
    
    <div class="card">
        <h2>Accounts API</h2>
        <ul>
            <li><a href="account/list.php">List All Accounts</a></li>
            <li><a href="account/get.php?id=<?= $config['test_account_id'] ?>">Get Account Details</a></li>
            <li><a href="account/opt-outs.php?id=<?= $config['test_account_id'] ?>">Get Opt-Outs</a></li>
        </ul>
    </div>
    
    <div class="card">
        <h2>Contributions API</h2>
        <ul>
            <li><a href="contributions/submit.php">Submit Contributions (Example)</a></li>
            <li><a href="contributions/status.php">Check Contribution Status</a></li>
        </ul>
    </div>
    
    <div class="card">
        <h2>Documentation</h2>
        <ul>
            <li><a href="https://developer.peoplespartnership.co.uk/getting-started/" target="_blank">Getting Started</a></li>
            <li><a href="https://developer.peoplespartnership.co.uk/develop/v2/api-reference/" target="_blank">API Reference</a></li>
            <li><a href="https://developer.peoplespartnership.co.uk/fields/" target="_blank">Fields Reference</a></li>
            <li><a href="https://developer.peoplespartnership.co.uk/errors/" target="_blank">Error Codes</a></li>
        </ul>
    </div>
</body>
</html>
