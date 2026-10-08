<?php

/**
 * Submits a contribution for an employee and lists contributions filtered by state.
 *
 * Run: SMART_ACCESS_TOKEN=... php examples/smart-pension/api/contributions.php <companyId> <employeeId>
 */

require __DIR__.'/../../../vendor/autoload.php';

use SMART\Api\Exceptions\ApiException;
use SMART\Api\Exceptions\ValidationException;
use SMART\Api\SmartClient;

[, $companyId, $employeeId] = $argv + [null, 1, 1];

try {
    $smart = new SmartClient(getenv('SMART_ACCESS_TOKEN'), ['environment' => 'sandbox']);

    $smart->contributions()->createContribution($companyId, $employeeId, [
        'starts_on' => '2024-04-06',
        'ends_on' => '2024-05-05',
        'period_type' => 'Monthly',
        'pensionable_earnings' => 1000,
        'gross_qualifying_earnings' => 1200,
        'employee_amount' => 40,
        'company_amount' => 30,
    ]);
    echo "Contribution submitted\n";

    $list = $smart->contributions()->listCompanyContributions($companyId, ['filter' => ['state' => ['pending']]])->getArray();
    echo count($list['data'] ?? []), " pending contributions\n";
} catch (ValidationException $e) {
    foreach ($e->getErrors() as $error) {
        echo json_encode($error), "\n";
    }
} catch (ApiException $e) {
    echo "API error {$e->getStatusCode()}: {$e->getMessage()}\n";
}
