<?php
require 'auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION['admin_username']; ?></h2>

<p>You are successfully logged in.</p>

<a href="logout.php">Logout</a>

</body>
</html>
