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
<html>
<head>
    <title>Ticket Dashboard</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
        }
        th {
            background: #eee;
        }
    </style>
</head>
<body>

<h2>IT Support – Ticket Dashboard</h2>

<p>Logged in as: <strong><?php echo $_SESSION['admin_username']; ?></strong></p>

<table>
    <thead>
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
                <td colspan="5">No tickets found</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tickets as $ticket): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($ticket['ticket_number']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($ticket['sender_email']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($ticket['subject']); ?>
                    </td>
                    <td>
                        <?php echo $ticket['status']; ?>
                    </td>
                    <td>
                        <?php echo $ticket['created_at']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<br>
<a href="logout.php">Logout</a>

</body>
</html>