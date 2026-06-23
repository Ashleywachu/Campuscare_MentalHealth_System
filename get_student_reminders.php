<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$admission_no = $_SESSION['admissionNo'];

$stmt = $pdo->prepare("
    SELECT id, dean_name, note, created_at
    FROM student_reminders
    WHERE student_id = ?
    ORDER BY created_at DESC
    LIMIT 20
");
$stmt->execute([$admission_no]);
$reminders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'reminders' => $reminders]);
