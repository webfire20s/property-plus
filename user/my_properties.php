<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// Logic remains untouched
$stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$properties = $stmt->fetchAll();
?>



<style>
    .page-header {
        padding: 60px 0;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 40px;
        margin-top: 80px; /* Offset for sticky navbar */
    }
    .listing-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        transition: all 0.3s ease;
    }
    .listing-card:hover {
        border-color: #2eca6a;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .property-thumb {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 15px;
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 8px;
    }
    .status-approved { background: #dcfce7; color: #166534; }
    .status-pending { background: #fef9c3; color: #854d0e; }
    
    .btn-manage {
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 10px;
        padding: 8px 15px;
        transition: 0.2s;
    }
</style>

<div class="page-header" data-aos="fade-down">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="fw-bold mb-1" style="color: #000;">My Property Listings</h1>
            <p class="text-secondary mb-0">Manage and track your active real estate portfolio.</p>
        </div>
        <a href="add_property.php" class="btn btn-success rounded-pill px-4 fw-bold" style="background: #2eca6a; border: none;">
            <i class="bi bi-plus-lg me-2"></i>New Listing
        </a>
    </div>
</div>

<div class="container pb-5">
    <?php if (count($properties) > 0): ?>
        <?php foreach ($properties as $p): ?>
            <div class="listing-card p-3 mb-4 shadow-sm" data-aos="fade-up">
                <?php
                $docs = $pdo->prepare("SELECT * FROM property_documents WHERE property_id=?");
                $docs->execute([$p['id']]);
                $documents = $docs->fetchAll();
                ?>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <?php 
                        $img = $pdo->prepare("SELECT * FROM property_images WHERE property_id = ? LIMIT 1");
                        $img->execute([$p['id']]);
                        $main_img = $img->fetch();
                        
                        if ($main_img): ?>
                            <img src='../uploads/<?php echo $main_img['image_path']; ?>' class="property-thumb">
                        <?php else: ?>
                            <div class="property-thumb d-flex align-items-center justify-content-center bg-light">
                                <i class="bi bi-building text-secondary" style="font-size: 2rem;"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col px-md-4">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="status-badge <?php echo ($p['status'] == 'approved') ? 'status-approved' : 'status-pending'; ?>">
                                <i class="bi bi-circle-fill small me-1" style="font-size: 8px;"></i> <?php echo ucfirst($p['status']); ?>
                            </span>
                            <span class="text-muted small">ID: #<?php echo 1000 + $p['id']; ?></span>
                        </div>
                        <h4 class="fw-bold mb-1" style="font-size: 1.25rem; color: #000;"><?php echo htmlspecialchars($p['title']); ?></h4>
                        <p class="text-secondary small mb-2">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?php echo htmlspecialchars($p['city']); ?>
                        </p>
                        <div class="fw-bold text-success" style="font-size: 1.2rem;">
                            ₹<?php echo number_format($p['price']); ?>
                        </div>
                        <?php if(!empty($documents)): ?>
                        <div class="mt-3">

                            <small class="fw-bold text-muted">Document Status:</small>

                            <?php foreach($documents as $doc): ?>

                                <div class="mt-2 p-2 border rounded bg-light">

                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small fw-semibold">
                                            <?= strtoupper(str_replace('_',' ', $doc['document_type'])) ?>
                                        </span>

                                        <span class="badge 
                                            <?= $doc['status']=='verified' ? 'bg-success' : ($doc['status']=='rejected' ? 'bg-danger' : 'bg-warning') ?>">
                                            <?= ucfirst($doc['status']) ?>
                                        </span>
                                    </div>

                                    <?php if($doc['status'] == 'rejected' && !empty($doc['rejection_reason'])): ?>
                                        <div class="mt-2">
                                            <small class="text-danger">
                                                <strong>Reason:</strong> <?= htmlspecialchars($doc['rejection_reason']) ?>
                                            </small>
                                        </div>

                                        <div class="mt-1">
                                            <small class="text-muted">
                                                Please re-upload this document to continue verification.
                                            </small>
                                        </div>
                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>
                    </div>

                    <div class="col-md-3 text-md-end mt-3 mt-md-0 border-start-md">
                        <div class="d-flex flex-md-column gap-2 justify-content-end">
                            <a href="edit_property.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-dark btn-manage">
                                <i class="bi bi-pencil-square me-2"></i>Edit
                            </a>
                            <a href="delete_property.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-danger btn-manage" onclick="return confirm('Permanently delete this listing?')">
                                <i class="bi bi-trash3 me-2"></i>Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5" data-aos="zoom-in">
            <div class="mb-4">
                <i class="bi bi-house-exclamation text-light" style="font-size: 5rem;"></i>
            </div>
            <h3 class="fw-bold">Your portfolio is empty</h3>
            <p class="text-muted mb-4">You haven't uploaded any properties yet. Start listing today.</p>
            <a href="add_property.php" class="btn btn-success btn-lg rounded-pill px-5 fw-bold" style="background: #2eca6a; border: none;">
                List Your First Property
            </a>
        </div>
    <?php endif; ?>
</div>

<?php 
// 2. Include the footer (Handles AOS and JS scripts)
include('../includes/footer.php'); 
?>