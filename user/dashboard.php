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



<style>
    .dash-container { padding: 40px 0; }
    .welcome-card {
        background: #0f172a;
        background-image: radial-gradient(circle at 20% 150%, #1e293b 0%, #0f172a 100%);
        color: white;
        border-radius: 28px;
        padding: 40px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }
    .stat-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 24px;
        padding: 30px;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .plan-badge {
        background: rgba(46, 202, 106, 0.1);
        color: #2eca6a;
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 800;
        display: inline-flex;
        margin-bottom: 15px;
    }
    .btn-action-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        color: #0f172a;
        border-radius: 20px;
        padding: 25px 15px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        display: block;
        transition: 0.3s;
    }
    .btn-action-card:hover {
        background: #2eca6a;
        color: white !important;
        transform: translateY(-5px);
    }
    .btn-action-card i { font-size: 2.5rem; margin-bottom: 15px; display: block; }
</style>

<div class="container dash-container" style="margin-top: 100px;">
    
    <div class="welcome-card shadow-lg d-flex justify-content-between align-items-center" data-aos="fade-up">
        <div>
            <span style="color: #2eca6a;" class="fw-bold small text-uppercase">Overview</span>
            <h1 class="fw-extrabold mb-2" style="font-weight: 800; color: #fff;">User Dashboard</h1>
            <p class="mb-0 opacity-75">Welcome back! Manage your property portfolio and leads.</p>
        </div>
        <div class="d-none d-md-block">
            <i class="bi bi-grid-3x3-gap-fill opacity-25" style="font-size: 4rem;"></i>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4 col-md-5" data-aos="fade-right">
            <div class="stat-card shadow-sm">
                <h6 class="fw-bold text-secondary mb-4 text-uppercase small">Account Status</h6>
                
                <?php if ($membership && strtotime($membership['expiry_date']) >= time()): ?>
                    <div class="plan-badge">
                        <i class="bi bi-patch-check-fill me-2"></i><?php echo $membership['plan_name']; ?>
                    </div>
                    <h3 class="fw-bold mb-1">Active Plan</h3>
                    <p class="text-secondary small mb-4">
                        <i class="bi bi-calendar-event me-2"></i>Valid until: <b><?php echo date("d M, Y", strtotime($membership['expiry_date'])); ?></b>
                    </p>
                    <a href="membership.php" class="btn btn-success w-100 rounded-pill py-2 fw-bold" style="background:#2eca6a; border:none;">Manage Plan</a>
                
                <?php else: ?>
                    <div class="badge bg-light text-danger p-2 mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Inactive
                    </div>
                    <h3 class="fw-bold mb-2">No Active Plan</h3>
                    <p class="text-secondary small mb-4">Upgrade to list properties and access verified leads.</p>
                    <a href='membership.php' class="btn btn-warning w-100 fw-bold py-2 rounded-pill">Get Premium Access</a>
                <?php endif; ?>
                
                <hr class="my-4 opacity-50">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-light p-3">
                            <i class="bi bi-shield-lock text-dark"></i>
                        </div>
                    </div>
                    <div class="ms-3">
                        <p class="small mb-0 text-secondary">Security Status</p>
                        <p class="fw-bold mb-0">Verified Profile</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-7" data-aos="fade-left">
            <div class="row g-3">
                <div class="col-sm-6">
                    <a href="add_property.php" class="btn-action-card">
                        <i class="bi bi-plus-circle text-primary"></i>
                        <span>List New Property</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="my_properties.php" class="btn-action-card">
                        <i class="bi bi-houses text-success"></i>
                        <span>My Listings</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="my_requests.php" class="btn-action-card">
                        <i class="bi bi-send text-info"></i>
                        <span>Outgoing Requests</span>
                    </a>
                </div>
                <div class="col-sm-6">
                    <a href="requests.php" class="btn-action-card">
                        <i class="bi bi-inbox text-warning"></i>
                        <span>Inbound Requests</span>
                    </a>
                </div>
                <div class="col-12">
                    <a href="profile.php" class="btn-action-card d-flex align-items-center justify-content-center py-4">
                        <i class="bi bi-person-gear me-3 mb-0 text-secondary" style="font-size: 1.5rem;"></i>
                        <span>Account & Security Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// 2. Include the new footer
include('../includes/footer.php'); 
?>