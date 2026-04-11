<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->prepare("
    SELECT cr.*, p.title, u.phone AS requester_phone
    FROM contact_requests cr
    JOIN properties p ON cr.property_id = p.id
    JOIN users u ON cr.sender_id = u.id
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
    <title>Access Requests | PropertyPlus</title>
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
            --rose-600: #e11d48;
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .header-section {
            padding: 50px 0;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            margin-bottom: 40px;
        }

        .request-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .request-card:hover {
            transform: translateY(-3px);
            border-color: var(--brand-gold);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
        }

        .property-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94a3b8;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .status-pill {
            font-size: 0.75rem;
            font-weight: 800;
            padding: 6px 16px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .pill-pending { background: rgba(251, 191, 36, 0.1); color: #b45309; }
        .pill-accepted { background: rgba(22, 163, 74, 0.1); color: var(--brand-green); }
        .pill-rejected { background: rgba(225, 29, 72, 0.1); color: var(--rose-600); }

        .btn-approve {
            background-color: var(--brand-green);
            color: white !important;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
        }

        .btn-reject {
            background-color: transparent;
            color: var(--rose-600) !important;
            border: 1px solid #fee2e2;
            border-radius: 14px;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
        }

        .btn-approve:hover { 
            background-color: #15803d; 
            transform: scale(1.03);
        }
        
        .btn-reject:hover { 
            background-color: #fff1f2; 
            border-color: var(--rose-600);
        }

        .requester-info {
            background: var(--slate-50);
            padding: 10px 15px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
            border: 1px solid var(--slate-200);
        }

        .empty-state {
            text-align: center;
            padding: 100px 0;
        }
        
        .empty-icon {
            height: 100px;
            width: 100px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.03);
            color: var(--slate-200);
        }
    </style>
</head>
<body>

<div class="header-section">
    <div class="container">
        <h1 class="fw-extrabold mb-1" style="font-weight: 800; letter-spacing: -1px;">Access Requests</h1>
        <p class="text-secondary mb-0 fw-medium">Securely manage inquiries for your private listings.</p>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <?php if (count($requests) > 0): ?>
                <?php foreach ($requests as $r): ?>
                    <div class="request-card shadow-sm">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="property-label">Property Details</div>
                                <h5 class="fw-bold mb-3"><?php echo htmlspecialchars($r['title']); ?></h5>
                                <div class="requester-info">
                                    <i class="fa-solid fa-user-check me-2 text-gold"></i>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($r['requester_phone']); ?></span>
                                </div>
                            </div>
                            
                            <div class="col-md-3 text-md-center py-4 py-md-0">
                                <span class="status-pill pill-<?php echo $r['status']; ?>">
                                    <i class="fa-solid fa-circle small me-1"></i> <?php echo $r['status']; ?>
                                </span>
                            </div>

                            <div class="col-md-4 text-md-end">
                                <?php if ($r['status'] == 'pending'): ?>
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <a href='reject.php?id=<?php echo $r['id']; ?>' class="btn-reject">
                                            <i class="fa-solid fa-xmark me-2"></i> Decline
                                        </a>
                                        <a href='approve.php?id=<?php echo $r['id']; ?>' class="btn-approve">
                                            <i class="fa-solid fa-check me-2"></i> Approve
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted small">
                                        <i class="fa-regular fa-clock me-1"></i> 
                                        Processed on <span class="fw-bold"><?php echo date("d M, Y"); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa-solid fa-inbox fa-3x"></i>
                    </div>
                    <h3 class="fw-bold text-dark">No Pending Requests</h3>
                    <p class="text-muted">You're all caught up! New inquiries will appear here.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>