<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$stmt = $pdo->query("
    SELECT admission_no AS user_id, full_name, email, 'Student' AS role, created_at FROM students
    UNION ALL
    SELECT staff_id, full_name, email, 'Counselor', created_at FROM counselors
    UNION ALL
    SELECT staff_id, full_name, email, 'Dean', created_at FROM dean
    UNION ALL
    SELECT username, full_name, email, 'Admin', created_at FROM admins
    ORDER BY role, full_name
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'users' => $users]);
