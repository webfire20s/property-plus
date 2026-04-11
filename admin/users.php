<?php
require 'auth.php';
require '../config/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | PropertyPlus Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Content area padding to handle the fixed sidebar */
        .main-content { padding: 40px; }

        .main-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
            overflow: hidden;
        }

        .table { vertical-align: middle; margin-bottom: 0; }
        .table thead th { 
            background: #f8fafc; 
            color: #64748b; 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 18px 20px;
            border-bottom: 1px solid #e2e8f0;
        }
        .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; }

        .badge-pending { background: #fffbeb; color: #92400e; border: 1px solid #fef3c7; }
        .badge-active { background: #f0fdf4; color: #166534; border: 1px solid #dcfce7; }
        .badge-blocked { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

        .btn-action { 
            padding: 7px 14px; 
            font-size: 0.8rem; 
            font-weight: 700; 
            border-radius: 10px; 
            text-decoration: none; 
            transition: all 0.2s; 
            display: inline-flex;
            align-items: center;
        }
        .btn-approve { background: #10b981; color: white !important; }
        .btn-block { background: #fee2e2; color: #dc2626 !important; }
        .btn-activate { background: #6366f1; color: white !important; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <?php include 'admin_sidebar.php'; ?>

        <div class="col-12 col-md-10 offset-md-2 main-content">
            
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold mb-1">User Management</h2>
                    <p class="text-secondary mb-0">Review partner applications and manage account access.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-white border shadow-sm rounded-3 px-3">
                        <i class="fa-solid fa-download me-2"></i>Export CSV
                    </button>
                </div>
            </div>

            <div class="main-card">
                <div class="p-3 border-bottom bg-light bg-opacity-50">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                <input type="text" class="form-control border-start-0" placeholder="Search by name, phone or RERA...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Partner Details</th>
                                <th>Contact Info</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Fetching logic remains untouched
                        $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
                        foreach ($users as $u):
                        ?>
                        <tr>
                            <td class="text-secondary fw-bold">#<?= $u['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($u['business_name'] ?? 'N/A') ?></div>
                                <div class="text-muted small" style="font-size: 0.75rem;">
                                    <span class="badge bg-light text-dark border fw-normal">RERA: <?= htmlspecialchars($u['rera_number'] ?? 'N/A') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-600 d-flex align-items-center">
                                    <i class="fa-solid fa-phone-volume me-2 text-primary" style="font-size: 0.8rem;"></i>
                                    <?= htmlspecialchars($u['phone']) ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($u['status'] == 'pending'): ?>
                                    <span class="badge badge-pending px-3 py-2 rounded-pill">Pending Review</span>
                                <?php elseif ($u['status'] == 'active' || $u['status'] == 'approved'): ?>
                                    <span class="badge badge-active px-3 py-2 rounded-pill">Verified Active</span>
                                <?php elseif ($u['status'] == 'blocked'): ?>
                                    <span class="badge badge-blocked px-3 py-2 rounded-pill">Blocked</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if ($u['status'] == 'pending'): ?>
                                        <a href="approve_user.php?id=<?= $u['id'] ?>" class="btn-action btn-approve" onclick="return confirm('Verify and approve this partner?')">
                                            <i class="fa-solid fa-check-double me-1"></i> Approve
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($u['status'] == 'active' || $u['status'] == 'approved'): ?>
                                        <a href="block_user.php?id=<?= $u['id'] ?>" class="btn-action btn-block" onclick="return confirm('Are you sure you want to block this user?')">
                                            <i class="fa-solid fa-user-slash me-1"></i> Block
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($u['status'] == 'blocked'): ?>
                                        <a href="activate_user.php?id=<?= $u['id'] ?>" class="btn-action btn-activate">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Unblock
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-3 bg-light bg-opacity-50 border-top">
                    <small class="text-muted">Showing <?= count($users) ?> total registered partners.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>