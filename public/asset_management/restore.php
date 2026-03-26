<?php
require('../auth.php');
require('../../config/db.php');

$id = (int) $_GET['id'];

$pdo->prepare("UPDATE assets SET status='available' WHERE id=?")
    ->execute([$id]);

header("Location: retired.php");