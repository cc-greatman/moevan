<?php
// proxy.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");

$url = 'https://autopilotng.com/v1/validate/smartcard-no';
$ch = curl_init($url);

// Forward request headers and body
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Bearer ' . $_SERVER['HTTP_AUTHORIZATION']
));
curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents("php://input"));

$response = curl_exec($ch);
curl_close($ch);

echo $response;
