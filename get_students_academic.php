<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Security — must be a logged-in student
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$admission_no = $_SESSION['admissionNo'];

// Get active semester
$sem = $pdo->query("SELECT * FROM semesters WHERE is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$sem) {
    echo json_encode(['success' => false, 'message' => 'No active semester found.']);
    exit;
}
$semester_id   = $sem['id'];
$semester_name = $sem['semester_name'];

// ── GRADES ───────────────────────────────────────────────
$gradeStmt = $pdo->prepare("
    SELECT g.*, u.unit_name
    FROM grades g
    JOIN units u ON g.unit_code = u.unit_code
    WHERE g.admission_no = ? AND g.semester_id = ?
    ORDER BY u.unit_code
");
$gradeStmt->execute([$admission_no, $semester_id]);
$grades = $gradeStmt->fetchAll(PDO::FETCH_ASSOC);

// ── ATTENDANCE PER UNIT ──────────────────────────────────
$attStmt = $pdo->prepare("
    SELECT
        a.unit_code,
        u.unit_name,
        COUNT(*) AS total_classes,
        SUM(CASE WHEN a.status = 'present' OR a.status = 'late' THEN 1 ELSE 0 END) AS attended,
        SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absences,
        ROUND((SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) AS absence_pct
    FROM attendance a
    JOIN units u ON a.unit_code = u.unit_code
    WHERE a.admission_no = ? AND a.semester_id = ?
    GROUP BY a.unit_code, u.unit_name
    ORDER BY a.unit_code
");
$attStmt->execute([$admission_no, $semester_id]);
$attendance = $attStmt->fetchAll(PDO::FETCH_ASSOC);

// ── FLAGS ────────────────────────────────────────────────
// Count how many units the student is failing or at risk
$failing_units = array_filter($grades, fn($g) => $g['total'] < 40);
$at_risk_attendance = array_filter($attendance, fn($a) => $a['absence_pct'] >= 40);

echo json_encode([
    'success'            => true,
    'semester'           => $semester_name,
    'grades'             => $grades,
    'attendance'         => $attendance,
    'failing_count'      => count($failing_units),
    'at_risk_attendance' => count($at_risk_attendance)
]);
?>