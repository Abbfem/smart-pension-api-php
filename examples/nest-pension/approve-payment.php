<?php

/**
 * NEST Pension - Approve Payment Example
 * 
 * This file demonstrates how to approve a payment to NEST.
 */

require_once __DIR__ . '/config.php';

use NestPension\Services\ApprovePaymentService;
use NestPension\Models\Request\ApprovePaymentRequest;
use NestPension\Exceptions\NestException;

try {
    // Initialize the service
    $paymentService = new ApprovePaymentService();
    
    // Create the approve payment request
    $request = new ApprovePaymentRequest();
    
    // Set payment details
    $request->setPaymentSourceId('PS001');
    $request->setAmount(1500.00);
    $request->setPaymentDate('2024-03-25');
    $request->setPaymentReference('PAY-2024-03-001');
    
    // Add schedule IDs for the contributions being paid
    $request->addScheduleId('SCH001');
    $request->addScheduleId('SCH002');
    
    // Your employer reference number
    $empRefNo = 'EMP123456';
    
    echo "Approving payment...\n";
    echo "Amount: £" . number_format($request->getAmount(), 2) . "\n";
    echo "Payment Date: " . $request->getPaymentDate() . "\n";
    echo "Payment Reference: " . $request->getPaymentReference() . "\n";
    
    // Submit the payment approval
    $asyncResponse = $paymentService->approvePayment($empRefNo, $request);
    
    echo "Request submitted. UID: " . $asyncResponse->getUid() . "\n";
    echo "Location URL: " . $asyncResponse->getLocationUrl() . "\n";
    
    // Poll for completion
    $maxAttempts = 60;
    $attempt = 0;
    
    while ($attempt < $maxAttempts) {
        $status = $paymentService->getApprovePaymentStatus($empRefNo, $asyncResponse->getUid());
        
        echo "Status check #{$attempt}: " . $status->getStatusDescription() . "\n";
        
        if ($status->isCompleted()) {
            $response = $paymentService->getApprovePaymentResponse($empRefNo, $asyncResponse->getUid());
            
            if ($response->isSuccessful()) {
                echo "\nPayment approved successfully!\n";
                echo "Payment Reference: " . $response->getPaymentReference() . "\n";
                echo "Status: " . $response->getStatus() . "\n";
            } else {
                echo "\nPayment approval failed.\n";
                echo "Status: " . $response->getStatus() . "\n";
                foreach ($response->getMessages() as $message) {
                    print_r($message);
                }
            }
            
            break;
        }
        
        if ($status->isFailed()) {
            echo "Payment approval failed: " . $status->getErrorMessage() . "\n";
            break;
        }
        
        $attempt++;
        sleep(5);
    }
    
} catch (NestException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
