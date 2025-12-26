<?php

use SMART\Scope\Scope;
use SMART\Oauth2\Provider;
use Illuminate\Support\Facades\Session;

require_once __DIR__.'/../../../vendor/autoload.php';
require_once __DIR__.'/../../helpers.php';
require_once __DIR__.'/../../config.php';

if (!isset($_GET['client_id']) || !isset($_GET['client_secret'])) {
    exit('Error: Please fill both client id and client secret before test again.');
}


Session::put('client_id', $_GET['client_id']);
Session::put('client_secret', $_GET['client_secret']);
Session::put('callback_uri', $adviserRedirectUri);
Session::put('caller', '/index.php');

$provider = new Provider(
    $_GET['client_id'],
    $_GET['client_secret'],
    $adviserRedirectUri
);
$scope = [Scope::SMP_USER];
$provider->redirectToAuthorizationURL($scope);
