<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['cLoggedIn']) || $_SESSION['cLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$staff_id = $_SESSION['cId'];

$stmt = $pdo->prepare("
    SELECT se.id, se.student_id, se.session_date, se.session_time, se.attendance, se.notes,
           s.full_name AS student_name, s.avatar_url AS student_avatar
    FROM sessions se
    JOIN students s ON se.student_id = s.admission_no
    WHERE se.counselor_id = ?
    ORDER BY se.session_date DESC, se.session_time DESC
");
$stmt->execute([$staff_id]);
$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'sessions' => $sessions]);
