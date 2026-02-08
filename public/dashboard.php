<?php
require 'auth.php';
require '../config/db.php';

/* ================= FLASH MESSAGE ================= */

// Normalize old GET-based success into session
if (isset($_GET['fetched']) && empty($_SESSION['fetch_success'])) {
    $_SESSION['fetch_success'] = 'Emails fetched successfully';
}

/* ================= PAGINATION ================= */

$limit = 5; // tickets per page
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page  = max($page, 1);

$offset = ($page - 1) * $limit;


$offset = ($page - 1) * $limit;

/* ===== COUNT TOTAL TICKETS ===== */
$totalStmt = $pdo->query("SELECT COUNT(*) FROM tickets where status != 'Closed'");
$totalTickets = (int)$totalStmt->fetchColumn();
$totalPages   = (int)ceil($totalTickets / $limit);

/* ===== FETCH PAGINATED TICKETS ===== */
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

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm rounded mb-4 px-3">
        <a class="navbar-brand d-flex align-items-center fw-semibold" href="dashboard.php">
            <img
                src="../public/assets/company-logo.png"
                alt="Company Logo"
                height="32"
                class="me-2"
            >
            IT Support
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">

            <a href="reports.php" class="btn btn-sm btn-outline-secondary">
                📊 Reports
            </a>

            <a href="export_monthly_pdf.php" class="btn btn-sm btn-outline-success">
                📊 Monthly Report
            </a>

            <a href="run_fetch.php" class="btn btn-sm btn-outline-primary">
                📥 Fetch Emails
            </a>

            <span class="text-muted">
                <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </span>

            <a href="logout.php" class="text-decoration-none">
                Logout
            </a>
        </div>
    </nav>

    <!-- Success Alert -->
    <?php if (!empty($_SESSION['fetch_success'])): ?>
        <div id="fetchAlert" class="alert alert-success alert-dismissible fade show">
            ✅ <?php echo htmlspecialchars($_SESSION['fetch_success']); ?>
            <?php unset($_SESSION['fetch_success']); ?>
        </div>
    <?php endif; ?>

    <!-- Page Title -->
    <div class="mb-3">
        <h4 class="mb-0">Ticket Dashboard</h4>
        <small class="text-muted">
            Showing <?php echo count($tickets); ?> of <?php echo $totalTickets; ?> tickets
        </small>
    </div>

    <?php if ($totalClosed > 0): ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <span>
            ℹ️ There are <strong><?php echo $totalClosed; ?></strong> closed tickets archived.
        </span>
        <button
            class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#closedTicketsModal"
        >
            📁 View Closed Tickets
        </button>
    </div>
<?php endif; ?>

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

                <?php if (empty($tickets)): ?>
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
                            default => 'secondary'
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
                                <?php echo date('l, M j, Y \a\t g:i A', strtotime($ticket['created_at'])); ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>

        </div>

    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">

                <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>

            </ul>
        </nav>
    <?php endif; ?>

    <br>

     <a href="backup.php?backup=1" class="btn btn-sm btn-outline-primary">
    📥 Backup Database
    </a>

</div>

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

<div class="modal fade" id="closedTicketsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">📁 Closed Tickets (Archive)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <?php if (empty($closedTickets)): ?>
                    <p class="text-muted text-center">No closed tickets found.</p>
                <?php else: ?>
                    <table class="table table-hover align-middle">
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
                                    <td>
                                        <a href="ticket.php?id=<?php echo $ct['id']; ?>">
                                            <?php echo htmlspecialchars($ct['ticket_number']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($ct['sender_email']); ?></td>
                                    <td><?php echo htmlspecialchars($ct['subject']); ?></td>
                                    <td class="text-muted small">
                                        <?php echo date('M j, Y g:i A', strtotime($ct['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <!-- Pagination -->
                <?php if ($closedPages > 1): ?>
                    <nav>
                        <ul class="pagination justify-content-center">

                            <?php for ($i = 1; $i <= $closedPages; $i++): ?>
                                <li class="page-item <?php echo $i === $closedPage ? 'active' : ''; ?>">
                                    <a
                                        class="page-link"
                                        href="?closed_page=<?php echo $i; ?>"
                                        data-bs-dismiss="modal"
                                    >
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                        </ul>
                    </nav>
                <?php endif; ?>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
