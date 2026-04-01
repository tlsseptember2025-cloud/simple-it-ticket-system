<?php
require('../auth.php');
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

// reactivate employee
$pdo->prepare("
    UPDATE employees 
    SET status='active' 
    WHERE id=?
")->execute([$id]);

header("Location: employees.php");
exit;