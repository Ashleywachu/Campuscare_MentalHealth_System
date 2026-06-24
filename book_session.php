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

$admission_no  = $_SESSION['admissionNo'];
$session_date  = trim($_POST['session_date'] ?? '');
$session_time  = trim($_POST['session_time'] ?? '');

if (!$session_date || !$session_time) {
    echo json_encode(['success' => false, 'message' => 'Choose a date and time first.']);
    exit;
}

$today = date('Y-m-d');
if ($session_date < $today) {
    echo json_encode(['success' => false, 'message' => 'Please choose a date in the future.']);
    exit;
}

// Look up this student's accepted counselor — never trust a client-supplied counselor id
$stmt = $pdo->prepare("
    SELECT assigned_staff_id FROM counselor_requests
    WHERE admission_no = ? AND status = 'accepted'
    LIMIT 1
");
$stmt->execute([$admission_no]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    echo json_encode(['success' => false, 'message' => 'You need an assigned counselor before booking a session.']);
    exit;
}

$insert = $pdo->prepare("
    INSERT INTO sessions (student_id, counselor_id, session_date, session_time)
    VALUES (?, ?, ?, ?)
");
$insert->execute([$admission_no, $request['assigned_staff_id'], $session_date, $session_time]);

echo json_encode(['success' => true, 'message' => 'Session booked! Your counselor will see it under their Sessions tab.']);
