<?php require('../auth.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>IT Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">IT Assets</a>
        <div>
            <a class="btn btn-light btn-sm" href="index.php">Assets</a>
            <a class="btn btn-light btn-sm" href="retired.php">Retired</a>
            <a class="btn btn-light btn-sm" href="employees.php">Employees</a>
            <a class="btn btn-light btn-sm" href="assign.php">Assign</a>
            <a class="btn btn-light btn-sm" href="return.php">Return</a>
            <a class="btn btn-light btn-sm" href="history.php">History</a>
            <a class="btn btn-light btn-sm" href="../dashboard.php">IT Support</a>
            <a class="btn btn-danger btn-sm" href="../logout.php">Logout</a>
        </div>
    </div>
</nav>

<?php if(isset($_SESSION['error'])){ ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php } ?>

<?php if(isset($_SESSION['success'])){ ?>
    <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
<?php } ?>

<div class="container mt-4">