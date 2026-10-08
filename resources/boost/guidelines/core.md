## Smart Pension API (shynne109/smart-pension-api-php)

- Call the Smart Pension (Keystone v12) API through `SMART\Api\SmartClient`; every one of its 401 endpoints is a method: `$smart->{resource}()->{method}(...pathIds, $body, $query, $headers)`, returning `SMART\Response\Response` (`getArray()`).
- Build clients with `SmartClient::withClientCredentials($id, $secret, $scopes, ['environment' => 'sandbox'])`, `new SmartClient($accessToken, $options)` or `SmartClient::fromSession()`. Prefer one client per tenant/token over the global `SMART\Environment\Environment` + session token used by the legacy `SMART\Company\...`, `SMART\Employee\...` classes.
- Never guess a method name: look it up in `vendor/shynne109/smart-pension-api-php/docs/smart/endpoints.md` (search by URL) and read the resource page in `docs/smart/resources/` for parameters and body fields.
- Catch `SMART\Api\Exceptions\ApiException` (subclasses: `ValidationException` 422, `UnauthorizedException` 401, `NotFoundException` 404, `RateLimitException` 429). Use `getErrors()` for the normalised error list.
- Activate the `smart-pension-api` skill for any Smart Pension / autoenrolment.co.uk integration work.
