<?php
require '../config/db.php';
session_start();

// ✅ Secure session-based user
$user_id = $_SESSION['temp_user_id'] ?? null;

if (!$user_id) {
    die("Session expired. Please register again.");
}

// ✅ Fetch existing payment
$stmt = $pdo->prepare("
    SELECT * FROM payment_screenshots 
    WHERE user_id=? AND type='registration'
    ORDER BY id DESC LIMIT 1
");
$stmt->execute([$user_id]);
$payment = $stmt->fetch();

// ✅ Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_FILES['screenshot']) || $_FILES['screenshot']['error'] !== 0) {
        die("Upload failed");
    }

    $file = $_FILES['screenshot'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'pay_' . time() . '_' . rand(1000,9999) . '.' . $ext;

    $uploadPath = '../uploads/payments/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        die("File upload error");
    }

    $pdo->prepare("
        INSERT INTO payment_screenshots (user_id, type, screenshot, amount)
        VALUES (?, 'registration', ?, 1000)
    ")->execute([$user_id, $filename]);

    header("Location: registration_payment.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Fee | EstateAgency</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { background: #f8fafc; font-family: 'Poppins', sans-serif; }
        
        .payment-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            max-width: 500px;
            margin: 80px auto;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        /* Top accent bar matching your theme */
        .payment-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: #2eca6a;
        }

        .qr-wrapper {
            background: #fdfdfd;
            border: 2px dashed #e2e8f0;
            border-radius: 15px;
            padding: 20px;
        }

        .amount-tag {
            background: rgba(46, 202, 106, 0.1);
            color: #2eca6a;
            padding: 10px 20px;
            border-radius: 50px;
            display: inline-block;
            font-weight: 800;
            font-size: 1.5rem;
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

        .btn-submit {
            background: #2eca6a;
            color: white;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            border: none;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background: #25a556;
            transform: translateY(-2px);
        }

        .status-badge {
            border-radius: 12px;
            padding: 15px;
            font-weight: 600;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="payment-card shadow-lg" data-aos="fade-up">

        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark mb-1">Membership Verification</h4>
            <p class="text-secondary small">Complete your registration to unlock all features</p>
        </div>

        <div class="text-center mb-4">
            <div class="qr-wrapper mb-3">
                <p class="text-muted small fw-bold text-uppercase mb-2">Scan QR Code to Pay</p>
                <img src="../assets/qr.jpeg" style="max-height:180px; width: auto;" class="mb-3 img-fluid rounded">
                <div class="d-block mb-2">
                    <span class="text-secondary small">UPI ID:</span> 
                    <span class="fw-bold">yespay.smessi10062303@yesbankltd</span>
                </div>
            </div>
            <div class="amount-tag">₹1000</div>
        </div>

        <?php if ($payment): ?>
            <div class="status-badge mb-4 
                <?= $payment['status']=='approved' ? 'alert-success text-success' : ($payment['status']=='rejected' ? 'alert-danger text-danger' : 'alert-warning text-warning') ?>">
                
                <i class="fa-solid <?= $payment['status']=='approved' ? 'fa-circle-check' : ($payment['status']=='rejected' ? 'fa-circle-xmark' : 'fa-clock') ?> me-2"></i>
                Status: <strong><?= ucfirst($payment['status']) ?></strong>

                <?php if ($payment['status']=='rejected'): ?>
                    <div class="mt-2 small border-top pt-2" style="border-color: rgba(0,0,0,0.05)">
                        <strong>Reason:</strong> <?= htmlspecialchars($payment['rejection_reason']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="text-secondary small fw-bold mb-2">Submitted Screenshot:</label>
                <img src="../uploads/payments/<?= $payment['screenshot'] ?>" class="img-fluid rounded border shadow-sm" style="max-height: 150px; width: 100%; object-fit: cover;">
            </div>
        <?php endif; ?>

        <?php if (!$payment || $payment['status']=='rejected'): ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark small">Upload Payment Confirmation</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-camera text-muted"></i></span>
                        <input type="file" name="screenshot" class="form-control border-start-0 shadow-none" required>
                    </div>
                    <div class="form-text mt-2">Upload a clear screenshot of the transaction (JPG/PNG).</div>
                </div>

                <button class="btn-submit w-100 shadow-sm">
                    Submit Payment Details <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </form>

        <?php else: ?>

            <div class="alert alert-info border-0 text-center py-4" style="background: #eff6ff; border-radius: 15px;">
                <i class="fa-solid fa-hourglass-half fa-2x text-primary mb-3"></i>
                <p class="mb-0 fw-bold text-primary">Payment Under Review</p>
                <small class="text-secondary">Please wait while our team verifies your transaction. This usually takes 1-2 hours.</small>
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>