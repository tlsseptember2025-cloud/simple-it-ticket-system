<?php
require('../auth.php');
require('../../config/db.php');

$id = (int) ($_GET['id'] ?? 0);

// update status instead of deleting
$pdo->prepare("
    UPDATE employees 
    SET status='terminated' 
    WHERE id=?
")->execute([$id]);

header("Location: employees.php");
exit;