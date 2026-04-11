<?php
require '../config/db.php';

// Maintain exact logic and variable names
$user_id = $_GET['user_id'];
$txn_id = "REG" . time();

// Maintain exact database interaction
$pdo->prepare("
    INSERT INTO payments (user_id, amount, type, txn_id, status)
    VALUES (?, 1000, 'registration', ?, 'pending')
")->execute([$user_id, $txn_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Fee | EstateAgency</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --theme-green: #2eca6a;
            --theme-dark: #2b2b2b;
            --bg-light: #f7f7f7;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Poppins', sans-serif;
            color: var(--theme-dark);
        }

        .checkout-container {
            max-width: 500px;
            margin: 80px auto;
        }

        .payment-card {
            background: #ffffff;
            border: 1px solid #ebebeb;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .payment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--theme-green);
            border-radius: 24px 24px 0 0;
        }

        .fee-badge {
            background: rgba(46, 202, 106, 0.1);
            color: var(--theme-green);
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 20px;
        }

        .order-summary {
            background: #f9f9f9;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 30px;
            border: 1px dashed #d1d1d1;
        }

        .txn-text {
            font-family: monospace;
            color: #888;
            font-size: 0.85rem;
        }

        .btn-pay {
            background-color: var(--theme-green);
            color: white !important;
            border: none;
            border-radius: 12px;
            padding: 18px;
            width: 100%;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-pay:hover {
            background-color: #25a556;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 202, 106, 0.2);
        }

        .secure-note {
            text-align: center;
            margin-top: 20px;
            color: #aaa;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="container checkout-container">
    <div class="payment-card shadow-lg">
        <div class="text-center mb-4">
            <div class="fee-badge">Account Activation</div>
            <h3 class="fw-bold">Registration Fee</h3>
            <p class="txn-text">Ref: <?= $txn_id ?></p>
        </div>

        <div class="order-summary">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Service</span>
                <span class="fw-bold">Partner Onboarding</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary">Validity</span>
                <span class="fw-bold">Lifetime Access</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="h6 fw-bold mb-0">Total Amount</span>
                <span class="h4 fw-bold text-dark mb-0">₹1,000</span>
            </div>
        </div>

        <div class="mb-4">
            <div class="alert alert-light border d-flex align-items-center" style="font-size: 0.85rem;">
                <i class="fa-solid fa-circle-info text-success me-3 fa-lg"></i>
                <span>This is a one-time non-refundable fee for verified partner listing privileges.</span>
            </div>
        </div>

        <a href="registration_success.php?txn_id=<?= $txn_id ?>" class="btn-pay">
            Pay & Activate Account
        </a>

        <div class="secure-note">
            <i class="fa-solid fa-lock me-1"></i> SSL Encrypted Secure Transaction
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="../index.php" class="text-decoration-none text-muted small">
            <i class="fa-solid fa-arrow-left me-1"></i> Cancel and return home
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>