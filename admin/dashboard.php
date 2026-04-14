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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }

        /* Stat Cards */
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            border: 1px solid #e2e8f0;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: #2eca6a;
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        /* Theme Specific Colors */
        .bg-soft-green { background: rgba(46, 202, 106, 0.1); color: #2eca6a; }
        .bg-soft-blue { background: #eff6ff; color: #3b82f6; }
        .bg-soft-purple { background: #faf5ff; color: #a855f7; }
        .bg-soft-gold { background: #fffbeb; color: #f59e0b; }

        .stat-value { 
            font-size: 2rem; 
            font-weight: 800; 
            color: #0f172a; 
            line-height: 1.2;
        }
        
        .stat-label { 
            color: #64748b; 
            font-size: 0.75rem; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .insight-pill {
            background: white;
            padding: 20px;
            border-radius: 16px;
            border-left: 5px solid #2eca6a;
            margin-bottom: 15px;
            border-top: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            transition: 0.2s;
        }

        .insight-pill:hover {
            background: #fdfdfd;
        }

        .main-content {
            padding: 40px;
            min-height: 100vh;
        }

        .page-header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 20px;
            margin-bottom: 35px;
        }

        .calendar-badge {
            background: #fff;
            color: #475569;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            padding: 8px 16px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 offset-md-2 main-content">
            
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-800 mb-1">System Overview</h2>
                    <p class="text-muted small mb-0">Welcome back, Administrator. Here's what's happening today.</p>
                </div>
                <div class="calendar-badge shadow-sm">
                    <i class="fa-solid fa-calendar-day me-2 text-success"></i> <?= date('d M, Y') ?>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-green"><i class="fa-solid fa-indian-rupee-sign"></i></div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">₹<?= number_format($totalRevenue) ?></div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-blue"><i class="fa-solid fa-users"></i></div>
                        <div class="stat-label">Active Users</div>
                        <div class="stat-value"><?= $activeUsers ?> <span class="text-muted fw-light" style="font-size: 1rem;">/ <?= $totalUsers ?></span></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-gold"><i class="fa-solid fa-building-circle-check"></i></div>
                        <div class="stat-label">Approved Listings</div>
                        <div class="stat-value"><?= $approvedProperties ?> <span class="text-muted fw-light" style="font-size: 1rem;">/ <?= $totalProperties ?></span></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card shadow-sm">
                        <div class="stat-icon bg-soft-purple"><i class="fa-solid fa-chart-line"></i></div>
                        <div class="stat-label">Accepted Leads</div>
                        <div class="stat-value"><?= $acceptedLeads ?></div>
                    </div>
                </div>
            </div>

            <h4 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-bolt me-2 text-warning"></i>Priority Insights</h4>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="insight-pill d-flex justify-content-between align-items-center shadow-sm">
                        <div>
                            <div class="small text-uppercase fw-700 text-muted mb-1" style="font-size: 0.65rem;">Lead Conversion</div>
                            <span class="fw-bold text-dark fs-5"><?= round($conversion, 2) ?>% Rate</span>
                        </div>
                        <i class="fa-solid fa-arrow-trend-up text-success fs-4"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="insight-pill d-flex justify-content-between align-items-center shadow-sm" style="border-left-color: #f59e0b;">
                        <div>
                            <div class="small text-uppercase fw-700 text-muted mb-1" style="font-size: 0.65rem;">Pending Approvals</div>
                            <span class="fw-bold text-dark fs-5"><?= $totalProperties - $approvedProperties ?> Listings</span>
                        </div>
                        <i class="fa-solid fa-clock-rotate-left text-warning fs-4"></i>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="insight-pill d-flex justify-content-between align-items-center shadow-sm" style="border-left-color: #ef4444;">
                        <div>
                            <div class="small text-uppercase fw-700 text-muted mb-1" style="font-size: 0.65rem;">Inactive Accounts</div>
                            <span class="fw-bold text-dark fs-5"><?= $totalUsers - $activeUsers ?> Users</span>
                        </div>
                        <i class="fa-solid fa-user-slash text-danger fs-4"></i>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>