<?php
require 'auth.php';
require '../config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ticket ID');
}

$ticketId = (int) $_GET['id'];

// Fetch ticket
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die('Ticket not found');
}

// Fetch updates
$stmt = $pdo->prepare("
    SELECT * FROM updates
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$ticketId]);
$updates = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['status'];
    $message = trim($_POST['message']);

    if ($message !== '') {
        $stmt = $pdo->prepare("
            INSERT INTO updates (ticket_id, message, sent_to_user)
            VALUES (?, ?, 0)
        ");
        $stmt->execute([$ticketId, $message]);
    }

    $stmt = $pdo->prepare("
        UPDATE tickets SET status = ? WHERE id = ?
    ");
    $stmt->execute([$newStatus, $ticketId]);

    header("Location: ticket.php?id=" . $ticketId);
    exit;
}
?>

<html>
<head>
    <title>View Ticket</title>
    <style>
        .box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<a href="dashboard.php">← Back to Dashboard</a>

<h2>Ticket <?php echo htmlspecialchars($ticket['ticket_number']); ?></h2>

<div class="box">
    <strong>From:</strong> <?php echo htmlspecialchars($ticket['sender_email']); ?><br>
    <strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?><br>
    <strong>Status:</strong> <?php echo $ticket['status']; ?><br>
    <strong>Created:</strong> <?php echo $ticket['created_at']; ?>
</div>

<div class="box">
    <h3>Original Message</h3>
    <p><?php echo nl2br(htmlspecialchars($ticket['message'])); ?></p>
</div>

<div class="box">
    <h3>Updates</h3>

    <?php if (count($updates) === 0): ?>
        <p>No updates yet.</p>
    <?php else: ?>
        <?php foreach ($updates as $update): ?>
            <p>
                <small><?php echo $update['created_at']; ?></small><br>
                <?php echo nl2br(htmlspecialchars($update['message'])); ?>
            </p>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="box">
    <h3>Add Update / Change Status</h3>

    <form method="post">
        <label>Status</label><br>
        <select name="status">
            <?php
            $statuses = ['Open','In Progress','Waiting','Closed'];
            foreach ($statuses as $status) {
                $selected = ($ticket['status'] === $status) ? 'selected' : '';
                echo "<option value=\"$status\" $selected>$status</option>";
            }
            ?>
        </select><br><br>

        <label>Update message (internal for now)</label><br>
        <textarea name="message" rows="5" cols="60"></textarea><br><br>

        <button type="submit">Save</button>
    </form>
</div>

</body>
</html>