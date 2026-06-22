<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$admission_no = $_SESSION['admissionNo'];
$message = trim($_POST['message'] ?? '');

// Prevent duplicate open requests (pending, assigned, or accepted = already in flight)
$check = $pdo->prepare("
    SELECT id FROM counselor_requests
    WHERE admission_no = ? AND status IN ('pending', 'assigned', 'accepted')
    LIMIT 1
");
$check->execute([$admission_no]);
if ($check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'You already have an active or pending counselor request.']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO counselor_requests (admission_no, message, status)
    VALUES (?, ?, 'pending')
");
$stmt->execute([$admission_no, $message ?: null]);

echo json_encode(['success' => true, 'message' => 'Your request has been submitted. The admin team will assign you a counselor shortly.']);