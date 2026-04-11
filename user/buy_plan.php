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
<?php
// Preserving your existing logic for $txn_id and $plan_id
// Example: $txn_id = bin2hex(random_bytes(8)); $plan_id = $_GET['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Securing Connection | EstateAgency</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body { 
            background-color: #f7f7f7; 
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .loader-card {
            background: #ffffff;
            padding: 50px 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            max-width: 420px;
            width: 90%;
            border: 1px solid #ebebeb;
        }
        .spinner-container {
            position: relative;
            display: inline-block;
            margin-bottom: 25px;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #2eca6a; /* Theme Green */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 
            0% { transform: rotate(0deg); } 
            100% { transform: rotate(360deg); } 
        }
        .lock-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #2eca6a;
            font-size: 1.2rem;
        }
        .secure-badge {
            font-size: 0.7rem;
            color: #aaaaaa;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
        }
        h4 {
            color: #000000;
            font-weight: 700;
        }
        p {
            color: #555555;
            line-height: 1.6;
        }
    </style>
    <meta http-equiv="refresh" content="2;url=payment_page.php?txn_id=<?= $txn_id ?>&plan_id=<?= $plan_id ?>">
</head>
<body>

<div class="loader-card">
    <div class="spinner-container">
        <div class="spinner"></div>
        <i class="bi bi-shield-lock lock-icon"></i>
    </div>
    
    <h4>Securing Connection</h4>
    <p class="small mb-4">Please wait while we transfer you to our secure payment gateway. Do not refresh the page.</p>
    
    <div class="pt-4 border-top">
        <div class="secure-badge">
            <i class="bi bi-patch-check-fill me-1"></i> PCI-DSS Compliant Gateway
        </div>
    </div>
</div>

</body>
</html>