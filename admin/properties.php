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

                        <h5 class="fw-bold text-dark mb-2">
                            <?= htmlspecialchars($p['title']) ?>
                        </h5>

                        <p class="mb-1 small text-muted">
                            <strong>₹<?= number_format($p['price']) ?></strong> • <?= htmlspecialchars($p['city']) ?>
                        </p>

                        <p class="mb-2 small text-muted">
                            <?= htmlspecialchars($p['property_type']) ?> • <?= htmlspecialchars($p['purpose'] ?? '') ?>
                        </p>

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
                        <button class="btn btn-sm btn-outline-dark w-100 mb-2" data-bs-toggle="modal" data-bs-target="#viewModal<?= $p['id'] ?>">
                            View Full Details
                        </button>
                    </div>
                </div>
                <div class="modal fade" id="viewModal<?= $p['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($p['title']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <p><strong>Price:</strong> ₹<?= number_format($p['price']) ?></p>
                                <p><strong>City:</strong> <?= htmlspecialchars($p['city']) ?></p>
                                <p><strong>Type:</strong> <?= htmlspecialchars($p['property_type']) ?></p>
                                <p><strong>Transaction:</strong> <?= htmlspecialchars($p['purpose']) ?></p>
                                <p><strong>Area:</strong> <?= htmlspecialchars($p['area'] ?? '-') ?></p>
                                <p><strong>Address:</strong> <?= htmlspecialchars($p['address'] ?? '-') ?></p>
                                <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($p['description'])) ?></p>

                                <hr>

                                <h6 class="fw-bold">Property Images</h6>

                                <div class="row">
                                <?php
                                $imgs = $pdo->prepare("SELECT * FROM property_images WHERE property_id=?");
                                $imgs->execute([$p['id']]);
                                foreach ($imgs as $img):
                                ?>
                                    <div class="col-4 mb-3 text-center">
                                        <img src="../uploads/<?= $img['image_path'] ?>" class="img-fluid rounded mb-2" style="height:120px; object-fit:cover;">
                                        
                                        <a href="../uploads/<?= $img['image_path'] ?>" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-1">
                                            Preview
                                        </a>

                                        <a href="../uploads/<?= $img['image_path'] ?>" download class="btn btn-sm btn-outline-dark w-100">
                                            Download
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                                </div>

                                <hr>

                                <h6 class="fw-bold">Documents</h6>

                                <?php
                                $docs = $pdo->prepare("SELECT * FROM property_documents WHERE property_id=?");
                                $docs->execute([$p['id']]);
                                $allDocs = $docs->fetchAll();
                                foreach ($allDocs as $doc):
                                ?>
                                <div class="border rounded p-3 mb-3">

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong><?= strtoupper(str_replace('_',' ', $doc['document_type'])) ?></strong>

                                        <span class="badge 
                                            <?= $doc['status']=='verified' ? 'bg-success' : ($doc['status']=='rejected' ? 'bg-danger' : 'bg-warning') ?>">
                                            <?= ucfirst($doc['status']) ?>
                                        </span>
                                    </div>

                                    <div class="d-flex gap-2">

                                        <a href="../uploads/docs/<?= $doc['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            Preview
                                        </a>

                                        <a href="../uploads/docs/<?= $doc['file_path'] ?>" download class="btn btn-sm btn-outline-dark">
                                            Download
                                        </a>

                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <?php if($doc['status'] == 'rejected' && !empty($doc['rejection_reason'])): ?>
                                            <div class="mt-2 p-2 bg-light border rounded">
                                                <small class="text-danger">
                                                    <strong>Reason:</strong> <?= htmlspecialchars($doc['rejection_reason']) ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>

                                        <a href="verify_doc.php?id=<?= $doc['id'] ?>&status=verified" 
                                        class="btn btn-sm btn-success">
                                            ✔ Verify
                                        </a>

                                        <button 
                                            class="btn btn-sm btn-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#rejectModal<?= (int)$doc['id'] ?>">
                                            ✖ Reject
                                        </button>

                                    </div>
                                </div>
                                <?php endforeach; ?>

                            </div>

                        </div>
                    </div>
                </div>
                <?php foreach ($allDocs as $doc): ?>
                <div class="modal fade" id="rejectModal<?= (int)$doc['id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <form method="POST" action="verify_doc.php">

                                <div class="modal-header">
                                    <h5 class="modal-title">Reject Document</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <input type="hidden" name="doc_id" value="<?= $doc['id'] ?>">
                                    <input type="hidden" name="status" value="rejected">

                                    <label class="form-label">Reason for Rejection</label>
                                    <textarea name="reason" class="form-control" required
                                        placeholder="e.g. Blurry document, invalid proof, missing pages..."></textarea>

                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger">Reject Document</button>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
                <?php endforeach; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>