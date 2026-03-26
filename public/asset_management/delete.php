<?php
require('../auth.php');
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

// mark as retired instead of deleting
$stmt = $pdo->prepare("UPDATE assets SET status='retired' WHERE id=?");
$stmt->execute([$id]);

header("Location: index.php");
exit;