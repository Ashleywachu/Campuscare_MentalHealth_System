<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

// Security — must be a logged-in counselor
if (!isset($_SESSION['cLoggedIn']) || $_SESSION['cLoggedIn'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

// Get active semester
$sem = $pdo->query("SELECT * FROM semesters WHERE is_active = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if (!$sem) {
    echo json_encode(['success' => false, 'message' => 'No active semester.']);
    exit;
}
$semester_id = $sem['id'];

// Get all students (in a real system you'd filter by assigned counselor)
$students = $pdo->query("SELECT admission_no, full_name FROM students")->fetchAll(PDO::FETCH_ASSOC);

$result = [];

foreach ($students as $student) {
    $adm = $student['admission_no'];

    // Grades summary
    $gradeStmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total_units,
            SUM(CASE WHEN total < 40 THEN 1 ELSE 0 END) AS failing_units,
            ROUND(AVG(total), 1) AS avg_score
        FROM grades
        WHERE admission_no = ? AND semester_id = ?
    ");
    $gradeStmt->execute([$adm, $semester_id]);
    $gradeSummary = $gradeStmt->fetch(PDO::FETCH_ASSOC);

    // Attendance summary
    $attStmt = $pdo->prepare("
        SELECT
            COUNT(*) AS total_classes,
            SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS total_absences,
            ROUND(
                (SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) / COUNT(*)) * 100
            , 1) AS overall_absence_pct
        FROM attendance
        WHERE admission_no = ? AND semester_id = ?
    ");
    $attStmt->execute([$adm, $semester_id]);
    $attSummary = $attStmt->fetch(PDO::FETCH_ASSOC);

    // Determine academic risk flag
    $flag = 'Normal';
    if (
        ($gradeSummary['failing_units'] >= 3) ||
        ($attSummary['overall_absence_pct'] >= 40)
    ) {
        $flag = 'At Risk';
    } elseif (
        ($gradeSummary['failing_units'] >= 1) ||
        ($attSummary['overall_absence_pct'] >= 25)
    ) {
        $flag = 'Monitor';
    }

    $result[] = [
        'admission_no'       => $adm,
        'name'               => $student['full_name'],
        'avg_score'          => $gradeSummary['avg_score'],
        'failing_units'      => $gradeSummary['failing_units'],
        'total_units'        => $gradeSummary['total_units'],
        'overall_absence_pct'=> $attSummary['overall_absence_pct'],
        'academic_flag'      => $flag
    ];
}

echo json_encode([
    'success'  => true,
    'semester' => $sem['semester_name'],
    'students' => $result
]);
?>