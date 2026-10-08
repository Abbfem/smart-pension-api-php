# AGENTS.md — guide for AI agents working on this library

`shynne109/smart-pension-api-php` is a PHP 8.2 Composer library that talks to three UK workplace
pension providers. This file is for agents **changing the library**. If you are **using** the
library in an application, read the skill at
[resources/boost/skills/smart-pension-api/SKILL.md](resources/boost/skills/smart-pension-api/SKILL.md)
and the guide at [docs/smart/README.md](docs/smart/README.md).

## Map of the repository

| Path | What it is | Edit by hand? |
| --- | --- | --- |
| `src/SMART/Api/SmartClient.php` | Smart Pension (Keystone API v12) client: HTTP, auth, errors, pagination | yes |
| `src/SMART/Api/Auth/` | `TokenProvider` + static, session and client-credentials providers | yes |
| `src/SMART/Api/Exceptions/` | `ApiException` and its 401/404/422/429 subclasses, `MissingTokenException` | yes |
| `src/SMART/Api/Query.php`, `Hosts.php` | Rails-style query builder; environment → host URLs | yes |
| `src/SMART/Api/Resources/AbstractResource.php` | Base class of the generated resources | yes |
| `src/SMART/Api/Resources/*.php` (all others) | **Generated**: 133 classes, one method per endpoint (401) | **no** |
| `docs/smart/endpoints.md`, `docs/smart/resources/*.md` | **Generated** endpoint reference | **no** |
| `docs/smart/README.md` | Hand-written usage guide for the Smart client | yes |
| `bin/generate-smart-api.php` | Generator: OpenAPI spec → resources + docs | yes |
| `src/SMART/{Company,Employee,Contributions,Group,...}` | Legacy Smart request classes (one class per call). Kept for backward compatibility; do not add new ones | only to fix bugs |
| `src/SMART/Oauth2/`, `src/SMART/Scope/`, `src/SMART/Environment/` | OAuth provider, session token store, scope constants, global sandbox/live switch (shared by legacy and new code) | yes |
| `src/SMART/Response/Response.php` | Response wrapper used by both legacy and new code | yes, keep it backward compatible |
| `src/PeoplesPension/` | The People's Pension REST API (own README) | yes |
| `src/NestPension/` | NEST web services (XML) API (own README) | yes |
| `examples/` | Runnable example scripts per provider | yes |
| `resources/boost/` | Guideline and skill shipped to Laravel Boost users of the package | yes |
| `tests/` | PHPUnit 11 tests (`tests/Api` covers the Smart client) | yes |

## Smart Pension API facts you need

* Official docs: <https://developers.autoenrolment.co.uk/smart/> (Stoplight project `cHJqOjEyNDU4NA`).
  The raw OpenAPI 3.0.3 spec: see `SPEC_URL` in `bin/generate-smart-api.php`.
* Hosts: `api.` / `id.` / `account-claiming.` + `dev.`/`sandbox.`/(none for live) + `autoenrolment.co.uk`.
* OAuth: authorization code (scopes `user` adviser, `customer` employer admin, `employee` member) or
  client credentials (`read:*` scopes, `Token-Type: jwt` header, JWT lives 10 minutes).
* Lists: `{limit, offset, total, links, data}`, `limit` max 100. Queries use Rails brackets
  (`include[]=`, `filter[x]=`).
* Errors come in several JSON shapes; `ApiException::normaliseErrors()` handles them.
* Rate limits: 500/min default; 80/min for list/create employees; 250/min for create contribution.

## Common tasks

### Smart published new or changed endpoints

```powershell
php bin/generate-smart-api.php                       # downloads the spec
php bin/generate-smart-api.php path\to\swagger.json  # or from a file
```

Then review `git diff --stat src/SMART/Api/Resources docs/smart`. Never hand-edit generated files.

* Ugly or clashing method name → add `"METHOD /path" => 'name'` to `NAME_OVERRIDES`.
* Endpoint landed in the wrong class → map its spec tag in `TAG_ALIASES`.
* The generator throws on duplicate method names inside a class; fix with an override.
* A renamed or removed method is a **breaking change** for consumers: call it out in the commit and
  changelog.

### Change client behaviour (auth, errors, query encoding, uploads)

Edit the hand-written files listed above and add or adjust tests in `tests/Api/`. Tests use
Guzzle `MockHandler` + `Middleware::history`; base class `tests/Api/ApiTestCase.php`.

### Add documentation

Usage guidance goes in `docs/smart/README.md`. Generated per-endpoint docs come from the spec, so fix
wording there by improving the generator's renderers rather than the Markdown output.

## Commands

PHP and Composer come from Laravel Herd; on Windows run them through PowerShell.

```powershell
composer install
php vendor/bin/phpunit tests/Api            # Smart client tests (run the narrowest folder/filter you need)
php -l src/SMART/Api/SmartClient.php        # quick syntax check
```

## Rules

* Never commit credentials, tokens or real member data (NI numbers, names, salaries) in code, tests,
  examples or docs. Use obviously fake values (`QQ123456C`, `Jane Doe`).
* Keep PHP 8.2 compatibility; no new runtime dependencies without asking.
* Do not change public signatures of legacy classes; they are used by existing applications
  (for example ABBPay payroll).
* Pension contribution maths and compliance logic belongs in the consuming application, not here.
  This library only transports data faithfully (money as numbers, dates as `Y-m-d`).
* Sandbox is the default environment. Never point tests or examples at `live`.
