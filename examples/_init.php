<?php

use Symfony\Component\HttpFoundation\Request as HttpRequest;

define('CURRENT_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
require dirname(__DIR__).DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR.'bootstrap.php';

$request = HttpRequest::createFromGlobals();

$gateway = new \Paytic\Omnipay\Btipay\Gateway(null, $request);

$gateway->initialize(
    [
        'username' => getenv('BTIPAY_USERNAME'),
        'password' => getenv('BTIPAY_PASSWORD'),
        'callback_token' => getenv('BTIPAY_CALLBACK_TOKEN'),
//     'lang' => 'en',
        'testMode' => true,
    ]
);

return $gateway;
