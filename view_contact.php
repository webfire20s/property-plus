<?php
require 'config/db.php';
session_start();

// --- START LOGIC (Untouched) ---

if (!isset($_SESSION['user_id'])) {
    die("Login required");
}

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'];

// Get current usage
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_views WHERE user_id=?");
$stmt->execute([$user_id]);
$count = $stmt->fetchColumn();

// Get user plan
$stmt2 = $pdo->prepare("
    SELECT m.name 
    FROM user_memberships um
    JOIN memberships m ON um.membership_id = m.id
    WHERE um.user_id=? AND um.status='active'
    ORDER BY um.id DESC LIMIT 1
");
$stmt2->execute([$user_id]);
$userPlan = strtolower($stmt2->fetchColumn() ?? 'listing');

// Define limits
$view_limit = 2;

switch ($userPlan) {
    case 'basic':
        $view_limit = 10;
        break;
    case 'silver':
        $view_limit = 25;
        break;
    case 'gold':
        $view_limit = 50;
        break;
    case 'platinum':
        $view_limit = 999;
        break;
}

$error_message = "";
$contact = "";

// Prevent duplicate
$check = $pdo->prepare("
    SELECT * FROM contact_views 
    WHERE user_id=? AND property_id=?
");
$check->execute([$user_id, $property_id]);

$isNew = $check->rowCount() == 0;

if ($isNew) {
    // Check limit ONLY for new views
    if ($count >= $view_limit) {
        $error_message = "Contact view limit reached for your plan (" . ucfirst($userPlan) . ").";
    } else {
        // Insert
        $pdo->prepare("
            INSERT INTO contact_views (user_id, property_id)
            VALUES (?, ?)
        ")->execute([$user_id, $property_id]);
    }
}

// Fetch property contact if no error
if (empty($error_message)) {
    $stmt = $pdo->prepare("
        SELECT u.phone 
        FROM properties p
        JOIN users u ON p.user_id = u.id
        WHERE p.id=?
    ");
    $stmt->execute([$property_id]);
    $contact = $stmt->fetchColumn();
}

// --- END LOGIC ---

include 'includes/navbar.php'; 
?>

<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }
    .view-container {
        padding: 140px 0 80px;
        min-height: 70vh;
        display: flex;
        align-items: center;
    }
    .contact-card {
        background: #fff;
        border-radius: 15px;
        padding: 40px;
        border: 1px solid #ebebeb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        text-align: center;
    }
    .contact-title {
        font-weight: 700;
        color: #000;
        margin-bottom: 10px;
        border-left: 5px solid #2eca6a;
        padding-left: 15px;
        display: inline-block;
        text-align: left;
    }
    .phone-display {
        font-size: 2.2rem;
        font-weight: 800;
        color: #2eca6a;
        letter-spacing: 1px;
        margin: 20px 0;
    }
    .btn-action {
        background: #2eca6a;
        color: #fff;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        text-decoration: none;
        transition: 0.3s;
        display: inline-block;
    }
    .btn-action:hover {
        background: #000;
        color: #fff;
    }
    .icon-circle {
        width: 60px;
        height: 60px;
        background: rgba(46, 202, 106, 0.1);
        color: #2eca6a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.5rem;
    }
    .limit-reached {
        color: #dc3545;
        background: rgba(220, 53, 69, 0.1);
    }
</style>

<div class="container view-container">
    <div class="row justify-content-center w-100">
        <div class="col-md-6 col-lg-5">
            <div class="contact-card">
                
                <?php if (!empty($error_message)): ?>
                    <div class="icon-circle limit-reached">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Limit Reached</h4>
                    <p class="text-muted mt-3"><?= $error_message ?></p>
                    <a href="pricing.php" class="btn-action mt-3">Upgrade Plan</a>
                <?php else: ?>
                    <div class="icon-circle">
                        <i class="bi bi-telephone-outbound-fill"></i>
                    </div>
                    <h4 class="contact-title">Owner's Contact</h4>
                    <p class="text-muted small">Please mention that you found this on our portal.</p>
                    
                    <div class="phone-display">
                        <i class="bi bi-whatsapp me-2"></i><?= htmlspecialchars($contact) ?>
                    </div>
                    
                    <p class="text-muted small mb-4">Plan: <span class="badge bg-light text-dark border"><?= ucfirst($userPlan) ?></span></p>
                <?php endif; ?>

                <div class="border-top pt-4 mt-2">
                    <a href="property_details.php?id=<?= $property_id ?>" class="text-success fw-600 text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Property
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>