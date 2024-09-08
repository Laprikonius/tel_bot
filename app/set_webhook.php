<?php

$apiToken = '7113684977:AAEbj7bSq29KwRYE0M5T7kerpUCdlNO9stA';
$apiUrl = "https://api.telegram.org/bot$apiToken/";

// URL вашого сервера
$webhookUrl = 'https://db07-185-237-216-6.ngrok-free.app/telegram_bot.php';

$params = [
    'url' => $webhookUrl
];

function sendRequest($method, $params = []) {
    global $apiUrl;
    $url = $apiUrl . $method;
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($params),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

$result = sendRequest('setWebhook', $params);

if ($result['ok']) {
    echo 'Webhook set successfully!';
} else {
    echo 'Error setting webhook: ' . $result['description'];
}