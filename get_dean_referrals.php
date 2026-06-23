<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['dLoggedIn']) || $_SESSION['dLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$stmt = $pdo->query("
    SELECT r.id AS referral_id, r.student_id, r.risk_level, r.category, r.status, r.referred_at,
           s.full_name AS student_name,
           c.title AS counselor_title, c.full_name AS counselor_name
    FROM referrals r
    JOIN students s ON r.student_id = s.admission_no
    JOIN counselors c ON r.counselor_id = c.staff_id
    ORDER BY r.referred_at DESC
");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$referrals = array_map(function ($r) {
    return [
        'referral_id' => (int)$r['referral_id'],
        'id'          => $r['student_id'],
        'name'        => $r['student_name'],
        'risk'        => $r['risk_level'],
        'category'    => $r['category'],
        'status'      => $r['status'],
        'counselor'   => trim($r['counselor_title'] . ' ' . $r['counselor_name']),
        'referred'    => substr($r['referred_at'], 0, 10),
    ];
}, $rows);

echo json_encode(['success' => true, 'students' => $referrals]);
