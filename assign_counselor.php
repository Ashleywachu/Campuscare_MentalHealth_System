<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$request_id = (int)($_POST['request_id'] ?? 0);
$staff_id   = trim($_POST['staff_id'] ?? '');

if (!$request_id || !$staff_id) {
    echo json_encode(['success' => false, 'message' => 'Missing request or counselor.']);
    exit;
}

// Confirm the counselor exists
$check = $pdo->prepare("SELECT staff_id FROM counselors WHERE staff_id = ? LIMIT 1");
$check->execute([$staff_id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Counselor not found.']);
    exit;
}

// Only allow assigning requests that are pending or declined (not already assigned/accepted)
$stmt = $pdo->prepare("
    UPDATE counselor_requests
    SET status = 'assigned', assigned_staff_id = ?, assigned_at = NOW(), responded_at = NULL
    WHERE id = ? AND status IN ('pending', 'declined')
");
$stmt->execute([$staff_id, $request_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Request could not be assigned (it may already be assigned or accepted).']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Counselor assigned. Awaiting their response.']);