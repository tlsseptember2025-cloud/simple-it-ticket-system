<?php
require 'auth.php';
require '../config/db.php';

/* ================= FLASH MESSAGE ================= */

if (isset($_GET['fetched']) && empty($_SESSION['fetch_success'])) {
    $_SESSION['fetch_success'] = 'Emails fetched successfully';
}

$limit = 5;

/* ===== SEARCH FILTER ===== */
$searchEmail = isset($_GET['search']) ? trim($_GET['search']) : '';

/* ===== CURRENT PAGE ===== */
$page = isset($_GET['page']) && is_numeric($_GET['page'])
    ? (int) $_GET['page']
    : 1;

$page = max(1, $page);

/* ===== COUNT OPEN TICKETS ===== */
if ($searchEmail !== '') {

    $totalStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM tickets
        WHERE sender_email LIKE :search
    ");

    $totalStmt->bindValue(':search', "%$searchEmail%");

} else {

    $totalStmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM tickets
        WHERE status != 'Closed'
    ");
}

$totalStmt->execute();
$totalTickets = (int) $totalStmt->fetchColumn(); // ✅ FIXED (removed duplicate)

/* ===== TOTAL PAGES ===== */
$totalPages = ($totalTickets > 0)
    ? (int) ceil($totalTickets / $limit)
    : 1;

/* 🔒 SAFETY */
if ($page > $totalPages) {
    $page = $totalPages;
}

/* ===== OFFSET ===== */
$offset = ($page - 1) * $limit;

/* ===== FETCH OPEN TICKETS ===== */
if ($searchEmail !== '') {

    $stmt = $pdo->prepare("
        SELECT 
            t.id, 
            t.ticket_number, 
            t.sender_email, 
            t.subject,
            t.category,
            t.status, 
            t.created_at,
            (
                SELECT COUNT(*) 
                FROM updates u 
                WHERE u.ticket_id = t.id 
                  AND u.sent_to_user = 0
            ) AS user_updates
        FROM tickets t
        WHERE sender_email LIKE :search
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindValue(':search', "%$searchEmail%");

} else {

    $stmt = $pdo->prepare("
        SELECT 
            t.id, 
            t.ticket_number, 
            t.sender_email, 
            t.subject,
            t.category, 
            t.status, 
            t.created_at,
            (
                SELECT COUNT(*) 
                FROM updates u 
                WHERE u.ticket_id = t.id 
                  AND u.sent_to_user = 0
            ) AS user_updates
        FROM tickets t
        WHERE status != 'Closed'
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");
}

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll();

/* ================= CATEGORY COUNTS ================= */

$categoryStmt = $pdo->query("
    SELECT 
        SUM(status = 'Open') AS open_count,
        SUM(status = 'In Progress') AS in_progress_count,
        SUM(status = 'Waiting') AS waiting_count,
        SUM(status = 'Closed') AS closed_count
    FROM tickets
");

$categoryCounts = $categoryStmt->fetch(PDO::FETCH_ASSOC);

$openCount        = (int) ($categoryCounts['open_count'] ?? 0);
$inProgressCount  = (int) ($categoryCounts['in_progress_count'] ?? 0);
$waitingCount     = (int) ($categoryCounts['waiting_count'] ?? 0);
$closedCount      = (int) ($categoryCounts['closed_count'] ?? 0);

/* ================= DASHBOARD STATS ================= */

$firstStmt = $pdo->query("
    SELECT ticket_number, created_at 
    FROM tickets 
    ORDER BY created_at ASC 
    LIMIT 1
");
$firstTicket = $firstStmt->fetch();

$lastStmt = $pdo->query("
    SELECT ticket_number, created_at 
    FROM tickets 
    ORDER BY created_at DESC 
    LIMIT 1
");
$lastTicket = $lastStmt->fetch();

/* ===== Monthly Average ===== */
$monthStart = date('Y-m-01');
$monthEnd   = date('Y-m-t');

$monthStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tickets 
    WHERE DATE(created_at) BETWEEN ? AND ?
");
$monthStmt->execute([$monthStart, $monthEnd]);
$monthTotal = (int)$monthStmt->fetchColumn();

$daysPassed = (int)date('j');
$avgPerDay = ($daysPassed > 0)
    ? (int) ceil($monthTotal / $daysPassed)
    : 0;

/* ================= CLOSED TICKETS (MODAL) ================= */

$closedLimit = 10;
$closedPage  = isset($_GET['closed_page']) && is_numeric($_GET['closed_page'])
    ? (int)$_GET['closed_page']
    : 1;

$closedPage = max(1, $closedPage);
$closedOffset = ($closedPage - 1) * $closedLimit;

/* ===== Count Closed ===== */

$whereClosed = "status = 'Closed'";
$paramsClosed = [];

if (!empty($_GET['closed_category'])) {
    $whereClosed .= " AND category = :closed_category";
    $paramsClosed[':closed_category'] = $_GET['closed_category'];
}

$closedCountStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM tickets 
    WHERE $whereClosed
");

foreach ($paramsClosed as $key => $value) {
    $closedCountStmt->bindValue($key, $value);
}

$closedCountStmt->execute();
$totalClosed = (int) $closedCountStmt->fetchColumn();

$closedPages = (int) ceil($totalClosed / $closedLimit);

/* ===== Fetch Closed ===== */

$closedStmt = $pdo->prepare("
    SELECT id, ticket_number, sender_email, subject, category,status_updated_at
    FROM tickets
    WHERE $whereClosed
    ORDER BY status_updated_at DESC
    LIMIT :limit OFFSET :offset
");

foreach ($paramsClosed as $key => $value) {
    $closedStmt->bindValue($key, $value);
}

$closedStmt->bindValue(':limit', $closedLimit, PDO::PARAM_INT);
$closedStmt->bindValue(':offset', $closedOffset, PDO::PARAM_INT);

$closedStmt->execute();
$closedTickets = $closedStmt->fetchAll();

/* ================= TOTAL & COMPLETION ================= */

$totalTicketsAll = $openCount + $inProgressCount + $waitingCount + $closedCount;

$completionRate = ($totalTicketsAll > 0)
    ? round(($closedCount / $totalTicketsAll) * 100)
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>IT Support Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* Closed Tickets Modal Compact Mode */
#closedTicketsModal table {
    font-size: 0.72rem;
}

#closedTicketsModal th,
#closedTicketsModal td {
    padding: 4px 6px;
    vertical-align: middle;
}

#closedTicketsModal thead th {
    font-size: 0.75rem;
}

#closedTicketsModal .pagination .page-link,
#closedTicketsModal button {
    font-size: 0.75rem;
    padding: 4px 8px;
}
</style>


</head>

<body class="bg-light">

<div class="container py-4">
    <nav class="navbar navbar-expand-lg bg-white shadow-sm rounded mb-4 px-3"> 
        <a class="navbar-brand d-flex align-items-center fw-semibold" href="dashboard.php"> 
            <img src="../public/assets/company-logo.png" alt="Company Logo" height="32" class="me-2" > IT Support 
        </a> 
        <div class="ms-auto d-flex align-items-center gap-3"> 
            <a href="reports.php" class="btn btn-sm btn-outline-secondary"> 📊 Reports </a> 
            <a href="run_fetch.php" class="btn btn-sm btn-outline-primary"> 📥 Fetch Emails </a> 
            <span class="text-muted"> <?php echo htmlspecialchars($_SESSION['admin_username']); ?> </span> 
            <a href="logout.php" class="text-decoration-none"> Logout </a> 
        </div> 
    </nav>
    <?php if (!empty($_SESSION['fetch_success'])): ?> 
        <div id="fetchAlert" class="alert alert-success alert-dismissible fade show"> 
            <?php echo $_SESSION['fetch_success']; unset($_SESSION['fetch_success']); ?> 
        </div> <?php endif; ?>



<h4>Ticket Dashboard</h4>

<!-- SEARCH BOX -->
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-center">

            <div class="col-md-4">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by staff email..."
                    value="<?php echo htmlspecialchars($searchEmail); ?>"
                >
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    🔍 Search
                </button>
                <a href="dashboard.php" class="btn btn-secondary btn-sm">
                    Reset
                </a>
            </div>

        </form>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap justify-content-between align-items-center small">

        <div class="me-4 mb-2">
            🟢 <strong>First:</strong>
            <?php echo $firstTicket['ticket_number'] ?? '-'; ?>
        </div>

        <div class="me-4 mb-2">
            🚀 <strong>Last:</strong>
            <?php echo $lastTicket['ticket_number'] ?? '-'; ?>
        </div>

        <div class="me-4 mb-2">
            📊 <strong>Avg (<?php echo date('F Y'); ?>):</strong>
            <?php echo $avgPerDay; ?>/day
        </div>

        <div class="me-4 mb-2">
            📦 <strong>Total:</strong>
            <?php echo $totalTicketsAll; ?>
        </div>

        <div class="mb-2">
            ✅ <strong>Completion:</strong>
            <?php echo $completionRate; ?>%
        </div>

    </div>
</div>

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

<!-- OPEN TICKETS TABLE (UNCHANGED) -->
<div class="card shadow-sm">
<div class="card-body p-0">
<table class="table table-hover mb-0">
<thead class="table-light">
<tr>
<th>Ticket #</th>
<th>From</th>
<th>Subject</th>
<th>Category</th>
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
<td><?php echo htmlspecialchars($t['category'] ?? '-'); ?></td>
<td>

<?php
$status = $t['status'];
$hasUpdate = ($t['user_updates'] > 0);

/* ===== NORMAL STATUS COLORS ===== */
switch ($status) {
    case 'Open':
        $badgeClass = 'primary';      // Blue
        break;

    case 'Waiting':
        $badgeClass = 'warning';      // Orange
        break;

    case 'In Progress':
        $badgeClass = 'info';         // Light Blue
        break;

    case 'Closed':
        $badgeClass = 'success';      // Green
        break;

    default:
        $badgeClass = 'secondary';
}

/* ===== IF NEW UPDATE → FORCE RED ===== */
if ($hasUpdate) {
    $badgeClass = 'danger';
}
?>

<span class="badge bg-<?php echo $badgeClass; ?>">
    <?php echo htmlspecialchars($status); ?>
    <?php if ($hasUpdate): ?>
        (New)
    <?php endif; ?>
</span>

</td>
<td><?php echo date('M j, Y g:i A', strtotime($t['created_at'])); ?></td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>

<?php if ($totalPages > 1): ?>
<nav class="mt-4">
    <ul class="pagination justify-content-center">

        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="?page=<?php echo max(1, $page - 1); ?>&search=<?php echo urlencode($searchEmail); ?>">
                Previous
            </a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                <a class="page-link"
                   href="?page=<?php echo $i; ?>&search=<?php echo urlencode($searchEmail); ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
            <a class="page-link"
               href="?page=<?php echo min($totalPages, $page + 1); ?>&search=<?php echo urlencode($searchEmail); ?>">
                Next
            </a>
        </li>

    </ul>
</nav>
<?php endif; ?>


<br>
<a href="backup.php?backup=1" class="btn btn-sm btn-outline-primary"> 📥 Backup Database </a>

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

<form method="GET" class="mb-3 d-flex gap-2">

    <input type="hidden" name="closed_page" value="1">

    <select name="closed_category" class="form-select">
        <option value="">All Categories</option>
        <option value="Email">Email</option>
        <option value="Hardware">Hardware</option>
        <option value="Software">Software</option>
        <option value="Network">Network</option>
        <option value="ERP / ODOO">ERP / ODOO</option>
        <option value="security">Security</option>
        <option value="other">Other</option>
    </select>

    <button class="btn btn-primary btn-sm">Search</button>

</form>


<!-- INITIAL CONTENT (PAGE 1) -->
<table class="table table-hover">
<thead class="table-light">
<tr>
<th>Ticket #</th>
<th>From</th>
<th>Subject</th>
<th>Category</th>
<th>Closed Date</th>
</tr>
</thead>
<tbody>
<?php foreach ($closedTickets as $ct): ?>
<tr>
<td><a href="ticket.php?id=<?php echo $ct['id']; ?>"><?php echo htmlspecialchars($ct['ticket_number']); ?></a></td>
<td><?php echo htmlspecialchars($ct['sender_email']); ?></td>
<td><?php echo htmlspecialchars($ct['subject']); ?></td>
<td><?php echo htmlspecialchars($ct['category'] ?? '-'); ?></td>
<td><?php echo date('M j, Y g:i A', strtotime($ct['status_updated_at'])); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if ($closedPages > 1): ?>
<nav class="text-center">
<?php for ($i = 1; $i <= $closedPages; $i++): ?>
<a href="?closed_page=<?php echo $i; ?>&search=<?php echo urlencode($searchEmail); ?>"
   class="btn btn-sm btn-outline-primary mx-1
   <?php echo ($i == $closedPage) ? 'active' : ''; ?>">
    <?php echo $i; ?>
</a>
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
document.addEventListener('DOMContentLoaded', function () {
    const alert = document.getElementById('fetchAlert');
    if (!alert) return;

    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 3000);
});
</script>

<script>
function loadClosedTickets(page) {
    const category = document.querySelector('[name="closed_category"]').value;

    fetch('ajax_closed_tickets.php?page=' + page + '&closed_category=' + category)
        .then(res => res.text())
        .then(html => {
            document.getElementById('closedTicketsContainer').innerHTML = html;
        });
}
</script>

<script>
let lastChange = 0;

// First load — get initial timestamp
fetch('check_updates.php')
    .then(res => res.json())
    .then(data => {
        lastChange = data.last_change;
    });

// Check every 30 seconds
setInterval(() => {
    fetch('check_updates.php')
        .then(res => res.json())
        .then(data => {
            if (data.last_change > lastChange) {
                location.reload();
            }
        });
}, 30000);
</script>

<?php if (isset($_GET['closed_page'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function(){
    var modal = new bootstrap.Modal(document.getElementById('closedTicketsModal'));
    modal.show();
});
</script>
<?php endif; ?>

</body>
</html>