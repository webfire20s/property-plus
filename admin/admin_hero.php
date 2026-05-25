<?php
require 'auth.php';
require '../config/db.php'; 

$message = "";
$error = "";

// Directory path to store uploaded files
$uploadDir = '../uploads/hero/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// ─────────────────────────────────────────────────────────────────────────
// HANDLE ACTIONS: ADD / UPDATE / DELETE
// ─────────────────────────────────────────────────────────────────────────

// 1. ADD NEW SLIDE
if (isset($_POST['add_slide'])) {
    if (isset($_FILES['slide_image']) && $_FILES['slide_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['slide_image']['tmp_name'];
        $fileName = $_FILES['slide_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate distinct filename to prevent overrides
            $newFileName = 'hero_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $stmt = $pdo->prepare("INSERT INTO hero_slides (image_path) VALUES (?)");
                $stmt->execute([$newFileName]);
                $message = "New slide uploaded successfully!";
            } else {
                $error = "Error shifting file to destination folder.";
            }
        } else {
            $error = "Invalid file type. Allowed formats: JPG, JPEG, PNG, WEBP.";
        }
    } else {
        $error = "Please choose a valid file to upload.";
    }
}

// 2. UPDATE EXISTING SLIDE
if (isset($_POST['update_slide'])) {
    $slideId = (int)$_POST['slide_id'];
    $oldImage = $_POST['old_image'];
    
    if (isset($_FILES['slide_image']) && $_FILES['slide_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['slide_image']['tmp_name'];
        $fileName = $_FILES['slide_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = 'hero_update_' . time() . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update file registration entry inside the database
                $stmt = $pdo->prepare("UPDATE hero_slides SET image_path = ? WHERE id = ?");
                $stmt->execute([$newFileName, $slideId]);
                
                // Clear old tracking image physically from backend storage
                if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                    unlink($uploadDir . $oldImage);
                }
                $message = "Slide updated successfully!";
            } else {
                $error = "Failed to update image storage resource.";
            }
        } else {
            $error = "Invalid file format framework rules.";
        }
    }
}

// 3. REMOVE SLIDE
if (isset($_GET['delete_id'])) {
    $deleteId = (int)$_GET['delete_id'];
    
    // Read contextual information to get asset file name trace matching the row entry
    $stmt = $pdo->prepare("SELECT image_path FROM hero_slides WHERE id = ?");
    $stmt->execute([$deleteId]);
    $slide = $stmt->fetch();
    
    if ($slide) {
        if (!empty($slide['image_path']) && file_exists($uploadDir . $slide['image_path'])) {
            unlink($uploadDir . $slide['image_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$deleteId]);
        $message = "Slide dropped and completely cleared successfully.";
    }
}

// Fetch up-to-date slider items
$stmt = $pdo->prepare("SELECT * FROM hero_slides ORDER BY id ASC");
$stmt->execute();
$slides = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero Slider | PropertyPlus Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        
        .main-content { padding: 40px; }

        .main-card { 
            background: white; 
            border-radius: 16px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.01);
            padding: 24px;
        }

        .slide-preview-wrapper {
            position: relative;
            height: 180px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
        }

        .slide-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .slot-badge {
            font-size: 0.725rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #64748b;
        }

        /* Platform Aligned Action Buttons */
        .btn-action { 
            padding: 10px 18px; 
            font-size: 0.8rem; 
            font-weight: 600; 
            border-radius: 10px; 
            text-decoration: none; 
            transition: all 0.2s ease; 
            display: inline-flex;
            align-items: center;
            border: none;
        }
        .btn-approve { background: #2eca6a; color: white !important; }
        .btn-approve:hover { background: #25b35c; }
        
        .btn-block { background: #fee2e2; color: #dc2626 !important; }
        .btn-block:hover { background: #fca5a5; }
        
        .btn-activate { background: #2563eb; color: white !important; }
        .btn-activate:hover { background: #1d4ed8; }

        /* Custom Structured Inputs */
        .form-control-custom {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 0.9rem;
            outline: none;
            background-color: #ffffff;
            color: #1e293b;
        }
        .form-control-custom:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        /* Modal Customizations matching platform layouts */
        .modal-content {
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 20px 24px;
        }
        .modal-body {
            padding: 24px;
        }
        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 16px 24px;
        }

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
                    <h2 class="fw-800 text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">Hero Slider</h2>
                    <p class="text-secondary mb-0" style="font-size: 0.925rem;">Monitor platform image loops and handle dynamic slider layouts.</p>
                </div>
                <a href="../index.php" target="_blank" class="btn border shadow-sm rounded-3 px-3 fw-600 text-decoration-none text-dark d-flex align-items-center" style="font-size: 0.85rem; background: white; border-color: #e2e8f0 !important; height: 40px;">
                    <i class="fa-solid fa-arrow-up-right-from-square me-2 text-success"></i>Live Website
                </a>
            </div>

            <?php if(!empty($message)): ?>
                <div class="alert border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert" style="background: #f0fdf4; color: #166534; padding: 14px 20px;">
                    <i class="fa-solid fa-circle-check me-2c fs-5 me-2"></i> 
                    <div class="fw-600 small"><?= htmlspecialchars($message) ?></div>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($error)): ?>
                <div class="alert border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert" style="background: #fef2f2; color: #991b1b; padding: 14px 20px;">
                    <i class="fa-solid fa-triangle-exclamation fs-5 me-2"></i> 
                    <div class="fw-600 small"><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <div class="main-card mb-4">
                <div class="fw-700 text-dark mb-3 d-flex align-items-center" style="font-size: 0.95rem; letter-spacing: -0.2px;">
                    <i class="fa-solid fa-circle-plus me-2 text-primary"></i>Add New Image Slide Slot
                </div>
                
                <?php if(count($slides) >= 6): ?>
                    <div class="p-3 rounded-3 small border d-flex align-items-center" style="background: #fffbeb; color: #92400e; border-color: #fef3c7; font-weight: 500;">
                        <i class="fa-solid fa-circle-info me-2 fs-5 text-warning"></i> Maximum structural threshold limit reached (6 active layouts). Drop or swap an existing slide element to apply updates.
                    </div>
                <?php else: ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row align-items-center g-3">
                            <div class="col-md-9">
                                <input type="file" name="slide_image" class="form-control form-control-custom" required>
                                <div class="text-muted mt-2 ps-1" style="font-size: 0.75rem;">
                                    Optimal operational canvas properties: <span class="fw-600">1920x1080px</span> (Allowed frameworks: JPG, JPEG, PNG, WEBP)
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="add_slide" class="btn-action btn-activate w-100 justify-content-center text-uppercase">
                                    <i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Slide
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>

            <div class="main-card">
                <div class="mb-4 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                    <span class="slot-badge">Active Exhibition Slots (<?= count($slides) ?> / 6)</span>
                </div>
                
                <?php if(count($slides) === 0): ?>
                    <div class="text-center py-5">
                        <i class="fa-solid fa-images fa-3x text-muted opacity-25 mb-3"></i>
                        <div class="fw-700 text-secondary mb-1" style="font-size: 1rem;">No custom slider files updated yet</div>
                        <p class="text-muted small mb-0">The application slideshow engine is falling back onto system-level assets.</p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach($slides as $slide): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="p-2 border rounded-4 bg-white h-100 d-flex flex-column justify-content-between shadow-sm" style="border-color: #e2e8f0 !important;">
                                    
                                    <div class="slide-preview-wrapper mb-3">
                                        <img src="../uploads/hero/<?= htmlspecialchars($slide['image_path']) ?>" class="slide-preview-img" alt="Workspace Hero Resource Track">
                                    </div>
                                    
                                    <div class="d-flex gap-2 mt-auto pt-1">
                                        <button type="button" class="btn-action btn-activate flex-grow-1 justify-content-center" onclick="triggerUpdate(<?= $slide['id'] ?>, '<?= htmlspecialchars($slide['image_path']) ?>')">
                                            <i class="fa-solid fa-arrows-rotate me-2"></i>Swap
                                        </button>
                                        <a href="?delete_id=<?= $slide['id'] ?>" class="btn-action btn-block justify-content-center" style="width: 44px; padding: 10px 0;" onclick="return confirm('Drop and wipe out this item slot target completely?')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="updateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" enctype="multipart/form-data" class="modal-content border-0">
            <div class="modal-header">
                <h5 class="modal-title fw-700 text-dark" style="font-size: 1.05rem; letter-spacing: -0.2px;">Swap Slider Asset File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="slide_id" id="modal_slide_id">
                <input type="hidden" name="old_image" id="modal_old_image">
                
                <div class="mb-2">
                    <label class="form-label small text-secondary fw-600 mb-2">Choose replacement element image</label>
                    <input type="file" name="slide_image" class="form-control form-control-custom" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-3 px-3 fw-600 small text-secondary border" data-bs-dismiss="modal" style="font-size: 0.8rem; padding: 10px 16px; border-color: #cbd5e1 !important;">Cancel</button>
                <button type="submit" name="update_slide" class="btn-action btn-approve">Commit Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function triggerUpdate(id, currentFileName) {
    document.getElementById('modal_slide_id').value = id;
    document.getElementById('modal_old_image').value = currentFileName;
    var targetUpdateModal = new bootstrap.Modal(document.getElementById('updateModal'));
    targetUpdateModal.show();
}
</script>
</body>
</html>