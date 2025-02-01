<?php

use SMART\Scope\Scope;
use SMART\Oauth2\Provider;

session_start();
require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../helpers.php';
require_once __DIR__.'/../../config.php';

if (!isset($_GET['client_id']) || !isset($_GET['client_secret'])) {
    exit('Error: Please fill both client id and client secret before test again.');
}


$_SESSION['client_id'] = $_GET['client_id'];
$_SESSION['client_secret'] = $_GET['client_secret'];
$_SESSION['callback_uri'] = $adviserRedirectUri;
$_SESSION['caller'] = '/index.php';

$provider = new Provider(
    $_GET['client_id'],
    $_GET['client_secret'],
    $adviserRedirectUri
);
$scope = [Scope::SMP_USER];
$provider->redirectToAuthorizationURL($scope);
