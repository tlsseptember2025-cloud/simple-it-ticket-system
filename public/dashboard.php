<?php
require 'auth.php';
require '../config/db.php';

// Fetch all tickets (newest first)
$stmt = $pdo->query("
    SELECT id, ticket_number, sender_email, subject, status, created_at
    FROM tickets
    ORDER BY created_at DESC
");
$tickets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IT Support Dashboard</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm rounded mb-4 px-3">
        <span class="navbar-brand fw-semibold">🛠 IT Support</span>

        <div class="ms-auto text-muted">
            <?php echo htmlspecialchars($_SESSION['admin_username']); ?> |
            <a href="logout.php" class="text-decoration-none">Logout</a>
        </div>
    </nav>

    <!-- Page Title -->
    <div class="mb-3">
        <h4 class="mb-0">Ticket Dashboard</h4>
        <small class="text-muted">All incoming support requests</small>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ticket #</th>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (count($tickets) === 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No tickets found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>

                        <?php
                        $badge = match ($ticket['status']) {
                            'Open' => 'secondary',
                            'In Progress' => 'primary',
                            'Waiting' => 'warning',
                            'Closed' => 'success',
                        };
                        ?>

                        <tr>
                            <td>
                                <a href="ticket.php?id=<?php echo $ticket['id']; ?>"
                                   class="fw-semibold text-decoration-none">
                                    <?php echo htmlspecialchars($ticket['ticket_number']); ?>
                                </a>
                            </td>

                            <td class="text-muted">
                                <?php echo htmlspecialchars($ticket['sender_email']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($ticket['subject']); ?>
                            </td>

                            <td>
                                <span class="badge bg-<?php echo $badge; ?>">
                                    <?php echo $ticket['status']; ?>
                                </span>
                            </td>

                            <td class="text-muted small">
                                <?php echo date('Y-m-d H:i', strtotime($ticket['created_at'])); ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>

</body>
</html>
