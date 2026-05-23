<?php
require 'config/db.php';
session_start();

// Initialize operation variables
$status_type = "success";
$message = "";
$property_id = (int)($_GET['id'] ?? 0);

// 1. Session Authentication Check
if (!isset($_SESSION['user_id'])) {
    $status_type = "error";
    $message = "Please login first to send property contact requests.";
}

// 2. Validate Property Reference ID Parameter
if ($status_type === "success" && !$property_id) {
    $status_type = "error";
    $message = "Invalid property selection context specified.";
}

if ($status_type === "success") {
    $user_id = $_SESSION['user_id'];

    // Fetch current usage counters
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_requests WHERE sender_id=?");
    $stmt->execute([$user_id]);
    $count = $stmt->fetchColumn();

    // Fetch user membership dynamic limitations
    $stmt2 = $pdo->prepare("
        SELECT m.name 
        FROM user_memberships um
        JOIN memberships m ON um.membership_id = m.id
        WHERE um.user_id=? AND um.status='active'
        ORDER BY um.id DESC LIMIT 1
    ");
    $stmt2->execute([$user_id]);
    $userPlan = strtolower($stmt2->fetchColumn() ?? 'listing');

    // Define tier plan threshold parameters
    $request_limit = 1;
    switch ($userPlan) {
        case 'basic':    $request_limit = 10;  break;
        case 'silver':   $request_limit = 20;  break;
        case 'gold':     $request_limit = 40;  break;
        case 'platinum': $request_limit = 999; break;
    }

    // 3. Enforce Tier Limits Validation Check
    if ($count >= $request_limit) {
        $status_type = "error";
        $message = "You have reached the contact request limit tier rules for your current (" . strtoupper($userPlan) . ") membership plan.";
    }

    // 4. Prevent Duplicate Queries Processing
    if ($status_type === "success") {
        $check = $pdo->prepare("
            SELECT * FROM contact_requests 
            WHERE sender_id=? AND property_id=?
        ");
        $check->execute([$user_id, $property_id]);

        if ($check->rowCount() > 0) {
            $status_type = "error";
            $message = "You have already transmitted a matching connection application request for this unit resource portfolio.";
        }
    }

    // 5. Commit Valid Transaction Records
    if ($status_type === "success") {
        $pdo->prepare("
            INSERT INTO contact_requests (sender_id, property_id)
            VALUES (?, ?)
        ")->execute([$user_id, $property_id]);

        $message = "Request sent successfully! The real estate property owner will be notified shortly.";
    }
}

// Include navigation header once execution rules complete
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
        border-radius: 0px; /* Aligned with EstateAgency geometric flat layout design models */
        padding: 50px;
        text-align: center;
        border: 1px solid #ebebeb;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .icon-box {
        width: 80px;
        height: 80px;
        line-height: 80px;
        border-radius: 50%;
        margin: 0 auto 25px;
        font-size: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
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
        letter-spacing: -0.5px;
    }
    .btn-theme {
        background: #2eca6a;
        color: #fff;
        border-radius: 0px;
        padding: 12px 30px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
        margin-top: 20px;
    }
    .btn-theme:hover {
        background: #2eca6a;
        color: #fff;
    }
</style>

<div class="container status-container">
    <div class="row justify-content-center w-100">
        <div class="col-md-6">
            <div class="status-card">
                <?php if($status_type == "success"): ?>
                    <div class="icon-box icon-success">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <h2 class="status-title">Success!</h2>
                <?php else: ?>
                    <div class="icon-box icon-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <h2 class="status-title">Action Stopped</h2>
                <?php endif; ?>

                <p class="text-secondary" style="font-size: 0.95rem; line-height: 1.6;"><?= htmlspecialchars($message) ?></p>
                
                <div class="mt-4">
                    <?php if($property_id > 0): ?>
                        <a href="property_details.php?id=<?= $property_id ?>" class="btn-theme">Back to Property</a>
                    <?php else: ?>
                        <a href="index.php" class="btn-theme">Return Home</a>
                    <?php endif; ?>
                    
                    <a href="user/my_requests.php" class="btn btn-outline-dark rounded-0 ms-2 py-2.5 px-4" style="font-weight:600; margin-top:20px; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; border-width: 2px;">View All Requests</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>