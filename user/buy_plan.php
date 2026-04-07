<?php
require '../includes/auth_check.php';
require '../config/db.php';
// We don't include navbar here because this is a transitional processing page

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$plan_id = $_GET['id'];

// Get plan (Logic Untouched)
$stmt = $pdo->prepare("SELECT * FROM memberships WHERE id=?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    die("Invalid Plan");
}

// Create payment entry (Logic Untouched)
$txn_id = "TXN" . time() . rand(1000,9999);

$stmt = $pdo->prepare("
    INSERT INTO payments (user_id, amount, type, txn_id, plan_id, status)
    VALUES (?, ?, 'membership', ?, ?, 'pending')
");

$stmt->execute([
    $_SESSION['user_id'],
    $plan['price'],
    $txn_id,
    $plan_id
]);

// --- PROFESSIONAL REDIRECT UI ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Securing Connection | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loader-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            max-width: 400px;
            width: 90%;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f1f5f9;
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .secure-badge {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
        }
    </style>
    <meta http-equiv="refresh" content="2;url=payment_page.php?txn_id=<?= $txn_id ?>&plan_id=<?= $plan_id ?>">
</head>
<body>

<div class="loader-card">
    <div class="spinner"></div>
    <h4 class="fw-bold mb-2">Securing Connection</h4>
    <p class="text-secondary small mb-4">Please wait while we redirect you to our secure payment partner...</p>
    
    <div class="pt-3 border-top">
        <div class="secure-badge">
            <i class="fa-solid fa-shield-halved me-1"></i> 256-Bit SSL Secured
        </div>
    </div>
</div>

</body>
</html>