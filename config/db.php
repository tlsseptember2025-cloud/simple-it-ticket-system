<?php

$host = '127.0.0.1';
$db   = 'it_ticket_system';   // MUST MATCH phpMyAdmin
$user = 'root';
$pass = '';
$port = 3307;                 // IMPORTANT (your MySQL is on 3307)

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed');
}
