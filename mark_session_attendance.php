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

$staff_id     = $_SESSION['cId'];
$session_id   = (int)($_POST['session_id'] ?? 0);
$attendance   = $_POST['attendance'] ?? '';

if (!$session_id || !in_array($attendance, ['attended', 'missed'], true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

$stmt = $pdo->prepare("
    UPDATE sessions
    SET attendance = ?
    WHERE id = ? AND counselor_id = ?
");
$stmt->execute([$attendance, $session_id, $staff_id]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Session not found or not yours to update.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Attendance updated.']);
