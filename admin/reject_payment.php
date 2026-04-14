<?php
require 'auth.php';
require '../config/db.php';

$id = $_POST['id'];
$reason = $_POST['reason'];

$pdo->prepare("
    UPDATE payment_screenshots 
    SET status='rejected', rejection_reason=? 
    WHERE id=?
")->execute([$reason, $id]);

header("Location: payment_verifications.php");