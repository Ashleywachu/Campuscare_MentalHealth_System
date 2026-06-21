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
$email    = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

// Look up the dean by email
$stmt = $pdo->prepare("SELECT * FROM dean WHERE email = ?");
$stmt->execute([$email]);
$dean = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify password against the stored hash
if ($dean && password_verify($password, $dean['password'])) {

    // Save everything needed by the dean portal into the session
    $_SESSION['dLoggedIn'] = true;
    $_SESSION['userType']  = 'dean';
    $_SESSION['dId']       = $dean['staff_id'];
    $_SESSION['dName']     = $dean['full_name'];
    $_SESSION['dEmail']    = $dean['email'];

    echo json_encode([
        'success'  => true,
        'name'     => $dean['full_name'],
        'staff_id' => $dean['staff_id'],
        'email'    => $dean['email']
    ]);

} else {
    echo json_encode(['success' => false, 'message' => 'Incorrect email or password.']);
}
?>