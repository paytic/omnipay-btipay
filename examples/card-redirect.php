<?php

$gateway = require '_init.php';

$parameters = [
    'amount' => 20,
    'currency' => 'RON',
    'orderId' => time(),
    'description' => 'Test Order',
    'lang' => 'en',
    'returnUrl' => CURRENT_URL,
    'testMode' => false,
    'notifyUrl' => 'https://hookb.in/aBgWzq1mMjI1oobLKDPr',
    'card' => ['firstName' => 'Gabriel', 'lastName' => 'Solomon','email' => 'test@yahoo.com'],
];
$request = $gateway->purchase($parameters);
$response = $request->send();

// Send the Symfony HttpRedirectResponse
$response->send();
