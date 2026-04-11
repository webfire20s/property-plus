<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->prepare("
    SELECT cr.*, p.title, u.phone AS requester_phone
    FROM contact_requests cr
    JOIN properties p ON cr.property_id = p.id
    JOIN users u ON cr.sender_id = u.id
    WHERE p.user_id = ?
");

$stmt->execute([$_SESSION['user_id']]);
$requests = $stmt->fetchAll();
?>


<style>
    .header-section {
        padding: 60px 0;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 40px;
        margin-top: 80px; /* Offset for sticky navbar */
    }

    .request-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .request-card:hover {
        border-color: #2eca6a;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .property-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #2eca6a;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .status-pill {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 16px;
        border-radius: 12px;
        text-transform: uppercase;
    }

    .pill-pending { background: #fef9c3; color: #854d0e; }
    .pill-accepted { background: #dcfce7; color: #166534; }
    .pill-rejected { background: #fee2e2; color: #991b1b; }

    .btn-approve {
        background-color: #2eca6a;
        color: white !important;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 18px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-reject {
        background-color: transparent;
        color: #dc3545 !important;
        border: 1px solid #f8d7da;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 10px 18px;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-approve:hover { background-color: #25a556; }
    .btn-reject:hover { background-color: #fff5f5; border-color: #dc3545; }

    .requester-info {
        background: #f1f5f9;
        padding: 8px 12px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="header-section" data-aos="fade-down">
    <div class="container">
        <h1 class="fw-bold mb-1" style="color: #000;">Access Requests</h1>
        <p class="text-secondary mb-0">Securely manage inquiries for your private listings.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <?php if (count($requests) > 0): ?>
                <?php foreach ($requests as $r): ?>
                    <div class="request-card shadow-sm" data-aos="fade-up">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="property-label">Property Details</div>
                                <h5 class="fw-bold mb-3" style="color: #000;"><?php echo htmlspecialchars($r['title']); ?></h5>
                                <div class="requester-info">
                                    <i class="bi bi-person-badge me-2 text-success"></i>
                                    <span class="fw-bold"><?php echo htmlspecialchars($r['requester_phone']); ?></span>
                                </div>
                            </div>
                            
                            <div class="col-md-3 text-md-center py-4 py-md-0">
                                <span class="status-pill pill-<?php echo $r['status']; ?>">
                                    <i class="bi bi-dot small"></i> <?php echo ucfirst($r['status']); ?>
                                </span>
                            </div>

                            <div class="col-md-4 text-md-end">
                                <?php if ($r['status'] == 'pending'): ?>
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <a href='reject.php?id=<?php echo $r['id']; ?>' class="btn-reject">
                                            <i class="bi bi-x-lg me-1"></i> Decline
                                        </a>
                                        <a href='approve.php?id=<?php echo $r['id']; ?>' class="btn-approve">
                                            <i class="bi bi-check-lg me-1"></i> Approve
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i> 
                                        Processed on <span class="fw-bold"><?php echo date("d M, Y"); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5" data-aos="zoom-in">
                    <div class="mb-4">
                        <i class="bi bi-inbox text-light" style="font-size: 5rem;"></i>
                    </div>
                    <h3 class="fw-bold">No Pending Requests</h3>
                    <p class="text-muted">You're all caught up! New inquiries will appear here.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php 
// 2. Include the footer (Handles AOS and JS scripts)
include('../includes/footer.php'); 
?>