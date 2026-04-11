<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Fetch user's sent requests
$stmt = $pdo->prepare("
    SELECT cr.*, p.title 
    FROM contact_requests cr
    JOIN properties p ON cr.property_id = p.id
    WHERE cr.sender_id = ?
    ORDER BY cr.id DESC
");

$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Requests | Property Plus</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f8fafc;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.container-box {
    padding: 40px 0;
}

.card-request {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    padding: 20px;
    margin-bottom: 15px;
}

.status-pill {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-pending {
    background: #fff7ed;
    color: #c2410c;
}

.status-accepted {
    background: #ecfdf5;
    color: #065f46;
}

.status-rejected {
    background: #fef2f2;
    color: #991b1b;
}
</style>
</head>

<body>

<div class="container container-box">

    <h3 class="fw-bold mb-4">My Contact Requests</h3>

    <?php if (count($requests) > 0): ?>

        <?php foreach ($requests as $r): ?>

            <div class="card-request shadow-sm">

                <h5 class="fw-bold mb-2">
                    <?= htmlspecialchars($r['title']) ?>
                </h5>

                <p class="text-muted small mb-2">
                    Requested on: <?= date("d M Y", strtotime($r['created_at'])) ?>
                </p>

                <?php
                    $status = $r['status'] ?? 'pending';
                ?>

                <span class="status-pill status-<?= $status ?>">
                    <?= ucfirst($status) ?>
                </span>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="alert alert-light border">
            You haven't sent any requests yet.
        </div>

    <?php endif; ?>

</div>

</body>
</html>