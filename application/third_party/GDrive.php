<?php
set_include_path(dirname(__FILE__)."/google-api-php-client-master/src/" . PATH_SEPARATOR . get_include_path());
require_once 'Google/Client.php';
require_once 'Google/Http/MediaFileUpload.php';
require_once 'Google/Service/Drive.php';

function driveRefreshToken($full_token) {
    $client_id = 'SNIPPED';
    $client_secret = 'SNIPPED';
    $redirect_uri = 'http://manicyak.com/settings/drive/';

    $client = new Google_Client();
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->addScope("https://www.googleapis.com/auth/drive");

    return $client->authenticate($full_token);
}

function driveAuthenticate() {
    $client_id = 'SNIPPED';
    $client_secret = 'SNIPPED';
    $redirect_uri = 'http://manicyak.com/settings/drive/';

    $client = new Google_Client();
    $client->setClientId($client_id);
    $client->setClientSecret($client_secret);
    $client->setRedirectUri($redirect_uri);
    $client->addScope("https://www.googleapis.com/auth/drive");

    header("Location: ".$client->createAuthUrl());
}