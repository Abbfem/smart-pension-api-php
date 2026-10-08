<?php

/**
 * Machine-to-machine (client credentials) access to the Smart Pension API; lists companies.
 *
 * Run: SMART_CLIENT_ID=... SMART_CLIENT_SECRET=... php examples/smart-pension/api/client-credentials.php
 */

require __DIR__.'/../../../vendor/autoload.php';

use SMART\Api\Exceptions\ApiException;
use SMART\Api\SmartClient;
use SMART\Scope\Scope;

try {
    $smart = SmartClient::withClientCredentials(
        getenv('SMART_CLIENT_ID'),
        getenv('SMART_CLIENT_SECRET'),
        [Scope::SMP_COMPANIES, Scope::SMP_EMPLOYEES],
        ['environment' => 'sandbox']
    );

    $companies = $smart->companies()->listCompanies(['limit' => 10])->getArray();

    foreach ($companies['data'] ?? [] as $company) {
        echo "{$company['id']}: {$company['name']}\n";
    }
} catch (ApiException $e) {
    echo "API error {$e->getStatusCode()}: {$e->getMessage()}\n";
}
