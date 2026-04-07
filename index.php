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
    $where[] = "type = ?";
    $params[] = $_GET['type'];
}

$sql = "SELECT * FROM properties WHERE " . implode(" AND ", $where);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Plus | Premium Real Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #fcfcfd; font-family: 'Inter', sans-serif; color: #1a1a1a; }
        
        /* Premium Hero Filter Section */
        .hero-section {
            background: #ffffff;
            border-bottom: 1px solid #eaeaea;
            padding: 50px 0;
            margin-bottom: 40px;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        .form-control, .form-select {
            border: none;
            font-weight: 500;
            padding: 12px;
        }

        .form-control:focus, .form-select:focus { box-shadow: none; }

        .btn-search {
            background-color: #1a1a1a;
            color: white;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-search:hover { background-color: #333; color: white; }

        /* Property Cards */
        .prop-card {
            background: #ffffff;
            border: 1px solid #efefef;
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .prop-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.06);
        }

        .prop-img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background-color: #f5f5f5;
        }

        .no-img {
            height: 220px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ccc;
        }

        .prop-details { padding: 20px; }

        .prop-price {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .prop-location {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .prop-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        /* Access Logic UI */
        .access-box {
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-access {
            background: #f5f5f7;
            color: #1a1a1a;
            border: 1px solid #e1e1e3;
            border-radius: 8px;
            width: 100%;
            padding: 10px;
            font-weight: 600;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: 0.2s;
        }

        .btn-access:hover { background: #1a1a1a; color: white; border-color: #1a1a1a; }

        .details-unlocked {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.9rem;
            color: #166534;
        }
    </style>
</head>
<body>

<section class="hero-section">
    <div class="container text-center">
        <h2 class="fw-bold mb-4">Discover Exclusive Listings</h2>
        <div class="filter-card mx-auto" style="max-width: 800px;">
            <form method="GET" class="row g-0 align-items-center">
                <div class="col-md-5 border-end">
                    <div class="d-flex align-items-center px-3">
                        <i class="fa-solid fa-location-dot text-muted"></i>
                        <input name="city" class="form-control" placeholder="City" value="<?= htmlspecialchars($_GET['city'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-4 border-end">
                    <div class="d-flex align-items-center px-3">
                        <i class="fa-solid fa-house text-muted"></i>
                        <select name="type" class="form-select">
                            <option value="">Property Type</option>
                            <option <?= (($_GET['type'] ?? '')=='Apartment')?'selected':'' ?>>Apartment</option>
                            <option <?= (($_GET['type'] ?? '')=='Villa')?'selected':'' ?>>Villa</option>
                            <option <?= (($_GET['type'] ?? '')=='Plot')?'selected':'' ?>>Plot</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 p-1">
                    <button class="btn btn-search w-100">Search</button>
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
                    
                    <?php 
                    // ✅ Fetch image (Logic Untouched)
                    $imgStmt = $pdo->prepare("SELECT image_path FROM property_images WHERE property_id=? LIMIT 1");
                    $imgStmt->execute([$p['id']]);
                    $image = $imgStmt->fetchColumn();

                    if ($image): ?>
                        <img src='uploads/<?= $image ?>' class="prop-img" alt="Property">
                    <?php else: ?>
                        <div class="no-img"><i class="fa-regular fa-image fa-3x"></i></div>
                    <?php endif; ?>

                    <div class="prop-details">
                        <div class="prop-price">₹<?= number_format($p['price']) ?></div>
                        <div class="prop-location">
                            <i class="fa-solid fa-map-pin me-2 text-muted"></i> <?= htmlspecialchars($p['city']) ?>
                        </div>
                        <div class="prop-title"><?= htmlspecialchars($p['title']) ?></div>

                        <div class="access-box">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php
                                // ✅ Access Logic (Logic Untouched)
                                $check = $pdo->prepare("
                                    SELECT * FROM access_requests 
                                    WHERE property_id=? AND requester_id=? AND status='approved'
                                ");
                                $check->execute([$p['id'], $_SESSION['user_id']]);

                                if ($check->rowCount() > 0): ?>
                                    <div class="details-unlocked">
                                        <i class="fa-solid fa-circle-check me-2"></i>
                                        <b>Full Details:</b> <?= htmlspecialchars($p['description']) ?>
                                    </div>
                                <?php else: ?>
                                    <a href='request_access.php?id=<?= $p['id'] ?>' class="btn-access">
                                        Request Full Access
                                    </a>
                                <?php endif; ?>

                            <?php else: ?>
                                <a href="auth/login.php" class="text-decoration-none text-muted small fw-bold">
                                    <i class="fa-solid fa-lock me-1"></i> Login to Request Access
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