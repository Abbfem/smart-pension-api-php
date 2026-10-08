<?php

/**
 * Uploads a PAPDIS CSV through the multipart imports endpoint and polls the import results.
 *
 * Run: SMART_ACCESS_TOKEN=... php examples/smart-pension/api/papdis-import.php <companyId> <file.csv>
 */

require __DIR__.'/../../../vendor/autoload.php';

use SMART\Api\Exceptions\ApiException;
use SMART\Api\SmartClient;

[, $companyId, $file] = $argv + [null, 1, 'papdis.csv'];

try {
    $smart = new SmartClient(getenv('SMART_ACCESS_TOKEN'), ['environment' => 'sandbox']);

    $import = $smart->imports()->createImport($companyId, [
        'type' => 'PapdisImport',
        'file' => fopen($file, 'r'),
    ])->getArray();
    echo "Import {$import['id']} created\n";

    for ($i = 0; $i < 10; $i++) {
        sleep(3);
        $results = $smart->imports()->listImportResults($companyId, $import['id'], ['limit' => 50])->getArray();
        echo count($results['data'] ?? []), " results so far\n";
        if (!empty($results['data'])) {
            break;
        }
    }
} catch (ApiException $e) {
    echo "API error {$e->getStatusCode()}: {$e->getMessage()}\n";
}
