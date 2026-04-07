<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

$id = $_GET['id'];
if (!$request || $request['user_id'] != $_SESSION['user_id']) {
    die("Unauthorized");
}

$pdo->prepare("UPDATE access_requests SET status='rejected' WHERE id=?")
    ->execute([$id]);

echo "Rejected";