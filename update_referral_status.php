<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['dLoggedIn']) || $_SESSION['dLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$referral_id = (int)($_POST['referral_id'] ?? 0);
$status      = trim($_POST['status'] ?? '');

if (!$referral_id || !in_array($status, ['Open', 'In Progress', 'Follow-Up', 'Resolved'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$stmt = $pdo->prepare("UPDATE referrals SET status = ? WHERE id = ?");
$stmt->execute([$status, $referral_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Referral not found.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Case status updated.']);
