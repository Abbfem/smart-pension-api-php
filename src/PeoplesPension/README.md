# People's Pension API PHP Library

A PHP library for integrating with [The People's Pension Payroll Integration API](https://developer.peoplespartnership.co.uk/).

## Requirements

- PHP 8.2 or higher
- Composer

## Installation

The library is included in this package. After cloning, run:

```bash
composer install
```

## Configuration

1. Register at the [People's Pension Developer Hub](https://developer.peoplespartnership.co.uk/registration/)
2. Create an application to get your client credentials
3. Configure your redirect URI in the Developer Hub Dashboard

## Quick Start

### 1. Set Up Environment

```php
use PeoplesPension\Environment\Environment;

// Use sandbox for development (default)
Environment::getInstance()->setToSandbox();

// Use live for production
Environment::getInstance()->setToLive();
```

### 2. OAuth2 Authentication

The API uses OAuth 2.0 Authorization Code Grant Flow.

```php
use PeoplesPension\Oauth2\Provider;
use PeoplesPension\Oauth2\AccessToken;

// Create provider
$provider = new Provider(
    clientID: 'your-client-id',
    clientSecret: 'your-client-secret',
    redirectUri: 'https://your-app.com/callback'
);

// Redirect user to authorization
$authUrl = $provider->getRedirectAuthorizationURL();
// or directly redirect:
// $provider->redirectToAuthorizationURL();

// Store state for CSRF protection
$_SESSION['oauth_state'] = $provider->getOAuthState();
```

In your callback handler:

```php
// Verify state
if ($_GET['state'] !== $_SESSION['oauth_state']) {
    throw new Exception('Invalid state');
}

// Exchange code for token
$accessToken = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

// Store token
AccessToken::set($accessToken);
```

### 3. Make API Requests

#### Get Accounts

```php
use PeoplesPension\Account\GetAccounts;
use PeoplesPension\Account\GetAccount;

// Get all accounts
$request = new GetAccounts();
$accounts = $request->getAccountSummaries();

foreach ($accounts as $account) {
    echo $account->accountName . ' (' . $account->id . ')' . PHP_EOL;
}

// Get specific account
$request = new GetAccount('855969');
$account = $request->getAccount();

echo $account->companyName;
echo $account->nextPayReferencePeriod->start;
```

#### Get Opt-Outs

```php
use PeoplesPension\Account\GetOptOuts;

$request = new GetOptOuts(
    accountId: '855969',
    startDate: '2024-01-01',  // Optional
    endDate: '2024-12-31'     // Optional
);

$optOuts = $request->getOptOuts();

foreach ($optOuts as $optOut) {
    echo $optOut->forename . ' ' . $optOut->surname;
    echo ' opted out on ' . $optOut->optOutDate;
}
```

#### Submit Contributions

```php
use PeoplesPension\Contributions\SubmitContributions;
use PeoplesPension\Contributions\Request\ContributionsPostBody;
use PeoplesPension\Models\DateRange;
use PeoplesPension\Models\Address;
use PeoplesPension\Models\EmployeeContribution;

// Create employee contribution
$employee = new EmployeeContribution(
    title: 'Mrs',
    gender: 'F',
    forename: 'Jane',
    surname: 'Smith',
    dateOfBirth: '1985-03-15',
    uniqueId: 'EMP001',
    address: new Address(
        line1: '123 Main Street',
        line2: 'Apartment 4',
        line3: 'London',
        line5: 'SW1A 1AA'
    ),
    employmentPeriod: new DateRange(start: '2020-01-01'),
    workerGroupId: 'S',
    autoEnrolmentStatus: 'Eligible',
    pensionableEarnings: 2500.00,
    employerContributionAmount: 75.00,
    employeeContributionAmount: 125.00,
    niNumber: 'AB123456C',
    autoEnrolmentDate: '2020-02-01'
);

// Create contributions body
$postBody = new ContributionsPostBody(
    accountId: '855969',
    payReferencePeriod: new DateRange(
        start: '2024-01-01',
        end: '2024-01-31'
    ),
    employees: [$employee],
    total: 200.00  // Sum of all contributions
);

// Submit
$request = new SubmitContributions($postBody);
$status = $request->submit();

echo 'Contribution ID: ' . $status->id;
```

#### Check Contribution Status

```php
use PeoplesPension\Contributions\GetContributionStatus;
use PeoplesPension\Contributions\GetContributionErrors;

// Check status
$statusRequest = new GetContributionStatus('contribution-id-here');
$status = $statusRequest->getStatus();

if ($status->processed) {
    echo 'Contributions processed successfully!';
} elseif ($status->failed) {
    // Get errors
    $errorsRequest = new GetContributionErrors('contribution-id-here');
    $errors = $errorsRequest->getErrors();
    
    foreach ($errors as $error) {
        echo $error->code . ': ' . $error->title;
    }
}
```

## API Reference

### Environment

| Method | Description |
|--------|-------------|
| `setToSandbox()` | Use sandbox environment |
| `setToLive()` | Use live environment |
| `isSandbox()` | Check if using sandbox |
| `isLive()` | Check if using live |

### Account Endpoints

| Class | Endpoint | Description |
|-------|----------|-------------|
| `GetAccounts` | `GET /accounts` | List all accessible accounts |
| `GetAccount` | `GET /accounts/{id}` | Get account details |
| `GetOptOuts` | `GET /accounts/{id}/opt-outs` | Get employee opt-outs |

### Contribution Endpoints

| Class | Endpoint | Description |
|-------|----------|-------------|
| `SubmitContributions` | `POST /contributions` | Submit employee contributions |
| `GetContributionStatus` | `GET /contributions/{id}/status` | Check submission status |
| `GetContributionErrors` | `GET /contributions/{id}/errors` | Get validation errors |

## Models

- `Account` - Admin account details with worker groups
- `AccountSummary` - Summary information for account listing
- `WorkerGroup` - Worker group with contribution settings
- `OptOut` - Employee opt-out information
- `EmployeeContribution` - Employee contribution data
- `ContributionsStatus` - Submission processing status
- `ContributionError` - Validation error details
- `Address` - Employee address
- `DateRange` - Date range (start/end)

## Error Handling

The library throws specific exceptions:

```php
use PeoplesPension\Exceptions\MissingAccessTokenException;
use PeoplesPension\Exceptions\InvalidPostBodyException;
use PeoplesPension\Exceptions\ApiException;

try {
    $request = new GetAccount('invalid');
    $account = $request->getAccount();
} catch (MissingAccessTokenException $e) {
    // Not authenticated
} catch (InvalidPostBodyException $e) {
    // Invalid request data
} catch (ApiException $e) {
    echo $e->getStatusCode();
    print_r($e->getErrors());
}
```

## Sandbox Testing

The sandbox environment provides test accounts for development:

- Account ID: `855969` - Franecki Group Weekly
- Account ID: `859763` - Hauck-Stracke Weekly
- Account ID: `840834` - Reynolds PLC Fortnightly

See [Test Data Documentation](https://developer.peoplespartnership.co.uk/develop/test-data/) for more details.

## Documentation

- [Getting Started](https://developer.peoplespartnership.co.uk/getting-started/)
- [Using the API](https://developer.peoplespartnership.co.uk/develop/using-the-api/)
- [API Reference](https://developer.peoplespartnership.co.uk/develop/v2/api-reference/)
- [Fields Reference](https://developer.peoplespartnership.co.uk/fields/)
- [Error Codes](https://developer.peoplespartnership.co.uk/errors/)

## Examples

See the `examples/peoples-pension/` directory for working examples:

- OAuth2 authentication flow
- List and view accounts
- Get opt-outs
- Submit contributions
- Check contribution status

## License

MIT License
