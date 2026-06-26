<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if (isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true) {
    $role  = 'admin';
    $table = 'admins';
    $where = 'username';
    $id    = $_SESSION['adminUsername'];
} elseif (isset($_SESSION['dLoggedIn']) && $_SESSION['dLoggedIn'] === true) {
    $role  = 'dean';
    $table = 'dean';
    $where = 'staff_id';
    $id    = $_SESSION['dId'];
} elseif (isset($_SESSION['cLoggedIn']) && $_SESSION['cLoggedIn'] === true) {
    $role  = 'counselor';
    $table = 'counselors';
    $where = 'staff_id';
    $id    = $_SESSION['cId'];
} elseif (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true) {
    $role  = 'student';
    $table = 'students';
    $where = 'admission_no';
    $id    = $_SESSION['admissionNo'];
} else {
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Please choose an image to upload.']);
    exit;
}

$file = $_FILES['avatar'];

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$mime = mime_content_type($file['tmp_name']);

if (!isset($allowed[$mime])) {
    echo json_encode(['success' => false, 'message' => 'Only JPG, PNG, WEBP, or GIF images are allowed.']);
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Image must be smaller than 2MB.']);
    exit;
}

$ext      = $allowed[$mime];
$filename = $role . '_' . preg_replace('/[^A-Za-z0-9_-]/', '', $id) . '_' . time() . '.' . $ext;
$destDir  = __DIR__ . '/uploads/avatars/';
$destPath = $destDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destPath)) {
    echo json_encode(['success' => false, 'message' => 'Could not save the uploaded image.']);
    exit;
}

$publicUrl = 'uploads/avatars/' . $filename;

$stmt = $pdo->prepare("UPDATE $table SET avatar_url = ? WHERE $where = ?");
$stmt->execute([$publicUrl, $id]);

if ($role === 'admin') {
    $_SESSION['adminAvatar'] = $publicUrl;
} elseif ($role === 'dean') {
    $_SESSION['dAvatar'] = $publicUrl;
} elseif ($role === 'counselor') {
    $_SESSION['cAvatar'] = $publicUrl;
} else {
    $_SESSION['userAvatar'] = $publicUrl;
}

echo json_encode(['success' => true, 'message' => 'Profile picture updated.', 'avatar' => $publicUrl]);
