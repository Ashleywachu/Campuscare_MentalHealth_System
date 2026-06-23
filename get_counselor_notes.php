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
    SELECT cn.id, cn.student_id, cn.note, cn.created_at,
           s.full_name AS student_name, s.avatar_url AS student_avatar
    FROM counselor_notes cn
    JOIN students s ON cn.student_id = s.admission_no
    WHERE cn.counselor_id = ?
    ORDER BY cn.created_at DESC
    LIMIT 50
");
$stmt->execute([$staff_id]);
$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'notes' => $notes]);
