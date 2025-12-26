<?php

/**
 * Get account details.
 * 
 * GET /accounts/{accountId}
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Account\GetAccount;

$config = loadConfig();
initializeEnvironment($config);
startSession();

if (!isAuthenticated()) {
    header('Location: ../oauth2/authorize.php');
    exit;
}

$accountId = $_GET['id'] ?? $config['test_account_id'];

try {
    $request = new GetAccount($accountId);
    $response = $request->fire();
    
    if (!$response->isSuccess()) {
        echo '<h1>Error</h1>';
        echo '<pre>' . json_encode($response->getErrors(), JSON_PRETTY_PRINT) . '</pre>';
        echo '<p><a href="../index.php">Back to Examples</a></p>';
        exit;
    }
    
    $account = $request->getAccount();

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account <?= htmlspecialchars($accountId) ?> - People's Pension API</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1, h2 { color: #333; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
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
        .prp {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="back"><a href="../index.php">← Back to Examples</a></div>
    
    <?php if ($account): ?>
    <h1><?= htmlspecialchars($account->accountName) ?></h1>
    
    <div class="card">
        <h2>Account Details</h2>
        <table>
            <tr>
                <th>Account ID</th>
                <td><?= htmlspecialchars($account->id) ?></td>
            </tr>
            <tr>
                <th>Company Name</th>
                <td><?= htmlspecialchars($account->companyName) ?></td>
            </tr>
            <tr>
                <th>Staging Date</th>
                <td><?= formatDate($account->stagingDate) ?></td>
            </tr>
            <tr>
                <th>PRP Frequency</th>
                <td><?= htmlspecialchars($account->prpFrequency) ?></td>
            </tr>
            <tr>
                <th>Payroll Frequency</th>
                <td><?= htmlspecialchars($account->payrollFrequency) ?></td>
            </tr>
            <tr>
                <th>Tax Basis</th>
                <td><?= htmlspecialchars($account->taxBasis) ?></td>
            </tr>
        </table>
        
        <?php if ($account->nextPayReferencePeriod): ?>
        <div class="prp">
            <strong>Next Pay Reference Period:</strong><br>
            <?= formatDate($account->nextPayReferencePeriod->start) ?> 
            to 
            <?= formatDate($account->nextPayReferencePeriod->end) ?>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Worker Groups (<?= count($account->workerGroups) ?>)</h2>
        
        <?php if (!empty($account->workerGroups)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Earnings Basis</th>
                    <th>Employee %</th>
                    <th>Employer %</th>
                    <th>Effective From</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($account->workerGroups as $wg): ?>
                <tr>
                    <td><?= htmlspecialchars($wg->id) ?></td>
                    <td><?= htmlspecialchars($wg->description) ?></td>
                    <td><?= htmlspecialchars($wg->earningsBasis) ?></td>
                    <td>
                        <?php if ($wg->employeeContributionPercent > 0): ?>
                            <?= $wg->employeeContributionPercent ?>%
                        <?php else: ?>
                            <?= formatCurrency($wg->employeeContributionAmount) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($wg->employerContributionPercent > 0): ?>
                            <?= $wg->employerContributionPercent ?>%
                        <?php else: ?>
                            <?= formatCurrency($wg->employerContributionAmount) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $wg->effective ? formatDate($wg->effective->start) : 'N/A' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No worker groups found.</p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h2>Actions</h2>
        <p>
            <a href="opt-outs.php?id=<?= urlencode($accountId) ?>">View Opt-Outs</a> |
            <a href="../contributions/submit.php?account_id=<?= urlencode($accountId) ?>">Submit Contributions</a>
        </p>
    </div>
    
    <?php else: ?>
    <div class="card">
        <h1>Account Not Found</h1>
        <p>The account with ID <?= htmlspecialchars($accountId) ?> was not found.</p>
    </div>
    <?php endif; ?>
</body>
</html>
