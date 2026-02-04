<?php
session_start();
require '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | IT Support</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh;">

<div class="card shadow-sm p-4" style="width:100%; max-width:420px;">
    
    <div class="text-center mb-4">
        <h3 class="fw-bold">🛠 IT Support</h3>
        <p class="text-muted mb-0">Admin Login</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger py-2">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input
                type="text"
                name="username"
                class="form-control"
                required
                autofocus
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input
                type="password"
                name="password"
                class="form-control"
                required
            >
        </div>

        <button type="submit" class="btn btn-primary w-100">
            🔐 Login
        </button>

    </form>

    <div class="text-center mt-4">
     <div class="text-muted small">
        IT Support System
    </div>
    <img
        src="assets/company-logo.png"
        alt="Company Logo"
        style="max-height:150px; opacity:0.85;"
        class="mb-2"
    >
    </div>


</div>

</body>
</html>