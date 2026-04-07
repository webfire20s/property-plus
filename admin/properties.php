<?php
require 'auth.php';
require '../config/db.php';

$stmt = $pdo->query("SELECT * FROM properties");
$properties = $stmt->fetchAll();

foreach ($properties as $p) {

    echo "<h3>{$p['title']}</h3>";
    echo "Status: {$p['status']}<br>";

    if ($p['status'] == 'pending') {
        echo "<a href='approve.php?id={$p['id']}'>Approve</a> | ";
        echo "<a href='reject.php?id={$p['id']}'>Reject</a>";
    }

    echo "<hr>";
}