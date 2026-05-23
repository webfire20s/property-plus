<?php
require 'config/db.php';
include 'includes/navbar.php';

$user_id = (int)($_GET['user_id'] ?? 0);

if (!$user_id) {
    die("Invalid Partner");
}

// Fetch user
$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id=?
");

$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Partner not found");
}

// Fetch properties
$stmt = $pdo->prepare("
    SELECT *
    FROM properties
    WHERE user_id=?
    AND status='approved'
    ORDER BY id DESC
");

$stmt->execute([$user_id]);
$properties = $stmt->fetchAll();
?>

<div class="container py-5" style="margin-top:100px;">

    <div class="mb-5">

        <div class="d-flex align-items-center gap-3 mb-3">

            <div style="
                width:80px;
                height:80px;
                border-radius:50%;
                background:#2eca6a;
                color:#fff;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:2rem;
            ">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>
                <h2 class="fw-bold mb-1">
                    <?= htmlspecialchars($user['business_name']) ?>
                </h2>

                <div class="text-muted">
                    <?= htmlspecialchars($user['district']) ?>,
                    <?= htmlspecialchars($user['state']) ?>
                </div>
            </div>

        </div>

    </div>

    <div class="row g-4">

        <?php foreach($properties as $p): ?>

            <div class="col-md-6 col-lg-4">

                <div class="card border-0 shadow-sm h-100"
                     style="border-radius:16px; overflow:hidden;">

                    <?php
                    $imgStmt = $pdo->prepare("
                        SELECT image_path
                        FROM property_images
                        WHERE property_id=?
                        LIMIT 1
                    ");

                    $imgStmt->execute([$p['id']]);
                    $img = $imgStmt->fetchColumn();
                    ?>

                    <?php if($img): ?>

                        <img src="uploads/<?= $img ?>"
                             style="height:240px; object-fit:cover; width:100%;">

                    <?php else: ?>

                        <div style="
                            height:240px;
                            background:#f1f5f9;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">
                            <i class="fa-solid fa-house fa-3x text-muted"></i>
                        </div>

                    <?php endif; ?>

                    <div class="card-body">

                        <div class="fw-bold text-success fs-5 mb-2">
                            ₹<?= number_format($p['price']) ?>
                        </div>

                        <h5 class="fw-bold">
                            <?= htmlspecialchars($p['title']) ?>
                        </h5>

                        <div class="text-muted small mb-3">
                            <i class="fa-solid fa-location-dot text-danger me-1"></i>
                            <?= htmlspecialchars($p['city']) ?>
                        </div>

                        <div class="d-flex gap-2">

                            <a href="view_contact.php?id=<?= $p['id'] ?>"
                               class="btn btn-outline-dark w-50">

                                View Contact

                            </a>

                            <a href="send_request.php?id=<?= $p['id'] ?>"
                               class="btn btn-success w-50"
                               style="background:#2eca6a; border:none;">

                                Request Contact

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>