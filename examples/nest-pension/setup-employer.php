<?php

/**
 * NEST Pension - Setup Employer Example
 * 
 * This file demonstrates how to setup a new employer in NEST.
 */

require_once __DIR__ . '/config.php';

use NestPension\Services\SetupEmployerService;
use NestPension\Models\Request\SetupEmployerRequest;
use NestPension\Exceptions\NestException;

try {
    // Initialize the service
    $setupService = new SetupEmployerService();
    
    // Create the setup employer request
    $request = new SetupEmployerRequest();
    
    // Set employer details
    $request->setEmployerName('Example Company Ltd');
    $request->setPayeReference('123/A456');
    $request->setCompaniesHouseNumber('12345678');
    $request->setStagingDate('2024-04-01');
    $request->setFirstContributionDueDate('2024-05-01');
    
    // Set employer address
    $request->setAddress([
        'AddressLine1' => '123 Business Street',
        'AddressLine2' => 'Suite 100',
        'Town' => 'London',
        'County' => 'Greater London',
        'PostCode' => 'SW1A 1AA',
        'Country' => 'UK',
    ]);
    
    // Set contact details
    $request->setContactDetails([
        'ContactName' => 'John Manager',
        'Email' => 'john.manager@example.com',
        'Phone' => '020 1234 5678',
    ]);
    
    // Add default group
    $request->addGroup([
        'GroupId' => 'DEFAULT_GROUP',
        'GroupName' => 'Standard Pension Scheme',
        'EmployerContributionRate' => 3.0,
        'MemberContributionRate' => 5.0,
        'PensionCalculationBasis' => 'QualifyingEarnings',
        'TaxReliefMethod' => 'ReliefAtSource',
        'IsDefault' => 'true',
    ]);
    
    // Add alternative group for higher earners
    $request->addGroup([
        'GroupId' => 'SENIOR_GROUP',
        'GroupName' => 'Senior Staff Scheme',
        'EmployerContributionRate' => 5.0,
        'MemberContributionRate' => 5.0,
        'PensionCalculationBasis' => 'TotalEarnings',
        'TaxReliefMethod' => 'NetPay',
        'IsDefault' => 'false',
    ]);
    
    // Add payment source
    $request->addPaymentSource([
        'PaymentSourceId' => 'PS001',
        'PaymentSourceName' => 'Main Payroll',
        'BankAccountName' => 'Example Company Ltd',
        'SortCode' => '123456',
        'AccountNumber' => '12345678',
    ]);
    
    echo "Setting up new employer...\n";
    echo "Employer: " . $request->getEmployerName() . "\n";
    echo "PAYE Reference: " . $request->getPayeReference() . "\n";
    
    // Submit the setup request
    $asyncResponse = $setupService->setupNewEmployer($request);
    
    echo "Request submitted. UID: " . $asyncResponse->getUid() . "\n";
    echo "Location URL: " . $asyncResponse->getLocationUrl() . "\n";
    
    // Poll for completion
    $maxAttempts = 60;
    $attempt = 0;
    
    while ($attempt < $maxAttempts) {
        $status = $setupService->getSetupEmployerStatus($asyncResponse->getUid());
        
        echo "Status check #{$attempt}: " . $status->getStatusDescription() . "\n";
        
        if ($status->isCompleted()) {
            $response = $setupService->getSetupEmployerResponse($asyncResponse->getUid());
            
            if ($response->isSuccessful()) {
                echo "\nEmployer setup completed successfully!\n";
                echo "Employer Reference Number: " . $response->getEmployerReferenceNumber() . "\n";
                echo "\n*** IMPORTANT: Save this reference number for future API calls ***\n";
            } else {
                echo "\nEmployer setup completed with issues.\n";
                foreach ($response->getMessages() as $message) {
                    print_r($message);
                }
            }
            
            break;
        }
        
        if ($status->isFailed()) {
            echo "Setup failed: " . $status->getErrorMessage() . "\n";
            break;
        }
        
        $attempt++;
        sleep(5);
    }
    
} catch (NestException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
