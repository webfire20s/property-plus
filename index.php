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
        <!-- g-2 on mobile for tighter spacing, g-4 on desktop -->
        <div class="row g-2 g-md-4">
            <?php foreach ($properties as $p): ?>
                <!-- col-6 ensures 2 per row on mobile -->
                <div class="col-6 col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <a href="property_details.php?id=<?= $p['id'] ?>" style="text-decoration:none; color:inherit;">
                        <div class="prop-card h-100 shadow-sm" style="background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
                            
                            <div class="prop-img-wrapper" style="position: relative;">
                                <!-- Scaled down badge for mobile -->
                                <span class="badge" style="position: absolute; top: 10px; right: 10px; background: #2eca6a; color: #fff; padding: 4px 8px; z-index: 2; font-size: 0.7rem;">
                                    <?= ucfirst($p['purpose'] ?? 'Listing') ?>
                                </span>
                                
                                <?php if($p['status']=='approved'): ?>
                                    <span class="badge bg-success" style="position: absolute; top: 10px; left: 10px; z-index: 2; font-size: 0.7rem;">Verified</span>
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
                                                <!-- Responsive height: 150px on mobile, 240px on desktop -->
                                                <img src="uploads/<?= $img ?>" 
                                                    style="width:100%; object-fit:cover;" 
                                                    class="prop-img-height">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <div class="prop-img-height" style="background: #f1f5f9; display:flex; align-items:center; justify-content:center;">
                                        <i class="fa-solid fa-house-chimney fa-2x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Reduced padding for mobile (p-2 on mobile, p-3/4 on desktop) -->
                            <div class="prop-details" style="padding: 12px;">
                                <div class="prop-price" style="font-weight: 800; color: #2eca6a; line-height: 1;">₹<?= number_format($p['price']) ?></div>
                                
                                <div class="prop-location text-truncate" style="color: #64748b; font-size: 0.75rem; margin: 5px 0;">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i> <?= htmlspecialchars($p['city']) ?>
                                </div>
                                
                                <h3 class="prop-title" style="font-weight: 700; margin-bottom: 10px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; min-height: 34px; line-height: 1.2;">
                                    <?= htmlspecialchars($p['title']) ?>
                                </h3>
                                
                                <!-- Meta: Hidden on very small screens to save space, or scaled down -->
                                <div class="prop-meta border-top pt-2 mt-1 d-flex flex-wrap" style="gap: 8px; font-size: 0.7rem; color: #64748b;">
                                    <span class="text-truncate"><i class="fa-solid fa-ruler-combined me-1"></i> <?= $p['area'] ?? '-' ?></span>
                                </div>

                                <div class="access-box pt-2 mt-2">
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <?php
                                        // Logic remains exactly as you provided
                                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM contact_views WHERE user_id=?");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $viewCount = $stmt->fetchColumn();

                                        $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM contact_requests WHERE sender_id=?");
                                        $stmt2->execute([$_SESSION['user_id']]);
                                        $requestCount = $stmt2->fetchColumn();

                                        $stmt3 = $pdo->prepare("SELECT m.name FROM user_memberships um JOIN memberships m ON um.membership_id = m.id WHERE um.user_id=? AND um.status='active' ORDER BY um.id DESC LIMIT 1");
                                        $stmt3->execute([$_SESSION['user_id']]);
                                        $userPlan = strtolower($stmt3->fetchColumn() ?? 'listing');

                                        $view_limit = 2; $request_limit = 2;
                                        switch ($userPlan) {
                                            case 'basic': $view_limit = 10; $request_limit = 10; break;
                                            case 'silver': $view_limit = 25; $request_limit = 20; break;
                                            case 'gold': $view_limit = 50; $request_limit = 40; break;
                                            case 'platinum': $view_limit = 999; $request_limit = 999; break;
                                        }
                                        ?>

                                        <div class="row g-1">
                                            <div class="col-12 col-md-6">
                                                <?php if ($viewCount < $view_limit): ?>
                                                    <a href="view_contact.php?id=<?= $p['id'] ?>" class="btn btn-outline-dark btn-sm w-100 py-1" style="font-size: 0.7rem;">View</a>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-danger w-100 py-2" style="font-size: 0.6rem;">Limit</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <?php if ($requestCount < $request_limit): ?>
                                                    <a href="send_request.php?id=<?= $p['id'] ?>" class="btn btn-dark btn-sm w-100 py-1" style="font-size: 0.7rem;">Contact</a>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-danger w-100 py-2" style="font-size: 0.6rem;">Limit</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <a href="auth/login.php" class="btn btn-outline-success btn-sm w-100 py-1" style="font-size: 0.75rem;">
                                            <i class="fa-solid fa-lock me-1"></i> Login
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

<!-- Add this small CSS block to your stylesheet or header to handle responsive text/heights -->
<style>
    .prop-img-height { height: 150px; }
    .prop-price { font-size: 1rem; }
    .prop-title { font-size: 0.85rem; }
    
    @media (min-width: 768px) {
        .prop-img-height { height: 240px; }
        .prop-price { font-size: 1.4rem; }
        .prop-title { font-size: 1.1rem; }
    }
</style>
<?php 
// 2. Include the new footer (this handles copyright and JS scripts)
include('includes/footer.php'); 
?>