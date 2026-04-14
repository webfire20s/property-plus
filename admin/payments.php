<?php
require 'auth.php';
require '../config/db.php';

// Logic remains untouched
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.user_id,
        u.phone,
        p.amount,
        p.type,
        p.txn_id,
        p.status,
        p.created_at,
        'razorpay' as source,
        NULL as screenshot,
        p.plan_id
    FROM payments p
    JOIN users u ON p.user_id = u.id

    UNION ALL

        SELECT 
        ps.id,
        ps.user_id,
        u.phone,
        ps.amount,
        ps.type,
        NULL as txn_id,
        ps.status,
        ps.created_at,
        'qr' as source,
        ps.screenshot,
        ps.plan_id
    FROM payment_screenshots ps
    JOIN users u ON ps.user_id = u.id

    ORDER BY created_at DESC
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        
        .main-content { padding: 40px; }
        
        .main-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); 
            overflow: hidden;
            padding: 20px;
        }

        /* Table Styling */
        .table thead th { 
            background: #f1f5f9; 
            color: #475569; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 1.2px; 
            padding: 18px 20px;
            font-weight: 800;
            border-bottom: 2px solid #e2e8f0;
        }

        .table tbody td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .txn-id {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 700;
            color: #475569;
            font-size: 0.8rem;
        }

        .amount-text { font-weight: 800; color: #0f172a; font-size: 1rem; }

        /* Payment Status Pills */
        .status-pill {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
        }
        .status-success, .status-approved { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .status-failed, .status-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        .type-badge {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 6px;
            background: #eff6ff;
            color: #2563eb;
            text-transform: uppercase;
            display: inline-block;
            margin-top: 4px;
        }

        /* Custom DataTables UI */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 15px;
            margin-bottom: 15px;
            outline: none;
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
                    <h2 class="fw-800 text-dark mb-1">Payment Transactions</h2>
                    <p class="text-secondary mb-0">Track all registration fees and membership subscriptions.</p>
                </div>
                <div class="text-end">
                    <div class="bg-white p-3 rounded-4 shadow-sm border border-light-subtle">
                        <small class="text-muted d-block fw-600 mb-1">Total Verified Revenue</small>
                        <span class="fw-800 text-success fs-4">
                            ₹<?= number_format(array_sum(array_column(
                                array_filter($payments, fn($p) => 
                                    in_array(strtolower($p['status']), ['success', 'approved'])
                                ), 
                                'amount'
                            ))) ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="main-card">
                <div class="table-responsive">
                    <table id="paymentsTable" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Partner Details</th>
                                <th>Billing</th>
                                <th>Transaction Ref</th>
                                <th>Status</th>
                                <th class="pe-4">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="text-muted fw-bold">#<?= $p['id'] ?></span>
                                </td>
                                <td>
                                    <div class="fw-700 text-dark"><?= htmlspecialchars($p['phone']) ?></div>
                                    <small class="text-muted">UID: <?= $p['user_id'] ?></small>
                                </td>
                                <td>
                                    <div class="amount-text">₹<?= number_format($p['amount'], 2) ?></div>
                                    <span class="type-badge">
                                        <?= ucfirst($p['type']) ?>
                                    </span>
                                    <div class="text-muted" style="font-size: 0.7rem; margin-top: 2px;">
                                        <i class="fa-solid <?= $p['source'] == 'qr' ? 'fa-qrcode' : 'fa-globe' ?> me-1"></i>
                                        <?= $p['source'] == 'qr' ? 'Manual QR' : 'Online Gateway' ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($p['source'] == 'razorpay' || !empty($p['txn_id'])): ?>
                                        <span class="txn-id"><?= htmlspecialchars($p['txn_id']) ?></span>
                                    <?php else: ?>
                                        <a href="../uploads/payments/<?= $p['screenshot'] ?>" 
                                           target="_blank" 
                                           class="btn btn-sm btn-light border fw-700 px-3 py-1 rounded-pill">
                                            <i class="fa-solid fa-image me-1"></i> Proof
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                        $s = strtolower($p['status']);
                                        $label = ucfirst($s);
                                    ?>
                                    <span class="status-pill status-<?= $s ?>">
                                        <i class="fa-solid <?= $s == 'success' || $s == 'approved' ? 'fa-circle-check' : ($s == 'pending' ? 'fa-clock' : 'fa-circle-xmark') ?> me-2" style="font-size: 0.6rem;"></i>
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="text-dark fw-bold" style="font-size: 0.85rem;"><?= date("d M, Y", strtotime($p['created_at'])) ?></div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">
                                        <i class="fa-regular fa-clock me-1"></i><?= date("h:i A", strtotime($p['created_at'])) ?>
                                    </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#paymentsTable').DataTable({
            "pageLength": 10,
            "order": [[0, "desc"]], // Default sort by ID descending
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search transactions...",
                "paginate": {
                    "previous": "<i class='fa-solid fa-angle-left'></i>",
                    "next": "<i class='fa-solid fa-angle-right'></i>"
                }
            }
        });
    });
</script>
</body>
</html>