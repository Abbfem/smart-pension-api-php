<?php

use SMART\Oauth2\Provider;
use SMART\Oauth2\AccessToken;



require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../helpers.php';
require_once __DIR__.'/../../config.php';

session_start();

if (!isset($_GET['code'])) {
    exit('Error: Please fill both client id and client secret before test again.');
}

$provider = new Provider(
    $clientId,
    $clientSecret,
    $adviserRedirectUri
);

// Try to get an access token using the authorization code grant.
$accessToken = $provider->getAccessToken('authorization_code', [
    'code' => $_GET['code'],
]);
AccessToken::set($accessToken);
header('Location: /index.php');
exit;
