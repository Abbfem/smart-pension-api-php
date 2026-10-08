---
name: regenerate-smart-api
description: Refresh, rename or regroup the generated Smart Pension endpoint classes and docs in this library from the Keystone OpenAPI spec (bin/generate-smart-api.php). Use when Smart publishes API changes, a generated method name needs changing, or generated docs look wrong.
---

# Regenerate the Smart Pension API layer

Generated (never edit by hand): `src/SMART/Api/Resources/*.php` except `AbstractResource.php`,
`docs/smart/endpoints.md`, `docs/smart/resources/*.md`.

## Steps

1. Download the spec into the scratchpad (keeps the repo clean and lets you diff it):
   ```bash
   curl -sL "<SPEC_URL from bin/generate-smart-api.php>" -o <scratchpad>/swagger.json
   ```
   The docs site (developers.autoenrolment.co.uk) is a Stoplight SPA: fetching its HTML gives nothing
   useful. The table of contents is at
   `https://stoplight.io/api/v1/projects/cHJqOjEyNDU4NA/table-of-contents` and guide articles at
   `.../nodes/<id>` (field `data` holds the Markdown).
2. Run the generator (PowerShell, Herd PHP):
   ```powershell
   php bin/generate-smart-api.php <scratchpad>\swagger.json
   ```
   It prints `Generated N resources with M endpoints (API vX)`. A `Duplicate method` error means two
   endpoints map to one name: add a `NAME_OVERRIDES` entry.
3. Lint: `Get-ChildItem src\SMART\Api -Recurse -Filter *.php | % { php -l $_.FullName }`.
4. Review names: `grep -oE "public function [a-zA-Z]+\(" src/SMART/Api/Resources/*.php`. Fix awkward
   ones (repeated nouns, `create…` for actions) with `NAME_OVERRIDES`; move endpoints between classes
   with `TAG_ALIASES`; teach new action verbs via `ACTION_SEGMENTS`. Re-run step 2.
5. Check the diff for **removed or renamed public methods**: these break consumers (e.g. ABBPay).
   Prefer adding an override that keeps the old name; otherwise list the change in the commit message.
6. Run `php vendor/bin/phpunit tests/Api` in the background and update `docs/smart/README.md` if
   endpoint counts, flows or examples changed.

## Naming rules the generator applies

- Leading `companies/{id}`, `employees/{id}`, `advisers/{id}` pairs and `payments_service`,
  `identity`, `api` prefixes are dropped (the class gives context).
- Verb: GET collection → `list` (plural) or `get`; GET/PUT/PATCH/DELETE item → `get`/`replace`/
  `update`/`delete`; POST → `create`; trailing action segment (`submit`, `cancel`, ...) → that verb.
- Nouns before a `{param}` and POST targets are singularised.
- Name clashes inside a class get the stripped context prefixed (`listCompanyTasks` / `listEmployeeTasks`).
- Endpoints without `security` use `AUTH_OPTIONAL`; per-operation `servers` set the `service` host.
