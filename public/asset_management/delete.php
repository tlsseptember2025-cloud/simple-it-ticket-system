<?php
require('../auth.php');
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

// 🔒 Check if asset is still assigned
$stmt = $pdo->prepare("
    SELECT id FROM asset_assignments
    WHERE asset_id=? AND returned_at IS NULL
");
$stmt->execute([$id]);

if($stmt->rowCount() > 0){
    $_SESSION['error'] = "Cannot retire asset. It is currently assigned!";
    header("Location: index.php");
    exit;
}

// ✅ Safe to retire
$pdo->prepare("UPDATE assets SET status='retired' WHERE id=?")
    ->execute([$id]);

$_SESSION['success'] = "Asset retired successfully";
header("Location: index.php");
exit;