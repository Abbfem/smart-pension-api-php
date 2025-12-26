<?php

/**
 * Submit contributions example.
 * 
 * POST /contributions
 * 
 * This is an example showing how to submit contributions.
 * In a real application, you would collect this data from your payroll system.
 */

require_once __DIR__ . '/../helpers.php';

use PeoplesPension\Account\GetAccount;
use PeoplesPension\Contributions\SubmitContributions;
use PeoplesPension\Contributions\Request\ContributionsPostBody;
use PeoplesPension\Models\DateRange;
use PeoplesPension\Models\Address;
use PeoplesPension\Models\EmployeeContribution;

$config = loadConfig();
initializeEnvironment($config);
startSession();

if (!isAuthenticated()) {
    header('Location: ../oauth2/authorize.php');
    exit;
}

$accountId = $_GET['account_id'] ?? $config['test_account_id'];
$submitted = false;
$result = null;
$error = null;

// Get account details to show next PRP
try {
    $accountRequest = new GetAccount($accountId);
    $account = $accountRequest->getAccount();
} catch (Exception $e) {
    $account = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Create employee contribution from form data
        $employee = new EmployeeContribution(
            title: $_POST['title'],
            gender: $_POST['gender'],
            forename: $_POST['forename'],
            surname: $_POST['surname'],
            dateOfBirth: $_POST['date_of_birth'],
            uniqueId: $_POST['unique_id'],
            address: new Address(
                line1: $_POST['address_line1'],
                line2: $_POST['address_line2'] ?: null,
                line3: $_POST['address_line3'] ?: null,
                line5: $_POST['address_postcode'] ?: null
            ),
            employmentPeriod: new DateRange(
                start: $_POST['employment_start']
            ),
            workerGroupId: $_POST['worker_group_id'],
            autoEnrolmentStatus: $_POST['ae_status'],
            pensionableEarnings: (float) $_POST['pensionable_earnings'],
            employerContributionAmount: (float) $_POST['employer_contribution'],
            employeeContributionAmount: (float) $_POST['employee_contribution'],
            niNumber: $_POST['ni_number'] ?: null,
            autoEnrolmentDate: $_POST['ae_date'] ?: null
        );
        
        $total = $employee->getTotalContribution();
        
        // Create contributions post body
        $postBody = new ContributionsPostBody(
            accountId: $accountId,
            payReferencePeriod: new DateRange(
                start: $_POST['prp_start'],
                end: $_POST['prp_end']
            ),
            employees: [$employee],
            total: $total
        );
        
        // Submit contributions
        $request = new SubmitContributions($postBody);
        $response = $request->fire();
        
        if ($response->isAccepted()) {
            $result = $request->submit();
            $submitted = true;
        } else {
            $error = 'Submission failed: ' . json_encode($response->getErrors());
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
    <title>Submit Contributions - People's Pension API</title>
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
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        button {
            padding: 12px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
        }
        button:hover {
            background: #0056b3;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        .back {
            margin-bottom: 20px;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="back"><a href="../index.php">← Back to Examples</a></div>
    
    <h1>Submit Contributions</h1>
    
    <?php if ($submitted && $result): ?>
    <div class="card">
        <div class="success">
            <h2>✓ Contributions Submitted!</h2>
            <p>Contribution ID: <?= htmlspecialchars($result->id) ?></p>
            <p>Status: Received</p>
            <?php if ($result->nextPollAfter): ?>
            <p>Check status after: <?= htmlspecialchars($result->nextPollAfter) ?></p>
            <?php endif; ?>
            <p><a href="status.php?id=<?= urlencode($result->id) ?>">Check Status</a></p>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="warning">
        <strong>Note:</strong> This is a demonstration form. In production, ensure all data complies with 
        The People's Pension validation rules. Do not use real personal data in the sandbox environment.
    </div>
    
    <form method="POST" action="">
        <div class="card">
            <h2>Pay Reference Period</h2>
            <p>Account ID: <?= htmlspecialchars($accountId) ?></p>
            <?php if ($account && $account->nextPayReferencePeriod): ?>
            <p><em>Next expected PRP: <?= formatDate($account->nextPayReferencePeriod->start) ?> to <?= formatDate($account->nextPayReferencePeriod->end) ?></em></p>
            <?php endif; ?>
            
            <div class="row">
                <div class="form-group">
                    <label for="prp_start">PRP Start Date</label>
                    <input type="date" id="prp_start" name="prp_start" required
                           value="<?= htmlspecialchars($account?->nextPayReferencePeriod?->start ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="prp_end">PRP End Date</label>
                    <input type="date" id="prp_end" name="prp_end" required
                           value="<?= htmlspecialchars($account?->nextPayReferencePeriod?->end ?? '') ?>">
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Employee Details</h2>
            
            <div class="row">
                <div class="form-group">
                    <label for="title">Title</label>
                    <select id="title" name="title" required>
                        <option value="Mr">Mr</option>
                        <option value="Mrs">Mrs</option>
                        <option value="Miss">Miss</option>
                        <option value="Ms">Ms</option>
                        <option value="Mx">Mx</option>
                        <option value="Dr">Dr</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <label for="forename">Forename</label>
                    <input type="text" id="forename" name="forename" required maxlength="30" value="Test">
                </div>
                <div class="form-group">
                    <label for="surname">Surname</label>
                    <input type="text" id="surname" name="surname" required maxlength="45" value="Employee">
                </div>
            </div>
            
            <div class="row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required value="1985-06-15">
                </div>
                <div class="form-group">
                    <label for="ni_number">NI Number (optional)</label>
                    <input type="text" id="ni_number" name="ni_number" maxlength="9" 
                           pattern="[A-Z]{2}[0-9]{6}[A-D]" placeholder="AB123456C">
                </div>
                <div class="form-group">
                    <label for="unique_id">Unique ID</label>
                    <input type="text" id="unique_id" name="unique_id" required maxlength="50" 
                           value="EMP<?= date('YmdHis') ?>">
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Address</h2>
            <div class="form-group">
                <label for="address_line1">Address Line 1</label>
                <input type="text" id="address_line1" name="address_line1" required maxlength="50" 
                       value="123 Test Street">
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="address_line2">Address Line 2</label>
                    <input type="text" id="address_line2" name="address_line2" maxlength="50">
                </div>
                <div class="form-group">
                    <label for="address_line3">Town/City</label>
                    <input type="text" id="address_line3" name="address_line3" maxlength="50" value="London">
                </div>
                <div class="form-group">
                    <label for="address_postcode">Postcode</label>
                    <input type="text" id="address_postcode" name="address_postcode" maxlength="25" value="SW1A 1AA">
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Employment & Enrolment</h2>
            <div class="row">
                <div class="form-group">
                    <label for="employment_start">Employment Start Date</label>
                    <input type="date" id="employment_start" name="employment_start" required value="2020-01-01">
                </div>
                <div class="form-group">
                    <label for="worker_group_id">Worker Group ID</label>
                    <input type="text" id="worker_group_id" name="worker_group_id" required maxlength="40" 
                           value="<?= htmlspecialchars($account?->workerGroups[0]?->id ?? 'S') ?>">
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <label for="ae_status">Auto Enrolment Status</label>
                    <select id="ae_status" name="ae_status" required>
                        <option value="Eligible">Eligible</option>
                        <option value="Non Eligible">Non Eligible</option>
                        <option value="Entitled">Entitled</option>
                        <option value="Contractual Enrolment">Contractual Enrolment</option>
                        <option value="Not Known">Not Known</option>
                        <option value="Already In Qualifying Scheme">Already In Qualifying Scheme</option>
                        <option value="Not Applicable">Not Applicable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ae_date">Auto Enrolment Date (if Eligible)</label>
                    <input type="date" id="ae_date" name="ae_date">
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2>Contributions</h2>
            <div class="row">
                <div class="form-group">
                    <label for="pensionable_earnings">Pensionable Earnings (£)</label>
                    <input type="number" id="pensionable_earnings" name="pensionable_earnings" 
                           required step="0.01" min="0" value="2000.00">
                </div>
                <div class="form-group">
                    <label for="employee_contribution">Employee Contribution (£)</label>
                    <input type="number" id="employee_contribution" name="employee_contribution" 
                           required step="0.01" min="0" value="100.00">
                </div>
                <div class="form-group">
                    <label for="employer_contribution">Employer Contribution (£)</label>
                    <input type="number" id="employer_contribution" name="employer_contribution" 
                           required step="0.01" min="0" value="60.00">
                </div>
            </div>
        </div>
        
        <div class="card">
            <button type="submit">Submit Contributions</button>
        </div>
    </form>
</body>
</html>
