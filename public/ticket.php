<?php
require 'auth.php';
require '../config/db.php';
require '../config/mailer.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ticket ID');
}

$ticketId = (int) $_GET['id'];

/* ================= FETCH DATA ================= */

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die('Ticket not found');
}

$stmt = $pdo->prepare("
    SELECT * FROM attachments
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$ticketId]);
$attachments = $stmt->fetchAll();

$stmt = $pdo->prepare("
    SELECT * FROM updates
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$ticketId]);
$updates = $stmt->fetchAll();

/* ================= HANDLE POST ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newStatus   = $_POST['status'];
    $message     = trim($_POST['message']);
    $sendToUser  = isset($_POST['send_email']);

    // Save update if message exists
    if ($message !== '') {
        $pdo->prepare("
            INSERT INTO updates (ticket_id, message, sent_to_user)
            VALUES (?, ?, ?)
        ")->execute([$ticketId, $message, $sendToUser ? 1 : 0]);

        if ($sendToUser) {

            // ===== EMAIL CONTENT =====
            if ($newStatus === 'Closed') {

                // FINAL CLOSURE EMAIL
                $emailBody  = "Your IT support ticket has been CLOSED.\n\n";
                $emailBody .= "Ticket Number: {$ticket['ticket_number']}\n\n";
                $emailBody .= "Resolution / Summary:\n";
                $emailBody .= $message;

                sendMail(
                    $ticket['sender_email'],
                    "Ticket Closed: {$ticket['ticket_number']}",
                    $emailBody
                );

            } else {

                // NORMAL UPDATE EMAIL
                $emailBody  = $message;
                $emailBody .= "\n\nTicket Number: {$ticket['ticket_number']}";
                $emailBody .= "\nStatus: {$newStatus}";

                sendMail(
                    $ticket['sender_email'],
                    "Update on Ticket {$ticket['ticket_number']}",
                    $emailBody
                );
            }
        }
    }

    // Update ticket status
    $pdo->prepare("
        UPDATE tickets SET status = ? WHERE id = ?
    ")->execute([$newStatus, $ticketId]);

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-4">

    <!-- Navbar -->
    <nav class="navbar bg-white shadow-sm rounded mb-4 px-3">
        <span class="navbar-brand fw-semibold">🛠 IT Support</span>
        <div class="ms-auto text-muted">
            <?php echo htmlspecialchars($_SESSION['admin_username']); ?> |
            <a href="logout.php" class="text-decoration-none">Logout</a>
        </div>
    </nav>

    <a href="dashboard.php" class="text-decoration-none mb-3 d-inline-block">
        ← Back to Dashboard
    </a>

    <h4 class="mb-3">
        Ticket <?php echo htmlspecialchars($ticket['ticket_number']); ?>
    </h4>

    <!-- Ticket Details -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <p><strong>From:</strong> <?php echo htmlspecialchars($ticket['sender_email']); ?></p>
            <p><strong>Subject:</strong> <?php echo htmlspecialchars($ticket['subject']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['status']); ?></p>
            <p class="mb-0"><strong>Created:</strong> <?php echo $ticket['created_at']; ?></p>
        </div>
    </div>

    <!-- Original Message -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6>Original Message</h6>
            <?php echo nl2br(htmlspecialchars($ticket['message'])); ?>
        </div>
    </div>

    <!-- Attachments -->
    <?php if ($attachments): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6>Attachments</h6>
            <div class="row">
                <?php foreach ($attachments as $file): ?>
                    <?php $isImage = preg_match('/\.(jpg|jpeg|png|gif)$/i', $file['filename']); ?>
                    <div class="col-md-3 text-center mb-3">
                        <?php if ($isImage): ?>
                            <a href="../<?php echo $file['filepath']; ?>" target="_blank">
                                <img src="../<?php echo $file['filepath']; ?>" class="img-fluid rounded shadow-sm">
                            </a>
                        <?php else: ?>
                            <a href="../<?php echo $file['filepath']; ?>" target="_blank">
                                <?php echo htmlspecialchars($file['filename']); ?>
                            </a>
                        <?php endif; ?>
                        <small class="text-muted d-block"><?php echo htmlspecialchars($file['filename']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Updates -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6>Updates</h6>
            <?php if (!$updates): ?>
                <p class="text-muted">No updates yet.</p>
            <?php else: ?>
                <?php foreach ($updates as $update): ?>
                    <div class="mb-3">
                        <small class="text-muted"><?php echo $update['created_at']; ?></small>
                        <p><?php echo nl2br(htmlspecialchars($update['message'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Update Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h6>Add Update / Change Status</h6>

            <form method="post">
                <label class="form-label">Status</label>
                <select name="status" class="form-select mb-3">
                    <?php
                    foreach (['Open','In Progress','Waiting','Closed'] as $s) {
                        $sel = ($ticket['status'] === $s) ? 'selected' : '';
                        echo "<option value=\"$s\" $sel>$s</option>";
                    }
                    ?>
                </select>

                <label class="form-label">Update message</label>
                <textarea name="message" rows="5" class="form-control mb-3"></textarea>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="send_email" checked>
                    <label class="form-check-label">Send this update to staff by email</label>
                </div>

                <button type="submit" class="btn btn-primary">
                    💾 Save Update
                </button>
            </form>
        </div>
    </div>

</div>
</body>
</html>