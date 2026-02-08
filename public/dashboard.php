<?php
require 'auth.php';
require '../config/db.php';

/* ================= FLASH MESSAGE ================= */

if (isset($_GET['fetched']) && empty($_SESSION['fetch_success'])) {
    $_SESSION['fetch_success'] = 'Emails fetched successfully';
}

/* ================= PAGINATION ================= */

$limit = 5;
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page  = max($page, 1);
$offset = ($page - 1) * $limit;

/* ===== COUNT TOTAL TICKETS (OPEN ONLY) ===== */
$totalStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tickets 
    WHERE status != 'Closed'
");
$totalStmt->execute();
$totalTickets = (int)$totalStmt->fetchColumn();
$totalPages   = (int)ceil($totalTickets / $limit);

/* ===== FETCH OPEN TICKETS ===== */
$stmt = $pdo->prepare("
    SELECT id, ticket_number, sender_email, subject, status, created_at
    FROM tickets
    WHERE status != 'Closed'
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll();

/* ================= CLOSED TICKETS (MODAL) ================= */

$closedLimit = 5;
$closedPage  = isset($_GET['closed_page']) && is_numeric($_GET['closed_page'])
    ? (int)$_GET['closed_page']
    : 1;
$closedPage = max(1, $closedPage);
$closedOffset = ($closedPage - 1) * $closedLimit;

/* Count closed tickets */
$closedCountStmt = $pdo->query("
    SELECT COUNT(*) 
    FROM tickets 
    WHERE status = 'Closed'
");
$totalClosed = (int) $closedCountStmt->fetchColumn();
$closedPages = (int) ceil($totalClosed / $closedLimit);

/* Fetch closed tickets */
$closedStmt = $pdo->prepare("
    SELECT id, ticket_number, sender_email, subject, created_at
    FROM tickets
    WHERE status = 'Closed'
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");
$closedStmt->bindValue(':limit', $closedLimit, PDO::PARAM_INT);
$closedStmt->bindValue(':offset', $closedOffset, PDO::PARAM_INT);
$closedStmt->execute();
$closedTickets = $closedStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>IT Support Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

<!-- NAVBAR (UNCHANGED) -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm rounded mb-4 px-3">
    <a class="navbar-brand fw-semibold" href="dashboard.php">IT Support</a>
    <div class="ms-auto">
        <?php echo htmlspecialchars($_SESSION['admin_username']); ?> |
        <a href="logout.php">Logout</a>
    </div>
</nav>

<?php if (!empty($_SESSION['fetch_success'])): ?>
<div id="fetchAlert" class="alert alert-success">
    ✅ <?php echo htmlspecialchars($_SESSION['fetch_success']); ?>
    <?php unset($_SESSION['fetch_success']); ?>
</div>
<?php endif; ?>

<h4>Ticket Dashboard</h4>

<?php if ($totalClosed > 0): ?>
<div class="alert alert-info d-flex justify-content-between align-items-center">
    <span>ℹ️ There are <strong><?php echo $totalClosed; ?></strong> closed tickets archived.</span>
    <button
        id="viewClosedBtn"
        class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#closedTicketsModal">
        📁 View Closed Tickets
    </button>
</div>
<?php endif; ?>

<!-- OPEN TICKETS TABLE (UNCHANGED) -->
<div class="card shadow-sm">
<div class="card-body p-0">
<table class="table table-hover mb-0">
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
<?php if (!$tickets): ?>
<tr><td colspan="5" class="text-center">No tickets found</td></tr>
<?php else: foreach ($tickets as $t): ?>
<tr>
<td><a href="ticket.php?id=<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['ticket_number']); ?></a></td>
<td><?php echo htmlspecialchars($t['sender_email']); ?></td>
<td><?php echo htmlspecialchars($t['subject']); ?></td>
<td><?php echo htmlspecialchars($t['status']); ?></td>
<td><?php echo date('M j, Y g:i A', strtotime($t['created_at'])); ?></td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
</div>

<!-- CLOSED TICKETS MODAL -->
<div class="modal fade" id="closedTicketsModal" tabindex="-1">
<div class="modal-dialog modal-xl modal-dialog-scrollable">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">📁 Closed Tickets (Archive)</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body" id="closedTicketsContainer">

<!-- INITIAL CONTENT (PAGE 1) -->
<table class="table table-hover">
<thead class="table-light">
<tr>
<th>Ticket #</th>
<th>From</th>
<th>Subject</th>
<th>Created</th>
</tr>
</thead>
<tbody>
<?php foreach ($closedTickets as $ct): ?>
<tr>
<td><a href="ticket.php?id=<?php echo $ct['id']; ?>"><?php echo htmlspecialchars($ct['ticket_number']); ?></a></td>
<td><?php echo htmlspecialchars($ct['sender_email']); ?></td>
<td><?php echo htmlspecialchars($ct['subject']); ?></td>
<td><?php echo date('M j, Y g:i A', strtotime($ct['created_at'])); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if ($closedPages > 1): ?>
<nav class="text-center">
<?php for ($i = 1; $i <= $closedPages; $i++): ?>
<button class="btn btn-sm btn-outline-primary mx-1"
onclick="loadClosedTickets(<?php echo $i; ?>)">
<?php echo $i; ?>
</button>
<?php endfor; ?>
</nav>
<?php endif; ?>

</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

</div>
</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- ✅ AJAX FIX (ADDITION ONLY) -->
<script>
function loadClosedTickets(page) {
    fetch('ajax_closed_tickets.php?page=' + page)
        .then(res => res.text())
        .then(html => {
            document.getElementById('closedTicketsContainer').innerHTML = html;
        });
}
</script>

</body>
</html>