<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Get what the login form sent
$admission = strtoupper(trim($_POST['admission_no'] ?? ''));
$password  = $_POST['password'] ?? '';

// Basic validation
if (empty($admission) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

// Look up the student
$stmt = $pdo->prepare("SELECT * FROM students WHERE admission_no = ?");
$stmt->execute([$admission]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Check password
if ($student && password_verify($password, $student['password'])) {
    // Login success — save to session
    $_SESSION['loggedIn']    = true;
    $_SESSION['userType']    = 'student';
    $_SESSION['userName']    = $student['full_name'];
    $_SESSION['admissionNo'] = $student['admission_no'];
    $_SESSION['userAvatar']  = $student['avatar_url'];

    echo json_encode(['success' => true, 'name' => $student['full_name'], 'avatar' => $student['avatar_url'] ?? '']);
} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect admission number or password.']);
}
?>