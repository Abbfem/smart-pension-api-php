<?php

/**
 * NEST Pension - Update Contributions Example
 * 
 * This file demonstrates how to submit contribution updates to NEST.
 */

require_once __DIR__ . '/config.php';

use NestPension\Services\UpdateContributionsService;
use NestPension\Models\Request\UpdateContributionsRequest;
use NestPension\Models\Common\ContributionDetails;
use NestPension\Exceptions\NestException;

try {
    // Initialize the service
    $contributionService = new UpdateContributionsService();
    
    // Create the update contributions request
    $request = new UpdateContributionsRequest();
    
    // Set contribution period details
    $request->setPayrollPeriod('2024-03');
    $request->setEarnPeriodStart('2024-03-01');
    $request->setEarnPeriodEnd('2024-03-31');
    $request->setPaymentSource('PS001');
    
    // Add contributions using array
    $request->addContribution([
        'EmployeeId' => 'EMP001',
        'MemberId' => 'NEST123456',
        'PensionableEarnings' => 2916.67, // Monthly salary
        'EmployerContribution' => 87.50,  // 3% of earnings
        'MemberContribution' => 145.83,   // 5% of earnings
        'ContributionPeriodStart' => '2024-03-01',
        'ContributionPeriodEnd' => '2024-03-31',
        'ContributionType' => 'Regular',
    ]);
    
    // Or use the ContributionDetails model
    $contribution = new ContributionDetails();
    $contribution
        ->setEmployeeId('EMP002')
        ->setMemberId('NEST654321')
        ->setPensionableEarnings(3500.00)
        ->setEmployerContribution(105.00)
        ->setMemberContribution(175.00)
        ->setContributionPeriodStart('2024-03-01')
        ->setContributionPeriodEnd('2024-03-31')
        ->setContributionType('Regular')
        ->setContributionBasis('QualifyingEarnings');
    
    $request->addContribution($contribution);
    
    // Add arrears contribution
    $request->addContribution([
        'EmployeeId' => 'EMP003',
        'MemberId' => 'NEST789012',
        'PensionableEarnings' => 2500.00,
        'EmployerContribution' => 75.00,
        'MemberContribution' => 125.00,
        'ContributionPeriodStart' => '2024-02-01',
        'ContributionPeriodEnd' => '2024-02-29',
        'ContributionType' => 'Arrears',
        'IsArrears' => 'true',
    ]);
    
    // Your employer reference number
    $empRefNo = 'EMP123456';
    
    echo "Submitting contribution update...\n";
    echo "Contributions: " . $request->getContributionCount() . "\n";
    echo "Total amount: £" . number_format($request->getTotalAmount(), 2) . "\n";
    
    // Submit the contributions
    $asyncResponse = $contributionService->updateContributions($empRefNo, $request);
    
    echo "Request submitted. UID: " . $asyncResponse->getUid() . "\n";
    echo "Location URL: " . $asyncResponse->getLocationUrl() . "\n";
    
    // Poll for completion
    $maxAttempts = 60;
    $attempt = 0;
    
    while ($attempt < $maxAttempts) {
        $status = $contributionService->getUpdateContributionsStatus($empRefNo, $asyncResponse->getUid());
        
        echo "Status check #{$attempt}: " . $status->getStatusDescription() . "\n";
        
        if ($status->isCompleted()) {
            $response = $contributionService->getUpdateContributionsResponse($empRefNo, $asyncResponse->getUid());
            
            echo "\nContribution update completed!\n";
            echo "Processed: " . $response->getProcessedCount() . "\n";
            echo "Failed: " . $response->getFailedCount() . "\n";
            
            if ($response->getTotalAmount() !== null) {
                echo "Total amount: £" . number_format($response->getTotalAmount(), 2) . "\n";
            }
            
            if (!$response->isFullySuccessful()) {
                echo "\nFailed contributions:\n";
                foreach ($response->getFailedContributions() as $failed) {
                    print_r($failed);
                }
            }
            
            break;
        }
        
        if ($status->isFailed()) {
            echo "Update failed: " . $status->getErrorMessage() . "\n";
            break;
        }
        
        $attempt++;
        sleep(5);
    }
    
} catch (NestException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
