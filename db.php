<?php
$host     = 'localhost';
$dbname   = 'campuscare_db';
$username = 'root';      // XAMPP default
$password = '';          // XAMPP default — no password

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed.']));
}
?>