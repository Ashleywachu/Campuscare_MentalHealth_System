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
    SELECT cr.*, c.full_name AS counselor_name, c.title AS counselor_title,
           c.email AS counselor_email, c.avatar_url AS counselor_avatar
    FROM counselor_requests cr
    LEFT JOIN counselors c ON cr.assigned_staff_id = c.staff_id
    WHERE cr.admission_no = ?
    ORDER BY cr.requested_at DESC
    LIMIT 1
");
$stmt->execute([$admission_no]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'request' => $request ?: null]);