<?php
require '../config/db.php';
require '../includes/auth_check.php';

$user_id = $_SESSION['user_id'];

// ✅ Get plan from session (set in buy_plan.php)
$plan_id = $_SESSION['membership_plan_id'] ?? null;

if (!$plan_id) {
    die("No plan selected");
}

// ✅ Fetch plan from DB
$stmt = $pdo->prepare("SELECT * FROM memberships WHERE id=?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch();

if (!$plan) {
    die("Invalid Plan");
}
$stmt = $pdo->prepare("
    SELECT * FROM user_memberships 
    WHERE user_id=? AND status='active' 
    ORDER BY id DESC LIMIT 1
");
$stmt->execute([$user_id]);
$activeMembership = $stmt->fetch();

if ($activeMembership && strtotime($activeMembership['expiry_date']) >= time()) {
    echo "<script>
        alert('You already have an active membership');
        window.location.href='dashboard.php';
    </script>";
    exit;
}
// ✅ Fetch existing payment
$stmt = $pdo->prepare("
    SELECT * FROM payment_screenshots 
    WHERE user_id=? AND type='membership' AND plan_id=?
    ORDER BY id DESC LIMIT 1
");
$stmt->execute([$user_id, $plan_id]);
$payment = $stmt->fetch();

// ✅ Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['screenshot'])) {
        die("No file uploaded");
    }

    $file = $_FILES['screenshot'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'membership_' . time() . '.' . $ext;

    move_uploaded_file($file['tmp_name'], "../uploads/payments/" . $filename);

    $pdo->prepare("
        INSERT INTO payment_screenshots (user_id, type, screenshot, plan_id, amount)
        VALUES (?, 'membership', ?, ?, ?)
    ")->execute([$user_id, $filename, $plan_id, $plan['price']]);
    


    header("Location: membership_payment.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Payment | Property Plus</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background: #f8fafc; font-family: 'Poppins', sans-serif; }
        
        .membership-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 500px;
            margin: 60px auto;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
        }

        /* Signature green top accent */
        .membership-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #2eca6a;
        }

        .qr-section {
            background: #fdfdfd;
            border: 2px dashed #e2e8f0;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .plan-price {
            background: rgba(46, 202, 106, 0.1);
            color: #2eca6a;
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 800;
            font-size: 1.4rem;
        }

        .status-box {
            border-radius: 12px;
            padding: 15px;
            font-weight: 600;
            margin-bottom: 20px;
            border: none;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
            border: 2px solid #f1f5f9;
        }

        .form-control:focus {
            border-color: #2eca6a;
            box-shadow: none;
        }

        .btn-pay {
            background: #2eca6a;
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            border: none;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-pay:hover {
            background: #25a556;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(46, 202, 106, 0.2);
        }
    </style>
</head>

<body>

<div class="container">
    <div class="membership-card">
        
        <div class="text-center mb-4">
            <span class="text-uppercase text-muted fw-bold small letter-spacing-1">Secure Checkout</span>
            <h3 class="fw-bold text-dark mt-1">Buy <?= htmlspecialchars($plan['name']) ?> Plan</h3>
        </div>

        <div class="text-center">
            <div class="qr-section">
                <p class="text-muted small fw-bold mb-3">SCAN TO PAY WITH ANY UPI APP</p>
                <img src="../assets/qr.jpeg" style="max-height:180px; width: auto;" class="mb-3 img-fluid rounded border">
                <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                    <span class="text-secondary small">UPI ID:</span>
                    <span class="fw-bold text-dark">yespay.smessi10062303@yesbankltd</span>
                    <i class="fa-regular fa-copy text-muted small cursor-pointer"></i>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="plan-price">₹<?= number_format($plan['price']) ?></div>
            </div>
        </div>

        <?php if ($payment): ?>
            <div class="status-box mb-4 alert 
                <?= $payment['status']=='approved' ? 'alert-success text-success' : ($payment['status']=='rejected' ? 'alert-danger text-danger' : 'alert-warning text-warning') ?>">
                
                <div class="d-flex align-items-center">
                    <i class="fa-solid <?= $payment['status']=='approved' ? 'fa-circle-check' : ($payment['status']=='rejected' ? 'fa-circle-xmark' : 'fa-clock') ?> me-2"></i>
                    <span>Status: <strong><?= ucfirst($payment['status']) ?></strong></span>
                </div>

                <?php if($payment['status']=='rejected'): ?>
                    <div class="mt-2 small border-top pt-2 mt-2" style="border-color: rgba(0,0,0,0.05)">
                        <strong>Rejection Reason:</strong> <?= htmlspecialchars($payment['rejection_reason']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <p class="text-muted small fw-bold mb-2">Uploaded Screenshot:</p>
                <img src="../uploads/payments/<?= $payment['screenshot'] ?>" class="img-fluid rounded border shadow-sm">
            </div>
        <?php endif; ?>

        <?php if (!$payment || $payment['status']=='rejected'): ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">Upload Payment Confirmation</label>
                    <input type="file" name="screenshot" class="form-control" required>
                    <div class="form-text mt-2 small">Please upload a clear screenshot of the successful transaction.</div>
                </div>
                <button type="submit" class="btn-pay w-100">
                    Submit Payment <i class="fa-solid fa-paper-plane ms-2"></i>
                </button>
            </form>
        <?php else: ?>

            <?php if ($payment['status'] == 'pending'): ?>
                <div class="alert alert-info border-0 text-center py-4" style="background: #eff6ff; border-radius: 15px;">
                    <i class="fa-solid fa-hourglass-half fa-2x text-primary mb-3"></i>
                    <p class="mb-0 fw-bold text-primary">Verification Pending</p>
                    <small class="text-secondary">Your request is being reviewed by the administration. You will be notified once activated.</small>
                </div>

            <?php elseif ($payment['status'] == 'approved'): ?>
                <div class="alert alert-success border-0 text-center py-4" style="background: #f0fdf4; border-radius: 15px;">
                    <i class="fa-solid fa-circle-check fa-2x text-success mb-3"></i>
                    <p class="mb-0 fw-bold text-success">Membership Active!</p>
                    <small class="text-secondary">Welcome to <?= htmlspecialchars($plan['name']) ?>. Enjoy your premium benefits.</small>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>
</div>

</body>
</html>