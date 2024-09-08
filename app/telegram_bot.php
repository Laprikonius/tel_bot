<?php

// Ваш API токен
$apiToken = '7113684977:AAEbj7bSq29KwRYE0M5T7kerpUCdlNO9stA';

// URL для API Telegram
$apiUrl = "https://api.telegram.org/bot$apiToken/";

// Отримання оновлень від Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Функція для відправки запиту до Telegram API
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

// Обробка повідомлень
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];

    if ($text === '/start') {
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'Запросити авторизаційні дані Trello', 'callback_data' => 'request_auth']
                ]
            ]
        ];

        $params = [
            'chat_id' => $chatId,
            'text' => 'Виберіть дію:',
            'reply_markup' => json_encode($keyboard),
        ];

        sendRequest('sendMessage', $params);
    }
}

// Обробка callback-запитів
if (isset($update['callback_query'])) {
    $callbackQuery = $update['callback_query'];
    $chatId = $callbackQuery['message']['chat']['id'];
    $messageId = $callbackQuery['message']['message_id'];
    $data = $callbackQuery['data'];

    if ($data === 'request_auth') {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => 'Будь ласка, надайте ваші авторизаційні дані для Trello.',
        ];

        sendRequest('editMessageText', $params);

        $params = [
            'chat_id' => $chatId,
            'text' => 'Введіть ваші авторизаційні дані для Trello:',
        ];

        sendRequest('sendMessage', $params);
    }
}
