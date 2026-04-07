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
    <title>My Listings | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
            --blue-600: #2563eb;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .page-header {
            padding: 40px 0;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            margin-bottom: 40px;
        }

        .listing-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 20px;
            transition: box-shadow 0.2s;
        }

        .listing-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .property-thumb {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            background: #f1f5f9;
        }

        .status-badge {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 30px;
        }

        .status-approved { background: #f0fdf4; color: #166534; }
        .status-pending { background: #fffbeb; color: #92400e; }
        .status-rejected { background: #fef2f2; color: #991b1b; }

        .btn-manage {
            border: 1px solid var(--slate-200);
            color: var(--slate-900);
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-manage:hover {
            background: var(--slate-900);
            color: white;
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">My Property Listings</h2>
            <p class="text-secondary mb-0">Manage and track your uploaded properties.</p>
        </div>
        <a href="add_property.php" class="btn btn-dark rounded-pill px-4">
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
                        <div class="d-flex gap-2">
                            <?php 
                            $img = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ? LIMIT 1");
                            $img->execute([$p['id']]);
                            $main_img = $img->fetch();
                            
                            if ($main_img): ?>
                                <img src='../uploads/<?php echo $main_img['image_path']; ?>' class="property-thumb">
                            <?php else: ?>
                                <div class="property-thumb d-flex align-items-center justify-content-center">
                                    <i class="fa-regular fa-image fa-2x text-light"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col px-md-4">
                        <div class="d-flex align-items-center gap-3 mb-1">
                            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($p['title']); ?></h5>
                            <span class="status-badge <?php echo ($p['status'] == 'approved') ? 'status-approved' : 'status-pending'; ?>">
                                <?php echo $p['status']; ?>
                            </span>
                        </div>
                        <p class="text-secondary small mb-2">
                            <i class="fa-solid fa-location-dot me-1"></i> <?php echo htmlspecialchars($p['city']); ?>
                        </p>
                        <div class="fw-bold text-dark">
                            ₹<?php echo number_format($p['price']); ?>
                        </div>
                    </div>

                    <div class="col-md-3 text-md-end">
                        <div class="d-flex flex-md-column gap-2 justify-content-end">
                            <a href="edit_property.php?id=<?php echo $p['id']; ?>" class="btn-manage">
                                <i class="fa-solid fa-pen-to-square me-2"></i>Edit
                            </a>
                            <a href="delete_property.php?id=<?php echo $p['id']; ?>" class="btn-manage text-danger border-danger-subtle" onclick="return confirm('Delete this property?')">
                                <i class="fa-solid fa-trash me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-house-chimney-crack fa-4x mb-3 text-light"></i>
            <h4 class="text-secondary">No properties found.</h4>
            <p class="text-muted">Start by adding your first property listing.</p>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>