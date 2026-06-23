<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
    $role = 'admin';
    $name = $_SESSION['adminName'] ?? 'Admin';
} elseif (isset($_SESSION['dLoggedIn']) && $_SESSION['dLoggedIn'] === true) {
    $role = 'dean';
    $name = $_SESSION['dName'] ?? 'Dean of Students';
} else {
    echo json_encode(['success' => false, 'message' => 'Only admins and the dean can send announcements.']);
    exit;
}

$message = trim($_POST['message'] ?? '');
if (!$message) {
    echo json_encode(['success' => false, 'message' => 'Write something before sending.']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO announcements (sender_role, sender_name, message)
    VALUES (?, ?, ?)
");
$stmt->execute([$role, $name, $message]);

echo json_encode(['success' => true, 'message' => 'Announcement sent.']);
