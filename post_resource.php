<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (isset($_SESSION['cLoggedIn']) && $_SESSION['cLoggedIn'] === true) {
    $role = 'counselor';
    $posted_by_id = $_SESSION['cId'];
} elseif (isset($_SESSION['dLoggedIn']) && $_SESSION['dLoggedIn'] === true) {
    $role = 'dean';
    $posted_by_id = $_SESSION['dId'];
} elseif (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
    $role = 'admin';
    $posted_by_id = $_SESSION['adminUsername'];
} else {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$type        = trim($_POST['resource_type'] ?? '');
$url         = trim($_POST['url'] ?? '');

if (!$title || !$url) {
    echo json_encode(['success' => false, 'message' => 'A title and a link are required.']);
    exit;
}

if (!in_array($type, ['Video', 'Article', 'Worksheet', 'Audio', 'Link'], true)) {
    $type = 'Link';
}

if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid link (include https://).']);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO resources (posted_by_id, posted_by_role, title, description, resource_type, url)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([$posted_by_id, $role, $title, $description ?: null, $type, $url]);

echo json_encode(['success' => true, 'message' => 'Resource posted.']);
