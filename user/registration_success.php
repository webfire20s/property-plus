<?php
require '../config/db.php';

$txn_id = $_GET['txn_id'];

// Get payment
$stmt = $pdo->prepare("SELECT * FROM payments WHERE txn_id=?");
$stmt->execute([$txn_id]);
$payment = $stmt->fetch();

if (!$payment) {
    die("Invalid Transaction");
}

// Mark success
$pdo->prepare("UPDATE payments SET status='success' WHERE txn_id=?")
    ->execute([$txn_id]);

// ✅ Activate user
$pdo->prepare("UPDATE users SET status='active' WHERE id=?")
    ->execute([$payment['user_id']]);

// Auto login
session_start();
$_SESSION['user_id'] = $payment['user_id'];

header("Location: dashboard.php");