<?php
// ============================================================
//  generate_attendance.php
//
//  Run once at http://localhost/campuscare/generate_attendance.php
//  Copy the SQL output → paste into phpMyAdmin SQL tab → click Go
//  DELETE this file immediately after.
// ============================================================

// ── 20 CLASS DATES (Mon + Thu pattern, March–May 2026) ──────
$dates = [
    '2026-03-03', '2026-03-06',   //  1,  2
    '2026-03-10', '2026-03-13',   //  3,  4
    '2026-03-17', '2026-03-20',   //  5,  6
    '2026-03-24', '2026-03-27',   //  7,  8
    '2026-04-01', '2026-04-04',   //  9,  10
    '2026-04-08', '2026-04-11',   // 11,  12
    '2026-04-15', '2026-04-18',   // 13,  14
    '2026-04-22', '2026-04-25',   // 15,  16
    '2026-05-02', '2026-05-06',   // 17,  18
    '2026-05-09', '2026-05-13'    // 19,  20
];

// ── UNITS ────────────────────────────────────────────────────
$units = [
    'CMP101','CMP102','CMP103','CMP104',
    'CMP105','CMP106','CMP107','CMP108'
];

// ── ATTENDANCE PATTERNS ──────────────────────────────────────
// Each value is an array of class INDEX numbers (1-based) the
// student is ABSENT for in that unit.
//
// 8+ absences out of 20 = 40%+ = AT RISK  (flagged red)
// 5–7 absences          = 25–35% = MONITOR (flagged amber)
// 1–4 absences          = normal
// ─────────────────────────────────────────────────────────────

$patterns = [

    // ── Sarah Mitchell — struggling in several units ──────────
    // Already seeded CMP101, but we wipe and redo uniformly
    'ADM-2024-001' => [
        'CMP101' => [2,4,6,8,10,12,14,16],           // 8 abs = 40% AT RISK
        'CMP102' => [1,3,5,7,9,11,13,15,17],          // 9 abs = 45% AT RISK
        'CMP103' => [3,7,11,15],                       // 4 abs = 20% normal
        'CMP104' => [2,4,6,8,10,12,14,16],            // 8 abs = 40% AT RISK
        'CMP105' => [5,12,18],                         // 3 abs = 15% normal
        'CMP106' => [4,8,12,16,18],                   // 5 abs = 25% MONITOR
        'CMP107' => [1,2,4,6,8,10,12,14,16,18],       // 10 abs = 50% AT RISK
        'CMP108' => [5,15],                            // 2 abs = 10% normal
    ],

    // ── James Brennan — performing well, rarely absent ────────
    'ADM-2024-002' => [
        'CMP101' => [3,15],                            // 2 abs = 10%
        'CMP102' => [7],                               // 1 abs = 5%
        'CMP103' => [2,18],                            // 2 abs = 10%
        'CMP104' => [10],                              // 1 abs = 5%
        'CMP105' => [5,14],                            // 2 abs = 10%
        'CMP106' => [1],                               // 1 abs = 5%
        'CMP107' => [8,16],                            // 2 abs = 10%
        'CMP108' => [12],                              // 1 abs = 5%
    ],

    // ── Li Wei — average, misses a few per unit ───────────────
    'ADM-2024-003' => [
        'CMP101' => [2,8,14],                          // 3 abs = 15%
        'CMP102' => [4,10,16],                         // 3 abs = 15%
        'CMP103' => [1,7,13,19],                       // 4 abs = 20%
        'CMP104' => [3,9,15],                          // 3 abs = 15%
        'CMP105' => [5,11,17],                         // 3 abs = 15%
        'CMP106' => [2,8,14,20],                       // 4 abs = 20%
        'CMP107' => [4,10,16],                         // 3 abs = 15%
        'CMP108' => [6,12,18],                         // 3 abs = 15%
    ],

    // ── Zhang Hao — good attendance ──────────────────────────
    'ADM-2024-004' => [
        'CMP101' => [5,13],                            // 2 abs = 10%
        'CMP102' => [3,11,19],                         // 3 abs = 15%
        'CMP103' => [7,15],                            // 2 abs = 10%
        'CMP104' => [2,10,18],                         // 3 abs = 15%
        'CMP105' => [4,12],                            // 2 abs = 10%
        'CMP106' => [6,14],                            // 2 abs = 10%
        'CMP107' => [1,9,17],                          // 3 abs = 15%
        'CMP108' => [8,16],                            // 2 abs = 10%
    ],

    // ── Fatima Al-Hassan — seriously struggling, high absences
    'ADM-2024-005' => [
        'CMP101' => [1,2,4,6,8,10,12,14],             // 8 abs = 40% AT RISK
        'CMP102' => [3,5,7,9,11,13,15,17,19],          // 9 abs = 45% AT RISK
        'CMP103' => [2,4,6,8,10,12,14,16,18,20],       // 10 abs = 50% AT RISK
        'CMP104' => [1,3,5,7,9,11,13,15],              // 8 abs = 40% AT RISK
        'CMP105' => [2,4,6,8,10,12,14,16,18],          // 9 abs = 45% AT RISK
        'CMP106' => [1,3,5,7,9,11,13],                 // 7 abs = 35% MONITOR
        'CMP107' => [2,4,6,8,10,12,14,16,18,20],       // 10 abs = 50% AT RISK
        'CMP108' => [1,3,5,7,9,11,13,15],              // 8 abs = 40% AT RISK
    ],

    // ── Omar Khalid — good attendance ────────────────────────
    'ADM-2024-006' => [
        'CMP101' => [4,12],                            // 2 abs = 10%
        'CMP102' => [2,10,18],                         // 3 abs = 15%
        'CMP103' => [6,14],                            // 2 abs = 10%
        'CMP104' => [3,11,19],                         // 3 abs = 15%
        'CMP105' => [5,13],                            // 2 abs = 10%
        'CMP106' => [1,9,17],                          // 3 abs = 15%
        'CMP107' => [7,15],                            // 2 abs = 10%
        'CMP108' => [4,12,20],                         // 3 abs = 15%
    ],

    // ── Priya Sharma — excellent, almost never absent ─────────
    'ADM-2024-007' => [
        'CMP101' => [10],                              // 1 abs = 5%
        'CMP102' => [],                                // 0 abs = 0%
        'CMP103' => [5],                               // 1 abs = 5%
        'CMP104' => [],                                // 0 abs = 0%
        'CMP105' => [15],                              // 1 abs = 5%
        'CMP106' => [],                                // 0 abs = 0%
        'CMP107' => [8],                               // 1 abs = 5%
        'CMP108' => [],                                // 0 abs = 0%
    ],

    // ── Arjun Patel — average attendance ─────────────────────
    'ADM-2024-008' => [
        'CMP101' => [3,7,13,19],                       // 4 abs = 20%
        'CMP102' => [1,6,12,18],                       // 4 abs = 20%
        'CMP103' => [4,9,15,20],                       // 4 abs = 20%
        'CMP104' => [2,8,14],                          // 3 abs = 15%
        'CMP105' => [5,10,16],                         // 3 abs = 15%
        'CMP106' => [3,9,15,20],                       // 4 abs = 20%
        'CMP107' => [1,6,12,17],                       // 4 abs = 20%
        'CMP108' => [4,11,18],                         // 3 abs = 15%
    ],

    // ── Ji-Yeon Park — good attendance ───────────────────────
    'ADM-2024-009' => [
        'CMP101' => [6,18],                            // 2 abs = 10%
        'CMP102' => [14],                              // 1 abs = 5%
        'CMP103' => [3,19],                            // 2 abs = 10%
        'CMP104' => [11],                              // 1 abs = 5%
        'CMP105' => [7,20],                            // 2 abs = 10%
        'CMP106' => [4],                               // 1 abs = 5%
        'CMP107' => [9,17],                            // 2 abs = 10%
        'CMP108' => [13],                              // 1 abs = 5%
    ],

    // ── Amara Diallo — borderline, some units monitored ──────
    'ADM-2024-010' => [
        'CMP101' => [2,6,10,14,18],                   // 5 abs = 25% MONITOR
        'CMP102' => [1,4,8,12,16,19],                 // 6 abs = 30% MONITOR
        'CMP103' => [3,7,11,15],                       // 4 abs = 20%
        'CMP104' => [2,5,9,13,17,20],                 // 6 abs = 30% MONITOR
        'CMP105' => [1,4,8,12,16,19],                 // 6 abs = 30% MONITOR
        'CMP106' => [3,6,10,14,18],                   // 5 abs = 25% MONITOR
        'CMP107' => [1,3,6,9,13,17,20],               // 7 abs = 35% MONITOR
        'CMP108' => [2,7,12,17],                       // 4 abs = 20%
    ],

];

// ── OUTPUT ───────────────────────────────────────────────────

echo "<pre style='font-family:monospace; font-size:13px; background:#1e1b2e; color:#c4b5fd; padding:24px; line-height:1.8;'>";

echo "-- ==================================================\n";
echo "-- FULL ATTENDANCE — All Students, All Units\n";
echo "-- Semester ID = 1  |  20 classes per unit\n";
echo "-- Run STEP 1 first to clear old Sarah CMP101 data\n";
echo "-- ==================================================\n\n";

echo "-- STEP 1: Clear any existing attendance so there are no duplicates\n";
echo "DELETE FROM attendance WHERE semester_id = 1;\n\n";

echo "-- STEP 2: Insert all attendance records\n\n";

foreach ($patterns as $admission_no => $unitPatterns) {

    echo "-- ── $admission_no ────────────────────────\n";

    foreach ($unitPatterns as $unit_code => $absentIndexes) {

        $totalClasses  = count($dates);
        $absentCount   = count($absentIndexes);
        $absencePct    = round(($absentCount / $totalClasses) * 100, 1);
        $flag          = $absencePct >= 40 ? ' ← AT RISK' : ($absencePct >= 25 ? ' ← MONITOR' : '');

        echo "-- $unit_code  |  $absentCount absent / $totalClasses  = {$absencePct}%$flag\n";

        foreach ($dates as $idx => $date) {
            $classNum = $idx + 1;  // 1-based
            $status   = in_array($classNum, $absentIndexes) ? 'absent' : 'present';
            echo "INSERT INTO attendance (admission_no, unit_code, semester_id, class_date, status) VALUES ('$admission_no','$unit_code',1,'$date','$status');\n";
        }

        echo "\n";
    }
}

echo "-- ==================================================\n";
echo "-- Done. Delete generate_attendance.php now.\n";
echo "-- ==================================================\n";

echo "</pre>";
?>