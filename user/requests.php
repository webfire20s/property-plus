<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->prepare("
    SELECT ar.*, p.title 
    FROM access_requests ar
    JOIN properties p ON ar.property_id = p.id
    WHERE p.user_id = ?
");

$stmt->execute([$_SESSION['user_id']]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Requests | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
            --blue-600: #2563eb;
            --emerald-600: #059669;
            --rose-600: #e11d48;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .header-section {
            padding: 40px 0;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            margin-bottom: 30px;
        }

        .request-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.2s;
        }

        .request-card:hover {
            border-color: var(--blue-600);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .property-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .status-pill {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .pill-pending { background: #fffbeb; color: #92400e; }
        .pill-approved { background: #f0fdf4; color: #166534; }
        .pill-rejected { background: #fef2f2; color: #991b1b; }

        .btn-approve {
            background-color: var(--emerald-600);
            color: white !important;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 16px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-reject {
            background-color: #fff;
            color: var(--rose-600) !important;
            border: 1px solid var(--rose-600);
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 8px 16px;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-approve:hover { background-color: #047857; }
        .btn-reject:hover { background-color: var(--rose-600); color: white !important; }

        .empty-state {
            text-align: center;
            padding: 80px 0;
            color: #94a3b8;
        }
    </style>
</head>
<body>

<div class="header-section">
    <div class="container">
        <h2 class="fw-bold mb-1">Access Requests</h2>
        <p class="text-secondary mb-0">Manage who can view your private property details.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <?php if (count($requests) > 0): ?>
                <?php foreach ($requests as $r): ?>
                    <div class="request-card">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="property-label">Property Title</div>
                                <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($r['title']); ?></h5>
                            </div>
                            
                            <div class="col-md-2 text-md-center py-3 py-md-0">
                                <span class="status-pill pill-<?php echo $r['status']; ?>">
                                    <?php echo $r['status']; ?>
                                </span>
                            </div>

                            <div class="col-md-4 text-md-end">
                                <?php if ($r['status'] == 'pending'): ?>
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <a href='approve.php?id=<?php echo $r['id']; ?>' class="btn-approve">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </a>
                                        <a href='reject.php?id=<?php echo $r['id']; ?>' class="btn-reject">
                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small italic">Processed on <?php echo date("d M"); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fa-solid fa-inbox fa-4x mb-3 opacity-25"></i>
                    <h4>No requests yet</h4>
                    <p>When users want to see your property details, they will appear here.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>