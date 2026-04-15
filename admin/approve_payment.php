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

    // ✅ Start transaction (VERY IMPORTANT)
    $pdo->beginTransaction();

    try {

        // ✅ Check existing active membership
        $stmt = $pdo->prepare("
            SELECT * FROM user_memberships 
            WHERE user_id=? AND status='active'
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([$user_id]);
        $existing = $stmt->fetch();

        // Decide start date
        if ($existing && strtotime($existing['expiry_date']) >= time()) {
            // Extend from current expiry
            $start_date = $existing['expiry_date'];
        } else {
            // Start from today
            $start_date = date('Y-m-d');
        }

        // Calculate expiry
        $expiry = date('Y-m-d', strtotime("$start_date +{$plan['duration_days']} days"));

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
            VALUES (?, ?, ?, ?, 'active')
        ")->execute([$user_id, $plan_id, $start_date, $expiry]);

        // ✅ Commit transaction
        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Membership activation failed");
    }
}
header("Location: payment_verifications.php");