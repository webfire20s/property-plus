<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    die("Access Denied");
}
// ✅ Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require '../config/db.php';

// Safety check
if (!isset($_SESSION['user_id'])) {
    die("User not logged in");
}

$stmt = $pdo->prepare("
    SELECT um.*, m.property_limit 
    FROM user_memberships um
    JOIN memberships m ON um.membership_id = m.id
    WHERE um.user_id = ? AND um.status = 'active'
    ORDER BY um.id DESC LIMIT 1
");

$stmt->execute([$_SESSION['user_id']]);
$membership = $stmt->fetch();

// Expiry check
if (!$membership || strtotime($membership['expiry_date']) < time()) {
    die("No active membership. Please purchase a plan.");
}


if ($membership && strtotime($membership['expiry_date']) < time()) {

    $pdo->prepare("UPDATE user_memberships SET status='expired' WHERE id=?")
        ->execute([$membership['id']]);

    die("Membership expired. Please renew.");
}
?>