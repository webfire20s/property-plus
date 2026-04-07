<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

$id = $_GET['id'];

// Get request + property owner
$stmt = $pdo->prepare("
    SELECT ar.*, p.user_id 
    FROM access_requests ar
    JOIN properties p ON ar.property_id = p.id
    WHERE ar.id = ?
");
$stmt->execute([$id]);
$request = $stmt->fetch();

// ❌ If not owner → block
if (!$request || $request['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized");
}

// Approve
$pdo->prepare("UPDATE access_requests SET status='approved' WHERE id=?")
    ->execute([$id]);

echo "Approved";