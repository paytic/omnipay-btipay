<?php

$gateway = require '_init.php';

//var_dump($_GET);
//var_dump($_POST);

$request = $gateway->completePurchase($_GET);
$response = $request->send();

echo 'CompletePurchaseResponse: <br>';
echo 'isSuccessful: ' . $response->isSuccessful() . '<br>';
echo 'getTransactionReference: ' . $response->getTransactionReference() . '<br>';
echo 'getTransactionId: ' . $response->getTransactionId() . '<br>';
echo 'getCode: ' . $response->getCode() . '<br>';
echo 'getMessage: ' . $response->getMessage() . '<br>';
echo 'getData: ' . json_encode($response->getData()) . '<br>';