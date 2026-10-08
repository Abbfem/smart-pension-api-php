# Smart Pension (Keystone) API — PHP usage guide

This library wraps the **Keystone API v12** used by Smart Pension
(<https://developers.autoenrolment.co.uk/smart/>). Every one of the **401 documented endpoints** is
available as a typed method on `SMART\Api\SmartClient`.

| Document | What it covers |
| --- | --- |
| This guide | Installation, authentication, calling endpoints, errors, pagination, uploads, versioning |
| [endpoints.md](endpoints.md) | Index of every endpoint → PHP method, with OAuth scopes |
| [resources/](resources/) | One page per resource: parameters, body fields, example bodies and responses |
| [examples/smart-pension/api](../../examples/smart-pension/api) | Runnable scripts |
| [AI agent skill](../../resources/boost/skills/smart-pension-api/SKILL.md) | Condensed instructions for coding agents (auto-installed by Laravel Boost; copy to `.claude/skills/` elsewhere). Maintainers: [AGENTS.md](../../AGENTS.md) |

---

## Contents

1. [Installation](#1-installation)
2. [Quick start](#2-quick-start)
3. [Environments](#3-environments)
4. [Authentication](#4-authentication)
5. [Calling endpoints](#5-calling-endpoints)
6. [Query parameters: pagination, sorting, filters, includes, fields](#6-query-parameters)
7. [Request bodies and file uploads](#7-request-bodies-and-file-uploads)
8. [Responses](#8-responses)
9. [Errors](#9-errors)
10. [Rate limits](#10-rate-limits)
11. [API versions](#11-api-versions)
12. [Batch requests](#12-batch-requests)
13. [Common integration flows](#13-common-integration-flows)
14. [Resource reference](#14-resource-reference)
15. [Testing your integration](#15-testing-your-integration)
16. [Regenerating from the OpenAPI spec](#16-regenerating-from-the-openapi-spec)
17. [Legacy request classes](#17-legacy-request-classes)

---

## 1. Installation

```bash
composer require shynne109/smart-pension-api-php
```

Requires PHP 8.2+, `guzzlehttp/guzzle` 7 and `league/oauth2-client` 2.

## 2. Quick start

```php
use SMART\Api\SmartClient;
use SMART\Api\Exceptions\ApiException;
use SMART\Scope\Scope;

$smart = SmartClient::withClientCredentials(
    getenv('SMART_CLIENT_ID'),
    getenv('SMART_CLIENT_SECRET'),
    [Scope::SMP_COMPANIES, Scope::SMP_EMPLOYEES, Scope::SMP_CONTRIBUTIONS],
    ['environment' => 'sandbox']
);

try {
    $company   = $smart->companies()->getCompany(1234, ['include' => ['owner']])->getArray();
    $employees = $smart->employees()->listEmployees(1234, ['limit' => 100, 'filter' => ['opt_state' => 'opted_in']])->getArray();

    $smart->contributions()->createContribution(1234, $employees['data'][0]['id'], [
        'starts_on'                 => '2026-09-01',
        'ends_on'                   => '2026-09-30',
        'period_type'               => 'Monthly',
        'gross_qualifying_earnings' => 2500.00,
        'pensionable_earnings'      => 2500.00,
    ]);
} catch (ApiException $e) {
    echo $e->getStatusCode(), ': ', $e->getMessage(), PHP_EOL;
    print_r($e->getErrors());
}
```

The pattern is always `$smart->{resource}()->{method}(...pathIds, $body, $query, $headers)`.

## 3. Environments

| Environment | API base URL | Identity (OAuth) | Notes |
| --- | --- | --- | --- |
| `dev` | `https://api.dev.autoenrolment.co.uk` | `https://id.dev.autoenrolment.co.uk` | Untested features, data purged often |
| `sandbox` (default) | `https://api.sandbox.autoenrolment.co.uk` | `https://id.sandbox.autoenrolment.co.uk` | Persistent test data; third parties simulated |
| `live` | `https://api.autoenrolment.co.uk` | `https://id.autoenrolment.co.uk` | Real payments and document signing |

Choose it per client:

```php
$smart = new SmartClient($token, ['environment' => 'live']);
```

or globally (also used by the legacy request classes):

```php
\SMART\Environment\Environment::getInstance()->setToLive();
```

To go through a proxy or a gateway, override the base URL. Other services (`id.`, `account-claiming.`)
are derived by swapping the leading `api.` sub-domain (a base URL that does not start with `api.` is used
as-is for every service):

```php
$smart = new SmartClient($token, ['base_url' => 'https://api.sandbox.my-proxy.example']);
```

All client options:

| Option | Type | Default | Purpose |
| --- | --- | --- | --- |
| `environment` | `dev`\|`sandbox`\|`live` | global `Environment` (sandbox) | Which Keystone environment to call |
| `base_url` | string | – | Override the API base URL |
| `version` | int\|null | `null` (latest) | Pin the API version, see [§11](#11-api-versions) |
| `headers` | array | `[]` | Extra headers on every request |
| `http_client` | `GuzzleHttp\ClientInterface` | new Guzzle client | Proxies, retries middleware, logging, mocks |
| `timeout` | float | `30` | Timeout when the default Guzzle client is built |
| `throw_on_error` | bool | `true` | Throw `ApiException` on non-2xx responses |

## 4. Authentication

Keystone uses OAuth 2.0. Get an API key by creating a partner account
([sandbox](https://partner.sandbox.autoenrolment.co.uk/partners/sign-up) /
[production](https://partner.autoenrolment.co.uk/partners/sign-up)); you need separate credentials per
environment.

`SmartClient` accepts any of:

| You have | Use |
| --- | --- |
| Client ID + secret (machine-to-machine) | `SmartClient::withClientCredentials($id, $secret, $scopes, $options)` |
| A token stored with `SMART\Oauth2\AccessToken::set()` | `SmartClient::fromSession($options)` |
| An access token string or `League\OAuth2\Client\Token\AccessToken` | `new SmartClient($token, $options)` |
| Your own token storage/refresh logic | implement `SMART\Api\Auth\TokenProvider` and pass it to `new SmartClient($provider)` |

### 4.1 Client credentials (server to server)

Recommended for payroll software, banks and other back-office integrations.

```php
use SMART\Scope\Scope;

$smart = SmartClient::withClientCredentials($clientId, $clientSecret, [
    Scope::SMP_COMPANIES,
    Scope::SMP_EMPLOYEES,
    Scope::SMP_CONTRIBUTIONS,
], ['environment' => 'sandbox']);
```

* Tokens are requested from `POST https://id.{env}.autoenrolment.co.uk/oauth/token` with the
  `Token-Type: jwt` header and are cached in memory.
* Keystone JWTs expire after **10 minutes** (whatever `expires_in` says). The provider reads the JWT
  `exp` claim and fetches a new token 30 seconds before expiry.
* If the API still answers `401`, the client fetches a fresh token and retries the request once.
* Scopes must be **requested in code and enabled for your app by Smart** (email
  api@smartpension.co.uk with your app name, the email used to create it and the scopes). A `read:`
  scope also allows writes. `include[]` relations need the scope of the included resource too
  (e.g. `include[]=expression_of_wish` needs `read:expression_of_wishes`).

Available client-credentials scopes (`SMART\Scope\Scope::SMP_*` constants, e.g. `read:fund_splits` → `Scope::SMP_FUND_SPLITS`):

```
read:companies  read:customers  read:employees  read:expression_of_wishes  read:funds  read:fund_splits
read:ssif_import_results  read:ssif_imports  read:bank_account_details  read:bank_details
read:benefit_groups  read:companies_automations  read:contributions  read:default_investment_instruments
read:economic_zones  read:employee_configurations  read:employee_plan_participations
read:employments_plan_statuses  read:envelopes  read:glidepaths  read:glidepath_steps  read:groups
read:know_your_customer_data  read:marketing_preferences  read:payroll_configurations  read:portfolios
read:postponements  read:provider_scheme_migrations  read:salaries  read:scheme_details
read:company_tax_reliefs  read:schemes  read:shortcuts  read:target_date_fund_groups  read:valuations
read:adviser_companies
```

Need the raw token (e.g. to share it with another process)?

```php
use SMART\Api\Auth\ClientCredentialsTokenProvider;

$provider = ClientCredentialsTokenProvider::forEnvironment('sandbox', $clientId, $clientSecret, 'read:companies');
$token    = $provider->getToken();
$expires  = $provider->getExpiresAt();      // unix timestamp
$smart    = new SmartClient($provider);
```

### 4.2 Authorization code (acting for a user)

Used when an adviser (`user` scope), employer admin (`customer` scope) or member (`employee` scope)
signs in through Smart and grants your app access. The endpoint pages list which of these scopes
each endpoint accepts.

```php
// 1. redirect to Smart
$provider = new \SMART\Oauth2\Provider($clientId, $clientSecret, 'https://your-app.example/smart/callback');
$provider->redirectToAuthorizationURL([\SMART\Scope\Scope::SMP_CUSTOMER]);

// 2. callback.php — exchange the code and store the token in the session
$accessToken = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
\SMART\Oauth2\AccessToken::set($accessToken);

// 3. anywhere afterwards
$smart = SmartClient::fromSession();
$me = $smart->customers()->getCustomerSession()->getArray();
```

`AccessToken` stores the token through `Illuminate\Support\Facades\Session`, so this works out of
the box in Laravel. Outside Laravel, keep the token yourself and pass it in:
`new SmartClient($accessToken)`.

Switch identities without rebuilding the client:

```php
$asOtherCustomer = $smart->withToken($otherAccessToken);
```

### 4.3 Public endpoints

Endpoints with no security requirement in the spec (e.g. `countries()->listCountries()`,
`schemes()->listAvailableSchemes()`, the account-claiming flow) send the token only if one is
available, so `new SmartClient()` with no token works for them. Calling a protected endpoint without a
token throws `SMART\Api\Exceptions\MissingTokenException` before any HTTP request is made.

## 5. Calling endpoints

Resources are grouped like the "APIs" section of the official documentation and are reached through
accessor methods on the client (`$smart->employees()`, `$smart->contributions()`, `$smart->groups()`,
…). Every method:

* takes the **path IDs first, in URL order** (`string|int`; slugs work wherever the API accepts them,
  e.g. `getCompany('acme-ltd')`),
* then `array $body` for `POST`/`PUT`/`PATCH`,
* then `array $query` and `array $headers`,
* returns `SMART\Response\Response`.

Naming convention:

| HTTP | Path shape | Method prefix | Example |
| --- | --- | --- | --- |
| `GET` | collection | `list…` | `employees()->listEmployees($companyId)` |
| `GET` | single item / singleton | `get…` | `employees()->getEmployee($companyId, $id)` |
| `POST` | collection | `create…` | `groups()->createGroup($companyId, $body)` |
| `PATCH` | item | `update…` (partial update) | `employees()->updateEmployee($companyId, $id, $body)` |
| `PUT` | item | `replace…` (full update) | `employees()->replaceEmployee($companyId, $id, $body)` |
| `DELETE` | item | `delete…` | `contributions()->deleteContribution($companyId, $employeeId, $id)` |
| `POST`/`PATCH` | action (`/submit`, `/cancel`, …) | the action verb | `ssifImports()->submitSsifImport($companyId, $id)` |

Look up the exact name in [endpoints.md](endpoints.md) (search for the URL you see in the official docs)
or let your IDE autocomplete — every method carries a docblock with the HTTP route, required scopes,
query parameters and body fields.

Anything not covered (a brand-new endpoint, say) can be called directly:

```php
$response = $smart->request('GET', '/companies/1234/some_new_endpoint', [
    'query'   => ['limit' => 10],
    'json'    => null,          // or an array body
    'headers' => [],
]);
```

## 6. Query parameters

Pass query parameters as a PHP array; nested arrays are converted to the Rails bracket notation the API
expects (`include[]=owner`, `filter[state]=paid`, `filter[name][]=A`). Booleans become `true`/`false`
and `DateTimeInterface` values become `Y-m-d` (or ISO 8601 with time when the time is not midnight).

| Parameter | Example | Notes |
| --- | --- | --- |
| `limit` | `['limit' => 100]` | Page size. Default 50, maximum 100 |
| `offset` | `['offset' => 100]` | Items to skip |
| `sort` | `['sort' => 'surname']` | Field to sort by (default: creation time) |
| `direction` | `['direction' => 'DESC']` | `ASC` or `DESC` |
| `include` | `['include' => ['owner', 'groups']]` | Embed related resources in one call |
| `filter` | `['filter' => ['state' => 'paid']]` | Filter by field value(s); arrays allowed |
| `operator` | `['operator' => 'or']` | How multiple filters combine, where supported |
| `fields` | `['fields' => ['id', 'forename']]` | Return only these fields |

The parameters each endpoint supports (including its filter keys) are listed on its resource page.

### Paginating

List responses look like:

```json
{ "limit": 50, "offset": 0, "total": 152, "links": [{"rel": "next", "href": "..."}], "data": [ ... ] }
```

`paginate()` walks every page lazily:

```php
foreach ($smart->paginate(
    fn (array $query) => $smart->employees()->listEmployees($companyId, $query),
    ['filter' => ['opt_state' => 'opted_in'], 'sort' => 'surname']
) as $employee) {
    echo $employee['forename'], ' ', $employee['surname'], PHP_EOL;
}
```

`paginate()` stops when a page is shorter than the page size or `offset` reaches `total`. A few list
endpoints return a bare JSON array instead of the envelope; for those only the first page is read.

## 7. Request bodies and file uploads

JSON bodies are plain arrays. Field lists, enums and an example body for every endpoint are on the
resource pages, e.g. [Employees](resources/Employees.md#createemployee) and
[Contributions](resources/Contributions.md#createcontribution).

```php
$employee = $smart->employees()->createEmployee($companyId, [
    'title'                     => 'Ms',
    'forename'                  => 'Jane',
    'surname'                   => 'Doe',
    'date_of_birth'             => '1990-04-12',
    'gender'                    => 'Female',
    'national_insurance_number' => 'QQ123456C',
    'email'                     => 'jane@example.com',
    'external_id'               => 'PAYROLL-0042',
    'starts_on'                 => '2026-01-01',
])->getArray();

$smart->employees()->updateEmployee($companyId, $employee['id'], ['email' => 'jane.doe@example.com']);
```

Dates should be sent as `YYYY-MM-DD`; values the API cannot parse as ISO 8601 are treated as `null`.
Money amounts are sent as numbers (floats keep their decimals, e.g. `1200.0`).

Endpoints that take `multipart/form-data` (company branding, benefit evidence uploads, PAPDIS/CSV
imports, SSIF imports) take the same `$body` array; pass files as stream resources or as Guzzle part
arrays when you need a file name or content type:

```php
$smart->imports()->createImport($companyId, [
    'file' => [
        'contents' => fopen('/path/to/papdis.csv', 'r'),
        'filename' => 'papdis.csv',
        'headers'  => ['Content-Type' => 'text/csv'],
    ],
]);

$smart->companyBranding()->createCompanyBranding($companyId, [
    'logo'                      => fopen('/path/to/logo.png', 'r'),
    'company_branding_stock_id' => 1,
    'description'               => 'Our brand',
]);
```

Nested arrays become bracketed field names (`['import' => ['file' => …]]` → `import[file]`).

## 8. Responses

Every method returns `SMART\Response\Response`:

| Method | Returns |
| --- | --- |
| `getArray()` | Body decoded to an associative array (`null` for empty bodies such as `204`) |
| `getJson()` | Body decoded to `stdClass` |
| `getBody()` | Raw PSR-7 stream (CSV/PDF downloads such as `getCompanyFeeInvoice()`) |
| `getStatusCode()` | HTTP status |
| `isSuccessful()` | `true` for any 2xx |
| `isSuccess()` | `true` only for 200 (kept for backward compatibility) |
| `getHeaderLine($name)` | A response header |
| `getGuzzleResponse()` | The underlying PSR-7 response |

Creates usually return `201` with the new object; updates and deletes usually return `204` with no
body.

## 9. Errors

With `throw_on_error` on (the default) any non-2xx response throws an exception that extends
`SMART\Api\Exceptions\ApiException`:

| Status | Exception | Typical cause |
| --- | --- | --- |
| 401 | `UnauthorizedException` | Missing/expired token, or the token lacks the endpoint's scope |
| 404 | `NotFoundException` | Unknown ID or not visible to this token |
| 422 | `ValidationException` | Invalid body; see `getErrors()` |
| 429 | `RateLimitException` | Rate limit hit; see `getRetryAfter()` |
| other (403, 405, 409, 5xx…) | `ApiException` | Business rule (403), state does not allow the change (405), duplicate/locked (409), server error (500) |

Keystone returns errors in several shapes; `getErrors()` always normalises them to
`[['code' => …, 'title' => …, 'detail' => …, 'source' => …], …]`.

```php
use SMART\Api\Exceptions\ValidationException;
use SMART\Api\Exceptions\RateLimitException;
use SMART\Api\Exceptions\ApiException;

try {
    $smart->contributions()->createContribution($companyId, $employeeId, $body);
} catch (ValidationException $e) {
    foreach ($e->getErrors() as $error) {
        printf("%s: %s %s\n", $error['code'], $error['title'], $error['detail']);
    }
} catch (RateLimitException $e) {
    sleep($e->getRetryAfter() ?? 60);
} catch (ApiException $e) {
    error_log($e->getMessage());          // includes method, URL, status and error titles
    $raw = $e->getBody();                 // decoded body, null for HTML 500 pages
}
```

Network failures (DNS, timeouts) surface as Guzzle's `GuzzleHttp\Exception\GuzzleException`.

Prefer to inspect responses yourself? Pass `'throw_on_error' => false` and check
`$response->isSuccessful()`.

## 10. Rate limits

The default limit is **500 requests per minute**. Stricter limits:

| Endpoint | Limit | PHP |
| --- | --- | --- |
| `GET /companies/:company_id/employees` | 80 / 60 s | `employees()->listEmployees()` |
| `POST /companies/:company_id/employees` | 80 / 60 s | `employees()->createEmployee()` |
| `POST /companies/:company_id/employees/:employee_id/contributions` | 250 / 60 s | `contributions()->createContribution()` |

For bulk payroll submissions prefer [batch requests](#12-batch-requests) or a PAPDIS/CSV
`imports()->createImport()` upload, and catch `RateLimitException`. You can also add Guzzle retry
middleware through the `http_client` option.

## 11. API versions

The current version is **12**. Without a version the API answers with the latest one. Pin a version to
protect your integration from future breaking changes (the client sends
`Accept: application/vnd.autoenrolment.v{N}+json`):

```php
$smart = new SmartClient($token, ['version' => 12]);
$v10   = $smart->withVersion(10);   // a copy for endpoints you still consume in v10 format
```

Requests sent with an older version are accepted and converted by Keystone, and responses keep the old
structure. See the official [Versions](https://developers.autoenrolment.co.uk/smart/42d8d78e1463a-versions)
page for field changes between v6 and v12.

## 12. Batch requests

Up to 50 operations, against any endpoint, in one HTTP request:

```php
$results = $smart->batch()->batch([
    'operations' => [
        ['http_method' => 'GET', 'path' => 'companies/19'],
        ['http_method' => 'GET', 'path' => 'companies/19/employees?limit=10'],
    ],
])->getArray();
```

## 13. Common integration flows

**Payroll software — full integration**

1. (optional) create the employer: `companies()->createCompany()`; otherwise store the existing company ID.
2. Before each pay run, refresh members: `employees()->listEmployees($companyId, ['filter' => …])`
   to pick up opt-outs and contribution percentage changes (`employees()->getOptState()`).
3. Create new starters: `employees()->createEmployee()` (use `external_id` for your payroll ID).
4. Submit contributions per employee: `contributions()->createContribution()`, or the whole run as one
   PAPDIS/CSV file with `imports()->createImport()` and read `imports()->listImportResults()` for errors.
5. Track payment: `contributions()->listCompanyContributions($companyId, ['filter' => ['state' => 'paid']])`,
   `payments()->listPayments()`, `contributionSummaries()->getCompanySummary()`.

**HR / benefits apps** — `employees()->listEmployees()`, `employeeConfigurations()->updateEmployeeConfigurations()`
to change contribution rates.

**Banking apps** — member authorization-code flow, then `valuations()->listMyValuations()` or
`valuations()->listValuations($companyId, $employeeId)`.

**Advisers** — `advisers()->…`, `adviserCompanies()->createAdviserCompany()`, `users()->…` (adviser
staff), `exports()->createExport($adviserId, …)`.

States worth knowing:

* **Contribution** `state`: `ignition` (editable until the 6th of the month; only
  `gross_qualifying_earnings` and `voluntary_amount` can change) → `payment_pending` → `paid`, or
  `charged_back` when refunded. `period_type`: `Weekly`, `Fortnightly`, `FourWeekly`, `Monthly`,
  `Quarterly`, `BiAnnually`, `Annually`.
* **Employee** `opt_state`: `ignition`, `opted_in`, `opted_out`, `rejoined`, `ceased_membership`.
* **Bills / billing details**: `ignition` until processed by GoCardless, then the GoCardless status.

Details: [Flows](https://developers.autoenrolment.co.uk/smart/ecabd5980520c-flows).

## 14. Resource reference

All 133 resources with their accessor and endpoint count are listed in [endpoints.md](endpoints.md#resources).
The most used ones:

| Area | Resources |
| --- | --- |
| Employer | [Companies](resources/Companies.md), [Customers](resources/Customers.md), [CustomerRoles](resources/CustomerRoles.md), [Groups](resources/Groups.md), [PayrollConfigurations](resources/PayrollConfigurations.md), [CompanySchemeDetails](resources/CompanySchemeDetails.md), [BankAccountDetails](resources/BankAccountDetails.md), [Mandates](resources/Mandates.md), [Envelopes](resources/Envelopes.md) |
| Members | [Employees](resources/Employees.md), [EmployeeConfigurations](resources/EmployeeConfigurations.md), [Postponements](resources/Postponements.md), [Enrolments](resources/Enrolments.md), [Assessments](resources/Assessments.md), [OptInRequests](resources/OptInRequests.md), [ExpressionOfWish](resources/ExpressionOfWish.md), [Letters](resources/Letters.md) |
| Money in | [Contributions](resources/Contributions.md), [ContributionSummaries](resources/ContributionSummaries.md), [Payments](resources/Payments.md), [PaymentApprovals](resources/PaymentApprovals.md), [Imports](resources/Imports.md), [SSIFImports](resources/SSIFImports.md), [Batch](resources/Batch.md) |
| Investments | [Valuations](resources/Valuations.md), [Funds](resources/Funds.md), [FundSplits](resources/FundSplits.md), [Portfolios](resources/Portfolios.md), [Subaccounts](resources/Subaccounts.md), [Switches](resources/Switches.md), [Transactions](resources/Transactions.md) |
| Retirement | [Benefits](resources/Benefits.md), [BenefitForms](resources/BenefitForms.md), [BenefitCategories](resources/BenefitCategories.md), [PensionForecast](resources/PensionForecast.md), [TransferIns](resources/TransferIns.md) |
| Advisers | [Advisers](resources/Advisers.md), [AdviserCompanies](resources/AdviserCompanies.md), [AdviserInvitations](resources/AdviserInvitations.md), [Users](resources/Users.md), [Exports](resources/Exports.md) |
| Identity & onboarding | [Tokens](resources/Tokens.md), [Identity](resources/Identity.md), [AccountClaiming](resources/AccountClaiming.md), [Onboarding](resources/Onboarding.md), [Accounts](resources/Accounts.md), [Profile](resources/Profile.md) |
| Reference data | [Countries](resources/Countries.md), [Currencies](resources/Currencies.md), [Constants](resources/Constants.md), [Schemes](resources/Schemes.md), [CompanyLookups](resources/CompanyLookups.md), [StagingDates](resources/StagingDates.md) |

## 15. Testing your integration

Inject a Guzzle client with a `MockHandler` to unit-test code that uses the library without network
access:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

$mock  = new MockHandler([new Response(200, [], json_encode(['id' => 1, 'name' => 'Acme']))]);
$smart = new SmartClient('fake-token', ['http_client' => new Client(['handler' => HandlerStack::create($mock)])]);

$smart->companies()->getCompany(1)->getArray();   // ['id' => 1, 'name' => 'Acme']
```

For end-to-end tests use the **sandbox** environment: its data persists and payments/e-signing are
simulated.

## 16. Regenerating from the OpenAPI spec

The resource classes in `src/SMART/Api/Resources` and the Markdown in `docs/smart/resources` and
`docs/smart/endpoints.md` are generated from the official OpenAPI document. When Smart publishes new
endpoints:

```bash
php bin/generate-smart-api.php                 # downloads the latest spec from Stoplight
php bin/generate-smart-api.php path/to/spec.json
```

Do not edit generated files by hand. To rename a method or regroup endpoints, edit `NAME_OVERRIDES` or
`TAG_ALIASES` at the top of the generator and run it again; it stops with an error if two endpoints
would get the same method name. Review the diff, because renamed methods are breaking changes for
users of the library.

## 17. Legacy request classes

The original per-endpoint classes (`SMART\Company\CompanyList`, `SMART\Employee\Crud\Create`,
`SMART\Contributions\Crud\Create`, …) still work unchanged and read their token from
`SMART\Oauth2\AccessToken` / `SMART\ServerToken\ServerToken`. New code should use `SmartClient`, which
covers every endpoint and adds error handling, pagination and client-credentials support.
