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
    <title>User Dashboard | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --blue-600: #2563eb;
            --emerald-500: #10b981;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        /* Dashboard Layout */
        .dash-container { padding: 40px 0; }
        
        .welcome-card {
            background: linear-gradient(135deg, var(--slate-900) 0%, #2c3e50 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            border: none;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            height: 100%;
            transition: transform 0.2s;
        }

        .plan-badge {
            background: rgba(16, 185, 129, 0.1);
            color: var(--emerald-500);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
            margin-bottom: 10px;
        }

        .no-plan-badge {
            background: rgba(244, 63, 94, 0.1);
            color: #f43f5e;
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-block;
        }

        .btn-action {
            background: white;
            border: 1px solid #e2e8f0;
            color: var(--slate-800);
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            display: block;
            transition: 0.2s;
        }

        .btn-action:hover {
            background: var(--slate-100);
            border-color: var(--slate-800);
        }

        .btn-upgrade {
            background-color: var(--blue-600);
            color: white !important;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container dash-container">
    <div class="welcome-card shadow-sm d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">User Dashboard</h2>
            <p class="mb-0 opacity-75 text-light">Welcome back! Manage your property listings and profile.</p>
        </div>
        <i class="fa-solid fa-circle-user fa-3x opacity-25"></i>
    </div>

    <div class="row g-4">
        <div class="col-md-5">
            <div class="stat-card shadow-sm">
                <h5 class="fw-bold mb-4">Account Status</h5>
                
                <?php if ($membership && strtotime($membership['expiry_date']) >= time()): ?>
                    <div class="plan-badge">
                        <i class="fa-solid fa-crown me-2"></i>Active Plan
                    </div>
                    <h3 class="fw-bold mb-1 text-dark"><?php echo $membership['plan_name']; ?></h3>
                    <p class="text-secondary small mb-4">
                        <i class="fa-regular fa-calendar-check me-2"></i>Expires on: <b><?php echo date("d M, Y", strtotime($membership['expiry_date'])); ?></b>
                    </p>
                    <a href="membership.php" class="btn btn-outline-dark btn-sm rounded-pill px-3">Upgrade / Renew</a>
                
                <?php else: ?>
                    <div class="no-plan-badge mb-3">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>No Active Membership
                    </div>
                    <p class="text-secondary small mb-4">Get a plan to start listing properties and viewing verified contacts.</p>
                    <a href='membership.php' class="btn-upgrade d-inline-block shadow-sm">Buy Plan</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-md-7">
            <div class="row g-3">
                <div class="col-6">
                    <a href="add_property.php" class="btn-action">
                        <i class="fa-solid fa-plus-circle fa-2x mb-2 d-block text-primary"></i>
                        Add Property
                    </a>
                </div>
                <div class="col-6">
                    <a href="my_properties.php" class="btn-action">
                        <i class="fa-solid fa-house-user fa-2x mb-2 d-block text-success"></i>
                        My Listings
                    </a>
                </div>
                <div class="col-6">
                    <a href="requests.php" class="btn-action">
                        <i class="fa-solid fa-envelope-open-text fa-2x mb-2 d-block text-warning"></i>
                        Requests
                    </a>
                </div>
                <div class="col-6">
                    <a href="profile.php" class="btn-action">
                        <i class="fa-solid fa-gears fa-2x mb-2 d-block text-secondary"></i>
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>