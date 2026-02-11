<?php
require '../config/db.php';

header('Content-Type: application/json');

/*
We check:
- Latest ticket creation
- Latest update
*/

$latestTicket = $pdo->query("
    SELECT UNIX_TIMESTAMP(MAX(created_at)) 
    FROM tickets
")->fetchColumn();

$latestUpdate = $pdo->query("
    SELECT UNIX_TIMESTAMP(MAX(created_at)) 
    FROM updates
")->fetchColumn();

$lastChange = max((int)$latestTicket, (int)$latestUpdate);

echo json_encode([
    'last_change' => $lastChange
]);