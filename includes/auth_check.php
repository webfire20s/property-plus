<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM users WHERE id=?");
$stmt->execute([$_SESSION['user_id']]);
$status = $stmt->fetchColumn();

if ($status == 'blocked') {
    session_destroy();
    die("Your account is blocked");
}

if ($status == 'pending' && basename($_SERVER['PHP_SELF']) != 'registration_payment.php') {
    header("Location: registration_payment.php");
    exit;
}