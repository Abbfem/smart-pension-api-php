<?php

/**
 * Uses an existing access token to fetch one company with an included relation.
 *
 * Run: SMART_ACCESS_TOKEN=... php examples/smart-pension/api/access-token.php <companyId>
 */

require __DIR__.'/../../../vendor/autoload.php';

use SMART\Api\Exceptions\ApiException;
use SMART\Api\SmartClient;

$companyId = $argv[1] ?? 1;

try {
    $smart = new SmartClient(getenv('SMART_ACCESS_TOKEN'), ['environment' => 'sandbox']);

    $company = $smart->companies()->getCompany($companyId, ['include' => ['scheme_detail']])->getArray();

    print_r($company);
} catch (ApiException $e) {
    echo "API error {$e->getStatusCode()}: {$e->getMessage()}\n";
}
