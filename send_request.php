<?php
require 'config/db.php';
session_start();

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'];

// Get current usage
$stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_requests WHERE sender_id=?");
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
$request_limit = 2;

switch ($userPlan) {
    case 'basic':
        $request_limit = 10;
        break;

    case 'silver':
        $request_limit = 20;
        break;

    case 'gold':
        $request_limit = 40;
        break;

    case 'platinum':
        $request_limit = 999;
        break;
}

// Enforce limit
if ($count >= $request_limit) {
    die("Contact request limit reached for your plan");
}


// Prevent duplicate request
$check = $pdo->prepare("
    SELECT * FROM contact_requests 
    WHERE sender_id=? AND property_id=?
");
$check->execute([$user_id, $property_id]);

if ($check->rowCount() > 0) {
    die("You have already sent request for this property");
}

// Insert request
$pdo->prepare("
    INSERT INTO contact_requests (sender_id, property_id)
    VALUES (?, ?)
")->execute([$user_id, $property_id]);

$status_type = "success";
$message = "Request sent successfully! The property owner will be notified.";

include 'includes/navbar.php';
?>

<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }
    .status-container {
        padding: 140px 0 80px;
        min-height: 80vh;
        display: flex;
        align-items: center;
    }
    .status-card {
        background: #fff;
        border-radius: 15px;
        padding: 50px;
        text-align: center;
        border: 1px solid #ebebeb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .icon-box {
        width: 80px;
        height: 80px;
        line-height: 80px;
        border-radius: 50%;
        margin: 0 auto 25px;
        font-size: 2.5rem;
    }
    .icon-success {
        background: rgba(46, 202, 106, 0.1);
        color: #2eca6a;
    }
    .icon-error {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }
    .status-title {
        font-weight: 700;
        margin-bottom: 15px;
        color: #000;
    }
    .btn-theme {
        background: #2eca6a;
        color: #fff;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }
    .btn-theme:hover {
        background: #000;
        color: #fff;
    }
</style>

<div class="container status-container">
    <div class="row justify-content-center w-100">
        <div class="col-md-6">
            <div class="status-card">
                <?php if($status_type == "success"): ?>
                    <div class="icon-box icon-success">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <h2 class="status-title">Success!</h2>
                <?php else: ?>
                    <div class="icon-box icon-error">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h2 class="status-title">Action Required</h2>
                <?php endif; ?>

                <p class="text-secondary"><?= $message ?></p>
                
                <div class="mt-4">
                    <a href="property_details.php?id=<?= $property_id ?>" class="btn-theme">Back to Property</a>
                    <a href="user/my_requests.php" class="btn btn-outline-dark rounded-8 ms-2 py-2 px-4" style="border-radius:8px; font-weight:600; margin-top:20px;">View All Requests</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>