<?php
$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-type: application/json\r\n",
        'content' => json_encode(['message' => 'اريد شراء فستان'])
    ]
];
$context = stream_context_create($options);
$result = file_get_contents('http://127.0.0.1:8000/api/assistant/chat', false, $context);
echo $result;
