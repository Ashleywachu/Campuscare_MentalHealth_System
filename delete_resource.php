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

$resource_id = (int)($_POST['resource_id'] ?? 0);

$stmt = $pdo->prepare("DELETE FROM resources WHERE id = ? AND posted_by_id = ? AND posted_by_role = ?");
$stmt->execute([$resource_id, $posted_by_id, $role]);

if ($stmt->rowCount() === 0) {
    echo json_encode(['success' => false, 'message' => 'Resource not found.']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Resource removed.']);
