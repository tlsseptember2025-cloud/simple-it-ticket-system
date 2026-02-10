<?php
require '../config/db.php';

$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

/* Total closed tickets */
$countStmt = $pdo->prepare("
    SELECT COUNT(*) FROM tickets WHERE status = 'Closed'
");
$countStmt->execute();
$total = (int) $countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $limit));

/* Fetch closed tickets */
$stmt = $pdo->prepare("
    SELECT id, ticket_number, sender_email, subject, created_at
    FROM tickets
    WHERE status = 'Closed'
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Render HTML (returned to modal) */
?>

<table class="table table-hover">
    <thead>
        <tr>
            <th>Ticket #</th>
            <th>From</th>
            <th>Subject</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tickets as $t): ?>
            <tr>
                <td>
                    <a href="ticket.php?id=<?php echo $t['id']; ?>">
                        <?php echo htmlspecialchars($t['ticket_number']); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($t['sender_email']); ?></td>
                <td><?php echo htmlspecialchars($t['subject']); ?></td>
                <td><?php echo date('M j, Y g:i A', strtotime($t['created_at'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<nav class="text-center">
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <button
        class="btn btn-sm btn-outline-primary mx-1"
        onclick="loadClosedTickets(<?php echo $i; ?>)">
        <?php echo $i; ?>
    </button>
<?php endfor; ?>
</nav>
