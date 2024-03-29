<?php

$gateway = require '_init.php';

//var_dump($_GET);
//var_dump($_POST);

$request = $gateway->completePurchase($_GET);
$response = $request->send();

var_dump($response->isSuccessful());
var_dump($response->getData());