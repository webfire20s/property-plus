<?php
require '../config/db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) die("Login required");

$stmt = $pdo->prepare("SELECT * FROM payment_screenshots WHERE user_id=? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user_id]);
$payment = $stmt->fetch();

?>

<div class="container py-5">
    <h3>Payment Status</h3>
    
    <?php if ($payment): ?>
        <p>Status: <strong><?= ucfirst($payment['status']) ?></strong></p>
        
        if ($payment && $payment['status'] == 'approved') {
            echo "<script>
                alert('Payment Approved! You can now access your dashboard.');
                window.location.href = 'dashboard.php';
            </script>";
            exit;
        }
        <?php if ($payment['status']=='rejected'): ?>
            <p class="text-danger">Reason: <?= $payment['rejection_reason'] ?></p>
        <?php endif; ?>

    <?php else: ?>
        <p>No payment found</p>
    <?php endif; ?>
</div>