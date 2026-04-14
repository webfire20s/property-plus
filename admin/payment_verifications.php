<?php
require 'auth.php';
require '../config/db.php';

$stmt = $pdo->query("
    SELECT ps.*, u.business_name, u.phone 
    FROM payment_screenshots ps
    JOIN users u ON ps.user_id = u.id
    ORDER BY ps.id DESC
");

$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verifications | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        
        .main-content { padding: 40px; }
        
        .payment-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #e2e8f0; 
            transition: transform 0.2s, box-shadow 0.2s;
            overflow: hidden;
            height: 100%;
        }
        
        .payment-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px -5px rgba(0,0,0,0.1);
        }

        .screenshot-preview {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
        }

        .status-badge {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 5px 12px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .type-label {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(4px);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .btn-action {
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px;
        }

        /* Offset for sidebar */
        @media (min-width: 768px) {
            .main-content { margin-left: 16.666667%; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-800 text-dark mb-1">Payment Verifications</h2>
                    <p class="text-secondary mb-0">Review manual transaction proofs and update account statuses.</p>
                </div>
                <div>
                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                        Pending Reviews: <?= count(array_filter($payments, fn($p) => $p['status'] == 'pending')) ?>
                    </span>
                </div>
            </div>

            <div class="row">
                <?php foreach ($payments as $p): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="payment-card shadow-sm position-relative">
                        <span class="type-label"><?= ucfirst($p['type']) ?></span>
                        
                        <img src="../uploads/payments/<?= $p['screenshot'] ?>" 
                             class="screenshot-preview" 
                             alt="Payment Proof"
                             data-bs-toggle="modal" 
                             data-bs-target="#imgModal<?= $p['id'] ?>">

                        <div class="p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-800 text-dark mb-0"><?= htmlspecialchars($p['business_name']) ?></h6>
                                    <small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($p['phone']) ?></small>
                                </div>
                                <span class="status-badge 
                                    <?= $p['status']=='approved' ? 'bg-success bg-opacity-10 text-success' : ($p['status']=='rejected' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-warning bg-opacity-10 text-warning') ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </div>

                            <?php if ($p['status'] == 'rejected' && !empty($p['rejection_reason'])): ?>
                                <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3 small mb-3 border border-danger border-opacity-10">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    <strong>Reason:</strong> <?= htmlspecialchars($p['rejection_reason']) ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($p['status'] == 'pending'): ?>
                                <div class="d-flex gap-2 mt-3">
                                    <a href="approve_payment.php?id=<?= $p['id'] ?>" 
                                       class="btn btn-success btn-action w-50">
                                       <i class="fa-solid fa-check me-1"></i> Approve
                                    </a>
                                    <button class="btn btn-outline-danger btn-action w-50"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal<?= $p['id'] ?>">
                                        <i class="fa-solid fa-xmark me-1"></i> Reject
                                    </button>
                                </div>
                            <?php else: ?>
                                <button class="btn btn-light btn-action w-100 disabled text-muted mt-3">
                                    <i class="fa-solid fa-lock me-1"></i> Verification Complete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="imgModal<?= $p['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 bg-transparent">
                            <div class="modal-body p-0 text-center">
                                <img src="../uploads/payments/<?= $p['screenshot'] ?>" class="img-fluid rounded shadow-lg">
                                <button type="button" class="btn btn-light mt-3 rounded-pill px-4" data-bs-dismiss="modal">Close Preview</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="rejectModal<?= $p['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                            <form method="POST" action="reject_payment.php">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="fw-800">Reject Payment</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <p class="text-secondary small">Please provide a clear reason why this payment proof was rejected. This will be shown to the user.</p>
                                    <label class="form-label fw-700 small">Rejection Reason</label>
                                    <textarea name="reason" class="form-control border-light-subtle bg-light" rows="4" placeholder="e.g. Screenshot is blurry or transaction ID doesn't match." required></textarea>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light px-4 fw-700" data-bs-dismiss="modal">Cancel</button>
                                    <button class="btn btn-danger px-4 fw-700">Confirm Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>