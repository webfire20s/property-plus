<?php
require 'auth.php';
require '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties | PropertyPlus Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .main-content { padding: 40px; }
        
        .property-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.05);
        }

        .status-badge {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 6px 12px;
            border-radius: 10px;
            font-weight: 700;
        }

        .status-pending { background: #fffbeb; color: #d97706; }
        .status-approved { background: #f0fdf4; color: #16a34a; }
        .status-rejected { background: #fef2f2; color: #dc2626; }

        .btn-approve { background: #2563eb; color: white; border-radius: 10px; font-weight: 600; font-size: 0.85rem; }
        .btn-approve:hover { background: #1d4ed8; color: white; }
        
        .btn-reject { background: #f1f5f9; color: #64748b; border-radius: 10px; font-weight: 600; font-size: 0.85rem; }
        .btn-reject:hover { background: #e2e8f0; color: #0f172a; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 offset-md-2 main-content">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Property Listings</h2>
                    <p class="text-secondary mb-0">Review and moderate all property submissions.</p>
                </div>
                <div class="badge bg-white text-dark border p-2 px-3 rounded-pill fw-600 shadow-sm">
                    <i class="fa-solid fa-house-circle-check me-2 text-primary"></i>
                    Total: <?= $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn(); ?>
                </div>
            </div>

            <div class="row g-4">
                <?php
                // Logic remains untouched
                $stmt = $pdo->query("SELECT * FROM properties ORDER BY id DESC");
                $properties = $stmt->fetchAll();

                foreach ($properties as $p):
                    $statusClass = 'status-' . strtolower($p['status']);
                ?>
                <div class="col-xl-4 col-md-6">
                    <div class="property-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="status-badge <?= $statusClass ?>">
                                <?= htmlspecialchars($p['status']) ?>
                            </span>
                            <small class="text-muted fw-bold">ID: #<?= $p['id'] ?></small>
                        </div>

                        <h5 class="fw-bold text-dark mb-3 line-clamp-2" style="min-height: 3rem;">
                            <?= htmlspecialchars($p['title']) ?>
                        </h5>

                        <div class="mt-auto pt-3 border-top">
                            <?php if ($p['status'] == 'pending'): ?>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="approve.php?id=<?= $p['id'] ?>" class="btn btn-approve w-100 py-2">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="reject.php?id=<?= $p['id'] ?>" class="btn btn-reject w-100 py-2">
                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-1">
                                    <small class="text-secondary italic">No actions required</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>