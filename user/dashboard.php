<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->prepare("
    SELECT um.*, m.name as plan_name 
    FROM user_memberships um
    JOIN memberships m ON um.membership_id = m.id
    WHERE um.user_id = ? AND um.status='active'
    ORDER BY um.id DESC LIMIT 1
");

$stmt->execute([$_SESSION['user_id']]);
$membership = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-gold: #fbbf24;
            --brand-green: #16a34a;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .dash-container { padding: 50px 0; }
        
        .welcome-card {
            background: var(--slate-900);
            background-image: radial-gradient(circle at 20% 150%, #1e293b 0%, var(--slate-900) 100%);
            color: white;
            border-radius: 28px;
            padding: 40px;
            margin-bottom: 40px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .welcome-card::after {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: var(--brand-gold);
            filter: blur(80px);
            opacity: 0.15;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--slate-100);
            border-radius: 24px;
            padding: 30px;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
        }

        .plan-badge {
            background: rgba(22, 163, 74, 0.1);
            color: var(--brand-green);
            padding: 8px 18px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
        }

        .no-plan-badge {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 8px 18px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            text-transform: uppercase;
            margin-bottom: 15px;
        }

        .btn-action {
            background: #ffffff;
            border: 1px solid var(--slate-100);
            color: var(--slate-800);
            border-radius: 20px;
            padding: 25px 15px;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .btn-action:hover {
            background: var(--slate-900);
            color: white !important;
            border-color: var(--slate-900);
            transform: scale(1.02);
        }

        .btn-action i {
            transition: transform 0.3s;
        }

        .btn-action:hover i {
            transform: translateY(-3px);
            color: var(--brand-gold) !important;
        }

        .btn-upgrade {
            background-color: var(--brand-gold);
            color: var(--slate-900) !important;
            border: none;
            padding: 12px 28px;
            border-radius: 14px;
            font-weight: 800;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .btn-upgrade:hover {
            background-color: #f59e0b;
            box-shadow: 0 10px 15px rgba(251, 191, 36, 0.3);
        }

        .text-gold { color: var(--brand-gold); }
    </style>
</head>
<body>

<div class="container dash-container">
    <div class="welcome-card shadow-lg d-flex justify-content-between align-items-center">
        <div>
            <span class="text-gold fw-bold small text-uppercase letter-spacing-1">Overview</span>
            <h1 class="fw-extrabold mb-2" style="font-weight: 800; letter-spacing: -1px;">User Dashboard</h1>
            <p class="mb-0 opacity-75 fw-medium">Welcome back! Manage your property portfolio and network.</p>
        </div>
        <div class="d-none d-md-block">
            <i class="fa-solid fa-shapes fa-4x opacity-25"></i>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-md-5">
            <div class="stat-card">
                <h6 class="fw-800 text-secondary mb-4 text-uppercase small">Account Status</h6>
                
                <?php if ($membership && strtotime($membership['expiry_date']) >= time()): ?>
                    <div class="plan-badge">
                        <i class="fa-solid fa-crown me-2"></i><?php echo $membership['plan_name']; ?>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">Active Plan</h3>
                    <p class="text-secondary small mb-4">
                        <i class="fa-regular fa-calendar-check me-2"></i>Valid until: <b><?php echo date("d M, Y", strtotime($membership['expiry_date'])); ?></b>
                    </p>
                    <a href="membership.php" class="btn btn-dark w-100 rounded-pill py-2 fw-bold">Manage Plan</a>
                
                <?php else: ?>
                    <div class="no-plan-badge">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>Inactive
                    </div>
                    <h3 class="fw-bold mb-2">No Active Plan</h3>
                    <p class="text-secondary small mb-4">Upgrade to list properties and access verified lead contacts.</p>
                    <a href='membership.php' class="btn-upgrade w-100 text-center shadow-sm">Get Premium Access</a>
                <?php endif; ?>
                
                <hr class="my-4 opacity-50">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-light p-3">
                            <i class="fa-solid fa-user-shield text-slate-800"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="small mb-0 text-secondary">Verified Profile</p>
                        <p class="fw-bold mb-0">Identity Confirmed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-7">
            <div class="row g-3">
                <div class="col-sm-6">
                    <a href="add_property.php" class="btn-action">
                        <i class="fa-solid fa-circle-plus fa-3x mb-3 d-block text-primary"></i>
                        <span class="d-block">List New Property</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="my_properties.php" class="btn-action">
                        <i class="fa-solid fa-house-chimney-window fa-3x mb-3 d-block text-success"></i>
                        <span class="d-block">My Listings</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="my_requests.php" class="btn-action">
                        <i class="fa-solid fa-paper-plane fa-3x mb-3 d-block text-info"></i>
                        <span class="d-block">Outgoing Requests</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="requests.php" class="btn-action">
                        <i class="fa-solid fa-bell-concierge fa-3x mb-3 d-block text-warning"></i>
                        <span class="d-block">Inbound Requests</span>
                    </a>
                </div>
                <div class="col-12">
                    <a href="profile.php" class="btn-action d-flex align-items-center justify-content-center py-4">
                        <i class="fa-solid fa-user-gear fa-2x me-3 text-secondary"></i>
                        <span>Account & Security Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>