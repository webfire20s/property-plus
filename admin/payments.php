<?php
require 'auth.php';
require '../config/db.php';

// Logic remains untouched
$stmt = $pdo->query("
    SELECT 
        p.*,
        u.phone
    FROM payments p
    JOIN users u ON p.user_id = u.id
    ORDER BY p.id DESC
");

$payments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Logs | PropertyPlus Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .main-content { padding: 40px; }
        
        .main-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
            overflow: hidden;
        }

        .table thead th { 
            background: #f8fafc; 
            color: #64748b; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 18px 20px;
        }

        .txn-id {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 600;
            color: #475569;
            font-size: 0.85rem;
        }

        .amount-text { font-weight: 800; color: #0f172a; }

        /* Payment Status Styles */
        .status-success { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-failed { background: #fee2e2; color: #991b1b; }

        .status-pill {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 10px;
            display: inline-block;
        }

        .type-badge {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 4px;
            background: #e0e7ff;
            color: #4338ca;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 offset-md-2 main-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Payment Transactions</h2>
                    <p class="text-secondary mb-0">Track all registration fees and membership subscriptions.</p>
                </div>
                <div class="d-flex gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block">Successful Revenue</small>
                        <span class="fw-bold text-success fs-5">
                            ₹<?= number_format(array_sum(array_column(array_filter($payments, fn($p) => $p['status'] === 'success'), 'amount'))) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="main-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Partner</th>
                                <th>Payment Details</th>
                                <th>Transaction ID</th>
                                <th>Status</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $p): ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold">#<?= $p['id'] ?></td>
                                <td>
                                    <div class="fw-600 text-dark"><?= htmlspecialchars($p['phone']) ?></div>
                                    <small class="text-muted">User ID: <?= $p['user_id'] ?></small>
                                </td>
                                <td>
                                    <div class="amount-text">₹<?= number_format($p['amount'], 2) ?></div>
                                    <span class="type-badge">
                                        <?= ($p['type'] == 'registration') ? 'Registration' : 'Membership' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="txn-id"><?= htmlspecialchars($p['txn_id']) ?></span>
                                </td>
                                <td>
                                    <?php 
                                        $s = strtolower($p['status']);
                                        $class = "status-$s";
                                    ?>
                                    <span class="status-pill <?= $class ?>">
                                        <i class="fa-solid <?= $s == 'success' ? 'fa-circle-check' : ($s == 'pending' ? 'fa-clock' : 'fa-circle-xmark') ?> me-1"></i>
                                        <?= ucfirst($s) ?>
                                    </span>
                                </td>
                                <td class="pe-4 text-secondary small fw-600">
                                    <?= date("d M Y", strtotime($p['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>