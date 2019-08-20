<?php
require "vendor/autoload.php";
require "config.php";

use GuzzleHttp\Client;


$client = new Client([
    'timeout' => 2.0,
    'verify' => false
]);

$response = $client->request('GET','https://accounts.google.com/.well-known/openid-configuration');

$discoveryJSON = json_decode((string)$response->getBody());
$tokenEndPoint = $discoveryJSON->token_endpoint;
$userInfoEndPoint = $discoveryJSON->userinfo_endpoint;

$response = $client->request('POST', 'https://www.googleapis.com/oauth2/v4/token',[
    'form_params' => [
        'code' => $_GET['code'],
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'redirect_uri' => 'http://auth.com/connect',
        'grant_type' => 'authorization_code'
    ]
]);

$accessToken = json_decode((string)$response->getBody())->access_token;

$response = $client->request('GET',$userInfoEndPoint,[
    'headers' => [
        'Authorization' => "Bearer ".$accessToken
    ]
]);

$email = json_decode((string)$response->getBody())->email;

dump($email);
