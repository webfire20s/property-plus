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



<style>
    body { 
        background-color: #f7f7f7; 
        font-family: 'Poppins', sans-serif; 
    }
    
    .success-card {
        background: #ffffff;
        border: 1px solid #ebebeb;
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
        max-width: 550px;
        margin: 100px auto;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.05);
    }

    .check-icon {
        width: 90px;
        height: 90px;
        background: rgba(46, 202, 106, 0.1); /* Template Green Opacity */
        color: #2eca6a; /* Template Primary Green */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 30px;
        animation: scaleUp 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes scaleUp {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .txn-details {
        background: #f9f9f9;
        border-radius: 15px;
        padding: 20px;
        margin: 30px 0;
        font-size: 0.95rem;
        color: #555;
        border: 1px dashed #d1d1d1;
    }

    .btn-dashboard {
        background-color: #2eca6a;
        color: white !important;
        border-radius: 10px;
        padding: 15px 35px;
        font-weight: 700;
        text-decoration: none;
        display: inline-block;
        transition: 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
    }

    .btn-dashboard:hover {
        background-color: #000;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .invoice-link {
        color: #2eca6a;
        font-weight: 600;
        transition: 0.3s;
    }
    
    .invoice-link:hover {
        color: #000;
    }
</style>

<div class="container" data-aos="zoom-in">
    <div class="success-card">
        <div class="check-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        
        <h2 class="fw-bold text-dark mb-3">Payment Successful!</h2>
        <p class="text-secondary mb-4">
            Your <b><?php echo htmlspecialchars($plan['name']); ?></b> membership is now active. 
            Welcome to the premium community of <b>PropertyPlus</b>.
        </p>

        <div class="txn-details text-start">
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Transaction ID</span>
                <span class="fw-bold text-dark"><?php echo $txn_id; ?></span>
            </div>
            <div class="d-flex justify-content-between mb-3">
                <span class="text-muted">Activation Date</span>
                <span class="fw-bold text-dark"><?php echo date("d M, Y"); ?></span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Expiry Date</span>
                <span class="fw-bold text-success"><?php echo date("d M, Y", strtotime($expiry)); ?></span>
            </div>
        </div>

        <div class="d-grid gap-3">
            <a href="dashboard.php" class="btn-dashboard">
                <i class="fa-solid fa-house-user me-2"></i> Go to Dashboard
            </a>
            
            <a href="invoice.php?txn=<?php echo $txn_id; ?>" class="invoice-link small text-decoration-none">
                <i class="fa-solid fa-file-arrow-down me-1"></i> Download Payment Receipt
            </a>
        </div>
    </div>
</div>

<?php 
// 2. Include the footer (Already part of your template structure)
include('../includes/footer.php'); 
?>