<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$isLoggedIn = (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true)
    || (isset($_SESSION['dLoggedIn']) && $_SESSION['dLoggedIn'] === true)
    || (isset($_SESSION['cLoggedIn']) && $_SESSION['cLoggedIn'] === true)
    || (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true);

if (!$isLoggedIn) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$stmt = $pdo->query("
    SELECT id, sender_role, sender_name, message, created_at
    FROM announcements
    ORDER BY created_at DESC
    LIMIT 20
");
$announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'announcements' => $announcements]);
