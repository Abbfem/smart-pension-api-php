---
name: smart-pension-api
description: Integrate with the Smart Pension (Keystone API v12, autoenrolment.co.uk) workplace pension API using shynne109/smart-pension-api-php - OAuth/client-credentials auth, employers (companies), members (employees), contributions, opt-outs, postponements, payments, imports, valuations and every other documented endpoint via SMART\Api\SmartClient.
---

# Smart Pension API

## When to use this skill

Use it whenever code talks to Smart Pension: connecting an employer via OAuth, creating or updating a
company, pension groups or members, submitting payroll contributions, reading opt-outs/opt states,
postponements, enrolments, payments and direct debits, uploading PAPDIS/CSV files, reading member
valuations or anything on `*.autoenrolment.co.uk`. Use the new client for new code. Only move an
existing flow off the legacy classes (`SMART\Company\CreateCompany`, `SMART\Employee\Crud\Create`,
`SMART\Contributions\Crud\Create`, ...) when that migration is the task you were given, one flow at a
time with its tests; do not refactor working legacy calls as a side effect of other work.

Documentation shipped with the package (paths relative to `vendor/shynne109/smart-pension-api-php/`):

| File | Use it for |
| --- | --- |
| `docs/smart/README.md` | Full usage guide: auth, query params, uploads, errors, versions, flows |
| `docs/smart/endpoints.md` | Every endpoint: HTTP route → PHP method → OAuth scopes |
| `docs/smart/resources/<Resource>.md` | Parameters, body fields with enums, example request/response per method |
| `src/SMART/Api/Resources/<Resource>.php` | Method signatures and docblocks |

## Workflow

1. **Find the method.** Search `docs/smart/endpoints.md` for the URL from the official docs
   (e.g. `/companies/{company_id}/employees/{employee_id}/contributions`). Never invent a method name.
2. **Read its section** in `docs/smart/resources/<Resource>.md`: required scopes, query parameters,
   body fields (with enums) and an example body.
3. **Call it** through a client (below), passing path IDs first, then `$body` (POST/PUT/PATCH),
   then `$query`, then `$headers`.
4. **Handle errors** with the exception classes. The client never retries: retrying is the caller's
   job. Only idempotent reads (GET) are safe to retry blindly after a 429, 5xx or timeout. A POST such
   as `createContribution` may already have been applied when it timed out or returned 5xx, so check
   (for example `listEmployeeContributions`) before sending it again. A 422 is a data problem to show
   the user and must not be retried unchanged.
5. **Test** with a Guzzle `MockHandler` injected through the `http_client` option.

## Building a client

```php
use SMART\Api\SmartClient;
use SMART\Scope\Scope;

// Machine to machine (partner app). Tokens are fetched, cached and refreshed automatically.
$smart = SmartClient::withClientCredentials($clientId, $clientSecret,
    [Scope::SMP_COMPANIES, Scope::SMP_EMPLOYEES, Scope::SMP_CONTRIBUTIONS],
    ['environment' => 'sandbox']);

// Acting for a connected employer/adviser/member: pass the stored OAuth access token.
$smart = new SmartClient($accessToken, ['environment' => config('smartpension.client_environment', 'sandbox')]);

// Same client, another tenant's token (no global state involved):
$tenantClient = $smart->withToken($otherTenantToken);
```

Options: `environment` (`dev`|`sandbox`|`live`), `base_url`, `version` (pin e.g. `12`), `headers`,
`http_client`, `timeout`, `throw_on_error`.

**Multi-tenant apps and queue workers:** the legacy classes read the token from the session and the
environment from a process-wide singleton. `SmartClient` keeps both on the instance, so build one
client per tenant/job and never rely on `SMART\Oauth2\AccessToken` in workers.

## Method naming

`list…` (GET collection), `get…` (GET item/singleton), `create…` (POST), `update…` (PATCH, partial),
`replace…` (PUT, full), `delete…` (DELETE), and the verb itself for actions (`submitSsifImport`,
`cancelBenefitForm`, `completeMandate`). When the same resource exists at company and employee level
the name says which: `listCompanyContributions` vs `listEmployeeContributions`,
`listCompanyPostponements` vs `listEmployeePostponements`.

Frequently used:

| Task | Call |
| --- | --- |
| Employer details (+ owner) | `companies()->getCompany($companyId, ['include' => ['owner']])` |
| Create / update employer | `companies()->createCompany($body)`, `companies()->updateCompany($companyId, $body)` |
| Pension groups | `groups()->listGroups($companyId)`, `createGroup`, `updateGroup` |
| Members (paginated, filterable) | `employees()->listEmployees($companyId, ['filter' => ['opt_state' => 'opted_out'], 'limit' => 100])` |
| One member / its opt state | `employees()->getEmployee($companyId, $id)`, `employees()->getOptState($companyId, $id)` |
| Create / update member | `employees()->createEmployee($companyId, $body)`, `employees()->updateEmployee($companyId, $id, $body)` |
| Contribution rates | `employeeConfigurations()->updateEmployeeConfigurations($companyId, $employeeId, $body)` |
| Submit a contribution | `contributions()->createContribution($companyId, $employeeId, $body)` |
| Company contributions by state | `contributions()->listCompanyContributions($companyId, ['filter' => ['state' => 'paid']])` |
| Postponements | `postponements()->createEmployeePostponement($companyId, $employeeId, $body)`, `createCompanyPostponement` |
| Upload PAPDIS/CSV and read errors | `imports()->createImport($companyId, ['file' => fopen($path, 'r')])`, `imports()->listImportResults($companyId, $importId)` |
| Amount due / payments | `contributionSummaries()->getCompanyPayableSummary($companyId)`, `payments()->listPayments($companyId)` |
| Up to 50 calls in one request | `batch()->batch(['operations' => [['http_method' => 'GET', 'path' => 'companies/1']]])` |
| Member valuation (member token) | `valuations()->listMyValuations()` |

## Query parameters

Arrays are encoded Rails-style: `['include' => ['owner']]` → `include[]=owner`,
`['filter' => ['state' => 'paid']]` → `filter[state]=paid`. Lists return
`{limit, offset, total, links, data}`; `limit` max 100. Walk every page with:

```php
foreach ($smart->paginate(fn ($q) => $smart->employees()->listEmployees($companyId, $q), ['sort' => 'surname']) as $member) { /* ... */ }
```

## Errors

```php
use SMART\Api\Exceptions\{ApiException, ValidationException, RateLimitException, UnauthorizedException};

try {
    $smart->contributions()->createContribution($companyId, $memberId, $body);
} catch (ValidationException $e) {        // 422: show $e->getErrors() (code/title/detail/source)
} catch (RateLimitException $e) {         // 429: not applied; safe to resend after $e->getRetryAfter() ?? 60 seconds
} catch (UnauthorizedException $e) {      // 401: token expired/revoked or missing scope: refresh or reconnect
} catch (ApiException $e) {               // 403 business rule, 405 state forbids change, 409 conflict, 5xx
                                          // (5xx: the write may have been applied; check before resending)
}
```

`MissingTokenException` is thrown before sending when a protected endpoint has no token.

## Domain rules that bite

- **Rate limits:** 500 req/min overall; `listEmployees`/`createEmployee` 80/min; `createContribution`
  250/min per company. For a large pay run use `batch()` (50 operations per call) or a PAPDIS/CSV
  `imports()->createImport()` instead of one call per member, and avoid a `getEmployee` per member
  when one paginated `listEmployees` call can return everyone's opt state.
- **Contribution states:** `ignition` (editable until the 6th of the month; only
  `gross_qualifying_earnings` and `voluntary_amount` can change) → `payment_pending` → `paid`;
  `charged_back` if refunded. Updates outside `ignition` fail with 405.
- **Opt states:** `ignition`, `opted_in`, `opted_out` (within the 30-day window), `rejoined`,
  `ceased_membership`. Always read the remote state before sending contributions.
- **Dates** must be `YYYY-MM-DD`.
- **Money** is sent as numbers and the client does **not** round (the legacy `PostRequest` classes
  did). Round every amount to 2 decimal places in the app before sending, e.g. `round($amount, 2)`,
  or floating-point noise such as `0.30000000000000004` reaches Smart.
- **IDs vs slugs:** companies accept their slug wherever `{company_id}` appears.
- **Scopes:** endpoint pages list `user` (adviser), `customer` (employer admin), `employee` (member)
  for OAuth tokens. Client-credentials apps need `read:*` scopes enabled by Smart
  (api@smartpension.co.uk); `include[]` relations need their own scope.
- **Environments:** sandbox data persists and payments are simulated; never test against `live`.

## Testing

```php
$mock = new \GuzzleHttp\Handler\MockHandler([new \GuzzleHttp\Psr7\Response(201, [], json_encode(['id' => 9]))]);
$smart = new SmartClient('test-token', ['http_client' => new \GuzzleHttp\Client(['handler' => \GuzzleHttp\HandlerStack::create($mock)])]);
```

Add `GuzzleHttp\Middleware::history($container)` to the handler stack to assert the URL, query string,
headers and JSON body that were sent.
