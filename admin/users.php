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
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    
    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        
        .main-content { padding: 40px; }

        .main-card { 
            background: white; 
            border-radius: 20px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); 
            overflow: hidden;
            padding: 20px;
        }

        /* DataTable Custom Styling */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            margin-left: 10px;
            outline: none;
        }
        
        .table { vertical-align: middle; margin: 20px 0 !important; }
        .table thead th { 
            background: #f8fafc; 
            color: #64748b; 
            font-size: 0.7rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 15px 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        .table tbody td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; }

        /* Status Badges */
        .badge-pending { background: #fffbeb; color: #92400e; border: 1px solid #fef3c7; }
        .badge-active { background: #f0fdf4; color: #166534; border: 1px solid #dcfce7; }
        .badge-blocked { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

        /* Actions */
        .btn-action { 
            padding: 8px 16px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            border-radius: 10px; 
            text-decoration: none; 
            transition: all 0.2s; 
            display: inline-flex;
            align-items: center;
            border: none;
        }
        .btn-approve { background: #2eca6a; color: white !important; }
        .btn-block { background: #fee2e2; color: #dc2626 !important; }
        .btn-activate { background: #3b82f6; color: white !important; }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        
        /* Sidebar offset fix */
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
                    <h2 class="fw-800 text-dark mb-1">User Management</h2>
                    <p class="text-secondary mb-0">Monitor partner activity and manage platform access.</p>
                </div>
                <button class="btn btn-white border shadow-sm rounded-3 px-3 fw-600">
                    <i class="fa-solid fa-file-export me-2 text-success"></i>Export Users
                </button>
            </div>

            <div class="main-card">
                <div class="table-responsive">
                    <table id="userTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Partner Details</th>
                                <th>Contact Info</th>
                                <th>Status</th>
                                <th class="text-end">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Logic remains untouched
                        $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
                        foreach ($users as $u):
                        ?>
                        <tr>
                            <td class="text-secondary fw-bold">#<?= $u['id'] ?></td>
                            <td>
                                <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($u['business_name'] ?? 'N/A') ?></div>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-light text-muted border fw-normal" style="font-size: 0.65rem;">
                                        RERA: <?= htmlspecialchars($u['rera_number'] ?? 'N/A') ?>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-600 small mb-1">
                                    <i class="fa-solid fa-phone me-2 text-muted"></i><?= htmlspecialchars($u['phone']) ?>
                                </div>
                                <div class="text-muted extra-small" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars($u['email'] ?? 'No Email') ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($u['status'] == 'pending'): ?>
                                    <span class="badge badge-pending px-3 py-2 rounded-pill"><i class="fa-solid fa-hourglass-start me-1"></i> Pending Review</span>
                                <?php elseif ($u['status'] == 'active' || $u['status'] == 'approved'): ?>
                                    <span class="badge badge-active px-3 py-2 rounded-pill"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                                <?php elseif ($u['status'] == 'blocked'): ?>
                                    <span class="badge badge-blocked px-3 py-2 rounded-pill"><i class="fa-solid fa-ban me-1"></i> Blocked</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <?php if ($u['status'] == 'pending'): ?>
                                        <a href="approve_user.php?id=<?= $u['id'] ?>" class="btn-action btn-approve" onclick="return confirm('Verify and approve this partner?')">
                                            <i class="fa-solid fa-check me-1"></i> Approve
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($u['status'] == 'active' || $u['status'] == 'approved'): ?>
                                        <a href="block_user.php?id=<?= $u['id'] ?>" class="btn-action btn-block" onclick="return confirm('Are you sure you want to block this user?')">
                                            <i class="fa-solid fa-user-slash me-1"></i> Block
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($u['status'] == 'blocked'): ?>
                                        <a href="activate_user.php?id=<?= $u['id'] ?>" class="btn-action btn-activate">
                                            <i class="fa-solid fa-undo me-1"></i> Unblock
                                        </a>
                                    <?php endif; ?>
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
        $('#userTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "info": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search users...",
                "paginate": {
                    "previous": "<i class='fa-solid fa-chevron-left'></i>",
                    "next": "<i class='fa-solid fa-chevron-right'></i>"
                }
            },
            "drawCallback": function() {
                $('.dataTables_paginate > .pagination').addClass('pagination-sm');
            }
        });
    });
</script>
</body>
</html>