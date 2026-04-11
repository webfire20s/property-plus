<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$txn_id = htmlspecialchars($_GET['txn_id']);
$plan_id = htmlspecialchars($_GET['plan_id']);

// Fetch plan details for the summary
$stmt = $pdo->prepare("SELECT * FROM memberships WHERE id=?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout | EstateAgency</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f7f7f7; 
            font-family: 'Poppins', sans-serif;
            color: #2b2b2b;
        }

        .checkout-container {
            max-width: 500px;
            margin: 60px auto;
        }

        .payment-card {
            background: #ffffff;
            border: 1px solid #ebebeb;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .order-summary {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
        }

        .txn-badge {
            font-family: monospace;
            background: #e2e8f0;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.85rem;
            color: #0f172a;
        }

        .btn-pay {
            background-color: #2eca6a; /* Theme Green */
            color: white !important;
            border: none;
            border-radius: 12px;
            padding: 16px;
            width: 100%;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px 0 rgba(46, 202, 106, 0.3);
        }

        .btn-pay:hover {
            background-color: #25a556;
            transform: translateY(-2px);
        }

        .secure-footer {
            text-align: center;
            margin-top: 25px;
            color: #94a3b8;
            font-size: 0.85rem;
        }
        
        .method-active {
            border: 1px solid #2eca6a !important;
            background: #f0fff4;
        }
    </style>
</head>
<body>

<div class="container checkout-container">
    <div class="payment-card">
        <div class="text-center mb-4">
            <div class="mb-2">
                <i class="fa-solid fa-shield-halved text-success fa-3x"></i>
            </div>
            <h3 class="fw-bold">Secure Checkout</h3>
            <p class="text-muted small">Transaction Reference: <span class="txn-badge"><?= $txn_id ?></span></p>
        </div>

        <div class="order-summary">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Plan Selected</span>
                <span class="fw-bold"><?= htmlspecialchars($plan['name']) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Duration</span>
                <span class="fw-bold">1 Year</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="h6 fw-bold mb-0">Total Amount</span>
                <span class="h4 fw-bold text-dark mb-0">₹<?= number_format($plan['price']) ?></span>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-uppercase text-muted">Select Payment Method</label>
            <div class="border rounded-3 p-3 d-flex align-items-center mb-2 method-active">
                <i class="fa-solid fa-credit-card me-3 text-success"></i>
                <div class="flex-grow-1">
                    <div class="fw-bold small">Simulated Gateway</div>
                    <div class="text-muted" style="font-size: 0.75rem;">Instant Activation</div>
                </div>
                <i class="fa-solid fa-circle-check text-success"></i>
            </div>
        </div>

        <a href="payment_success.php?txn_id=<?= $txn_id ?>&plan_id=<?= $plan_id ?>" class="btn-pay">
            Complete Payment
        </a>

        <div class="secure-footer">
            <i class="fa-solid fa-lock me-1"></i> Your payment information is encrypted and secure.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>