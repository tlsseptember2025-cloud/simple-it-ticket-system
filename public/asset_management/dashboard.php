<?php
require('../../config/db.php');
include('header.php');

// stats
$total_assets = $pdo->query("SELECT COUNT(*) FROM assets")->fetchColumn();
$assigned = $pdo->query("SELECT COUNT(*) FROM assets WHERE status='assigned'")->fetchColumn();
$available = $pdo->query("SELECT COUNT(*) FROM assets WHERE status='available'")->fetchColumn();
$employees = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
?>

<h2>Dashboard</h2>

<div class="row">
    <div class="col-md-3">
        <div class="card p-3 bg-primary text-white">
            Total Assets: <?= $total_assets ?>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-success text-white">
            Available: <?= $available ?>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-warning text-dark">
            Assigned: <?= $assigned ?>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-dark text-white">
            Employees: <?= $employees ?>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>