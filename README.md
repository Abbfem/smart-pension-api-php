# SMART API PHP
[![Build Status](https://travis-ci.org/s-patompong/smart-api-php.svg?branch=master)](https://travis-ci.org/s-patompong/smart-api-php)
[![StyleCI](https://github.styleci.io/repos/167135773/shield?branch=master)](https://github.styleci.io/repos/167135773)

This library can be used to connect and does operations on SMART API https://developers.autoenrolment.co.uk/smart/451fbda1e0bde-introduction.

## How to use

For user-restricted API call, please see the next section.

## User-Restricted API call
The easiest way to learn about this is by running the local server using `php -S localhost:8080` command at the root of this library. And then navigate to http://localhost:8080/examples/index.php on your browser. Don't forget to setup the credentials inside examples/config.php file.
```php
<?php

$clientId = 'clientid';
$clientSecret = 'clientsecret';

```
You can gain the access token by create SMART Oauth2 Provider and redirect to authorize URL (see example/oauth2/create-access-token.php for example).

```php
<?php

$callbackUri = "http://localhost:8080/examples/oauth2/callback.php" ;

$_SESSION[ 'client_id' ] = $_GET[ 'client_id' ];
$_SESSION[ 'client_secret' ] = $_GET[ 'client_secret' ];
$_SESSION[ 'callback_uri' ] = $callbackUri;
$_SESSION[ 'caller' ] = "/examples/index.php";

$provider = new \SMART\Oauth2\Provider(
    $_GET[ 'client_id' ],
    $_GET[ 'client_secret' ],
    $callbackUri
);
$scope = [ \SMART\Scope\Scope::VAT_READ, \SMART\Scope\Scope::HELLO, \SMART\Scope\Scope::VAT_WRITE ];
$provider->redirectToAuthorizationURL($scope);
```
After user grant authorize on SMART authorization page, it will redirect back to `$callbackUri`, which in the example above, the callback.php file.

Content of callback.php
```php
<?php

$provider = new \SMART\Oauth2\Provider(
    $_SESSION[ 'client_id' ],
    $_SESSION[ 'client_secret' ],
    $_SESSION[ 'callback_uri' ]
);

// Try to get an access token using the authorization code grant.
$accessToken = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code']
]);

\SMART\Oauth2\AccessToken::set($accessToken);

header("Location: /examples/index.php");
exit;
```
You need to use `\SMART\Oauth2\AccessToken` class to get and set access token. The class that do the request will get Access Token from this class.

After get the access token and save it inside `\SMART\Oauth2\AccessToken`, we can start calling user-restricted API. For example, here is the request to hello user endpoint.
```php
<?php

$request = new \SMART\Hello\HelloUserRequest;
$response = $request->fire();

return $response->getBody();
```
## Change between sandbox and live environment
In default mode, this library will talk with `sandbox` environment of SMART. If you want to use live environment, you can call it via `Environment` singleton.
```php
<?php

\SMART\Environment\Environment::getInstance()->setToLive();
```
## Development & Contribution
Contributor is more than welcome to help develop this library, all the important methods should have unit test.

To run test, simply call `composer test` command on terminal.
