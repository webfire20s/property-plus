<?php
require 'auth.php';
require '../config/db.php';

// SQL logic remains untouched
$stmt = $pdo->query("
    SELECT 
        cr.id,
        cr.status,
        cr.created_at,
        sender.phone AS sender_phone,
        owner.phone AS owner_phone,
        p.title
    FROM contact_requests cr
    JOIN users sender ON cr.sender_id = sender.id
    JOIN properties p ON cr.property_id = p.id
    JOIN users owner ON p.user_id = owner.id
    ORDER BY cr.id DESC
");

$leads = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Management | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .main-content { padding: 40px; }
        .main-card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow: hidden; }
        
        .table thead th { 
            background: #f8fafc; 
            color: #64748b; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 15px 20px; 
        }

        .status-pill {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
        }
        .status-pending { background: #fffbeb; color: #92400e; }
        .status-accepted { background: #f0fdf4; color: #166534; }
        .status-rejected { background: #fef2f2; color: #991b1b; }

        .phone-link { color: #0f172a; text-decoration: none; font-weight: 600; }
        .phone-link:hover { color: #2563eb; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 offset-md-2 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Lead Management</h2>
                    <p class="text-secondary mb-0">Monitor connections between buyers and property owners.</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-white text-dark border px-3 py-2 rounded-pill shadow-sm">
                        Total Leads: <?= count($leads) ?>
                    </span>
                </div>
            </div>

            <div class="main-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>Property Details</th>
                                <th>Requester (Buyer)</th>
                                <th>Listing Owner</th>
                                <th>Current Status</th>
                                <th class="pe-4">Submission Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $l): ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold">#<?= $l['id'] ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($l['title']) ?></div>
                                    <small class="text-muted">Direct Inquiry</small>
                                </td>
                                <td>
                                    <a href="tel:<?= $l['sender_phone'] ?>" class="phone-link">
                                        <i class="fa-solid fa-phone me-1 text-primary small"></i> 
                                        <?= htmlspecialchars($l['sender_phone']) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?= $l['owner_phone'] ?>" class="phone-link">
                                        <i class="fa-solid fa-user-tie me-1 text-secondary small"></i> 
                                        <?= htmlspecialchars($l['owner_phone']) ?>
                                    </a>
                                </td>
                                <td>
                                    <?php 
                                        $s = strtolower($l['status']);
                                        $label = ucfirst($s);
                                    ?>
                                    <span class="status-pill status-<?= $s ?>">
                                        <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i>
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="text-dark fw-500"><?= date("d M Y", strtotime($l['created_at'])) ?></div>
                                    <small class="text-muted small"><?= date("h:i A", strtotime($l['created_at'])) ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>