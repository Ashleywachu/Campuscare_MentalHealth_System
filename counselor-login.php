<?php

session_start();
require 'db.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get values sent from the login form
$staff_id = strtoupper(trim($_POST['staff_id'] ?? ''));
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($staff_id) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

// Look up the counselor by staff ID
$stmt = $pdo->prepare("SELECT * FROM counselors WHERE staff_id = ?");
$stmt->execute([$staff_id]);
$counselor = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify password against the stored hash
if ($counselor && password_verify($password, $counselor['password'])) {

    // Save everything needed by the counselor portal into the session
    $_SESSION['cLoggedIn'] = true;
    $_SESSION['userType']  = 'counselor';
    $_SESSION['cId']       = $counselor['staff_id'];
    $_SESSION['cTitle']    = $counselor['title'];
    $_SESSION['cName']     = $counselor['full_name'];
    $_SESSION['cEmail']    = $counselor['email'];
    $_SESSION['cDept']     = $counselor['department'];
    $_SESSION['cAvatar']   = $counselor['avatar_url'] ?? '';

    echo json_encode([
        'success' => true,
        'name'    => $counselor['full_name'],
        'title'   => $counselor['title'],
        'staff_id'=> $counselor['staff_id'],
        'email'   => $counselor['email'],
        'dept'    => $counselor['department'],
        'avatar'  => $counselor['avatar_url'] ?? ''
    ]);

} else {
    // Wrong staff ID or wrong password — same message for both (security best practice)
    echo json_encode(['success' => false, 'message' => 'Incorrect Staff ID or password.']);
}
?>