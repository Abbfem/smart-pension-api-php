<?php

/**
 * Check contribution status.
 * 
 * GET /contributions/{contributionId}/status
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Contributions\GetContributionStatus;
use PeoplesPension\Contributions\GetContributionErrors;

$config = loadConfig();
initializeEnvironment($config);
startSession();

if (!isAuthenticated()) {
    header('Location: ../oauth2/authorize.php');
    exit;
}

$contributionId = $_GET['id'] ?? '';
$status = null;
$errors = [];

if ($contributionId) {
    try {
        // Get status
        $statusRequest = new GetContributionStatus($contributionId);
        $status = $statusRequest->getStatus();
        
        // If failed, get errors
        if ($status && $status->failed) {
            $errorsRequest = new GetContributionErrors($contributionId);
            $errors = $errorsRequest->getErrors();
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contribution Status - People's Pension API</title>
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
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
        a {
            color: #007bff;
            text-decoration: none;
        }
        .back {
            margin-bottom: 20px;
        }
        .status-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin: 20px 0;
        }
        .status-item {
            text-align: center;
            padding: 15px;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .status-item.active {
            background: #007bff;
            color: white;
        }
        .status-item.success {
            background: #28a745;
            color: white;
        }
        .status-item.error {
            background: #dc3545;
            color: white;
        }
        .status-label {
            font-size: 0.8em;
            margin-top: 5px;
        }
        .error-list {
            background: #f8d7da;
            padding: 15px;
            border-radius: 4px;
            margin-top: 15px;
        }
        .error-item {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f5c6cb;
        }
        .error-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .error-code {
            font-family: monospace;
            background: #721c24;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="back"><a href="../index.php">← Back to Examples</a></div>
    
    <h1>Check Contribution Status</h1>
    
    <div class="card">
        <form method="GET" action="">
            <div class="form-group">
                <label for="id">Contribution ID</label>
                <input type="text" id="id" name="id" value="<?= htmlspecialchars($contributionId) ?>" 
                       placeholder="e.g., 3699f346-326f-46ab-9b6d-f1313ecc0bdc">
            </div>
            <button type="submit">Check Status</button>
        </form>
    </div>
    
    <?php if ($status): ?>
    <div class="card">
        <h2>Status for <?= htmlspecialchars($status->id) ?></h2>
        
        <div class="status-grid">
            <div class="status-item <?= $status->received ? 'active' : '' ?>">
                <div><?= $status->received ? '✓' : '○' ?></div>
                <div class="status-label">Received</div>
            </div>
            <div class="status-item <?= $status->validating ? 'active' : '' ?>">
                <div><?= $status->validating ? '⟳' : '○' ?></div>
                <div class="status-label">Validating</div>
            </div>
            <div class="status-item <?= $status->accepted ? 'success' : '' ?>">
                <div><?= $status->accepted ? '✓' : '○' ?></div>
                <div class="status-label">Accepted</div>
            </div>
            <div class="status-item <?= $status->failed ? 'error' : '' ?>">
                <div><?= $status->failed ? '✗' : '○' ?></div>
                <div class="status-label">Failed</div>
            </div>
            <div class="status-item <?= $status->processed ? 'success' : '' ?>">
                <div><?= $status->processed ? '✓' : '○' ?></div>
                <div class="status-label">Processed</div>
            </div>
        </div>
        
        <?php if ($status->nextPollAfter): ?>
        <p><strong>Next poll after:</strong> <?= htmlspecialchars($status->nextPollAfter) ?></p>
        <?php endif; ?>
        
        <?php if (!$status->isComplete()): ?>
        <p><a href="?id=<?= urlencode($status->id) ?>">↻ Refresh Status</a></p>
        <?php endif; ?>
    </div>
    
    <?php if ($status->failed && !empty($errors)): ?>
    <div class="card">
        <h2>Validation Errors (<?= count($errors) ?>)</h2>
        
        <div class="error-list">
            <?php foreach ($errors as $error): ?>
            <div class="error-item">
                <p>
                    <span class="error-code"><?= htmlspecialchars($error->code) ?></span>
                    <?= htmlspecialchars($error->title) ?>
                </p>
                <?php if ($error->uniqueId): ?>
                <p><strong>Employee:</strong> <?= htmlspecialchars($error->uniqueId) ?></p>
                <?php endif; ?>
                <?php if ($error->providedValue): ?>
                <p><strong>Provided:</strong> <?= htmlspecialchars($error->providedValue) ?></p>
                <?php endif; ?>
                <?php if ($error->expectedValue): ?>
                <p><strong>Expected:</strong> <?= htmlspecialchars($error->expectedValue) ?></p>
                <?php endif; ?>
                <?php if ($error->aboutLink): ?>
                <p><a href="<?= htmlspecialchars($error->aboutLink) ?>" target="_blank">More info →</a></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php elseif ($contributionId): ?>
    <div class="card">
        <p>No status found for contribution ID: <?= htmlspecialchars($contributionId) ?></p>
    </div>
    <?php endif; ?>
</body>
</html>
