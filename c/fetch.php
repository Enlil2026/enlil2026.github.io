<?php
// fetch.php
$storage_file = 'chat_storage.json';
$last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;
$data = json_decode(file_get_contents($storage_file), true);

$new_messages = array_filter($data, function($m) use ($last_id) {
    return $m['id'] > $last_id;
});

echo json_encode(array_values($new_messages));
