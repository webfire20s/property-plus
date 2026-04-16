<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// ✅ Get membership (Logic strictly preserved)
$stmt = $pdo->prepare("
    SELECT m.name 
    FROM user_memberships um
    JOIN memberships m ON um.membership_id = m.id
    WHERE um.user_id=? AND um.status='active'
    ORDER BY um.id DESC LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$plan = $stmt->fetchColumn();

// CSS for the EstateAgency Theme
?>
<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }

    .container-box {
        padding: 120px 0 60px;
    }

    .section-header {
        border-left: 5px solid #2eca6a;
        padding-left: 15px;
        margin-bottom: 40px;
    }

    .member-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ebebeb;
        padding: 30px;
        height: 100%;
        transition: all 0.3s ease;
    }

    .member-card:hover {
        transform: translateY(-8px);
        border-color: #2eca6a;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .biz-name {
        color: #000;
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 5px;
    }

    .loc-text {
        color: #888;
        font-size: 0.85rem;
    }

    .detail-item {
        font-size: 0.9rem;
        margin-bottom: 8px;
        color: #444;
    }

    .detail-item strong {
        color: #000;
    }

    .upgrade-card {
        background: #fff;
        border-radius: 15px;
        padding: 60px 40px;
        text-align: center;
        border: 1px solid #ebebeb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .btn-upgrade {
        background: #2eca6a;
        color: #fff;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-upgrade:hover {
        background: #000;
        color: #fff;
    }
</style>

<div class="container container-box">

    <?php 
    // ❌ Block free users (Themed Output)
    if (!$plan || strtolower($plan) == 'listing'): ?>
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <div class="upgrade-card shadow-sm">
                    <div class="mb-4">
                        <i class="bi bi-lock-fill text-success opacity-25" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold mb-3">Premium Access Only</h3>
                    <p class="text-muted mb-4">
                        The Verified Members Directory is only available to our premium partners. 
                        Upgrade your plan to network with verified agents and businesses.
                    </p>
                    <a href="membership.php" class="btn-upgrade">View Upgrade Options</a>
                </div>
            </div>
        </div>
    <?php else: 

    // ✅ Fetch all user details (Logic strictly preserved)
    $stmt = $pdo->query("
        SELECT id, phone, business_name, state, district, rera_number, gst_number
        FROM users
        WHERE status='active'
        ORDER BY id DESC
    ");
    $users = $stmt->fetchAll();
    ?>

    <div class="section-header">
        <h3 class="fw-bold m-0">Verified Members Directory</h3>
        <p class="text-muted small mt-1">Directly connect with registered professionals in the industry.</p>
    </div>

    <div class="row g-4">
        <?php foreach($users as $u): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="member-card shadow-sm">
                    
                    <div class="biz-name text-uppercase">
                        <?= htmlspecialchars($u['business_name'] ?: 'Independent Partner') ?>
                    </div>

                    <div class="loc-text mb-3">
                        <i class="bi bi-geo-alt-fill text-success me-1"></i>
                        <?= htmlspecialchars($u['district']) ?>, <?= htmlspecialchars($u['state']) ?>
                    </div>

                    <hr class="opacity-25">

                    <div class="detail-item">
                        <strong><i class="bi bi-telephone-fill me-2 text-muted small"></i>Phone:</strong> 
                        <span class="ms-1"><?= htmlspecialchars($u['phone']) ?></span>
                    </div>

                    <div class="detail-item">
                        <strong><i class="bi bi-shield-check me-2 text-muted small"></i>RERA:</strong> 
                        <span class="ms-1"><?= !empty($u['rera_number']) ? htmlspecialchars($u['rera_number']) : '<span class="text-muted italic">N/A</span>' ?></span>
                    </div>

                    <div class="detail-item">
                        <strong><i class="bi bi-file-earmark-text me-2 text-muted small"></i>GST:</strong> 
                        <span class="ms-1"><?= !empty($u['gst_number']) ? htmlspecialchars($u['gst_number']) : '<span class="text-muted italic">N/A</span>' ?></span>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>