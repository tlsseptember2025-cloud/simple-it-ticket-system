<?php
require 'auth.php';
require '../config/db.php';
require '../config/mailer.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ticket ID');
}

$ticketId = (int) $_GET['id'];

/**
 * Fetch ticket
 */
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die('Ticket not found');
}

/**
 * Fetch attachments
 */
$stmt = $pdo->prepare("
    SELECT * FROM attachments
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$ticketId]);
$attachments = $stmt->fetchAll();

/**
 * Fetch updates
 */
$stmt = $pdo->prepare("
    SELECT * FROM updates
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$stmt->execute([$ticketId]);
$updates = $stmt->fetchAll();

/**
 * Handle form submission
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $newStatus = $_POST['status'];
    $message   = trim($_POST['message']);
    $sendToUser = isset($_POST['send_email']) ? 1 : 0;

    if ($message !== '') {
        $stmt = $pdo->prepare("
            INSERT INTO updates (ticket_id, message, sent_to_user)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$ticketId, $message, $sendToUser]);

        if ($sendToUser) {
            sendMail(
                $ticket['sender_email'],
                "Update on Ticket {$ticket['ticket_number']}",
                $message . "\n\nTicket: {$ticket['ticket_number']}"
            );
        }
    }

    $stmt = $pdo->prepare("
        UPDATE tickets SET status = ? WHERE id = ?
    ");
    $stmt->execute([$newStatus, $ticketId]);

    header("Location: ticket.php?id=" . $ticketId);
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
            <h6 class="card-title">Ticket Details</h6>

            <p class="mb-1"><strong>From:</strong>
                <?php echo htmlspecialchars($ticket['sender_email']); ?>
            </p>

            <p class="mb-1"><strong>Subject:</strong>
                <?php echo htmlspecialchars($ticket['subject']); ?>
            </p>

            <p class="mb-1"><strong>Status:</strong>
                <?php echo htmlspecialchars($ticket['status']); ?>
            </p>

            <p class="mb-0"><strong>Created:</strong>
                <?php echo $ticket['created_at']; ?>
            </p>
        </div>
    </div>

    <!-- Original Message -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Original Message</h6>
            <p class="mb-0">
                <?php echo nl2br(htmlspecialchars($ticket['message'])); ?>
            </p>
        </div>
    </div>

    <!-- Attachments -->
    <?php if (!empty($attachments)): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Attachments</h6>

            <div class="row">
                <?php foreach ($attachments as $file): ?>
                    <?php
                    $isImage = preg_match('/\.(jpg|jpeg|png|gif)$/i', $file['filename']);
                    ?>
                    <div class="col-md-3 mb-3 text-center">
                        <?php if ($isImage): ?>
                            <a href="../<?php echo $file['filepath']; ?>" target="_blank">
                                <img
                                    src="../<?php echo $file['filepath']; ?>"
                                    class="img-fluid rounded border shadow-sm mb-1"
                                    alt="Attachment">
                            </a>
                        <?php else: ?>
                            <a href="../<?php echo $file['filepath']; ?>" target="_blank">
                                <?php echo htmlspecialchars($file['filename']); ?>
                            </a>
                        <?php endif; ?>

                        <small class="text-muted d-block">
                            <?php echo htmlspecialchars($file['filename']); ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
    <?php endif; ?>

    <!-- Updates -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Updates</h6>

            <?php if (empty($updates)): ?>
                <p class="text-muted">No updates yet.</p>
            <?php else: ?>
                <?php foreach ($updates as $update): ?>
                    <div class="mb-3">
                        <small class="text-muted">
                            <?php echo $update['created_at']; ?>
                        </small>
                        <p class="mb-0">
                            <?php echo nl2br(htmlspecialchars($update['message'])); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Update Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h6 class="card-title">Add Update / Change Status</h6>

            <form method="post">

                <label class="form-label">Status</label>
                <select name="status" class="form-select mb-3">
                    <?php
                    $statuses = ['Open','In Progress','Waiting','Closed'];
                    foreach ($statuses as $status) {
                        $selected = ($ticket['status'] === $status) ? 'selected' : '';
                        echo "<option value=\"$status\" $selected>$status</option>";
                    }
                    ?>
                </select>

                <label class="form-label">Update message</label>
                <textarea name="message" rows="5" class="form-control mb-3"></textarea>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="send_email" checked>
                    <label class="form-check-label">
                        Send this update to staff by email
                    </label>
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
