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
$risk_level = trim($_POST['risk_level'] ?? '');
$category   = trim($_POST['category'] ?? '');

if (!$student_id || !in_array($risk_level, ['Low', 'Medium', 'High'], true) || !$category) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid referral details.']);
    exit;
}

// Only allow referring a student this counselor has accepted
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

// Don't allow a second open referral for the same student
$dupe = $pdo->prepare("
    SELECT id FROM referrals
    WHERE student_id = ? AND status IN ('Open', 'In Progress', 'Follow-Up')
    LIMIT 1
");
$dupe->execute([$student_id]);
if ($dupe->fetch()) {
    echo json_encode(['success' => false, 'message' => 'This student already has an active referral with the Dean.']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO referrals (student_id, counselor_id, risk_level, category, status)
    VALUES (?, ?, ?, ?, 'Open')
");
$stmt->execute([$student_id, $staff_id, $risk_level, $category]);

echo json_encode(['success' => true, 'message' => 'Student referred to the Dean of Students.']);
