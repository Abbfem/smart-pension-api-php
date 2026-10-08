<?php

/**
 * Sends two operations in a single batch request.
 *
 * Run: SMART_ACCESS_TOKEN=... php examples/smart-pension/api/batch.php <companyId>
 */

require __DIR__.'/../../../vendor/autoload.php';

use SMART\Api\Exceptions\ApiException;
use SMART\Api\SmartClient;

$companyId = $argv[1] ?? 1;

try {
    $smart = new SmartClient(getenv('SMART_ACCESS_TOKEN'), ['environment' => 'sandbox']);

    $response = $smart->batch()->batch([
        'operations' => [
            ['path' => "companies/{$companyId}", 'http_method' => 'GET'],
            ['path' => "companies/{$companyId}/employees", 'http_method' => 'GET'],
        ],
    ]);

    print_r($response->getArray());
} catch (ApiException $e) {
    echo "API error {$e->getStatusCode()}: {$e->getMessage()}\n";
}
