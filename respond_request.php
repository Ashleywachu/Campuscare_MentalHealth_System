<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cLoggedIn']) || $_SESSION['cLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$staff_id   = $_SESSION['cId'];
$request_id = (int)($_POST['request_id'] ?? 0);
$action     = $_POST['action'] ?? ''; // 'accept' or 'decline'

if (!$request_id || !in_array($action, ['accept', 'decline'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$newStatus = $action === 'accept' ? 'accepted' : 'declined';

// Only allow responding to requests assigned to this counselor that are still awaiting a response
$stmt = $pdo->prepare("
    UPDATE counselor_requests
    SET status = ?, responded_at = NOW()
    WHERE id = ? AND assigned_staff_id = ? AND status = 'assigned'
");
$stmt->execute([$newStatus, $request_id, $staff_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Request could not be updated (it may have already been handled).']);
    exit;
}

$message = $action === 'accept'
    ? 'Request accepted. This student is now assigned to you.'
    : 'Request declined. It has been returned to the admin queue for reassignment.';

echo json_encode(['success' => true, 'message' => $message]);