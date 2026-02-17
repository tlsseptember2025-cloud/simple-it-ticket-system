<?php
require '../config/db.php';

$limit = 10;

$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int)$_GET['page']
    : 1;

$page = max(1, $page);
$offset = ($page - 1) * $limit;

$closedCategory = isset($_GET['closed_category']) 
    ? trim($_GET['closed_category']) 
    : '';

$where = "status = 'Closed'";
$params = [];

if ($closedCategory !== '') {
    $where .= " AND category = :category";
    $params[':category'] = $closedCategory;
}

/* COUNT */
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE $where");
foreach ($params as $k => $v) {
    $countStmt->bindValue($k, $v);
}
$countStmt->execute();
$total = (int)$countStmt->fetchColumn();
$totalPages = ceil($total / $limit);

/* FETCH */
$stmt = $pdo->prepare("
    SELECT id, ticket_number, sender_email, subject, category, status_updated_at
    FROM tickets
    WHERE $where
    ORDER BY status_updated_at DESC
    LIMIT :limit OFFSET :offset
");

foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();
$tickets = $stmt->fetchAll();
?>

<table class="table table-hover table-sm">
<thead class="table-light">
<tr>
<th>Ticket #</th>
<th>From</th>
<th>Subject</th>
<th>Category</th>
<th>Closed</th>
</tr>
</thead>
<tbody>
<?php foreach ($tickets as $t): ?>
<tr>
<td><a href="ticket.php?id=<?php echo $t['id']; ?>">
<?php echo htmlspecialchars($t['ticket_number']); ?>
</a></td>
<td><?php echo htmlspecialchars($t['sender_email']); ?></td>
<td><?php echo htmlspecialchars($t['subject']); ?></td>
<td><?php echo htmlspecialchars($t['category'] ?? '-'); ?></td>
<td><?php echo date('M j, Y g:i A', strtotime($t['status_updated_at'])); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if ($totalPages > 1): ?>
<nav class="text-center mt-3">
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
<button 
class="btn btn-sm btn-outline-primary mx-1"
onclick="loadClosedTickets(<?php echo $i; ?>)">
<?php echo $i; ?>
</button>
<?php endfor; ?>
</nav>
<?php endif; ?>