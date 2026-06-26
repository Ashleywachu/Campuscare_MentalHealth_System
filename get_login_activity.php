<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$today = $pdo->query("
    SELECT COUNT(*) FROM login_logs
    WHERE success = 1 AND DATE(created_at) = CURDATE()
")->fetchColumn();

$failed24h = $pdo->query("
    SELECT COUNT(*) FROM login_logs
    WHERE success = 0 AND created_at >= (NOW() - INTERVAL 24 HOUR)
")->fetchColumn();

$recent = $pdo->query("
    SELECT role, identifier, display_name, success, device, created_at
    FROM login_logs
    ORDER BY created_at DESC
    LIMIT 25
")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success'       => true,
    'logins_today'  => (int)$today,
    'failed_24h'    => (int)$failed24h,
    'recent'        => $recent
]);
