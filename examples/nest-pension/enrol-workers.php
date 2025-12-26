<?php

/**
 * NEST Pension - Enrol Workers Example
 * 
 * This file demonstrates how to enrol workers into a NEST pension scheme.
 */

require_once __DIR__ . '/config.php';

use NestPension\Services\EnrolWorkersService;
use NestPension\Models\Request\EnrolWorkersRequest;
use NestPension\Exceptions\NestException;

try {
    // Initialize the service
    $enrolService = new EnrolWorkersService();
    
    // Create the enrol workers request
    $request = new EnrolWorkersRequest();
    
    // Add workers to enrol
    $request->addWorker([
        'employee_id' => 'EMP001',
        'title' => 'Mr',
        'first_name' => 'John',
        'last_name' => 'Smith',
        'date_of_birth' => '1985-03-15',
        'ni_number' => 'AB123456C',
        'email' => 'john.smith@example.com',
        'employment_start_date' => '2024-01-15',
        'salary' => 35000.00,
        'joining_method' => 'Auto-enrolment',
        'group_id' => 'DEFAULT_GROUP',
    ]);
    
    $request->addWorker([
        'employee_id' => 'EMP002',
        'title' => 'Ms',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'date_of_birth' => '1990-07-22',
        'ni_number' => 'CD654321B',
        'email' => 'jane.doe@example.com',
        'employment_start_date' => '2024-02-01',
        'salary' => 42000.00,
        'joining_method' => 'Auto-enrolment',
        'group_id' => 'DEFAULT_GROUP',
    ]);
    
    // Add group configuration (optional)
    $request->addGroup([
        'group_id' => 'DEFAULT_GROUP',
        'group_name' => 'Standard Scheme',
        'employer_contribution_rate' => 3.0,
        'member_contribution_rate' => 5.0,
    ]);
    
    // Your employer reference number (provided by NEST)
    $empRefNo = 'EMP123456';
    
    echo "Submitting enrol workers request...\n";
    echo "Workers to enrol: " . $request->getWorkerCount() . "\n";
    
    // Option 1: Start async operation and poll for result
    // This is the recommended approach for production
    $asyncResponse = $enrolService->enrolWorkers($empRefNo, $request);
    
    echo "Request submitted. UID: " . $asyncResponse->getUid() . "\n";
    echo "Location URL: " . $asyncResponse->getLocationUrl() . "\n";
    
    // Poll for status (you might want to do this in a background job)
    $maxAttempts = 60;
    $attempt = 0;
    
    while ($attempt < $maxAttempts) {
        $status = $enrolService->getEnrolWorkersStatus($empRefNo, $asyncResponse->getUid());
        
        echo "Status check #{$attempt}: " . $status->getStatusDescription() . "\n";
        
        if ($status->isCompleted()) {
            // Get the final response
            $response = $enrolService->getEnrolWorkersResponse($empRefNo, $asyncResponse->getUid());
            
            echo "\nEnrolment completed!\n";
            echo "Enrolled workers: " . $response->getEnrolledCount() . "\n";
            echo "Failed workers: " . $response->getFailedCount() . "\n";
            
            if ($response->hasEnrolledWorkers()) {
                echo "\nSuccessfully enrolled:\n";
                foreach ($response->getEnrolledWorkers() as $worker) {
                    print_r($worker);
                }
            }
            
            if (!$response->isFullySuccessful()) {
                echo "\nFailed enrolments:\n";
                foreach ($response->getFailedWorkers() as $worker) {
                    print_r($worker);
                }
            }
            
            break;
        }
        
        if ($status->isFailed()) {
            echo "Enrolment failed: " . $status->getErrorMessage() . "\n";
            break;
        }
        
        $attempt++;
        sleep(5); // Wait 5 seconds before next status check
    }
    
    // Option 2: Use the convenience method (blocks until completion)
    // $response = $enrolService->enrolWorkersAndWait($empRefNo, $request, 300, 5);
    
} catch (NestException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (!empty($e->getContext())) {
        echo "Context: " . json_encode($e->getContext()) . "\n";
    }
}
