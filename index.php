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



<div class="hero-section" style="margin-top: 90px; background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%); padding: 80px 0 150px 0; position: relative; overflow: hidden;">
    
    <div style="position: absolute; right: -50px; bottom: 0; opacity: 0.05; pointer-events: none;">
        <i class="fa-solid fa-city" style="font-size: 300px; color: #000;"></i>
    </div>

    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="brand-badge mb-3" style="display: inline-block; background: rgba(46, 202, 106, 0.1); color: #2eca6a; padding: 5px 15px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fa-solid fa-house-circle-check me-2"></i>Verified Ecosystem
                </div>
                <h1 class="hero-title mb-3" style="font-weight: 800; font-size: 3.2rem; color: #0f172a; line-height: 1.2;">
                    A Platform <span style="color: #2eca6a;">Designed</span> for <br>Real Estate <span style="color: #fdb913;">Professionals.</span>
                </h1>
                <p class="text-secondary mb-4" style="font-size: 1.15rem; max-width: 600px; line-height: 1.7;">
                    Property Plus is a membership-based platform designed for builders, brokers, agents, and freelancers to connect, showcase opportunities, and operate within a verified real estate ecosystem.
                </p>
                <div class="d-flex gap-3">
                    <a href="auth/register.php" class="btn btn-success px-4 py-3 shadow-sm" style="background: #2eca6a; border: none; font-weight: 700; border-radius: 10px;">Register Now</a>
                    <a href="#property-listings" class="btn btn-outline-dark px-4 py-3" style="border-radius: 10px; font-weight: 600;">Explore Listings</a>
                </div>
            </div>
            
            <div class="col-lg-5 d-none d-lg-block" data-aos="fade-left">
                <div style="position: relative;">
                    <img src="assets/img/hero-property.jpg" class="img-fluid" style="border-radius: 30px; box-shadow: 20px 20px 60px rgba(0,0,0,0.1); border: 8px solid #fff;" alt="Modern Building">
                    <div style="position: absolute; bottom: -20px; left: -20px; background: #fff; padding: 20px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-left: 5px solid #2eca6a;">
                        <h5 class="mb-0 fw-bold">100% Verified</h5>
                        <small class="text-muted">Trusted by Professionals</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="filter-card mx-auto shadow-lg" style="max-width: 1000px; background: #fff; padding: 30px; border-radius: 20px; margin-top: -30px; position: relative; z-index: 1000; border: 1px solid #e2e8f0;">
            <div class="mb-3 ps-1">
                <span class="fw-bold" style="font-size: 0.9rem; color: #64748b;"><i class="fa-solid fa-sliders me-2"></i>Filter Property Search</span>
            </div>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                        <input name="city" class="form-control border-0 bg-light" placeholder="Search City..." value="<?= $_GET['city'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select border-0 bg-light">
                        <option value="">Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= (($_GET['category'] ?? '')==$cat)?'selected':'' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="purpose" class="form-select border-0 bg-light">
                        <option value="">Purpose</option>
                        <option value="lease" <?= (($_GET['purpose'] ?? '')=='lease')?'selected':'' ?>>Lease</option>
                        <option value="sell" <?= (($_GET['purpose'] ?? '')=='sell')?'selected':'' ?>>Sell</option>
                        <option value="rent" <?= (($_GET['purpose'] ?? '')=='rent')?'selected':'' ?>>Rent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control border-0 bg-light" placeholder="Min ₹">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control border-0 bg-light" placeholder="Max ₹">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100" style="background: #2eca6a; border: none; height: 100%;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>

        
    </div>
</div>

<section id="property-listings" class="section-property section-t8">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($properties as $p): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <a href="property_details.php?id=<?= $p['id'] ?>" style="text-decoration:none; color:inherit;">
                        <div class="prop-card h-100 shadow-sm" style="background: #fff; border-radius: 20px; overflow: hidden; border: 1px solid #e2e8f0;">
                            
                            <div class="prop-img-wrapper" style="position: relative;">
                                <span class="badge" style="position: absolute; top: 15px; right: 15px; background: #2eca6a; color: #fff; padding: 5px 12px; z-index: 2;">
                                    <?= ucfirst($p['purpose'] ?? 'Listing') ?>
                                </span>
                                <?php if($p['status']=='approved'): ?>
                                    <span class="badge bg-success">Verified</span>
                                <?php endif; ?>
                                
                                <?php 
                                $imgStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=?");
                                $imgStmt->execute([$p['id']]);
                                $images = $imgStmt->fetchAll(PDO::FETCH_COLUMN);

                                if (!empty($images)): ?>
                                <div id="carousel<?= $p['id'] ?>" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $index => $img): ?>
                                            <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                                                <img src="uploads/<?= $img ?>" 
                                                    style="width:100%; height:240px; object-fit:cover;">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <div style="height: 240px; background: #f1f5f9; display:flex; align-items:center; justify-content:center;">
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
                                        // Get counts
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_views WHERE user_id=?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $viewCount = $stmt->fetchColumn();

                                        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM contact_requests WHERE sender_id=?");
                                        $stmt2->execute([$_SESSION['user_id']]);
                                        $requestCount = $stmt2->fetchColumn();

                                        // Get user plan
                                        $stmt3 = $pdo->prepare("
                                            SELECT m.name 
                                            FROM user_memberships um
                                            JOIN memberships m ON um.membership_id = m.id
                                            WHERE um.user_id=? AND um.status='active'
                                            ORDER BY um.id DESC LIMIT 1
                                        ");
                                        $stmt3->execute([$_SESSION['user_id']]);
                                        $userPlan = strtolower($stmt3->fetchColumn() ?? 'listing');

                                        // Define limits
                                        $view_limit = 2;
                                        $request_limit = 2;

                                        switch ($userPlan) {
                                            case 'basic':
                                                $view_limit = 10;
                                                $request_limit = 10;
                                                break;

                                            case 'silver':
                                                $view_limit = 25;
                                                $request_limit = 20;
                                                break;

                                            case 'gold':
                                                $view_limit = 50;
                                                $request_limit = 40;
                                                break;

                                            case 'platinum':
                                                $view_limit = 999;
                                                $request_limit = 999;
                                                break;
                                        }
                                        ?>

                                        <div class="row g-2">
                                            <div class="col-6">
                                                <?php if ($viewCount < $view_limit): ?>
                                                    <a href="view_contact.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm w-100">View Contact</a>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-danger w-100 py-2">Limit Reached</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-6">
                                                <?php if ($requestCount < $request_limit): ?>
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
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php 
// 2. Include the new footer (this handles copyright and JS scripts)
include('includes/footer.php'); 
?>