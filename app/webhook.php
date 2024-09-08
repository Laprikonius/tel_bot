<?php

$telegramToken = '7113684977:AAEbj7bSq29KwRYE0M5T7kerpUCdlNO9stA';
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (isset($update['message']) && $update['message']['text'] === '/start') {
    $chatId = $update['message']['chat']['id'];
    $first_name = $update['message']['from']['first_name'];
    $username_id = $update['message']['from']['id'];
    $resSave = saveToDb($first_name, $chatId, $username_id);
    $message = 'Привіт, ' . $update['message']['from']['last_name'] . ' '. $update['message']['from']['first_name'] . '! Вітаємо в боті.' . $resSave;

    sendMessage($telegramToken, $chatId, $message);
}

if (isset($update['callback_query'])) {
    $callback_query = $update['callback_query'];
    $callback_query_id = $callback_query['id'];
    $callback_query_data = $callback_query['data'];
    $chat_id = $callback_query['message']['chat']['id'];

    // $response_text = "Получены данные: $callback_query_data $callback_query_id";

    // file_get_contents("https://api.telegram.org/bot$telegramToken/sendMessage?" . http_build_query([
    //     'chat_id' => $chat_id,
    //     'text' => $response_text
    // ]));

    //sendMessageTrello();
}

function sendMessage($telegramToken, $chatId, $message) {
    $url = "https://api.telegram.org/bot{$telegramToken}/sendMessage";
    $apiKey = '5b75242defe45912180724c99f96a2e7';
    $keyboard = [
        'inline_keyboard' => [
            [
                [
                    'text' => 'Авторизуватися у Trello',
                    //'callback_data' => 'request_auth',
                    'url' => "https://trello.com/1/authorize?key=$apiKey&name=Test&scope=read,write&response_type=token"

                ]
            ]
        ]
    ];
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'reply_markup' => json_encode($keyboard),
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

function saveToDb ($first_name, $chatId, $username_id) {
    //telegram_bot_users
    $dsn = 'mysql:host=mysql;dbname=test_db';
    $username = 'root';
    $password = 'root';
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $selectIfExist = $pdo->prepare("SELECT * FROM telegram_bot_users WHERE username_id = :username_id");
        $selectUsername_id = $username_id;
        $selectIfExist->execute(
            [
                'username_id' => $selectUsername_id
            ]
        );
        $user = $selectIfExist->fetch(PDO::FETCH_ASSOC);

        if ($user['id'] > 0) {
            $stmt = $pdo->prepare("UPDATE telegram_bot_users SET user_first_name = :user_first_name, chat_id = :chat_id WHERE username_id = :username_id");
            $stmt->execute(
                [
                    'user_first_name' => $first_name,
                    'chat_id' => $chatId,
                    'username_id' => $selectUsername_id
                ]
            );
        } else {
            $stmt = $pdo->prepare("INSERT INTO telegram_bot_users (user_first_name, chat_id, username_id) VALUES (:user_first_name, :chat_id, :username_id)");
            $stmt->execute(
                [
                    'user_first_name' => $first_name,
                    'chat_id' => $chatId,
                    'username_id' => $username_id
                ]
            );
        }
        //return "Успешное подключение к базе данных!" . $user['id'];
    } catch (PDOException $e) {
        //return "Ошибка подключения к базе данных: " . $e->getMessage();
    }
}



//https://api.telegram.org/bot7113684977:AAEbj7bSq29KwRYE0M5T7kerpUCdlNO9stA/setWebhook?url=https://db07-185-237-216-6.ngrok-free.app//webhook.php

//docker run --rm -it wernight/ngrok ngrok authtoken 2kK6p9KhtW4ZDqhhCDnSIS0IdWR_2Lz6qe8bkXSt4fpVxXfCj