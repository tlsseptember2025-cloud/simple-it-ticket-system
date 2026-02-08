<?php
require '../config/db.php';

if (empty($_GET['token'])) {
    die('Invalid ticket');
}

$token = $_GET['token'];

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
<div class="container py-5" style="max-width:500px">

    <div class="card shadow-sm">
        <div class="card-body text-center">

            <h5 class="mb-3">🎫 Ticket Status</h5>

            <p><strong>Ticket Number:</strong><br>
            <?= htmlspecialchars($ticket['ticket_number']) ?></p>

            <p><strong>Status:</strong><br>
            <span class="badge bg-success">
                <?= htmlspecialchars($ticket['status']) ?>
            </span></p>

            <p class="text-muted small mb-0">
                Last updated:
                <?= date('l, M j, Y g:i A', strtotime($ticket['status_updated_at'])) ?>
            </p>

        </div>
    </div>

</div>
</body>
</html>