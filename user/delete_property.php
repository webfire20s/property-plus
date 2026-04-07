<?php
require '../includes/auth_check.php';
require '../config/db.php';

$id = $_GET['id'];

// Verify ownership
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);

if ($stmt->rowCount() == 0) {
    die("Unauthorized");
}

// Delete
$pdo->prepare("DELETE FROM properties WHERE id=?")->execute([$id]);

echo "Deleted";