<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Listings | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-gold: #fbbf24;
            --brand-green: #16a34a;
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .page-header {
            padding: 50px 0;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            margin-bottom: 40px;
        }

        .listing-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            overflow: hidden;
            margin-bottom: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .listing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: var(--brand-gold);
        }

        .property-thumb {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 18px;
            background: #f1f5f9;
            transition: transform 0.3s;
        }

        .listing-card:hover .property-thumb {
            transform: scale(1.05);
        }

        .status-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 10px;
            letter-spacing: 0.5px;
        }

        .status-approved { background: rgba(22, 163, 74, 0.1); color: var(--brand-green); }
        .status-pending { background: rgba(251, 191, 36, 0.1); color: #b45309; }
        .status-rejected { background: #fef2f2; color: #991b1b; }

        .price-tag {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--slate-900);
            display: flex;
            align-items: center;
        }

        .btn-manage {
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            color: var(--slate-900);
            font-size: 0.85rem;
            font-weight: 700;
            border-radius: 12px;
            padding: 10px 18px;
            text-decoration: none;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-manage:hover {
            background: var(--slate-900);
            color: white !important;
        }

        .btn-delete {
            color: #ef4444 !important;
            border-color: #fee2e2;
        }

        .btn-delete:hover {
            background: #ef4444;
            border-color: #ef4444;
        }

        .new-listing-btn {
            background: var(--brand-gold);
            color: var(--slate-900) !important;
            font-weight: 800;
            border: none;
            padding: 12px 28px;
            border-radius: 14px;
            transition: 0.3s;
        }

        .new-listing-btn:hover {
            background: #f59e0b;
            box-shadow: 0 10px 15px rgba(251, 191, 36, 0.3);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-extrabold mb-1" style="font-weight: 800; letter-spacing: -1px;">My Property Listings</h1>
            <p class="text-secondary mb-0 fw-medium">Manage and track your active real estate portfolio.</p>
        </div>
        <a href="add_property.php" class="new-listing-btn text-decoration-none">
            <i class="fa-solid fa-plus me-2"></i>New Listing
        </a>
    </div>
</div>

<div class="container pb-5">
    <?php if (count($properties) > 0): ?>
        <?php foreach ($properties as $p): ?>
            <div class="listing-card p-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="overflow-hidden rounded-4">
                            <?php 
                            $img = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ? LIMIT 1");
                            $img->execute([$p['id']]);
                            $main_img = $img->fetch();
                            
                            if ($main_img): ?>
                                <img src='../uploads/<?php echo $main_img['image_path']; ?>' class="property-thumb">
                            <?php else: ?>
                                <div class="property-thumb d-flex align-items-center justify-content-center bg-light">
                                    <i class="fa-solid fa-building fa-2x text-slate-200"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col px-md-4">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <span class="status-badge <?php echo ($p['status'] == 'approved') ? 'status-approved' : 'status-pending'; ?>">
                                <i class="fa-solid fa-circle small me-1"></i> <?php echo $p['status']; ?>
                            </span>
                            <span class="text-muted small fw-medium">ID: #<?php echo 1000 + $p['id']; ?></span>
                        </div>
                        <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($p['title']); ?></h4>
                        <p class="text-secondary small mb-3">
                            <i class="fa-solid fa-location-dot me-1 text-gold"></i> <?php echo htmlspecialchars($p['city']); ?>
                        </p>
                        <div class="price-tag">
                            <span class="text-success me-1">₹</span><?php echo number_format($p['price']); ?>
                        </div>
                    </div>

                    <div class="col-md-3 text-md-end mt-3 mt-md-0">
                        <div class="d-flex flex-md-column gap-2 justify-content-end">
                            <a href="edit_property.php?id=<?php echo $p['id']; ?>" class="btn-manage">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Edit Details
                            </a>
                            <a href="delete_property.php?id=<?php echo $p['id']; ?>" class="btn-manage btn-delete" onclick="return confirm('Permanently delete this listing?')">
                                <i class="fa-solid fa-trash-can me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fa-solid fa-house-chimney-crack fa-5x text-slate-200"></i>
            </div>
            <h3 class="fw-bold text-dark">Your portfolio is empty</h3>
            <p class="text-muted mb-4">You haven't uploaded any properties yet. Start your journey today.</p>
            <a href="add_property.php" class="new-listing-btn text-decoration-none d-inline-block">
                List Your First Property
            </a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>