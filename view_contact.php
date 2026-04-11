<?php
require 'config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'];

// Check limit
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_views WHERE user_id=?");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

if ($count >= 2) {
    die("Contact view limit reached");
}

// Prevent duplicate
$check = $pdo->prepare("
    SELECT * FROM contact_views 
    WHERE user_id=? AND property_id=?
");
$check->execute([$user_id, $property_id]);

if ($check->rowCount() == 0) {
    $pdo->prepare("
        INSERT INTO contact_views (user_id, property_id)
        VALUES (?, ?)
    ")->execute([$user_id, $property_id]);
}

// Fetch property contact
$stmt = $pdo->prepare("
    SELECT u.phone 
    FROM properties p
    JOIN users u ON p.user_id = u.id
    WHERE p.id=?
");
$stmt->execute([$property_id]);
$contact = $stmt->fetchColumn();

echo "Contact: $contact";