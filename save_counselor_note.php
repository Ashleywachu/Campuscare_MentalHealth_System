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
$student_id = trim($_POST['student_id'] ?? '');
$note       = trim($_POST['note'] ?? '');

if (!$student_id || !$note) {
    echo json_encode(['success' => false, 'message' => 'Select a student and write a note first.']);
    exit;
}

// Only allow notes for students this counselor actually has accepted
$check = $pdo->prepare("
    SELECT id FROM counselor_requests
    WHERE admission_no = ? AND assigned_staff_id = ? AND status = 'accepted'
    LIMIT 1
");
$check->execute([$student_id, $staff_id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'This student is not assigned to you.']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO counselor_notes (counselor_id, student_id, note)
    VALUES (?, ?, ?)
");
$stmt->execute([$staff_id, $student_id, $note]);

echo json_encode(['success' => true, 'message' => 'Note saved.']);
