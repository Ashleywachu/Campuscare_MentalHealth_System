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

$stmt = $pdo->prepare("
    SELECT id, title, description, resource_type, url, created_at
    FROM resources
    WHERE posted_by_id = ? AND posted_by_role = ?
    ORDER BY created_at DESC
");
$stmt->execute([$posted_by_id, $role]);
$resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'resources' => $resources]);
