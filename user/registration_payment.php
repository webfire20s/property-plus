<?php
require '../config/db.php';

$user_id = $_GET['user_id'];

$txn_id = "REG" . time();

$pdo->prepare("
    INSERT INTO payments (user_id, amount, type, txn_id, status)
    VALUES (?, 1000, 'registration', ?, 'pending')
")->execute([$user_id, $txn_id]);

echo "<h2>Pay ₹1000 Registration Fee</h2>";
echo "<a href='registration_success.php?txn_id=$txn_id'>Simulate Payment</a>";