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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        
        .main-content { padding: 40px; }
        
        .main-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); 
            overflow: hidden; 
            padding: 20px;
        }
        
        /* Premium Table Header */
        .table thead th { 
            background: #f1f5f9; 
            color: #475569; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 1.2px; 
            padding: 18px 20px; 
            border-bottom: 2px solid #e2e8f0;
            font-weight: 800;
        }

        .table tbody td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Property Plus Status Pills */
        .status-pill {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
        }
        .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .status-accepted { background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; }
        .status-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }

        .phone-link { 
            color: #334155; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.9rem;
            transition: 0.2s;
        }
        .phone-link:hover { color: #2eca6a; }
        
        .property-title {
            color: #0f172a;
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .lead-id {
            background: #f1f5f9;
            color: #64748b;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
        }

        /* DataTable Custom UI */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 6px 12px;
            outline: none;
            margin-bottom: 10px;
        }

        /* Offset for sidebar */
        @media (min-width: 768px) {
            .main-content { margin-left: 16.666667%; }
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 main-content">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-800 text-dark mb-1">Lead Management</h2>
                    <p class="text-secondary mb-0">Track buyer inquiries and owner responses across the platform.</p>
                </div>
                <div class="text-end">
                    <div class="badge bg-white text-success border border-success border-opacity-25 px-4 py-2 rounded-pill shadow-sm fw-bold">
                        <i class="fa-solid fa-chart-line me-2"></i>Total Leads: <?= count($leads) ?>
                    </div>
                </div>
            </div>

            <div class="main-card">
                <div class="table-responsive">
                    <table id="leadsTable" class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Lead ID</th>
                                <th>Property Asset</th>
                                <th>Buyer (Requester)</th>
                                <th>Listing Owner</th>
                                <th>Status</th>
                                <th class="pe-4">Date Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $l): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="lead-id fw-bold">#<?= $l['id'] ?></span>
                                </td>
                                <td>
                                    <div class="property-title"><?= htmlspecialchars($l['title']) ?></div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        <i class="fa-solid fa-tag me-1"></i>Direct Marketplace Inquiry
                                    </div>
                                </td>
                                <td>
                                    <a href="tel:<?= $l['sender_phone'] ?>" class="phone-link">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-soft-blue me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:30px; height:30px; background: #eff6ff;">
                                                <i class="fa-solid fa-phone text-primary" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <?= htmlspecialchars($l['sender_phone']) ?>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:<?= $l['owner_phone'] ?>" class="phone-link">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-soft-green me-2 rounded-circle d-flex align-items-center justify-content-center" style="width:30px; height:30px; background: #f0fdf4;">
                                                <i class="fa-solid fa-user-tie text-success" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <?= htmlspecialchars($l['owner_phone']) ?>
                                        </div>
                                    </a>
                                </td>
                                <td>
                                    <?php 
                                        $s = strtolower($l['status']);
                                        $label = ucfirst($s);
                                    ?>
                                    <span class="status-pill status-<?= $s ?>">
                                        <i class="fa-solid fa-circle me-2" style="font-size: 0.4rem;"></i>
                                        <?= $label ?>
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="text-dark fw-bold" style="font-size: 0.85rem;"><?= date("d M, Y", strtotime($l['created_at'])) ?></div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">
                                        <i class="fa-regular fa-clock me-1"></i><?= date("h:i A", strtotime($l['created_at'])) ?>
                                    </div>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#leadsTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "order": [[0, "desc"]], // Default sort by Lead ID descending
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search leads...",
                "paginate": {
                    "previous": "<i class='fa-solid fa-angle-left'></i>",
                    "next": "<i class='fa-solid fa-angle-right'></i>"
                }
            }
        });
    });
</script>
</body>
</html>