<?php
require '../includes/auth_check.php';
require '../config/db.php';

$id = $_GET['id'];

// Get request + property owner
$stmt = $pdo->prepare("
    SELECT cr.*, p.user_id 
    FROM contact_requests cr
    JOIN properties p ON cr.property_id = p.id
    WHERE cr.id = ?
");
$stmt->execute([$id]);
$request = $stmt->fetch();

// सुरक्षा check
if (!$request || $request['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized");
}

// ❌ Reject
$pdo->prepare("UPDATE contact_requests SET status='rejected' WHERE id=?")
    ->execute([$id]);

header("Location: requests.php");