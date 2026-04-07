<?php
require 'auth.php';
require '../config/db.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();

foreach ($users as $u) {
    echo "ID: {$u['id']} | Phone: {$u['phone']} <br>";
}