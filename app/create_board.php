<?php
$apiKey = '5b75242defe45912180724c99f96a2e7';
$apiToken = 'TRELLO_API_KEY';

$boardName = 'my_new_test_board';

$url = 'https://api.trello.com/1/members/me/boards?key=' . $apiKey . '&token=' . $apiToken;

$response = file_get_contents($url);

if ($response === FALSE) {
    die('Error fetching boards');
}

$boards = json_decode($response, true);

$createBoard = false;
foreach ($boards as $board) {
    echo "Board Name: " . $board['name'] . " - Board ID: " . $board['id'] . "</br>";
    if ($board['name'] == $boardName) {
        die('Такая доска существует');
    } else {
        $createBoard = true;
    }
}
if ($createBoard) {
    $url = 'https://api.trello.com/1/boards/?name=' . $boardName . '&key=' . $apiKey . '&token=' . $apiToken;

    $options = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
        ),
    );

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        die('Error creating board');
    }

    $data = json_decode($response, true);
    $boardId = $data['id'];

    $url = 'https://api.trello.com/1/boards/' . $boardId . '/lists?key=' . $apiKey . '&token=' . $apiToken;

    $response = file_get_contents($url);

    if ($response === FALSE) {
        die('Error fetching lists');
    }

    $lists = json_decode($response, true);

    foreach ($lists as $list) {
        ?>
        <pre>
            <? var_dump($list); ?>
        </pre>
        <?
    }
}
/*

$url = 'https://api.trello.com/1/boards/' . $boardId . '/lists?key=' . $apiKey . '&token=' . $apiToken;

$response = file_get_contents($url);

if ($response === FALSE) {
    die('Error fetching lists');
}

$lists = json_decode($response, true);

// Виведення списків
foreach ($lists as $list) {
    $columnId = $list['id'];
    //echo "List Name: " . $list['name'] . " - List ID: " . $list['id'] . "\n";
    $url = 'https://api.trello.com/1/lists/' . $columnId . '?closed=true&key=' . $apiKey . '&token=' . $apiToken;

    // Виконання запиту
    $options = array(
        'http' => array(
            'method'  => 'PUT',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
        ),
    );

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    // Перевірка на успіх
    if ($response === FALSE) {
        echo 'Error archiving list with ID ' . $columnId . "\n";
    } else {
        echo 'List with ID ' . $columnId . ' archived successfully.' . "\n";
    }
}

Колонки
$columns = ['InProgress', 'Done'];

foreach ($columns as $column) {
    // URL для створення колонки
    $url = 'https://api.trello.com/1/lists?name=' . urlencode($column) . '&idBoard=' . $boardId . '&key=' . $apiKey . '&token=' . $apiToken;

    // Виконання запиту
    $response = file_get_contents($url, false, $context);

    // Перевірка на успіх
    if ($response === FALSE) {
        die('Error creating column ' . $column);
    }

    echo "Column '{$column}' created successfully.\n";
}*/
?>