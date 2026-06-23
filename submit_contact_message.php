<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$full_name = trim($_POST['name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$subject   = trim($_POST['subject'] ?? '');
$message   = trim($_POST['message'] ?? '');

if (!$full_name || !$email || !$subject || !$message) {
    echo json_encode(['success' => false, 'message' => 'Please fill in every field.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO contact_messages (full_name, email, subject, message)
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$full_name, $email, $subject, $message]);

echo json_encode(['success' => true, 'message' => 'Your message has been sent. Our team will get back to you soon.']);
