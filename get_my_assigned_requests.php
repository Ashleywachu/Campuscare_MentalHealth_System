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
    SELECT cr.id, cr.admission_no, cr.message, cr.status,
           cr.requested_at, cr.assigned_at, cr.responded_at,
           s.full_name AS student_name, s.avatar_url AS student_avatar
    FROM counselor_requests cr
    JOIN students s ON cr.admission_no = s.admission_no
    WHERE cr.assigned_staff_id = ?
    ORDER BY
        CASE cr.status WHEN 'assigned' THEN 0 WHEN 'accepted' THEN 1 ELSE 2 END,
        cr.assigned_at DESC
");
$stmt->execute([$staff_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'requests' => $requests]);