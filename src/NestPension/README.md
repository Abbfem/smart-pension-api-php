# NEST Pension API - PHP Library

This is the NEST Pension API integration for the Multi-Pension PHP library.

## Overview

NEST (National Employment Savings Trust) is a UK workplace pension scheme. This library provides a PHP client for interacting with the NEST Web Services API.

## Requirements

- PHP 8.2 or higher
- GuzzleHTTP
- Valid NEST PSP (Payroll Software Provider) credentials

## Installation

The NEST Pension integration is part of the main pension library. Ensure the main library is installed:

```bash
composer require shynne109/smart-pension-api-php
```

## Configuration

```php
use NestPension\Environment\Environment;

$env = Environment::getInstance();

// Set environment (sandbox or live)
$env->setToSandbox();
// $env->setToLive();

// Configure credentials
$env->setCredentials('your_username', 'your_password');

// Configure provider information (required by NEST)
$env->setProviderInfo('Your Software Name', '1.0.0');
```

## Features

### 1. Setup New Employer

```php
use NestPension\Services\SetupEmployerService;
use NestPension\Models\Request\SetupEmployerRequest;

$service = new SetupEmployerService();
$request = new SetupEmployerRequest();

$request->setEmployerName('Company Ltd');
$request->setPayeReference('123/A456');
$request->addGroup([
    'GroupId' => 'DEFAULT',
    'GroupName' => 'Standard Scheme',
    'EmployerContributionRate' => 3.0,
    'MemberContributionRate' => 5.0,
]);

$response = $service->setupNewEmployerAndWait($request);
$empRefNo = $response->getEmployerReferenceNumber();
```

### 2. Enrol Workers

```php
use NestPension\Services\EnrolWorkersService;
use NestPension\Models\Request\EnrolWorkersRequest;

$service = new EnrolWorkersService();
$request = new EnrolWorkersRequest();

$request->addWorker([
    'employee_id' => 'EMP001',
    'first_name' => 'John',
    'last_name' => 'Smith',
    'date_of_birth' => '1985-03-15',
    'ni_number' => 'AB123456C',
    'employment_start_date' => '2024-01-15',
]);

$response = $service->enrolWorkersAndWait('EMP123456', $request);
```

### 3. Update Contributions

```php
use NestPension\Services\UpdateContributionsService;
use NestPension\Models\Request\UpdateContributionsRequest;

$service = new UpdateContributionsService();
$request = new UpdateContributionsRequest();

$request->setPayrollPeriod('2024-03');
$request->setEarnPeriodStart('2024-03-01');
$request->setEarnPeriodEnd('2024-03-31');

$request->addContribution([
    'EmployeeId' => 'EMP001',
    'PensionableEarnings' => 2916.67,
    'EmployerContribution' => 87.50,
    'MemberContribution' => 145.83,
]);

$response = $service->updateContributionsAndWait('EMP123456', $request);
```

### 4. Approve Payment

```php
use NestPension\Services\ApprovePaymentService;
use NestPension\Models\Request\ApprovePaymentRequest;

$service = new ApprovePaymentService();
$request = new ApprovePaymentRequest();

$request->setPaymentSourceId('PS001');
$request->setAmount(1500.00);
$request->setPaymentDate('2024-03-25');

$response = $service->approvePaymentAndWait('EMP123456', $request);
```

## Async Operations

All NEST API operations are asynchronous. The library provides two ways to handle this:

### Option 1: Convenience Methods (Blocking)

```php
// Blocks until complete or timeout
$response = $service->enrolWorkersAndWait($empRefNo, $request, 300, 5);
```

### Option 2: Manual Polling

```php
// Start the operation
$asyncResponse = $service->enrolWorkers($empRefNo, $request);
$uid = $asyncResponse->getUid();

// Poll for status
$status = $service->getEnrolWorkersStatus($empRefNo, $uid);

if ($status->isCompleted()) {
    $response = $service->getEnrolWorkersResponse($empRefNo, $uid);
}
```

## API Endpoints

The library supports the following NEST Web Services endpoints:

| Operation | Endpoint |
|-----------|----------|
| Setup Employer | `/psp-webservices/employer/v1/setup-new-employer` |
| Enrol Workers | `/psp-webservices/employer/v1/{empRefNo}/enrol-workers` |
| Update Contributions | `/psp-webservices/employer/v1/{empRefNo}/update-contributions` |
| Approve Payment | `/psp-webservices/employer/v1/{empRefNo}/approve-payment` |

## Environment URLs

| Environment | URL |
|-------------|-----|
| Sandbox | `https://ws-test.nestpensions.org.uk` |
| Live | `https://ws.nestpensions.org.uk` |

## Error Handling

```php
use NestPension\Exceptions\NestException;
use NestPension\Exceptions\AuthenticationException;
use NestPension\Exceptions\HttpException;
use NestPension\Exceptions\ValidationException;

try {
    $response = $service->enrolWorkersAndWait($empRefNo, $request);
} catch (AuthenticationException $e) {
    // Handle authentication errors
} catch (HttpException $e) {
    // Handle HTTP errors
    $statusCode = $e->getHttpStatusCode();
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->getErrors();
} catch (NestException $e) {
    // Handle general NEST errors
    $context = $e->getContext();
}
```

## License

MIT License - see LICENSE.md for details.
