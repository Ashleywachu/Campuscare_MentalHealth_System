<?php
// This file is used to generate hashed passwords for the users.
$users = [
    // Students — default password = their admission number
    ['ADM-2024-001', 'Sarah Mitchell',   's.mitchell@university.edu',  'ADM-2024-001'],
    ['ADM-2024-002', 'James Brennan',    'j.brennan@university.edu',   'ADM-2024-002'],
    ['ADM-2024-003', 'Li Wei',           'l.wei@university.edu',       'ADM-2024-003'],
    ['ADM-2024-004', 'Zhang Hao',        'z.hao@university.edu',       'ADM-2024-004'],
    ['ADM-2024-005', 'Fatima Al-Hassan', 'f.alhassan@university.edu',  'ADM-2024-005'],
    ['ADM-2024-006', 'Omar Khalid',      'o.khalid@university.edu',    'ADM-2024-006'],
    ['ADM-2024-007', 'Priya Sharma',     'p.sharma@university.edu',    'ADM-2024-007'],
    ['ADM-2024-008', 'Arjun Patel',      'a.patel@university.edu',     'ADM-2024-008'],
    ['ADM-2024-009', 'Ji-Yeon Park',     'j.park@university.edu',      'ADM-2024-009'],
    ['ADM-2024-010', 'Amara Diallo',     'a.diallo@university.edu',    'ADM-2024-010'],
];

$counselors = [
    // Counselors — default password = their staff ID
    ['CSL-001', 'Dr.',  'Emily Carter',    'e.carter@university.edu',   'Student Wellness Centre',  'CSL-001'],
    ['CSL-002', 'Dr.',  'Chen Mei-Ling',   'c.meiling@university.edu',  'Mental Health Unit',       'CSL-002'],
    ['CSL-003', 'Mr.',  'Yusuf Ibrahim',   'y.ibrahim@university.edu',  'Student Wellness Centre',  'CSL-003'],
    ['CSL-004', 'Ms.',  'Kavya Nair',      'k.nair@university.edu',     'Counseling Department',    'CSL-004'],
    ['CSL-005', 'Mr.',  'Park Joon-Ho',    'p.joonho@university.edu',   'Mental Health Unit',       'CSL-005'],
];

$dean = [
    'DEN-001', 'Zhang CheLi', 'z.cheli@university.edu', 'Dean@2026'
];

$admins = [
    ['admin',   'System Administrator', 'admin@university.edu',    'Admin@2026'],
    ['sysop',   'Rebecca Fisher',       'r.fisher@university.edu', 'Admin@2026'],
];

echo "<pre style='font-family:monospace; font-size:13px; background:#1e1b2e; color:#c4b5fd; padding:20px;'>";


echo "-- STUDENTS\n";
foreach ($users as $u) {
    $hash = password_hash($u[3], PASSWORD_DEFAULT);
    echo "INSERT INTO students (admission_no, full_name, email, password) VALUES\n";
    echo "  ('{$u[0]}', '{$u[1]}', '{$u[2]}', '{$hash}');\n\n";
}

echo "-- COUNSELORS\n";
foreach ($counselors as $c) {
    $hash = password_hash($c[5], PASSWORD_DEFAULT);
    echo "INSERT INTO counselors (staff_id, title, full_name, email, department, password) VALUES\n";
    echo "  ('{$c[0]}', '{$c[1]}', '{$c[2]}', '{$c[3]}', '{$c[4]}', '{$hash}');\n\n";
}

echo "-- DEAN\n";
$hash = password_hash($dean[3], PASSWORD_DEFAULT);
echo "INSERT INTO dean (staff_id, full_name, email, password) VALUES\n";
echo "  ('{$dean[0]}', '{$dean[1]}', '{$dean[2]}', '{$hash}');\n\n";

echo "-- ADMINS\n";
foreach ($admins as $a) {
    $hash = password_hash($a[3], PASSWORD_DEFAULT);
    echo "INSERT INTO admins (username, full_name, email, password) VALUES\n";
    echo "  ('{$a[0]}', '{$a[1]}', '{$a[2]}', '{$hash}');\n\n";
}


echo "</pre>";
?>