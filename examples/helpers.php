<?php

use SMART\Oauth2\Provider;
use SMART\Oauth2\AccessToken;
use Illuminate\Support\Facades\Session;

function baseURL()
{
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ?
            'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
}

function refreshAccessTokenIfNeeded()
{
    if (!Session::has('client_id')) {
        return;
    }

    $provider = new Provider(
        Session::get('client_id'),
        Session::get('client_secret'),
        Session::get('callback_uri')
    );

    try {
        $existingAccessToken = AccessToken::get();

        if ($existingAccessToken->hasExpired()) {
            $newAccessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => $existingAccessToken->getRefreshToken(),
            ]);
            AccessToken::set($newAccessToken);
        }
    } catch (\Throwable $th) {
        //throw $th;
    }
}
