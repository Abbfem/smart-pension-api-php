<?php

/**
 * Creates an employee, lists employees with a filter (auto-paginated) and updates one.
 *
 * Run: SMART_ACCESS_TOKEN=... php examples/smart-pension/api/employees.php <companyId>
 */

require __DIR__.'/../../../vendor/autoload.php';

use SMART\Api\Exceptions\ApiException;
use SMART\Api\SmartClient;

$companyId = $argv[1] ?? 1;

try {
    $smart = new SmartClient(getenv('SMART_ACCESS_TOKEN'), ['environment' => 'sandbox']);

    $created = $smart->employees()->createEmployee($companyId, [
        'forename' => 'Ada',
        'surname' => 'Lovelace',
        'gender' => 'Female',
        'date_of_birth' => '1985-12-10',
        'starts_on' => '2024-04-06',
        'email' => 'ada@example.com',
    ])->getArray();
    echo "Created employee {$created['id']}\n";

    $fetch = fn (array $query) => $smart->employees()->listEmployees($companyId, $query);
    foreach ($smart->paginate($fetch, ['filter' => ['surname' => ['Lovelace']]]) as $employee) {
        echo "{$employee['id']}: {$employee['forename']} {$employee['surname']}\n";
    }

    $smart->employees()->updateEmployee($companyId, $created['id'], ['telephone' => '07700900000']);
    echo "Updated employee {$created['id']}\n";
} catch (ApiException $e) {
    echo "API error {$e->getStatusCode()}: {$e->getMessage()}\n";
}
