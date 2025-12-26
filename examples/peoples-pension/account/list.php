<?php

/**
 * List all accounts accessible to the authenticated user.
 * 
 * GET /accounts
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Account\GetAccounts;

$config = loadConfig();
initializeEnvironment($config);
startSession();

if (!isAuthenticated()) {
    header('Location: ../oauth2/authorize.php');
    exit;
}

try {
    $request = new GetAccounts();
    $response = $request->fire();
    
    if (!$response->isSuccess()) {
        echo '<h1>Error</h1>';
        echo '<pre>' . json_encode($response->getErrors(), JSON_PRETTY_PRINT) . '</pre>';
        exit;
    }
    
    $accounts = $request->getAccountSummaries();

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accounts - People's Pension API</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 { color: #333; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .account-name {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }
        .account-id {
            color: #666;
            font-size: 0.9em;
        }
        .supported {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .supported.yes {
            background: #d4edda;
            color: #155724;
        }
        .supported.no {
            background: #f8d7da;
            color: #721c24;
        }
        .reason {
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
        .links {
            margin-top: 10px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .back {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="back"><a href="../index.php">← Back to Examples</a></div>
    
    <h1>Your Accounts</h1>
    <p>Found <?= count($accounts) ?> account(s)</p>
    
    <?php foreach ($accounts as $account): ?>
    <div class="card">
        <div class="account-name"><?= htmlspecialchars($account->accountName) ?></div>
        <div class="account-id">Account ID: <?= htmlspecialchars($account->id) ?></div>
        
        <p>
            <span class="supported <?= $account->isSupported ? 'yes' : 'no' ?>">
                <?= $account->isSupported ? '✓ Supported' : '✗ Not Supported' ?>
            </span>
        </p>
        
        <?php if (!$account->isSupported && $account->reason): ?>
        <div class="reason"><?= htmlspecialchars($account->reason) ?></div>
        <?php endif; ?>
        
        <p>
            <strong>Contractual Enrolment:</strong> <?= $account->hasContractualEnrolment ? 'Yes' : 'No' ?><br>
            <strong>Using Assessment:</strong> <?= $account->isUsingAssessment ? 'Yes' : 'No' ?>
        </p>
        
        <?php if ($account->isSupported): ?>
        <div class="links">
            <a href="get.php?id=<?= urlencode($account->id) ?>">View Details</a> |
            <a href="opt-outs.php?id=<?= urlencode($account->id) ?>">View Opt-Outs</a>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($accounts)): ?>
    <div class="card">
        <p>No accounts found. Make sure you have accounts linked to your credentials.</p>
    </div>
    <?php endif; ?>
</body>
</html>
