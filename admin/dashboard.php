<?php
require 'auth.php';
require '../config/db.php';

// SQL Logic remains untouched
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$activeUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
$totalProperties = $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn();
$approvedProperties = $pdo->query("SELECT COUNT(*) FROM properties WHERE status='approved'")->fetchColumn();
$totalLeads = $pdo->query("SELECT COUNT(*) FROM contact_requests")->fetchColumn();
$acceptedLeads = $pdo->query("SELECT COUNT(*) FROM contact_requests WHERE status='accepted'")->fetchColumn();
$totalRevenue = $pdo->query("SELECT SUM(amount) FROM payments WHERE status='success'")->fetchColumn();
$totalRevenue = $totalRevenue ?: 0;

$conversion = ($totalLeads > 0) ? ($acceptedLeads / $totalLeads) * 100 : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f1f5f9; 
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e2e8f0;
            height: 100%;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .bg-soft-blue { background: #eff6ff; color: #2563eb; }
        .bg-soft-green { background: #f0fdf4; color: #16a34a; }
        .bg-soft-purple { background: #faf5ff; color: #9333ea; }
        .bg-soft-gold { background: #fffbeb; color: #d97706; }

        .stat-value { font-size: 1.75rem; font-weight: 800; color: #0f172a; }
        .stat-label { color: #64748b; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        .insight-pill {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            border-left: 4px solid #2563eb;
            margin-bottom: 10px;
        }

        /* Responsive adjustment for the content area */
        .main-content {
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 offset-md-2 main-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">System Overview</h2>
                <span class="badge bg-white text-dark border p-2 px-3 rounded-pill fw-600">
                    <i class="fa-solid fa-calendar me-1"></i> <?= date('d M, Y') ?>
                </span>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-green"><i class="fa-solid fa-wallet"></i></div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">₹<?= number_format($totalRevenue) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-blue"><i class="fa-solid fa-user-check"></i></div>
                        <div class="stat-label">Active Users</div>
                        <div class="stat-value"><?= $activeUsers ?> <small class="text-muted" style="font-size: 0.9rem;">/ <?= $totalUsers ?></small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-gold"><i class="fa-solid fa-house-lock"></i></div>
                        <div class="stat-label">Approved Listings</div>
                        <div class="stat-value"><?= $approvedProperties ?> <small class="text-muted" style="font-size: 0.9rem;">/ <?= $totalProperties ?></small></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-purple"><i class="fa-solid fa-bolt"></i></div>
                        <div class="stat-label">Accepted Leads</div>
                        <div class="stat-value"><?= $acceptedLeads ?></div>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-3">Priority Insights</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="insight-pill d-flex justify-content-between align-items-center shadow-sm">
                        <span class="text-secondary fw-600">Lead Conversion</span>
                        <span class="fw-bold text-dark"><?= round($conversion, 2) ?>%</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="insight-pill d-flex justify-content-between align-items-center shadow-sm" style="border-left-color: #f59e0b;">
                        <span class="text-secondary fw-600">Pending Approvals</span>
                        <span class="fw-bold text-warning"><?= $totalProperties - $approvedProperties ?> Listings</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="insight-pill d-flex justify-content-between align-items-center shadow-sm" style="border-left-color: #ef4444;">
                        <span class="text-secondary fw-600">Inactive Accounts</span>
                        <span class="fw-bold text-danger"><?= $totalUsers - $activeUsers ?> Users</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>