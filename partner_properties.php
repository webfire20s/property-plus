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

<div class="container py-4 py-sm-5" style="margin-top:100px;">

    <div class="mb-4 mb-sm-5">

        <div class="d-flex align-items-center gap-2 gap-sm-3 mb-3">

            <div style="
                width: calc(55px + 2vw);
                height: calc(55px + 2vw);
                border-radius:50%;
                background:#2eca6a;
                color:#fff;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size: calc(1.2rem + 0.5vw);
                flex-shrink: 0;
            ">
                <i class="fa-solid fa-building"></i>
            </div>

            <div>
                <h2 class="fw-bold mb-1 text-truncate" style="font-size: calc(1.2rem + 0.8vw); letter-spacing: -0.5px; line-height: 1.2;">
                    <?= htmlspecialchars($user['business_name']) ?>
                </h2>

                <div class="text-muted small" style="font-size: calc(0.75rem + 0.1vw);">
                    <i class="fa-solid fa-location-dot me-1 text-success"></i><?= htmlspecialchars($user['district']) ?>, <?= htmlspecialchars($user['state']) ?>
                </div>
            </div>

        </div>

    </div>

    <div class="row g-2 g-sm-4">

        <?php foreach($properties as $p): ?>

            <div class="col-6 col-md-6 col-lg-4">

                <div class="card border-0 shadow-sm h-100"
                     style="border-radius:12px; overflow:hidden; display: flex; flex-direction: column;">

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
                             style="height: calc(130px + 5vw); object-fit:cover; width:100%;">

                    <?php else: ?>

                        <div style="
                            height: calc(130px + 5vw);
                            background:#f1f5f9;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                        ">
                            <i class="fa-solid fa-house fa-2x text-muted opacity-50"></i>
                        </div>

                    <?php endif; ?>

                    <div class="card-body p-2.5 p-sm-4" style="display: flex; flex-direction: column; flex-grow: 1;">

                        <div class="fw-bold text-success mb-1" style="font-size: calc(0.95rem + 0.3vw);">
                            ₹<?= number_format($p['price']) ?>
                        </div>

                        <h5 class="fw-bold text-dark mb-1 text-truncate" style="font-size: calc(0.85rem + 0.2vw); font-family: 'Poppins', sans-serif; line-height: 1.3;">
                            <?= htmlspecialchars($p['title']) ?>
                        </h5>

                        <div class="text-muted mb-3 text-truncate" style="font-size: 0.72rem;">
                            <i class="fa-solid fa-location-dot text-danger me-1" style="font-size: 0.75rem;"></i><?= htmlspecialchars($p['city']) ?>
                        </div>

                        <div class="d-flex flex-column flex-sm-row gap-1.5 gap-sm-2 mt-auto">

                            <!-- <a href="view_contact.php?id=<?= $p['id'] ?>"
                               class="btn btn-outline-dark w-100 w-sm-50 d-flex align-items-center justify-content-center"
                               style="font-size: 0.7rem; padding: 8px 4px; font-weight: 600; border-radius: 4px;">
                                View Details
                            </a> -->

                            <a href="send_request.php?id=<?= $p['id'] ?>"
                               class="btn btn-success w-100 w-sm-50 d-flex align-items-center justify-content-center"
                               style="background:#2eca6a; border:none; font-size: 0.7rem; padding: 8px 4px; font-weight: 600; border-radius: 4px;">
                                Request
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<?php include 'includes/footer.php'; ?>