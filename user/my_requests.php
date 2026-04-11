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
        padding: 60px 0;
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
        transform: translateX(5px);
        border-color: #2eca6a;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }

    .status-pill {
        padding: 5px 15px;
        border-radius: 5px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
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
    }

    .req-date {
        color: #888;
        font-size: 0.85rem;
    }
</style>

<div class="container container-box">

    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold m-0" style="border-left: 5px solid #2eca6a; padding-left: 15px;">
                My Contact Requests
            </h3>
        </div>
        <div class="col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-md-end bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="dashboard.php" class="text-success">Dashboard</a></li>
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
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="req-title">
                                    <i class="bi bi-house-heart me-2 text-success"></i>
                                    <?= htmlspecialchars($r['title']) ?>
                                </h5>
                                <div class="req-date">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    Requested on: <?= date("d M Y", strtotime($r['created_at'])) ?>
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

        <div class="text-center py-5 bg-white rounded-4 border">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <p class="text-secondary mt-3">You haven't sent any requests yet.</p>
            <a href="properties.php" class="btn btn-success px-4 py-2">Browse Properties</a>
        </div>

    <?php endif; ?>

</div>

<?php 
// 2. Include the footer (Already part of your structure)
include('../includes/footer.php'); 
?>