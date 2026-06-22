<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['adminLoggedIn']) || $_SESSION['adminLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$stmt = $pdo->query("
    SELECT cr.id, cr.admission_no, cr.message, cr.status,
           cr.assigned_staff_id, cr.requested_at, cr.assigned_at, cr.responded_at,
           s.full_name AS student_name, s.avatar_url AS student_avatar,
           c.full_name AS counselor_name, c.title AS counselor_title
    FROM counselor_requests cr
    JOIN students s ON cr.admission_no = s.admission_no
    LEFT JOIN counselors c ON cr.assigned_staff_id = c.staff_id
    ORDER BY
        CASE cr.status
            WHEN 'pending' THEN 0
            WHEN 'declined' THEN 1
            WHEN 'assigned' THEN 2
            WHEN 'accepted' THEN 3
        END,
        cr.requested_at DESC
");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Also return the counselor list so the admin UI can populate the assign dropdown
$counselors = $pdo->query("SELECT staff_id, title, full_name, department FROM counselors ORDER BY full_name")
                   ->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success'    => true,
    'requests'   => $requests,
    'counselors' => $counselors
]);