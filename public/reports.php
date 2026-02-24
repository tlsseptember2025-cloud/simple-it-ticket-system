<?php
require 'auth.php';
require '../config/db.php';


/* ================= CATEGORY REPORT (ALL DATES) ================= */

$categoryAllStmt = $pdo->query("
    SELECT 
        category,
        COUNT(*) AS total,
        SUM(status = 'Open') AS open_count,
        SUM(status = 'In Progress') AS in_progress_count,
        SUM(status = 'Waiting') AS waiting_count,
        SUM(status = 'Closed') AS closed_count
    FROM tickets
    GROUP BY category
    ORDER BY total DESC
");

$categoryAllReport = $categoryAllStmt->fetchAll(PDO::FETCH_ASSOC);

/* ===== Ticket Summary ===== */

$stmt = $pdo->query("
    SELECT
        COUNT(*) AS total,
        SUM(status = 'Open') AS open,
        SUM(status = 'In Progress') AS in_progress,
        SUM(status = 'Waiting') AS waiting,
        SUM(status = 'Closed') AS closed
    FROM tickets
");

$summary = $stmt->fetch(PDO::FETCH_ASSOC);

/* ===== Date Range Report (DETAILED TICKETS) ===== */

$rangeTickets = [];
$rangeReport = [];

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {

    $where = "DATE(created_at) BETWEEN :from AND :to";
    $params = [
        ':from' => $_GET['from_date'],
        ':to'   => $_GET['to_date']
    ];

    if (!empty($_GET['category_filter'])) {
        $where .= " AND category = :category";
        $params[':category'] = $_GET['category_filter'];
    }

    $stmt = $pdo->prepare("
        SELECT id, ticket_number, sender_email, subject, status, category, created_at
        FROM tickets
        WHERE $where
        ORDER BY category ASC, created_at DESC
    ");

    $stmt->execute($params);

    $rangeTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ================= GENERATE CATEGORY PDF ================= */

/* ================= CATEGORY REPORT (DETAIL MODE) ================= */

$rangeReport = [];

if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {

    $from = $_GET['from_date'];
    $to   = $_GET['to_date'];
    $categoryFilter = $_GET['category_filter'] ?? '';

    $sql = "
        SELECT ticket_number, sender_email, subject, category, status, created_at
        FROM tickets
        WHERE DATE(created_at) BETWEEN :from AND :to
    ";

    if (!empty($categoryFilter)) {
        $sql .= " AND category = :category ";
    }

    $sql .= " ORDER BY created_at DESC";

    $rangeStmt = $pdo->prepare($sql);

    $rangeStmt->bindValue(':from', $from);
    $rangeStmt->bindValue(':to', $to);

    if (!empty($categoryFilter)) {
        $rangeStmt->bindValue(':category', $categoryFilter);
    }

    $rangeStmt->execute();

    $rangeReport = $rangeStmt->fetchAll(PDO::FETCH_ASSOC);
}


/* ===== STEP C: Tickets by Staff ===== */

$stmt = $pdo->query("
    SELECT sender_email, COUNT(*) AS total
    FROM tickets
    GROUP BY sender_email
    ORDER BY total DESC
");

$staffStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ===== STEP D: Resolution Time ===== */

$stmt = $pdo->query("
    SELECT
        AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) AS avg_minutes
    FROM tickets
    WHERE status = 'Closed'
");

$avgMinutes = (int) $stmt->fetchColumn();

$avgResolutionText = 'N/A';

if ($avgMinutes > 0) {
    $hours   = intdiv($avgMinutes, 60);
    $minutes = $avgMinutes % 60;

    if ($hours > 0) {
        $avgResolutionText = "{$hours}h {$minutes}m";
    } else {
        $avgResolutionText = "{$minutes}m";
    }
}

if (
    isset($_GET['generate_category_pdf']) &&
    !empty($_GET['from_date']) &&
    !empty($_GET['to_date'])
) {

    require __DIR__ . '/generate_category_pdf.php';

    $fileName = generateCategoryPDF(
        $_GET['from_date'],
        $_GET['to_date'],
        $_GET['category_filter'] ?? '',
        $pdo
    );

    $query = http_build_query([
    'from_date' => $_GET['from_date'],
    'to_date' => $_GET['to_date'],
    'category_filter' => $_GET['category_filter'] ?? '',
    'generated_category_pdf' => $fileName
]);

header("Location: reports.php?$query");
exit;

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar bg-white shadow-sm rounded mb-4 px-3">
    <a class="navbar-brand d-flex align-items-center fw-semibold" href="dashboard.php">
        <img
            src="../public/assets/company-logo.png"
            alt="Company Logo"
            height="32"
            class="me-2"
        >
        IT Support
    </a>

    <div class="ms-auto">
        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">
            ← Back to Dashboard
        </a>
    </div>
</nav>

<div class="container py-4">

    <h4 class="mb-4">📊 Ticket Summary Report</h4>

<div class="row g-3">

    <div class="col-md-2">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <small class="text-muted">Total</small>
                <h3><?= $summary['total'] ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <small class="text-muted">Open</small>
                <h3><?= $summary['open'] ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <small class="text-muted">In Progress</small>
                <h3><?= $summary['in_progress'] ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <small class="text-muted">Waiting</small>
                <h3><?= $summary['waiting'] ?></h3>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <small class="text-muted">Closed</small>
                <h3><?= $summary['closed'] ?></h3>
            </div>
        </div>
    </div>

</div>

<hr class="my-4">

<h5 class="mb-3">👤 Tickets by Staff</h5>

<table class="table table-bordered table-sm col-md-6">
    <thead class="table-light">
        <tr>
            <th>Staff Email</th>
            <th class="text-center">Total Tickets</th>
        </tr>
    </thead>
    <tbody>

        <?php if (empty($staffStats)): ?>
            <tr>
                <td colspan="2" class="text-center text-muted">
                    No data available
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($staffStats as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['sender_email']); ?></td>
                    <td class="text-center fw-semibold">
                        <?php echo $row['total']; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

    </tbody>
</table>

<?php if (!empty($_GET['category_email_sent'])): ?>
<div class="alert alert-success">
    📧 Category report emailed successfully.
</div>
<?php endif; ?>


<hr class="my-4">
<h5 class="mt-4">📅 Category Report (Specific Date Range)</h5>

<form method="GET" class="row g-2 mb-3">

    <div class="col-md-3">
        <input type="date" name="from_date" class="form-control"
               value="<?= $_GET['from_date'] ?? '' ?>" required>
    </div>

    <div class="col-md-3">
        <input type="date" name="to_date" class="form-control"
               value="<?= $_GET['to_date'] ?? '' ?>" required>
    </div>

    <div class="col-md-3">
        <select name="category_filter" class="form-select">
            <option value="">All Categories</option>
            <option value="Email">Email</option>
            <option value="Hardware">Hardware</option>
            <option value="Software">Software</option>
            <option value="Network">Network</option>
            <option value="ERP / Odoo">ERP / Odoo</option>
            <option value="Security">Security</option>
            <option value="other">Other</option>

        </select>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary btn-sm">Generate</button>
    </div>

</form>


<?php if (!empty($_GET['generated_category_pdf'])): ?>
<div class="alert alert-success d-flex justify-content-between align-items-center">
    <span>📄 Category report generated.</span>
    <div>
        <a
          href="../uploads/reports/<?= urlencode($_GET['generated_category_pdf']) ?>"
          class="btn btn-primary btn-sm"
          download
        >
          Download
        </a>

        <a
          href="send_category_report.php?<?= http_build_query([
              'file' => $_GET['generated_category_pdf'],
              'from_date' => $_GET['from_date'] ?? '',
              'to_date' => $_GET['to_date'] ?? '',
              'category_filter' => $_GET['category_filter'] ?? ''
          ]) ?>"
          class="btn btn-sm btn-danger"
        >
          Send by Email
        </a>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($rangeTickets)): ?>

<hr class="my-4">
<h5 class="mt-4">📊 Tickets by Category (Detailed Range)</h5>

<div class="card shadow-sm">
<div class="card-body">

<?php
$currentCategory = null;

foreach ($rangeTickets as $ticket):

    if ($ticket['category'] !== $currentCategory):

        if ($currentCategory !== null) {
            echo "</tbody></table><br>";
        }

        $currentCategory = $ticket['category'] ?? 'Uncategorized';

        echo "<h6 class='mt-3'>📁 Category: " . htmlspecialchars($currentCategory) . "</h6>";
        echo "<table class='table table-sm table-bordered'>";
        echo "<thead class='table-light'>
                <tr>
                    <th>Ticket #</th>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
              </thead><tbody>";
    endif;
?>

<tr>
<td>
    <a href="ticket.php?id=<?php echo $ticket['id']; ?>">
        <?php echo htmlspecialchars($ticket['ticket_number']); ?>
    </a>
</td>
<td><?php echo htmlspecialchars($ticket['sender_email']); ?></td>
<td><?php echo htmlspecialchars($ticket['subject']); ?></td>
<td><?php echo htmlspecialchars($ticket['status']); ?></td>
<td><?php echo date('M j, Y g:i A', strtotime($ticket['created_at'])); ?></td>
</tr>

<?php endforeach; ?>

</tbody>
</table>

<?php if (!empty($rangeReport)): ?>

<div class="alert alert-success d-flex justify-content-between align-items-center">
    📄 Category report generated

    <div>
        <a href="?from_date=<?= $_GET['from_date'] ?>
            &to_date=<?= $_GET['to_date'] ?>
            &category_filter=<?= $_GET['category_filter'] ?? '' ?>
            &generate_category_pdf=1"
           class="btn btn-primary btn-sm">
            Download
        </a>

        <a href="?from_date=<?= $_GET['from_date'] ?>
            &to_date=<?= $_GET['to_date'] ?>
            &category_filter=<?= $_GET['category_filter'] ?? '' ?>
            &generate_category_pdf=1
            &send_email=1"
           class="btn btn-danger btn-sm">
            Send by Email
        </a>
    </div>
</div>

<?php endif; ?>


</div>
</div>

<?php endif; ?>

<hr class="my-4">

<h5 class="mb-3">📅 Monthly Ticket Report</h5>

<form method="get" action="export_monthly_pdf.php" class="row g-2 align-items-end">

    <!-- Month -->
    <div class="col-md-3">
        <label class="form-label">Month</label>
        <select name="month" class="form-select" required>
            <?php
            for ($m = 1; $m <= 12; $m++) {
                $monthName = date('F', mktime(0, 0, 0, $m, 1));
                $selected = ($m == date('n')) ? 'selected' : '';
                echo "<option value=\"$m\" $selected>$monthName</option>";
            }
            ?>
        </select>
    </div>

    <!-- Year -->
    <div class="col-md-3">
        <label class="form-label">Year</label>
        <select name="year" class="form-select" required>
            <?php
            $currentYear = date('Y');
            for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                $selected = ($y == $currentYear) ? 'selected' : '';
                echo "<option value=\"$y\" $selected>$y</option>";
            }
            ?>
        </select>
    </div>

    <!-- Button -->
    <div class="col-md-3">
        <button type="submit" class="btn btn-danger w-100">
            📄 Generate Monthly PDF
        </button>
    </div>

</form>

<hr class="my-4">

<a href="export_excel.php" class="btn btn-success">
    ⬇ Export Tickets to Excel
</a>

<a href="export_pdf.php" class="btn btn-danger ms-2">
    📄 Export PDF
</a>

</div>

</body>
</html>