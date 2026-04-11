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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Plus | Discover Your Future Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-gold: #fbbf24; /* From your logo + */
            --brand-green: #16a34a; /* From your logo arch */
            --brand-silver: #94a3b8; /* From your logo house */
            --slate-900: #0f172a;
            --bg-light: #f8fafc;
        }

        body { 
            background-color: var(--bg-light); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--slate-900); 
        }
        
        /* Hero Section with Logo Inspiration */
        .hero-section {
            background: linear-gradient(rgba(15, 23, 42, 0.02), rgba(15, 23, 42, 0.05));
            border-bottom: 1px solid #e2e8f0;
            padding: 80px 0;
            margin-bottom: 40px;
        }

        .hero-title {
            font-weight: 800;
            font-size: 2.5rem;
            letter-spacing: -1px;
            color: var(--slate-900);
        }

        .hero-title span { color: var(--brand-green); }

        .filter-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .form-control, .form-select {
            border: 1px solid #f1f5f9;
            background: #f8fafc;
            font-weight: 600;
            padding: 12px;
            border-radius: 12px;
        }

        .form-control:focus, .form-select:focus { 
            background: #fff;
            border-color: var(--brand-gold);
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1); 
        }

        .btn-search {
            background-color: var(--brand-gold);
            color: var(--slate-900);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 800;
            transition: 0.3s;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        .btn-search:hover { background-color: #f59e0b; transform: scale(1.02); }

        /* Property Cards Style */
        .prop-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .prop-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border-color: var(--brand-green);
        }

        .prop-img-wrapper { position: relative; }

        .purpose-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(255,255,255,0.9);
            padding: 5px 12px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--brand-green);
            backdrop-filter: blur(4px);
        }

        .prop-img { width: 100%; height: 240px; object-fit: cover; }
        .no-img { height: 240px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; color: var(--brand-silver); }

        .prop-details { padding: 25px; }

        .prop-price {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--brand-green);
            margin-bottom: 5px;
        }

        .prop-location {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .prop-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: var(--slate-900);
            line-height: 1.4;
        }

        .prop-meta {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #64748b;
        }

        /* Access UI Refined */
        .details-unlocked {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 12px;
            padding: 12px;
            font-size: 0.85rem;
            color: #166534;
            line-height: 1.5;
        }

        .btn-access {
            background: #fff;
            color: var(--slate-900);
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            width: 100%;
            padding: 10px;
            font-weight: 700;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: 0.2s;
            font-size: 0.9rem;
        }

        .btn-access:hover { 
            background: var(--slate-900); 
            color: white; 
            border-color: var(--slate-900); 
        }

        .limit-reached {
            background: #fef2f2;
            color: #991b1b;
            padding: 8px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container text-center">
        <h1 class="hero-title mb-2">Find Your <span>Perfect</span> Space</h1>
        <p class="text-secondary mb-5 fw-600">Premium listings tailored to your lifestyle</p>
        
        <div class="filter-card mx-auto" style="max-width: 900px;">
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
                    <button class="btn btn-search w-100">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="row g-4">
        <?php foreach ($properties as $p): ?>
            <div class="col-lg-4 col-md-6">
                <div class="prop-card h-100">
                    <div class="prop-img-wrapper">
                        <span class="purpose-badge"><?= ucfirst($p['purpose'] ?? 'Listing') ?></span>
                        <?php 
                        $imgStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1");
                        $imgStmt->execute([$p['id']]);
                        $image = $imgStmt->fetchColumn();

                        if ($image): ?>
                            <img src='uploads/<?= $image ?>' class="prop-img" alt="Property">
                        <?php else: ?>
                            <div class="no-img"><i class="fa-solid fa-house-chimney fa-3x"></i></div>
                        <?php endif; ?>
                    </div>

                    <div class="prop-details">
                        <div class="prop-price">₹<?= number_format($p['price']) ?></div>
                        <div class="prop-location">
                            <i class="fa-solid fa-location-dot me-1 text-danger"></i> <?= htmlspecialchars($p['city']) ?>
                        </div>
                        <div class="prop-title"><?= htmlspecialchars($p['title']) ?></div>
                        
                        <div class="prop-meta">
                            <span><i class="fa-solid fa-layer-group me-1"></i> <?= $p['category'] ?? 'General' ?></span>
                            <span><i class="fa-solid fa-ruler-combined me-1"></i> <?= $p['area'] ?? '-' ?> <?= $p['area_unit'] ?? '' ?></span>
                        </div>

                        <div class="access-box pt-3 border-top">
                            <div class="details-unlocked mb-3">
                                <strong>Description:</strong> <?= htmlspecialchars(substr($p['description'], 0, 100)) ?>...
                            </div>

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
                                            <a href="view_contact.php?id=<?= $p['id'] ?>" class="btn-access">View Contact</a>
                                        <?php else: ?>
                                            <div class="limit-reached">Limit Reached</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-6">
                                        <?php if ($requestCount < 5): ?>
                                            <a href="send_request.php?id=<?= $p['id'] ?>" class="btn-access">Contact Owner</a>
                                        <?php else: ?>
                                            <div class="limit-reached">Limit Reached</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn-access">
                                    <i class="fa-solid fa-lock me-2"></i> Login to View Contact
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>