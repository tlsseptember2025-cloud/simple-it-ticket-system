<?php
require('../auth.php');
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

// 🔒 Check if employee still has assets
$stmt = $pdo->prepare("
    SELECT a.asset_tag 
    FROM asset_assignments aa
    JOIN assets a ON aa.asset_id = a.id
    WHERE aa.employee_id=? AND aa.returned_at IS NULL
");
$stmt->execute([$id]);
$assets = $stmt->fetchAll();

if(count($assets) > 0){

   $_SESSION['error'] = "Cannot deactivate employee. They still have assigned assets!";
    header("Location: employees.php");
    exit;
}

// ✅ Safe to deactivate
$pdo->prepare("
    UPDATE employees 
    SET status='terminated' 
    WHERE id=?
")->execute([$id]);

$_SESSION['success'] = "Employee deactivated successfully";
header("Location: employees.php");
exit;