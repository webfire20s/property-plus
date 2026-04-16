<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// Fetch user's sent requests
$stmt = $pdo->prepare("
    SELECT cr.*, p.title 
    FROM contact_requests cr
    JOIN properties p ON cr.property_id = p.id
    WHERE cr.sender_id = ?
    ORDER BY cr.id DESC
");

$stmt->execute([$user_id]);
$requests = $stmt->fetchAll();
?>



<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }

    .container-box {
        padding: 120px 0 60px; /* Adjusted top padding for fixed navbar compatibility */
    }

    .card-request {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ebebeb;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .card-request:hover {
        transform: translateX(8px);
        border-color: #2eca6a;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }

    .status-pill {
        padding: 6px 16px;
        border-radius: 5px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
    }

    /* Template color matching */
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-accepted {
        background: #2eca6a;
        color: #fff;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .req-title {
        color: #000;
        font-weight: 700;
        margin-bottom: 5px;
        font-size: 1.1rem;
        transition: 0.3s;
    }

    .card-request:hover .req-title {
        color: #2eca6a;
    }

    .req-date {
        color: #888;
        font-size: 0.85rem;
    }

    .section-header {
        border-left: 5px solid #2eca6a;
        padding-left: 15px;
    }

    .btn-browse {
        background: #2eca6a;
        color: #fff;
        border: none;
        transition: 0.3s;
        font-weight: 600;
    }

    .btn-browse:hover {
        background: #000;
        color: #fff;
    }
</style>

<div class="container container-box">

    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold m-0 section-header">
                My Contact Requests
            </h3>
            <p class="text-muted small mt-2 ps-3">Track the status of your inquiries for various properties.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-md-end bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="dashboard.php" class="text-success text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active">Requests</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if (count($requests) > 0): ?>

        <div class="row">
            <?php foreach ($requests as $r): ?>
                <div class="col-12" data-aos="fade-up">
                    <div class="card-request shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="req-title">
                                    <i class="bi bi-house-door me-2"></i>
                                    <?= htmlspecialchars($r['title']) ?>
                                </h5>
                                <div class="req-date">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Sent on: <?= date("d M Y", strtotime($r['created_at'])) ?>
                                </div>
                            </div>

                            <?php
                                $status = $r['status'] ?? 'pending';
                            ?>
                            <div class="text-end">
                                <span class="status-pill status-<?= strtolower($status) ?>">
                                    <?= ucfirst($status) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
            <div class="mb-3">
                <i class="bi bi-chat-left-dots text-muted opacity-25" style="font-size: 4rem;"></i>
            </div>
            <h5 class="fw-bold">No Requests Found</h5>
            <p class="text-secondary">You haven't initiated any contact requests for properties yet.</p>
            <a href="properties.php" class="btn btn-browse px-4 py-2 mt-2">
                Start Browsing
            </a>
        </div>

    <?php endif; ?>

</div>

<?php 
include('../includes/footer.php'); 
?>