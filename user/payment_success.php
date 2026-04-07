<?php
require '../includes/auth_check.php';
require '../config/db.php';

// Validate input
if (!isset($_GET['txn_id'])) {
    die("Invalid Request");
}

$txn_id = $_GET['txn_id'];

// ✅ STEP 1: Fetch payment FIRST
$stmt = $pdo->prepare("SELECT * FROM payments WHERE txn_id=?");
$stmt->execute([$txn_id]);
$payment = $stmt->fetch();

// ❌ Invalid transaction
if (!$payment || $payment['user_id'] != $_SESSION['user_id']) {
    die("Invalid Transaction");
}

// ❌ Already processed
if ($payment['status'] == 'success') {
    die("Payment already processed");
}

// ❌ Invalid state
if ($payment['status'] != 'pending') {
    die("Invalid Payment State");
}

// ✅ STEP 2: Get plan_id FROM DB
$plan_id = $payment['plan_id'];

// Get plan
$stmt = $pdo->prepare("SELECT * FROM memberships WHERE id=?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    die("Invalid Plan");
}

// ✅ STEP 3: Mark payment success
$pdo->prepare("UPDATE payments SET status='success' WHERE txn_id=?")
    ->execute([$txn_id]);

// ✅ STEP 4: Expire old plans
$pdo->prepare("UPDATE user_memberships SET status='expired' WHERE user_id=?")
    ->execute([$_SESSION['user_id']]);

// ✅ STEP 5: Activate new membership
$start = date('Y-m-d');
// Note: Assuming duration_days exists in your DB, otherwise you can hardcode 365
$days = $plan['duration_days'] ?? 365; 
$expiry = date('Y-m-d', strtotime("+{$days} days"));

$pdo->prepare("
    INSERT INTO user_memberships (user_id, membership_id, start_date, expiry_date, status)
    VALUES (?, ?, ?, ?, 'active')
")->execute([$_SESSION['user_id'], $plan_id, $start, $expiry]);

// --- START OF PROFESSIONAL SUCCESS UI ---
include '../includes/navbar.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .success-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            max-width: 550px;
            margin: 80px auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .check-icon {
            width: 80px;
            height: 80px;
            background: #f0fdf4;
            color: #16a34a;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 25px;
            animation: scaleUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleUp {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        .txn-details {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            margin: 25px 0;
            font-size: 0.9rem;
            color: #475569;
        }

        .btn-dashboard {
            background-color: #0f172a;
            color: white !important;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: 0.2s;
        }

        .btn-dashboard:hover {
            background-color: #2563eb;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="success-card">
        <div class="check-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        
        <h2 class="fw-bold text-dark mb-2">Payment Successful!</h2>
        <p class="text-secondary">Your <b><?php echo htmlspecialchars($plan['name']); ?></b> membership is now active. You have full access to premium features.</p>

        <div class="txn-details text-start">
            <div class="d-flex justify-content-between mb-2">
                <span>Transaction ID:</span>
                <span class="fw-bold text-dark"><?php echo $txn_id; ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Activation Date:</span>
                <span class="fw-bold text-dark"><?php echo date("d M, Y"); ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Expiry Date:</span>
                <span class="fw-bold text-dark"><?php echo date("d M, Y", strtotime($expiry)); ?></span>
            </div>
        </div>

        <a href="dashboard.php" class="btn-dashboard">
            <i class="fa-solid fa-gauge-high me-2"></i> Go to Dashboard
        </a>
        
        <div class="mt-4">
            <a href="invoice.php?txn=<?php echo $txn_id; ?>" class="text-primary small fw-bold text-decoration-none">
                <i class="fa-solid fa-file-invoice me-1"></i> Download Invoice
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>