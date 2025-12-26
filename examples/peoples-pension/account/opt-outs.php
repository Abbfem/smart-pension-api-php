<?php

/**
 * Get opt-outs for an account.
 * 
 * GET /accounts/{accountId}/opt-outs
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Account\GetOptOuts;

$config = loadConfig();
initializeEnvironment($config);
startSession();

if (!isAuthenticated()) {
    header('Location: ../oauth2/authorize.php');
    exit;
}

$accountId = $_GET['id'] ?? $config['test_account_id'];
$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

try {
    $request = new GetOptOuts($accountId, $startDate, $endDate);
    $response = $request->fire();
    
    if ($response->isNoContent()) {
        $optOuts = [];
        $hasOptOuts = false;
    } elseif ($response->isSuccess()) {
        $optOuts = $request->getOptOuts();
        $hasOptOuts = !empty($optOuts);
    } else {
        echo '<h1>Error</h1>';
        echo '<pre>' . json_encode($response->getErrors(), JSON_PRETTY_PRINT) . '</pre>';
        echo '<p><a href="../index.php">Back to Examples</a></p>';
        exit;
    }

} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opt-Outs for Account <?= htmlspecialchars($accountId) ?> - People's Pension API</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1000px;
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
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        label {
            font-weight: 500;
            font-size: 0.9em;
        }
        input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 8px 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .badge.processed { background: #d4edda; color: #155724; }
        .badge.processing { background: #fff3cd; color: #856404; }
        .badge.no-refund { background: #f8f9fa; color: #666; }
    </style>
</head>
<body>
    <div class="back"><a href="../index.php">← Back to Examples</a></div>
    
    <h1>Opt-Outs for Account <?= htmlspecialchars($accountId) ?></h1>
    
    <div class="card">
        <h2>Filter by Date</h2>
        <form method="GET" class="filter-form">
            <input type="hidden" name="id" value="<?= htmlspecialchars($accountId) ?>">
            <div class="form-group">
                <label for="start_date">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="end_date">End Date</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate ?? '') ?>">
            </div>
            <button type="submit">Filter</button>
        </form>
    </div>
    
    <div class="card">
        <h2>Opt-Outs (<?= count($optOuts) ?>)</h2>
        
        <?php if ($hasOptOuts): ?>
        <table>
            <thead>
                <tr>
                    <th>Unique ID</th>
                    <th>Name</th>
                    <th>NI Number</th>
                    <th>Opt-Out Date</th>
                    <th>Channel</th>
                    <th>Refund Status</th>
                    <th>Employee Refund</th>
                    <th>Employer Refund</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($optOuts as $optOut): ?>
                <tr>
                    <td><?= htmlspecialchars($optOut->uniqueId) ?></td>
                    <td><?= htmlspecialchars($optOut->forename . ' ' . $optOut->surname) ?></td>
                    <td><?= htmlspecialchars($optOut->niNumber ?? 'N/A') ?></td>
                    <td><?= formatDate($optOut->optOutDate) ?></td>
                    <td><?= htmlspecialchars($optOut->optOutChannel ?? 'N/A') ?></td>
                    <td>
                        <?php
                        $statusClass = match($optOut->refundStatus) {
                            'Processed' => 'processed',
                            'Processing' => 'processing',
                            default => 'no-refund'
                        };
                        ?>
                        <span class="badge <?= $statusClass ?>">
                            <?= htmlspecialchars($optOut->refundStatus ?? 'N/A') ?>
                        </span>
                    </td>
                    <td><?= formatCurrency($optOut->employeeRefund) ?></td>
                    <td><?= formatCurrency($optOut->employerRefund) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>No opt-outs found for this account<?= $startDate || $endDate ? ' with the specified date range' : '' ?>.</p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <a href="get.php?id=<?= urlencode($accountId) ?>">← Back to Account Details</a>
    </div>
</body>
</html>
