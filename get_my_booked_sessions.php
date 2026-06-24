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
    SELECT se.id, se.session_date, se.session_time, se.attendance,
           c.title AS counselor_title, c.full_name AS counselor_name
    FROM sessions se
    JOIN counselors c ON se.counselor_id = c.staff_id
    WHERE se.student_id = ?
    ORDER BY se.session_date DESC, se.session_time DESC
");
$stmt->execute([$admission_no]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'sessions' => $sessions]);
