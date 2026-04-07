<?php
require 'auth.php';
require '../config/db.php';

echo "<h2>Admin Dashboard</h2>";

// Stats
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProperties = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM properties WHERE status='pending'")->fetchColumn();
$totalPayments = $pdo->query("SELECT COUNT(*) FROM payments")->fetchColumn();

echo "Users: $totalUsers<br>";
echo "Properties: $totalProperties<br>";
echo "Pending Properties: $pending<br>";
echo "Payments: $totalPayments<br><hr>";

echo "
<a href='properties.php'>Manage Properties</a> |
<a href='users.php'>Manage Users</a> |
<a href='payments.php'>View Payments</a>
";