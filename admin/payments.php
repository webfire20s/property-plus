<?php
require 'auth.php';
require '../config/db.php';

$stmt = $pdo->query("SELECT * FROM payments");
$payments = $stmt->fetchAll();

foreach ($payments as $p) {
    echo "Txn: {$p['txn_id']} | Amount: {$p['amount']} | Status: {$p['status']}<br>";
}