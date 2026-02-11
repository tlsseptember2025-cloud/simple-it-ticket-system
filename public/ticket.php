<?php
require 'auth.php';
require '../config/db.php';
require '../config/mailer.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ticket ID');
}

$ticketId = (int) $_GET['id'];

// Mark updates as read when admin opens ticket
$markRead = $pdo->prepare("
    UPDATE updates
    SET sent_to_user = 1
    WHERE ticket_id = ?
");
$markRead->execute([$ticketId]);


/* ================= FETCH TICKET ================= */

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Ticket not found');
}

/* ================= FETCH UPDATES ================= */

$updatesStmt = $pdo->prepare("
    SELECT * FROM updates
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$updatesStmt->execute([$ticketId]);
$updates = $updatesStmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= FETCH ATTACHMENTS ================= */

$attachmentsStmt = $pdo->prepare("
    SELECT * FROM attachments
    WHERE ticket_id = ?
    ORDER BY created_at ASC
");
$attachmentsStmt->execute([$ticketId]);
$attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= HANDLE POST ================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ================= ADMIN ATTACHMENTS ================= */

$uploadedFiles = [];

if (!empty($_FILES['attachments']['name'][0])) {

    $uploadDir = __DIR__ . '/../uploads/tickets/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    foreach ($_FILES['attachments']['tmp_name'] as $i => $tmpPath) {

        if ($_FILES['attachments']['error'][$i] === UPLOAD_ERR_OK) {

            $originalName = $_FILES['attachments']['name'][$i];
            $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $finalPath = $uploadDir . $safeName;

            move_uploaded_file($tmpPath, $finalPath);

            // Save to DB
            $pdo->prepare("
                INSERT INTO attachments (ticket_id, filename, filepath)
                VALUES (?, ?, ?)
            ")->execute([
                $ticketId,
                $originalName,
                'uploads/tickets/' . $safeName
            ]);

            // Save for email
            $uploadedFiles[] = $finalPath;
        }
    }
}


    $newStatus = $_POST['status'];
    $message   = trim($_POST['message']);
    $sendEmail = isset($_POST['send_email']);

    /* ---- Save update ---- */
    if ($message !== '') {
        $pdo->prepare("
            INSERT INTO updates (ticket_id, message, sent_to_user)
            VALUES (?, ?, 1)
        ")->execute([$ticketId, $message]);
    }

    /* ---- Handle admin attachments ---- */
    if (!empty($_FILES['attachments']['name'][0])) {

        $uploadDir = __DIR__ . '/../uploads/tickets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach ($_FILES['attachments']['name'] as $i => $name) {

            if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmp  = $_FILES['attachments']['tmp_name'][$i];
            $safe = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);

            move_uploaded_file($tmp, $uploadDir . $safe);

            $pdo->prepare("
                INSERT INTO attachments (ticket_id, filename, filepath)
                VALUES (?, ?, ?)
            ")->execute([
                $ticketId,
                $name,
                'uploads/tickets/' . $safe
            ]);
        }
    }

    /* ---- Update ticket status ---- */
    $pdo->prepare("
        UPDATE tickets
        SET status = ?, status_updated_at = NOW()
        WHERE id = ?
    ")->execute([$newStatus, $ticketId]);

    /* ---- Send emails ---- */
    if ($sendEmail) {

        $tokenStmt = $pdo->prepare("
        SELECT status_token
        FROM tickets
        WHERE id = ?
        ");
    $tokenStmt->execute([$ticketId]);
    $tokenRow = $tokenStmt->fetch();

    if (empty($tokenRow['status_token'])) {
        die('Missing status token');
    }

    $statusToken = $tokenRow['status_token'];


        $statusLink = "http://localhost/simple-it-ticket-system/public/ticket_status.php?token={$ticket['status_token']}";

        if ($newStatus === 'Closed') {

            // Build full conversation
            $emailBody  = "Your IT Support ticket has been CLOSED.\n\n";
            $emailBody .= "Ticket Number: {$ticket['ticket_number']}\n\n";
            $emailBody .= "=============================\n";
            $emailBody .= "CONVERSATION HISTORY\n";
            $emailBody .= "=============================\n\n";

            $emailBody .= "[User | {$ticket['created_at']}]\n";
            $emailBody .= "{$ticket['message']}\n\n";

            $allUpdates = $pdo->prepare("
                SELECT * FROM updates
                WHERE ticket_id = ?
                ORDER BY created_at ASC
            ");
            $allUpdates->execute([$ticketId]);

            foreach ($allUpdates as $u) {
                $author = $u['sent_to_user'] ? 'IT Support' : 'User';
                $emailBody .= "[{$author} | {$u['created_at']}]\n";
                $emailBody .= "{$u['message']}\n\n";
            }

            $emailBody .= "=============================\n";
            $emailBody .= "Final Status: CLOSED\n\n";
            $emailBody .= "Check ticket status:\n$statusLink\n\n";
            $emailBody .= "This ticket is now closed.";

            sendMail(
    $ticket['sender_email'],
    "Ticket Closed - {$ticket['ticket_number']}",
    $emailBody,
    $uploadedFiles
);


        } else {

            $emailBody  = "There is an update on your IT Support ticket.\n\n";
            $emailBody .= "Ticket Number: {$ticket['ticket_number']}\n";
            $emailBody .= "Status: $newStatus\n\n";
            $emailBody .= "Message:\n$message\n\n";
            $emailBody .= "Check ticket status:\n$statusLink";

            sendMail(
    $ticket['sender_email'],
    "Update on Ticket {$ticket['ticket_number']}",
    $emailBody,
    $uploadedFiles
);

        }

        // Notify staff/admin
        sendMail(
            $_SESSION['admin_username'] . '@loopsautomation.com',
            "Admin update on {$ticket['ticket_number']}",
            "An update was added:\n\n$message"
        );
    }

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

    <nav class="navbar bg-white shadow-sm rounded mb-4 px-3">
        <span class="navbar-brand fw-semibold">🛠 IT Support</span>
        <div class="ms-auto">
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

    <!-- Ticket Info -->
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
            <?php
function cleanDisplay($text) {
    $text = preg_replace('/\[image:.*?\]/i', '', $text);
    $text = preg_split('/\ROn .* wrote:\R/i', $text)[0];
    $text = preg_split('/\R--\R/', $text)[0];
    return trim($text);
}
echo nl2br(htmlspecialchars(cleanDisplay($ticket['message'])));
?>

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
                        <div class="col-md-3 mb-3">
                            <?php if ($isImage): ?>
                                <a href="../<?php echo $file['filepath']; ?>" target="_blank">
                                    <img src="../<?php echo $file['filepath']; ?>" class="img-fluid rounded shadow-sm">
                                </a>
                            <?php else: ?>
                                <a href="../<?php echo $file['filepath']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($file['filename']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Conversation -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <h6>Conversation</h6>
            <?php if (!$updates): ?>
                <p class="text-muted">No updates yet.</p>
            <?php else: ?>
                <?php foreach ($updates as $u): ?>
                    <div class="mb-3">
                        <small class="text-muted"><?php echo $u['created_at']; ?></small>
                        <p><?php echo nl2br(htmlspecialchars($u['message'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Update Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h6>Add Update</h6>

            <form method="post" enctype="multipart/form-data">
                <label class="form-label">Status</label>
                <select name="status" class="form-select mb-3">
                    <?php foreach (['Open','In Progress','Waiting','Closed'] as $s): ?>
                        <option value="<?php echo $s; ?>" <?php echo $ticket['status']===$s?'selected':''; ?>>
                            <?php echo $s; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="form-label">Message</label>
                <textarea name="message" rows="4" class="form-control mb-3"></textarea>

                <label class="form-label">Attachments</label>
                <input type="file" name="attachments[]" multiple class="form-control mb-3">

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="send_email" checked>
                    <label class="form-check-label">Send update to user by email</label>
                </div>

                <button class="btn btn-primary">💾 Save Update</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>