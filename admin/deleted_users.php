<?php
require 'auth.php';
require '../config/db.php';

// Fetch deleted users
$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE account_deleted = 1
    ORDER BY deleted_at DESC
");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deleted Accounts | PropertyPlus Admin</title>
    
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
        .badge-deleted { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }
        .badge-plan { background: #f0fdf4; color: #166534; border: 1px solid #dcfce7; }
        
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
                    <h2 class="fw-800 text-dark mb-1">Deleted Accounts</h2>
                    <p class="text-secondary mb-0">View and audit previously terminated partner records.</p>
                </div>
            </div>

            <div class="main-card">
                <div class="table-responsive">
                    <table id="deletedUserTable" class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Partner</th>
                                <th>Business Details</th>
                                <th>Contact Info</th>
                                <th>Address</th>
                                <th>Membership</th>
                                <th>Deleted On</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(count($users)): ?>
                            <?php foreach($users as $index => $user): ?>
                            <tr>
                                <td class="text-secondary fw-bold">#<?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if(!empty($user['profile_photo'])): ?>
                                            <img src="../uploads/profile/<?= htmlspecialchars($user['profile_photo']) ?>" width="45" height="45" class="rounded-circle border">
                                        <?php else: ?>
                                            <img src="../assets/images/default-user.png" width="45" height="45" class="rounded-circle border">
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($user['name']) ?></div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">ID: <?= htmlspecialchars($user['id']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-1">
                                        <?= htmlspecialchars($user['business_name'] ?? 'N/A') ?>
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        <strong>Category:</strong> <?= htmlspecialchars($user['category'] ?? 'N/A') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-600 small mb-1">
                                        <i class="fa-solid fa-phone me-2 text-muted"></i><?= htmlspecialchars($user['phone']) ?>
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        <i class="fa-solid fa-envelope me-2"></i><?= htmlspecialchars($user['email'] ?? 'No Email') ?>
                                    </div>
                                </td>
                                <td style="max-width:200px;">
                                    <div class="small mb-1">
                                        <i class="fa-solid fa-location-dot me-2 text-danger"></i>
                                        <?= htmlspecialchars($user['district'] ?? 'N/A') ?>
                                    </div>
                                    <div class="small text-muted">
                                        <i class="fa-solid fa-map me-2"></i>
                                        <?= htmlspecialchars($user['state'] ?? 'N/A') ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-plan px-2 py-1 rounded-pill small">
                                        <i class="fa-solid fa-gem me-1"></i> <?= htmlspecialchars($user['membership_plan'] ?? 'None') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-deleted px-3 py-2 rounded-pill d-inline-flex align-items-center">
                                        <i class="fa-solid fa-calendar-xmark me-2"></i>
                                        <strong>
                                            <?php
                                            if(!empty($user['deleted_at'])){
                                                echo date("d M Y, h:i A", strtotime($user['deleted_at']));
                                            }else{
                                                echo "-";
                                            }
                                            ?>
                                        </strong>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#deletedUserTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "info": true,
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Search deleted accounts...",
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