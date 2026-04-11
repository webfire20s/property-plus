<?php
require 'config/db.php';
include 'includes/navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔥 FILTER LOGIC (UNTOUCHED)
$where = ["status='approved'"];
$params = [];

if (!empty($_GET['city'])) {
    $where[] = "city LIKE ?";
    $params[] = "%" . $_GET['city'] . "%";
}

if (!empty($_GET['type'])) {
    $where[] = "property_type = ?";
    $params[] = $_GET['type'];
}

if (!empty($_GET['category'])) {
    $where[] = "category = ?";
    $params[] = $_GET['category'];
}

if (!empty($_GET['purpose'])) {
    $where[] = "purpose = ?";
    $params[] = $_GET['purpose'];
}

if (!empty($_GET['min_price'])) {
    $where[] = "price >= ?";
    $params[] = $_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $where[] = "price <= ?";
    $params[] = $_GET['max_price'];
}

if (!empty($_GET['min_area'])) {
    $where[] = "area >= ?";
    $params[] = $_GET['min_area'];
}

if (!empty($_GET['max_area'])) {
    $where[] = "area <= ?";
    $params[] = $_GET['max_area'];
}

$sql = "SELECT * FROM properties WHERE " . implode(" AND ", $where);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();

$categories = [
    "Builder Floors", "Apartments", "Flats", "Independent Floors", "Independent Kothi",
    "Independent Villa", "Society Flats", "Commercial Building", "Commercial Shop",
    "Commercial Showroom", "Commercial Floor", "Land", "Commercial Land", "Agricultural Land"
];
?>



<div class="hero-section" style="margin-top: 80px; background: #f8fafc; padding: 60px 0; border-bottom: 1px solid #eee;">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="hero-title mb-2" style="font-weight: 800; font-size: 2.5rem; color: #0f172a;">
            Find Your <span style="color: #2eca6a;">Perfect</span> Space
        </h1>
        <p class="text-secondary mb-5 fw-600">Premium listings tailored to your lifestyle</p>
        
        <div class="filter-card mx-auto shadow-sm" style="max-width: 900px; background: #fff; padding: 20px; border-radius: 15px;">
            <form method="GET" class="row g-2">
                <div class="col-md-3">
                    <input name="city" class="form-control" placeholder="Search City..." value="<?= $_GET['city'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= (($_GET['category'] ?? '')==$cat)?'selected':'' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="purpose" class="form-select">
                        <option value="">Purpose</option>
                        <option value="buy" <?= (($_GET['purpose'] ?? '')=='buy')?'selected':'' ?>>Buy</option>
                        <option value="sell" <?= (($_GET['purpose'] ?? '')=='sell')?'selected':'' ?>>Sell</option>
                        <option value="rent" <?= (($_GET['purpose'] ?? '')=='rent')?'selected':'' ?>>Rent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Min ₹">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control" placeholder="Max ₹">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100" style="background: #2eca6a; border: none;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<section class="section-property section-t8">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($properties as $p): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="prop-card h-100 shadow-sm" style="background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0;">
                        
                        <div class="prop-img-wrapper" style="position: relative;">
                            <span class="badge" style="position: absolute; top: 15px; left: 15px; background: #2eca6a; color: #fff; padding: 5px 12px; z-index: 2;">
                                <?= ucfirst($p['purpose'] ?? 'Listing') ?>
                            </span>
                            
                            <?php 
                            $imgStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1");
                            $imgStmt->execute([$p['id']]);
                            $image = $imgStmt->fetchColumn();

                            if ($image): ?>
                                <img src='uploads/<?= $image ?>' style="width: 100%; height: 240px; object-fit: cover;" alt="Property">
                            <?php else: ?>
                                <div style="height: 240px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                    <i class="fa-solid fa-house-chimney fa-3x"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="prop-details" style="padding: 20px;">
                            <div class="prop-price" style="font-size: 1.4rem; font-weight: 800; color: #2eca6a;">₹<?= number_format($p['price']) ?></div>
                            <div class="prop-location" style="color: #64748b; font-size: 0.9rem; margin-bottom: 10px;">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i> <?= htmlspecialchars($p['city']) ?>
                            </div>
                            <h3 class="prop-title" style="font-size: 1.1rem; font-weight: 700; min-height: 50px;"><?= htmlspecialchars($p['title']) ?></h3>
                            
                            <div class="prop-meta border-top pt-3 mt-2" style="display: flex; gap: 15px; font-size: 0.85rem; color: #64748b;">
                                <span><i class="fa-solid fa-layer-group me-1"></i> <?= $cat ?></span>
                                <span><i class="fa-solid fa-ruler-combined me-1"></i> <?= $p['area'] ?? '-' ?> <?= $p['area_unit'] ?? '' ?></span>
                            </div>

                            <div class="access-box pt-3 mt-3">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <?php
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_views WHERE user_id=?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    $viewCount = $stmt->fetchColumn();

                                    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM contact_requests WHERE sender_id=?");
                                    $stmt2->execute([$_SESSION['user_id']]);
                                    $requestCount = $stmt2->fetchColumn();
                                    ?>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <?php if ($viewCount < 2): ?>
                                                <a href="view_contact.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm w-100">View Contact</a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-danger w-100 py-2">Limit Reached</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-6">
                                            <?php if ($requestCount < 5): ?>
                                                <a href="send_request.php?id=<?= $p['id'] ?>" class="btn btn-dark btn-sm w-100">Contact Owner</a>
                                            <?php else: ?>
                                                <span class="badge bg-light text-danger w-100 py-2">Limit Reached</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <a href="auth/login.php" class="btn btn-outline-success btn-sm w-100">
                                        <i class="fa-solid fa-lock me-1"></i> Login to View
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php 
// 2. Include the new footer (this handles copyright and JS scripts)
include('includes/footer.php'); 
?>