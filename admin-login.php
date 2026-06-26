<?php

session_start();
require 'db.php';
require 'login_logger.php';

header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get values sent from the login form
$username = strtolower(trim($_POST['username'] ?? ''));
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
    exit;
}

// Look up the admin by username
$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify password against the stored hash
if ($admin && password_verify($password, $admin['password'])) {

    // Save everything needed by the admin portal into the session
    $_SESSION['adminLoggedIn'] = true;
    $_SESSION['userType']      = 'admin';
    $_SESSION['adminId']       = $admin['id'];
    $_SESSION['adminUsername'] = $admin['username'];
    $_SESSION['adminName']     = $admin['full_name'];
    $_SESSION['adminEmail']    = $admin['email'];
    $_SESSION['adminAvatar']   = $admin['avatar_url'] ?? '';

    log_login_attempt($pdo, 'admin', $username, $admin['full_name'], true);

    echo json_encode([
        'success'  => true,
        'name'     => $admin['full_name'],
        'username' => $admin['username'],
        'email'    => $admin['email']
    ]);

} else {
    log_login_attempt($pdo, 'admin', $username, $admin['full_name'] ?? null, false);

    echo json_encode(['success' => false, 'message' => 'Incorrect username or password.']);
}
?>