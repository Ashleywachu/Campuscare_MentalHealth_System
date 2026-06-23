<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['dLoggedIn']) || $_SESSION['dLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Only the Dean of Students can send reminders.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$student_id = trim($_POST['student_id'] ?? '');
$note       = trim($_POST['note'] ?? '');
$deanName   = $_SESSION['dName'] ?? 'Dean of Students';

if (!$student_id || !$note) {
    echo json_encode(['success' => false, 'message' => 'Select a student and write a note first.']);
    exit;
}

$check = $pdo->prepare("SELECT admission_no FROM students WHERE admission_no = ? LIMIT 1");
$check->execute([$student_id]);
if (!$check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Student not found.']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO student_reminders (student_id, dean_name, note)
    VALUES (?, ?, ?)
");
$stmt->execute([$student_id, $deanName, $note]);

echo json_encode(['success' => true, 'message' => 'Reminder sent to student.']);
