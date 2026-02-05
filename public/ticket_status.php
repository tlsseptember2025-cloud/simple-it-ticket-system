<?php
require '../config/db.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die('Invalid ticket');
}

$stmt = $pdo->prepare("
    SELECT ticket_number, status, status_updated_at
    FROM tickets
    WHERE status_token = ?
");
$stmt->execute([$token]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die('Ticket not found');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4>Ticket Status</h4>
            <p><strong>Ticket #:</strong> <?= htmlspecialchars($ticket['ticket_number']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($ticket['status']) ?></p>
            <p class="text-muted">
                Last updated: <?= date('l, M j, Y g:i A', strtotime($ticket['status_updated_at'])) ?>
            </p>
        </div>
    </div>
</div>
</body>
</html>
