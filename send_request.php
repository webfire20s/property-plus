<?php
require 'config/db.php';
session_start();

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'];

// Check limit
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_requests WHERE sender_id=?");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

if ($count >= 5) {
    die("Contact request limit reached");
}

// Insert request
$pdo->prepare("
    INSERT INTO contact_requests (sender_id, property_id)
    VALUES (?, ?)
")->execute([$user_id, $property_id]);

echo "Request sent successfully";