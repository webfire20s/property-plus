<?php
require 'config/db.php';
include 'includes/navbar.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔥 FILTER LOGIC (UNTOUCHED)
// $where = ["status IN ('active','approved')"];
// $params = [];

// if (!empty($_GET['search'])) {

//     $where[] = "(
//         business_name LIKE ?
//         OR district LIKE ?
//         OR state LIKE ?
//         OR phone LIKE ?
//         OR rera_number LIKE ?
//     )";

//     $search = "%" . trim($_GET['search']) . "%";

//     $params[] = $search;
//     $params[] = $search;
//     $params[] = $search;
//     $params[] = $search;
//     $params[] = $search;
// }
// ================= USERS FETCH =================
// PAGINATION
$limit = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

$userWhere = ["u.status IN ('active','approved')"];
$userParams = [];

if (!empty($_GET['search'])) {

    $userWhere[] = "(
        u.business_name LIKE ?
        OR u.district LIKE ?
        OR u.state LIKE ?
        OR u.phone LIKE ?
        OR u.rera_number LIKE ?
    )";

    $search = "%" . trim($_GET['search']) . "%";

    $userParams[] = $search;
    $userParams[] = $search;
    $userParams[] = $search;
    $userParams[] = $search;
    $userParams[] = $search;
}

// MAIN USERS QUERY
$sql = "
    SELECT 
        u.id,
        u.business_name,
        u.state,
        u.district,
        u.profile_photo,

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

    LEFT JOIN properties p 
        ON p.user_id = u.id
        AND p.status='approved'

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
        u.profile_photo,
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
    'Builder Floors', 'Apartments', 'Flats', 'Independent Floors',
    'Independent Kothi', 'Independent Villa', 'Society Flats',
    'Commercial Building', 'Commercial Shop', 'Commercial Showroom',
    'Commercial Floor', 'Land', 'Commercial Land', 'Agricultural Land'
];
// Fetch admin-managed hero sliders
$sliderStmt = $pdo->prepare("SELECT * FROM hero_slides ORDER BY id ASC LIMIT 6");
$sliderStmt->execute();
$adminSlides = $sliderStmt->fetchAll();

// If the admin hasn't uploaded images yet, fallback to your original theme images seamlessly
if (empty($adminSlides)) {
    $slidesToShow = [
        ['image_path' => 'https://cf.bstatic.com/xdata/images/hotel/max1024x768/659933963.jpg?k=d38117fb188c0d5a55a091a75703d3b915a1ee5de4bc9758833964156351dba5&o='],
        ['image_path' => 'assets/img/slide-2.jpg'],
        ['image_path' => 'assets/img/slide-3.jpg'],
        ['image_path' => 'assets/img/slide-1.jpg']
    ];
} else {
    $slidesToShow = [];
    foreach ($adminSlides as $as) {
        // Appends the upload directory prefix if your admin table stores just the file names
        $slidesToShow[] = ['image_path' => 'uploads/hero/' . $as['image_path']];
    }
}
?>

<style>
    /* Full Viewport Slider Wrapper */
    .hero-slider-viewport {
        position: relative;
        /* height: 75vh; Perfect cinematic height for the pure slider canvas */
        min-height: 700px;
        width: 100%;
        overflow: hidden;
        background-color: #000000;
        
    }

    .hero-slider-viewport .carousel,
    .hero-slider-viewport .carousel-inner,
    .hero-slider-viewport .carousel-item {
        width: 100%;
        height: 100%;
    }

    /* Elegant bottom fading mask to transition smoothly into the light content area */
    .hero-slider-viewport .hero-sliding-mask {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.4) 70%, #f8fafc 100%);
        z-index: 1;
    }

    /* Content Area Below Slider */
    .hero-content-section {
        background-color: #f8fafc; /* Aligns with your platform background token */
        padding: 30px 0 40px 0;
        position: relative;
        z-index: 10;
    }

    /* EstateAgency Geometric Card Hover Dynamics */
    .theme-grid-box:hover {
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.05) !important;
        transform: translateY(-2px);
    }
    .theme-grid-box:hover .btn-theme-block {
        background: #2eca6a !important;
        color: #ffffff !important;
    }
    .theme-grid-box:hover .style-arrow {
        transform: translateX(3px);
    }
    
    /* Template Pagination Theme Controls */
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

    /* Custom form styling elements matching your standard */
    .filter-input-group {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        background: #f1f5f9;
        transition: all 0.2s ease;
    }
    .filter-input-group:focus-within {
        border-color: #2eca6a;
        box-shadow: 0 0 0 3px rgba(46, 202, 106, 0.15);
    }

    @media (max-width: 991px) {
        .hero-slider-viewport {
            height: 50vh;
            min-height: 350px;
        }
        .hero-content-section {
            padding: 40px 0 60px 0;
        }
    }
</style>

<section class="hero-slider-viewport">
    <div id="heroSlider" class="carousel slide carousel-fade position-absolute top-0 start-0 w-100 h-100" data-bs-ride="carousel" data-bs-interval="4000" style="z-index: 1;">
        <div class="carousel-inner">
            <?php foreach($slidesToShow as $index => $slide): ?>
                <div class="carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                    <div style="background-image: url('<?= $slide['image_path'] ?>'); background-size: cover; background-position: center; width: 100%; height: 100%;"></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="hero-sliding-mask"></div>
</section>

<section class="hero-content-section">
    <div class="container">
        
        <div class="row align-items-center mb-5">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="brand-badge mb-3" style="display: inline-block; background: rgba(46, 202, 106, 0.12); color: #2eca6a; padding: 6px 18px; border-radius: 50px; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(46,202,106,0.25);">
                    <i class="fa-solid fa-house-circle-check me-2"></i>Verified Ecosystem
                </div>
                
                <h1 class="hero-title mb-3" style="font-weight: 800; font-size: calc(1.8rem + 1.2vw); color: #0f172a; line-height: 1.2; letter-spacing: -0.5px;">
                    A Platform <span style="color: #2eca6a;">Designed</span> for <br>Real Estate <span style="color: #ea580c;">Professionals.</span>
                </h1>
                
                <p class="text-secondary mb-4" style="font-size: calc(0.95rem + 0.1vw); max-width: 700px; line-height: 1.6; font-weight: 400;">
                    Property Plus is a membership-based platform designed for builders, brokers, agents, and freelancers to connect, showcase opportunities, and operate within a verified real estate ecosystem.
                </p>
                
                <div class="d-flex gap-2 gap-sm-3">
                    <a href="auth/register.php" class="btn btn-success px-4 py-2.5 shadow-sm" style="background: #2eca6a; border: none; font-weight: 700; border-radius: 12px; transition: transform 0.2s; font-size: 0.9rem;">Register Now</a>
                    <a href="#property-listings" class="btn btn-outline-dark px-4 py-2.5" style="border-radius: 12px; font-weight: 600; font-size: 0.9rem; border-color: #cbd5e1; color: #334155;">Explore Listings</a>
                </div>
            </div>
            
            <div class="col-lg-4 d-none d-lg-block">
                <div class="border" style="background: #ffffff; border-color: #e2e8f0 !important; padding: 30px; border-radius: 24px; display: inline-block; float: right; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);" data-aos="zoom-in">
                    <div class="text-center">
                        <div style="font-size: 2.75rem; font-weight: 800; color: #0f172a; letter-spacing: -1px;">100%</div>
                        <div style="font-weight: 700; color: #2eca6a; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px;">Verified Listings</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="filter-card shadow-sm bg-white"
            style="width:100%;padding:24px;border-radius:20px;border:1px solid #e2e8f0;">

            <div class="mb-3 ps-1">
                <span class="fw-700"
                    style="font-size:0.85rem;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">
                    <i class="fa-solid fa-users me-2 text-success"></i>
                    Find Dealers & Agencies
                </span>
            </div>

            <form method="GET" class="row g-3">

                <div class="col-md-11">

                    <div class="input-group filter-input-group">

                        <span class="input-group-text border-0 bg-transparent ps-3">
                            <i class="fa-solid fa-users text-muted"></i>
                        </span>

                        <input
                            type="text"
                            name="search"
                            class="form-control border-0 bg-transparent"
                            placeholder="Search Dealers, Agencies, Address, State, Phone or RERA"
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                            style="height:48px;font-size:0.9rem;"
                        >

                    </div>

                </div>

                <div class="col-md-1">

                    <button class="btn btn-success w-100"
                            style="height:48px;background:#2eca6a;border:none;border-radius:12px;">

                        <i class="fa-solid fa-magnifying-glass"></i>

                    </button>

                </div>

            </form>

        </div>

    </div>
</section>

<section id="property-listings" class="section-property section-t8 py-5" style="background: linear-gradient(180deg, #f7f7f7 0%, #cff5df 1%, #f2faf5 100%);">
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
                <span class="badge px-3 py-2.5 bg-white text-dark border" style="font-family: 'Poppins', sans-serif; border-radius: 0px; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">
                    <?= count($users) ?> Registered Partners
                </span>
            </div>
        </div>

        <div class="row g-3 g-sm-4">
            <?php if(count($users) > 0): ?>
                <?php foreach($users as $u): ?>
                    <div class="col-6 col-md-6 col-lg-4">
                        <?php
                        $membership = strtolower($u['membership_name']);
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
                            
                            <div class="p-3 p-sm-4 position-relative" style="background: #ffffff; flex-grow: 1;">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge text-white px-2.5 py-1.5" style="background: <?= $tierBadgeBg ?>; border-radius: 0px; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;">
                                        <?= htmlspecialchars($u['membership_name']) ?>
                                    </span>
                                    <span style="color: #2eca6a; font-size: 1.1rem;"><i class="fa-solid fa-circle-check"></i></span>
                                </div>
                                
                                <h4 class="fw-bold text-dark mb-1 text-truncate" style="font-family: 'Poppins', sans-serif; font-size: calc(0.9rem + 0.5vw); letter-spacing: -0.5px;">
                                    <?= htmlspecialchars($u['business_name']) ?>
                                </h4>
                                                                            
                                <p class="text-muted mb-0 small text-truncate" style="font-size: 0.7rem; font-family: 'Poppins', sans-serif;">
                                    <i class="fa-solid fa-location-dot me-1 text-success" style="font-size: 0.75rem;"></i><?= htmlspecialchars($u['district']) ?>, <?= htmlspecialchars($u['state']) ?>
                                </p>
                            </div>

                            <div class="p-3 p-sm-4 pt-0" style="background: #ffffff;">

                                <div class="text-center mb-3">
                                    <?php if(!empty($u['profile_photo'])): ?>
                                        <img src="uploads/profile_photos/<?= htmlspecialchars($u['profile_photo']) ?>"
                                            alt="Profile Photo"
                                            style="
                                                width: 75px;
                                                height: 75px;
                                                object-fit: contain;
                                                border-radius: 0px;
                                                border: 3px solid #f3f4f6;
                                                box-shadow: 0 4px 10px rgba(0,0,0,0.08);
                                                background: #ffffff;
                                                padding: 3px;
                                            ">
                                    <?php else: ?>
                                        <div style="
                                            width: 75px;
                                            height: 75px;
                                            border-radius: 0px;
                                            background: #f3f4f6;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            margin: auto;
                                            border: 3px solid #f3f4f6;
                                        ">
                                            <i class="fa-solid fa-user text-secondary" style="font-size: 1.6rem;"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-center py-2.5 mb-3"
                                    style="border-top: 1px dashed #ebebeb; border-bottom: 1px dashed #ebebeb;">
                                    <span class="text-secondary small fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.65rem;">
                                        Portfolio
                                    </span>
                                    <span class="fw-bold text-dark" style="font-size: 0.7rem; font-family: 'Poppins', sans-serif;">
                                        <?= $u['total_properties'] ?> Properties
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <a href="partner_properties.php?user_id=<?= $u['id'] ?>"
                                    class="btn btn-theme-block w-100 d-flex align-items-center justify-content-center gap-2"
                                    style="
                                            background: #000000;
                                            border: none;
                                            border-radius: 0px;
                                            font-weight: 600;
                                            padding: 07px 5px;
                                            color: #ffffff;
                                            text-transform: uppercase;
                                            font-size: 0.7rem;
                                            letter-spacing: 0.5px;
                                            font-family: 'Poppins', sans-serif;
                                            transition: all 0.25s ease;
                                    ">
                                        <span class="d-none d-sm-inline">View Properties</span>
                                        <span class="d-inline d-sm-none">View</span>
                                        <i class="fa-solid fa-chevron-right style-arrow" style="font-size: 0.65rem; transition: transform 0.2s ease;"></i>
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
        <?php
        // Build base dynamic query string to append safely to links
        $queryString = '';
        if(!empty($_GET['city']))     $queryString .= '&city=' . urlencode($_GET['city']);
        if(!empty($_GET['category'])) $queryString .= '&category=' . urlencode($_GET['category']);
        if(!empty($_GET['purpose']))  $queryString .= '&purpose=' . urlencode($_GET['purpose']);

        // Determine calculation parameters for current sliding window bounds
        $range = 2; 
        $initial_page_loop = max(1, $page - $range);
        $terminal_page_loop = min($totalPages, $page + $range);
        ?>
        <div class="container mt-5">
            <nav class="d-flex justify-content-center align-items-center flex-wrap">
                <ul class="pagination flex-wrap justify-content-center align-items-center" style="border-radius: 0px; box-shadow: none; margin: 0; padding: 0;">
                    
                    <?php if($page > 1): ?>
                        <li class="page-item" style="margin: 2px;">
                            <a class="page-link template-pagination-link" href="?page=1<?= $queryString ?>" style="padding: 10px 14px; font-weight: 600; font-size: 0.82rem; border: 1px solid #ebebeb; color: #000000; background: #ffffff; border-radius: 0px; transition: all 0.2s ease;" title="First Page">
                                <i class="fa-solid fa-angles-left"></i>
                            </a>
                        </li>
                        <li class="page-item" style="margin: 2px;">
                            <a class="page-link template-pagination-link" href="?page=<?= $page - 1 ?><?= $queryString ?>" style="padding: 10px 14px; font-weight: 600; font-size: 0.82rem; border: 1px solid #ebebeb; color: #000000; background: #ffffff; border-radius: 0px; transition: all 0.2s ease;" title="Previous">
                                <i class="fa-solid fa-angle-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($initial_page_loop > 1): ?>
                        <li class="page-item disabled" style="margin: 2px;">
                            <span class="page-link border-0 bg-transparent text-muted" style="padding: 10px 12px; font-weight: 600; font-size: 0.85rem;">...</span>
                        </li>
                    <?php endif; ?>

                    <?php for($i = $initial_page_loop; $i <= $terminal_page_loop; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>" style="margin: 2px;">
                            <a class="page-link template-pagination-link" href="?page=<?= $i ?><?= $queryString ?>" 
                               style="padding: 10px 16px; font-weight: 600; font-size: 0.85rem; border: 1px solid #ebebeb; <?= ($i == $page) ? 'background-color: #2eca6a !important; border-color: #2eca6a !important; color: #ffffff !important;' : 'color: #000000; background: #ffffff;' ?> border-radius: 0px; transition: all 0.2s ease;">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if($terminal_page_loop < $totalPages): ?>
                        <li class="page-item disabled" style="margin: 2px;">
                            <span class="page-link border-0 bg-transparent text-muted" style="padding: 10px 12px; font-weight: 600; font-size: 0.85rem;">...</span>
                        </li>
                    <?php endif; ?>

                    <?php if($page < $totalPages): ?>
                        <li class="page-item" style="margin: 2px;">
                            <a class="page-link template-pagination-link" href="?page=<?= $page + 1 ?><?= $queryString ?>" style="padding: 10px 14px; font-weight: 600; font-size: 0.82rem; border: 1px solid #ebebeb; color: #000000; background: #ffffff; border-radius: 0px; transition: all 0.2s ease;" title="Next">
                                <i class="fa-solid fa-angle-right"></i>
                            </a>
                        </li>
                        <li class="page-item" style="margin: 2px;">
                            <a class="page-link template-pagination-link" href="?page=<?= $totalPages ?><?= $queryString ?>" style="padding: 10px 14px; font-weight: 600; font-size: 0.82rem; border: 1px solid #ebebeb; color: #000000; background: #ffffff; border-radius: 0px; transition: all 0.2s ease;" title="Last Page">
                                <i class="fa-solid fa-angles-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                </ul>
            </nav>
        </div>
    <?php endif; ?>
</section>

<?php 
include 'includes/footer.php'; 
?>