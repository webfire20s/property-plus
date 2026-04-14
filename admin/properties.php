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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: #1e293b;
        }
        
        .main-content { padding: 40px; }
        
        .property-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 800;
        }

        .status-pending { background: #fffbeb; color: #b45309; border: 1px solid #fef3c7; }
        .status-approved { background: #f0fdf4; color: #166534; border: 1px solid #dcfce7; }
        .status-rejected { background: #fef2f2; color: #991b1b; border: 1px solid #fee2e2; }

        .btn-approve { 
            background: #0f172a; 
            color: white; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 0.8rem; 
            transition: all 0.2s;
        }
        .btn-approve:hover { background: #1e293b; color: white; transform: scale(1.02); }
        
        .btn-reject { 
            background: #f1f5f9; 
            color: #64748b; 
            border-radius: 12px; 
            font-weight: 700; 
            font-size: 0.8rem; 
        }
        .btn-reject:hover { background: #e2e8f0; color: #0f172a; }

        .price-tag {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .detail-item {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Sidebar Offset */
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
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-800 text-dark mb-1">Property Listings</h2>
                    <p class="text-secondary mb-0">Review and moderate all property submissions.</p>
                </div>
                <div class="text-end">
                    <div class="bg-white p-2 px-4 rounded-pill fw-700 shadow-sm border border-light-subtle">
                        <i class="fa-solid fa-house-circle-check me-2 text-primary"></i>
                        <span class="text-muted small">Total Database:</span> 
                        <span class="text-dark ms-1"><?= $pdo->query("SELECT COUNT(*) FROM properties")->fetchColumn(); ?></span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <?php
                // Existing Logic
                $stmt = $pdo->query("SELECT * FROM properties ORDER BY id DESC");
                $properties = $stmt->fetchAll();

                foreach ($properties as $p):
                    $statusClass = 'status-' . strtolower($p['status']);
                ?>
                <div class="col-xl-4 col-md-6">
                    <div class="property-card p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="status-badge <?= $statusClass ?>">
                                <i class="fa-solid fa-circle me-1" style="font-size: 0.5rem;"></i>
                                <?= htmlspecialchars($p['status']) ?>
                            </span>
                            <small class="text-muted fw-bold bg-light px-2 py-1 rounded">#<?= $p['id'] ?></small>
                        </div>

                        <h5 class="fw-800 text-dark mb-2">
                            <?= htmlspecialchars($p['title']) ?>
                        </h5>

                        <div class="mb-3">
                            <div class="price-tag">₹<?= number_format($p['price']) ?></div>
                            <div class="detail-item mt-1">
                                <i class="fa-solid fa-location-dot me-1 text-danger"></i> 
                                <?= htmlspecialchars($p['city']) ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <span class="badge bg-slate-100 text-dark border fw-600 rounded-pill px-3 py-1" style="font-size: 0.7rem;">
                                <?= htmlspecialchars($p['property_type']) ?>
                            </span>
                            <span class="badge bg-slate-100 text-dark border fw-600 rounded-pill px-3 py-1" style="font-size: 0.7rem;">
                                <?= htmlspecialchars($p['purpose'] ?? 'N/A') ?>
                            </span>
                        </div>

                        <button class="btn btn-outline-dark btn-sm w-100 mb-3 fw-700" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#viewModal<?= $p['id'] ?>">
                            <i class="fa-solid fa-eye me-1"></i> View Full Details
                        </button>

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
                                <div class="text-center py-2 bg-light rounded-3">
                                    <small class="text-secondary fw-600">
                                        <i class="fa-solid fa-lock me-1"></i> Action Completed
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="viewModal<?= $p['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                            <div class="modal-header border-bottom-0 pt-4 px-4">
                                <h5 class="fw-800 text-dark modal-title">
                                    <i class="fa-solid fa-circle-info text-primary me-2"></i><?= htmlspecialchars($p['title']) ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body px-4 pb-4">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block fw-600 mb-1">Valuation</small>
                                            <span class="fw-800 text-dark">₹<?= number_format($p['price']) ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block fw-600 mb-1">Location</small>
                                            <span class="fw-800 text-dark"><?= htmlspecialchars($p['city']) ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block fw-600 mb-1">Surface Area</small>
                                            <span class="fw-800 text-dark"><?= htmlspecialchars($p['area'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-800 text-dark mb-3">Specifications</h6>
                                    <div class="row small">
                                        <div class="col-6 mb-2"><strong>Type:</strong> <?= htmlspecialchars($p['property_type']) ?></div>
                                        <div class="col-6 mb-2"><strong>Purpose:</strong> <?= htmlspecialchars($p['purpose']) ?></div>
                                        <div class="col-12 mb-2"><strong>Address:</strong> <?= htmlspecialchars($p['address'] ?? '-') ?></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="fw-800 text-dark mb-2">Description</h6>
                                    <p class="text-secondary small"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                                </div>

                                <hr class="my-4 opacity-50">

                                <h6 class="fw-800 text-dark mb-3"><i class="fa-solid fa-images me-2"></i>Property Media</h6>
                                <div class="row g-2">
                                    <?php
                                    $imgs = $pdo->prepare("SELECT * FROM property_images WHERE property_id=?");
                                    $imgs->execute([$p['id']]);
                                    foreach ($imgs as $img):
                                    ?>
                                    <div class="col-4 mb-3">
                                        <div class="card border-0 shadow-sm overflow-hidden h-100">
                                            <img src="../uploads/<?= $img['image_path'] ?>" class="img-fluid" style="height:140px; object-fit:cover;">
                                            <div class="p-1 d-flex gap-1">
                                                <a href="../uploads/<?= $img['image_path'] ?>" target="_blank" class="btn btn-sm btn-light w-50" style="font-size: 0.65rem; font-weight: 700;">View</a>
                                                <a href="../uploads/<?= $img['image_path'] ?>" download class="btn btn-sm btn-dark w-50" style="font-size: 0.65rem; font-weight: 700;">Save</a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>

                                <hr class="my-4 opacity-50">

                                <h6 class="fw-800 text-dark mb-3"><i class="fa-solid fa-file-shield me-2"></i>Legal Documents</h6>
                                <?php
                                $docs = $pdo->prepare("SELECT * FROM property_documents WHERE property_id=?");
                                $docs->execute([$p['id']]);
                                $allDocs = $docs->fetchAll();
                                foreach ($allDocs as $doc):
                                ?>
                                <div class="border rounded-4 p-3 mb-3 bg-white shadow-sm border-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="text-uppercase fw-800 text-slate-500 small" style="letter-spacing: 1px;">
                                                <?= str_replace('_',' ', $doc['document_type']) ?>
                                            </span>
                                        </div>
                                        <span class="badge rounded-pill <?= $doc['status']=='verified' ? 'bg-success' : ($doc['status']=='rejected' ? 'bg-danger' : 'bg-warning') ?>" style="font-size: 0.6rem; font-weight: 800;">
                                            <?= strtoupper($doc['status']) ?>
                                        </span>
                                    </div>

                                    <div class="d-flex gap-2 mb-3">
                                        <a href="../uploads/docs/<?= $doc['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-700 px-3">
                                            <i class="fa-solid fa-eye me-1"></i> Preview
                                        </a>
                                        <a href="../uploads/docs/<?= $doc['file_path'] ?>" download class="btn btn-sm btn-outline-dark fw-700 px-3">
                                            <i class="fa-solid fa-download me-1"></i> Download
                                        </a>
                                    </div>

                                    <?php if($doc['status'] == 'rejected' && !empty($doc['rejection_reason'])): ?>
                                        <div class="p-2 mb-3 bg-red-50 border border-danger-subtle rounded-3">
                                            <small class="text-danger fw-600">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Reason:</strong> <?= htmlspecialchars($doc['rejection_reason']) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>

                                    <div class="d-flex gap-2 pt-2 border-top">
                                        <a href="verify_doc.php?id=<?= $doc['id'] ?>&status=verified" class="btn btn-sm btn-success fw-700 px-3 py-1">
                                            <i class="fa-solid fa-check-circle me-1"></i> Verify
                                        </a>
                                        <button class="btn btn-sm btn-danger fw-700 px-3 py-1" data-bs-toggle="modal" data-bs-target="#rejectModal<?= (int)$doc['id'] ?>">
                                            <i class="fa-solid fa-circle-xmark me-1"></i> Reject
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php foreach ($allDocs as $doc): ?>
                <div class="modal fade" id="rejectModal<?= (int)$doc['id'] ?>" tabindex="-1" style="z-index: 1060;">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                            <form method="POST" action="verify_doc.php">
                                <div class="modal-header border-0 pb-0">
                                    <h6 class="modal-title fw-800 text-danger">Document Rejection</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                    <input type="hidden" name="status" value="rejected">
                                    <label class="form-label small fw-700 text-muted">Please specify why this document is being rejected:</label>
                                    <textarea name="reason" class="form-control rounded-3" rows="3" required 
                                              placeholder="e.g. Blurred image, incorrect legal info..."></textarea>
                                </div>
                                <div class="modal-footer border-0 pt-0">
                                    <button type="submit" class="btn btn-danger w-100 fw-700 py-2 rounded-3">Confirm Rejection</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php endforeach; ?> </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Styling for nested/overlapping modals and backgrounds */
    .bg-red-50 { background-color: #fef2f2; }
    .text-slate-500 { color: #64748b; }
    .bg-slate-100 { background-color: #f1f5f9; }
    
    /* Ensure the Reject Modal sits above the Details Modal */
    .modal-backdrop + .modal-backdrop {
        z-index: 1055;
    }
</style>

</body>
</html>