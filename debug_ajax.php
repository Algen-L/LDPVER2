<?php
// Script to test the AJAX endpoint
$url = 'http://localhost/LDPVer2/public/send-verification-code';
$data = ['email' => 'ggeenggeen@gmail.com'];

$options = [
    'http' => [
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
        'ignore_errors' => true // Fetch content even on 404/500
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$headers = $http_response_header;

echo "Response Headers:\n";
print_r($headers);
echo "\nResponse Body:\n";
var_dump($result);
