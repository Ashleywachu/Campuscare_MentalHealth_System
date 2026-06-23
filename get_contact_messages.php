<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

$isAuthorized = (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true)
    || (isset($_SESSION['dLoggedIn']) && $_SESSION['dLoggedIn'] === true);

if (!$isAuthorized) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$stmt = $pdo->query("
    SELECT id, full_name, email, subject, message, created_at
    FROM contact_messages
    ORDER BY created_at DESC
");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'messages' => $messages]);
