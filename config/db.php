<?php

$host = 'localhost';   // ← safer than 127.0.0.1 in XAMPP
$db   = 'it_ticket_system';
$user = 'root';
$pass = '';   // ← exactly what you tested
$port = 3306;                   // ← match my.ini

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}