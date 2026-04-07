<?php
require 'config/db.php';
include 'includes/navbar.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$property_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check owner
$stmt = $pdo->prepare("SELECT user_id FROM properties WHERE id=?");
$stmt->execute([$property_id]);
$owner = $stmt->fetchColumn();

if ($owner == $_SESSION['user_id']) {
    die("You cannot request your own property");
}

// Count requests
$stmt = $pdo->prepare("
    SELECT COUNT(*) FROM access_requests WHERE requester_id=?
");
$stmt->execute([$_SESSION['user_id']]);
$count = $stmt->fetchColumn();

if ($count >= 5) {
    die("Request limit reached");
}

// Check property exists
$stmt = $pdo->prepare("SELECT id FROM properties WHERE id=? AND status='approved'");
$stmt->execute([$property_id]);

if ($stmt->rowCount() == 0) {
    die("Invalid Property");
}

// Prevent duplicate requests
$check = $pdo->prepare("
    SELECT * FROM access_requests WHERE property_id=? AND requester_id=?
");
$check->execute([$property_id, $user_id]);

if ($check->rowCount() > 0) {
    echo "You already requested access. Please wait for approval.";
    exit;
}

// Insert request
$stmt = $pdo->prepare("
    INSERT INTO access_requests (property_id, requester_id)
    VALUES (?, ?)
");

$stmt->execute([$property_id, $user_id]);

echo "Request Sent!";
?>