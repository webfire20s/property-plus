<?php
require 'auth.php';
require '../config/db.php';

$id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("SELECT * FROM payment_screenshots WHERE id=?");
$stmt->execute([$id]);
$payment = $stmt->fetch();

if (!$payment) {
    die("Invalid payment");
}

// ✅ Approve payment
$pdo->prepare("UPDATE payment_screenshots SET status='approved' WHERE id=?")
    ->execute([$id]);

// ✅ HANDLE TYPES
if ($payment['type'] == 'registration') {

    $pdo->prepare("UPDATE users SET status='active' WHERE id=?")
        ->execute([$payment['user_id']]);

} elseif ($payment['type'] == 'membership') {

    $plan_id = $payment['plan_id'];
    $user_id = $payment['user_id'];

    // Get plan
    $stmt = $pdo->prepare("SELECT * FROM memberships WHERE id=?");
    $stmt->execute([$plan_id]);
    $plan = $stmt->fetch();

    if (!$plan) {
        die("Plan not found");
    }

    // ✅ FIXED HERE
    $expiry = date('Y-m-d', strtotime("+{$plan['duration_days']} days"));

    // Expire old memberships
    $pdo->prepare("
        UPDATE user_memberships 
        SET status='expired' 
        WHERE user_id=?
    ")->execute([$user_id]);

    // Insert new membership
    $pdo->prepare("
        INSERT INTO user_memberships 
        (user_id, membership_id, start_date, expiry_date, status)
        VALUES (?, ?, CURDATE(), ?, 'active')
    ")->execute([$user_id, $plan_id, $expiry]);
}
header("Location: payment_verifications.php");