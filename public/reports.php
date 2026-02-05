<?php
require 'auth.php';
require '../config/db.php';

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

/* ===== Date Range Report ===== */

$dateStats = [];

if (!empty($_GET['from']) && !empty($_GET['to'])) {

    $stmt = $pdo->prepare("
        SELECT status, COUNT(*) AS total
        FROM tickets
        WHERE created_at BETWEEN ? AND ?
        GROUP BY status
    ");

    $stmt->execute([
        $_GET['from'] . ' 00:00:00',
        $_GET['to'] . ' 23:59:59'
    ]);

    $dateStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

/* ===== STEP D: Resolution Time (Minutes Precision) ===== */

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

<h5 class="mb-3">📅 Tickets by Date Range</h5>

<form method="get" class="row g-2 mb-4">

    <div class="col-md-3">
        <label class="form-label">From</label>
        <input
            type="date"
            name="from"
            class="form-control"
            value="<?php echo $_GET['from'] ?? ''; ?>"
            required
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">To</label>
        <input
            type="date"
            name="to"
            class="form-control"
            value="<?php echo $_GET['to'] ?? ''; ?>"
            required
        >
    </div>

    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary">
            Generate
        </button>
    </div>

</form>

<?php if (!empty($dateStats)): ?>

<div class="card shadow-sm col-md-4">
    <div class="card-body">
        <h6 class="card-title">Results</h6>

        <ul class="list-group list-group-flush">
            <?php foreach ($dateStats as $row): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <?php echo htmlspecialchars($row['status']); ?>
                    <span class="badge bg-secondary">
                        <?php echo $row['total']; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

    </div>
</div>

<?php endif; ?>

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

<hr class="my-4">

<h5 class="mb-3">⏱ Average Resolution Time</h5>

<div class="alert alert-info col-md-4">
    <strong><?php echo $avgResolutionText; ?></strong>
    <br>
    <small class="text-muted">
        Average time to close a ticket
    </small>
</div>

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