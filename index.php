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

// ================= USERS FETCH =================
// ================= USERS FETCH =================

// PAGINATION
$limit = 12;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

$userWhere = ["u.status='active'"];
$userParams = [];

// Optional filters from property data
if (!empty($_GET['city'])) {

    $userWhere[] = "EXISTS (
        SELECT 1 FROM properties p2
        WHERE p2.user_id = u.id
        AND p2.status='approved'
        AND p2.city LIKE ?
    )";

    $userParams[] = "%" . $_GET['city'] . "%";
}

if (!empty($_GET['category'])) {

    $userWhere[] = "EXISTS (
        SELECT 1 FROM properties p2
        WHERE p2.user_id = u.id
        AND p2.status='approved'
        AND p2.category = ?
    )";

    $userParams[] = $_GET['category'];
}

if (!empty($_GET['purpose'])) {

    $userWhere[] = "EXISTS (
        SELECT 1 FROM properties p2
        WHERE p2.user_id = u.id
        AND p2.status='approved'
        AND p2.purpose = ?
    )";

    $userParams[] = $_GET['purpose'];
}

// MAIN USERS QUERY
$sql = "
    SELECT 
        u.id,
        u.business_name,
        u.state,
        u.district,

        COUNT(DISTINCT p.id) as total_properties,

        COALESCE(m.name, 'Listing') as membership_name,

        CASE LOWER(COALESCE(m.name, 'listing'))
            WHEN 'platinum' THEN 5
            WHEN 'gold' THEN 4
            WHEN 'silver' THEN 3
            WHEN 'basic' THEN 2
            ELSE 1
        END as membership_priority

    FROM users u

    -- SHOW ALL USERS
    LEFT JOIN properties p 
        ON p.user_id = u.id
        AND p.status='approved'

    -- SAFER MEMBERSHIP JOIN (latest active membership only)
    LEFT JOIN user_memberships um
        ON um.id = (
            SELECT um2.id
            FROM user_memberships um2
            WHERE um2.user_id = u.id
            AND um2.status='active'
            ORDER BY um2.id DESC
            LIMIT 1
        )

    LEFT JOIN memberships m
        ON m.id = um.membership_id

    WHERE " . implode(" AND ", $userWhere) . "

    GROUP BY 
        u.id,
        u.business_name,
        u.state,
        u.district,
        m.name

    ORDER BY
        membership_priority DESC,
        total_properties DESC,
        u.id DESC

    LIMIT $limit OFFSET $offset
";

$stmt = $pdo->prepare($sql);
$stmt->execute($userParams);
$users = $stmt->fetchAll();

// TOTAL COUNT FOR PAGINATION
$countSql = "
    SELECT COUNT(*) FROM (
        SELECT u.id

        FROM users u

        LEFT JOIN properties p
            ON p.user_id = u.id
            AND p.status='approved'

        WHERE " . implode(" AND ", $userWhere) . "

        GROUP BY u.id
    ) temp
";

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($userParams);

$totalUsers = $countStmt->fetchColumn();

$totalPages = ceil($totalUsers / $limit);

$categories = [
    'Builder Floors',
    'Apartments',
    'Flats',
    'Independent Floors',
    'Independent Kothi',
    'Independent Villa',
    'Society Flats',
    'Commercial Building',
    'Commercial Shop',
    'Commercial Showroom',
    'Commercial Floor',
    'Land',
    'Commercial Land',
    'Agricultural Land'
];
?>


<!-- Background Image CSS -->
<style>
    .hero-section {
        margin-top: 90px;
        /* Using a high-quality real estate background */
        background-image: linear-gradient(to right, rgba(255, 255, 255, 1) 30%, rgba(255, 255, 255, 0.7) 60%, rgba(255, 255, 255, 0) 90%), 
                          url('https://cf.bstatic.com/xdata/images/hotel/max1024x768/659933963.jpg?k=d38117fb188c0d5a55a091a75703d3b915a1ee5de4bc9758833964156351dba5&o=');
        background-size: cover;
        background-position: center right;
        background-attachment: fixed; /* Parallax Effect */
        padding: 100px 0 160px 0;
        position: relative;
        overflow: hidden;
    }

    /* Mobile adjustment: On small screens, we make the overlay darker so text remains readable over the image */
    @media (max-width: 991px) {
        .hero-section {
            background-image: linear-gradient(to bottom, rgba(255, 255, 255, 0.9) 100%, rgba(255, 255, 255, 0.9) 100%), 
                              url('https://cf.bstatic.com/xdata/images/hotel/max1024x768/659933963.jpg?k=d38117fb188c0d5a55a091a75703d3b915a1ee5de4bc9758833964156351dba5&o=');
            padding: 60px 0 120px 0;
        }
    }
</style>

<section class="hero-section">
    <!-- Decorative floating icon still kept for texture -->
    <div style="position: absolute; right: -50px; bottom: 0; opacity: 0.05; pointer-events: none;">
        <i class="fa-solid fa-city" style="font-size: 300px; color: #000;"></i>
    </div>

    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="brand-badge mb-3" style="display: inline-block; background: rgba(46, 202, 106, 0.15); color: #2eca6a; padding: 6px 18px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(46,202,106,0.3);">
                    <i class="fa-solid fa-house-circle-check me-2"></i>Verified Ecosystem
                </div>
                
                <h1 class="hero-title mb-3" style="font-weight: 800; font-size: 3.5rem; color: #0f172a; line-height: 1.2;">
                    A Platform <span style="color: #2eca6a;">Designed</span> for <br>Real Estate <span style="color: #fdb913;">Professionals.</span>
                </h1>
                
                <p class="text-secondary mb-4" style="font-size: 1.2rem; max-width: 600px; line-height: 1.7; color: #334155 !important; font-weight: 500;">
                    Property Plus is a membership-based platform designed for builders, brokers, agents, and freelancers to connect, showcase opportunities, and operate within a verified real estate ecosystem.
                </p>
                
                <div class="d-flex gap-3">
                    <a href="auth/register.php" class="btn btn-success px-4 py-3 shadow-lg" style="background: #2eca6a; border: none; font-weight: 700; border-radius: 12px; transition: transform 0.2s;">Register Now</a>
                    <a href="#property-listings" class="btn btn-outline-dark px-4 py-3" style="border-radius: 12px; font-weight: 600; background: #fff;">Explore Listings</a>
                </div>
            </div>
            
            <!-- We keep the Col-5 but leave it empty or with a small floating widget 
                 because the background image now fills the right side visual space -->
            <div class="col-lg-5 d-none d-lg-block">
                <div style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); padding: 30px; border-radius: 30px; display: inline-block; float: right;" data-aos="zoom-in">
                    <div class="text-center">
                        <div style="font-size: 2.5rem; font-weight: 800; color: #0f172a;">100%</div>
                        <div style="font-weight: 700; color: #2eca6a; text-transform: uppercase; font-size: 0.8rem;">Verified Listings</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="filter-card mx-auto shadow-lg" style="max-width: 1000px; background: #fff; padding: 30px; border-radius: 20px; margin-top: -30px; position: relative; z-index: 1000; border: 1px solid #e2e8f0;">
            <div class="mb-3 ps-1">
                <span class="fw-bold" style="font-size: 0.9rem; color: #64748b;"><i class="fa-solid fa-sliders me-2"></i>Filter Property Search</span>
            </div>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-location-dot text-muted"></i></span>
                        <input name="city" class="form-control border-0 bg-light" style="height: 45px;" placeholder="Search City..." value="<?= $_GET['city'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select border-0 bg-light" style="height: 45px;">
                        <option value="">Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= (($_GET['category'] ?? '')==$cat)?'selected':'' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="purpose" class="form-select border-0 bg-light" style="height: 45px;">
                        <option value="">Purpose</option>
                        <option value="lease" <?= (($_GET['purpose'] ?? '')=='lease')?'selected':'' ?>>Lease</option>
                        <option value="sell" <?= (($_GET['purpose'] ?? '')=='sell')?'selected':'' ?>>Sell</option>
                        <option value="rent" <?= (($_GET['purpose'] ?? '')=='rent')?'selected':'' ?>>Rent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control border-0 bg-light" style="height: 45px;" placeholder="Min ₹">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control border-0 bg-light" style="height: 45px;" placeholder="Max ₹">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-success w-100" style="background: #2eca6a; border: none; height: 45px;">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
<section id="property-listings" class="section-property section-t8 py-5" style="background: #f7f7f7;">
    <div class="container">

        <div class="row mb-5">
            <div class="col-md-12 col-lg-8">
                <div class="title-wrap">
                    <div class="title-box" style="border-left: 5px solid #2eca6a; padding-left: 15px;">
                        <h2 class="title-a" style="font-family: 'Poppins', sans-serif; font-weight: 700; color: #000000; margin: 0; letter-spacing: -0.5px;">
                            Verified Professionals
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-lg-4 d-flex align-items-center justify-content-lg-end mt-3 mt-lg-0">
                <span class="badge px-3 py-2.5 bg-light text-dark border" style="font-family: 'Poppins', sans-serif; border-radius: 0px; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">
                    <?= count($users) ?> Registered Partners
                </span>
            </div>
        </div>

        <div class="row g-4">

            <?php if(count($users) > 0): ?>

                <?php foreach($users as $u): ?>

                    <div class="col-12 col-md-6 col-lg-4">

                        <?php
                        $membership = strtolower($u['membership_name']);
                        
                        // Theme matching tier background indicators
                        $tierBadgeBg = '#2eca6a'; 
                        switch($membership) {
                            case 'platinum': $tierBadgeBg = '#7c3aed'; break;
                            case 'gold':     $tierBadgeBg = '#f59e0b'; break;
                            case 'silver':   $tierBadgeBg = '#555555'; break;
                            case 'basic':    $tierBadgeBg = '#2eca6a'; break;
                        }
                        ?>

                        <div class="card border-0 theme-grid-box"
                             style="border-radius: 0px; background: #ffffff; border: 1px solid #ebebeb !important; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column;">
                            
                            <div class="p-4 position-relative" style="background: #ffffff; flex-grow: 1;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge text-white px-2.5 py-1.5" style="background: <?= $tierBadgeBg ?>; border-radius: 0px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                                        <?= htmlspecialchars($u['membership_name']) ?>
                                    </span>
                                    <span style="color: #2eca6a; font-size: 1.1rem;"><i class="fa-solid fa-circle-check"></i></span>
                                </div>
                                
                                <h4 class="fw-bold text-dark mb-1 text-truncate" style="font-family: 'Poppins', sans-serif; font-size: 1.3rem; letter-spacing: -0.5px;">
                                    <?= htmlspecialchars($u['business_name']) ?>
                                </h4>
                                
                                <p class="text-muted mb-0 text-truncate small" style="font-family: 'Poppins', sans-serif;">
                                    <i class="fa-solid fa-location-dot me-1 text-success"></i><?= htmlspecialchars($u['district']) ?>, <?= htmlspecialchars($u['state']) ?>
                                </p>
                            </div>

                            <div class="p-4 pt-0" style="background: #ffffff;">
                                
                                <div class="d-flex justify-content-between align-items-center py-2.5 mb-3" style="border-top: 1px dashed #ebebeb; border-bottom: 1px dashed #ebebeb;">
                                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.7rem;">Active Portfolio</span>
                                    <span class="fw-bold text-dark" style="font-size: 1rem; font-family: 'Poppins', sans-serif;">
                                        <?= $u['total_properties'] ?> Properties
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <a href="partner_properties.php?user_id=<?= $u['id'] ?>"
                                       class="btn btn-theme-block w-100 d-flex align-items-center justify-content-center gap-2"
                                       style="background: #000000; border: none; border-radius: 0px; font-weight: 700; padding: 13px; color: #ffffff; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; font-family: 'Poppins', sans-serif; transition: all 0.25s ease;">
                                        View Properties
                                        <i class="fa-solid fa-chevron-right style-arrow" style="font-size: 0.7rem; transition: transform 0.2s ease;"></i>
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-12">
                    <div class="text-center p-5 border style-fallback-alert" style="background: #ffffff; border-color: #ebebeb !important;">
                        <i class="fa-solid fa-folder-open mb-3 text-muted opacity-50" style="font-size: 2.5rem;"></i>
                        <h4 class="fw-bold text-dark mb-1" style="font-family: 'Poppins', sans-serif;">No Partners Found</h4>
                        <p class="text-muted mb-0 small">No registered resource partners discovered inside this sector scope.</p>
                    </div>
                </div>

            <?php endif; ?>

        </div>

    </div>

    <?php if($totalPages > 1): ?>
        <div class="container mt-5">
            <nav class="d-flex justify-content-center">
                <ul class="pagination" style="border-radius: 0px; box-shadow: none;">

                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>" style="margin: 0 3px;">
                            <a class="page-link template-pagination-link"
                               href="?page=<?= $i ?><?php if(!empty($_GET['city'])): ?>&city=<?= urlencode($_GET['city']) ?><?php endif; ?><?php if(!empty($_GET['category'])): ?>&category=<?= urlencode($_GET['category']) ?><?php endif; ?><?php if(!empty($_GET['purpose'])): ?>&purpose=<?= urlencode($_GET['purpose']) ?><?php endif; ?>"
                               style="padding: 12px 18px; font-weight: 600; font-size: 0.85rem; border: 1px solid #ebebeb; color: #000000; background: #ffffff; border-radius: 0px; transition: all 0.2s ease;">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                </ul>
            </nav>
        </div>
    <?php endif; ?>
</section>

<style>
    .prop-img-height { height: 150px; }
    .prop-price { font-size: 1rem; }
    .prop-title { font-size: 0.85rem; }
    
    /* Hover Transitions (Flat Shadow Shifts, Solid Green Transitions) */
    .theme-grid-box:hover {
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05) !important;
    }
    .theme-grid-box:hover .btn-theme-block {
        background: #2eca6a !important;
        color: #ffffff !important;
    }
    .theme-grid-box:hover .style-arrow {
        transform: translateX(3px);
    }
    
    /* Pagination Color Matching */
    .pagination .page-item.active .template-pagination-link {
        background-color: #2eca6a !important;
        border-color: #2eca6a !important;
        color: #ffffff !important;
    }
    .template-pagination-link:hover {
        background-color: #000000 !important;
        color: #ffffff !important;
        border-color: #000000 !important;
    }
    
    @media (min-width: 768px) {
        .prop-img-height { height: 240px; }
        .prop-price { font-size: 1.4rem; }
        .prop-title { font-size: 1.1rem; }
    }
</style>

<?php 
include('includes/footer.php'); 
?>