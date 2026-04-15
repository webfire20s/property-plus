<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

// ✅ Get active membership
$stmt = $pdo->prepare("
    SELECT um.*, m.name, m.property_limit
    FROM user_memberships um
    JOIN memberships m ON um.membership_id = m.id
    WHERE um.user_id=? AND um.status='active'
    ORDER BY um.id DESC LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$membership = $stmt->fetch();

// ✅ DEFAULT: Listing Plan (FREE)
$plan_name = 'Listing';
$image_limit = 0;
$allow_video = false;
$property_limit = 999; // unlimited text listings

// ✅ If user has paid membership → override defaults
if ($membership) {

    $plan_name = strtolower($membership['name']);
    $property_limit = $membership['property_limit'];

    switch ($plan_name) {

        case 'basic':
            $image_limit = 5;
            break;

        case 'silver':
            $image_limit = 10;
            break;

        case 'gold':
            $image_limit = 20;
            break;

        case 'platinum':
            $image_limit = 999; // unlimited
            $allow_video = true;
            break;
    }
}

// ✅ HANDLE FORM
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Only check membership if user actually has a paid plan
    if ($membership) {
        require '../includes/membership_check.php';
    }

    // Count properties
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $count = $stmt->fetchColumn();

    // ✅ SAFE LIMIT CHECK
    if ($count >= $property_limit) {
        echo "<div class='alert alert-danger text-center'>
                Limit reached. <a href='membership.php'>Upgrade your plan</a>
              </div>";
    } else {

        // SAFE INPUTS
        $title = htmlspecialchars($_POST['title'] ?? '');
        $description = htmlspecialchars($_POST['description'] ?? '');
        $price = $_POST['price'] ?? 0;
        $city = htmlspecialchars($_POST['city'] ?? '');
        $type = $_POST['type'] ?? '';
        $area = $_POST['area'] ?? '';
        $transaction_type = $_POST['transaction_type'] ?? '';
        $address = htmlspecialchars($_POST['address'] ?? '');
        $pincode = $_POST['pincode'] ?? '';

        // INSERT PROPERTY
        $stmt = $pdo->prepare("
            INSERT INTO properties 
            (user_id, title, description, price, city, property_type, area, purpose, address, pincode, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            $price,
            $city,
            $type,
            $area,
            $transaction_type,
            $address,
            $pincode
        ]);

        $property_id = $pdo->lastInsertId();

        // ✅ IMAGE VALIDATION (NO FORCE)
        if (isset($_FILES['images'])) {

            $total_images = count(array_filter($_FILES['images']['name']));

            if ($total_images > $image_limit) {
                die("You can upload maximum $image_limit images in your current plan.");
            }

            if ($image_limit == 0 && $total_images > 0) {
                die("Your current plan does not allow image uploads.");
            }

            // Upload if allowed
            if ($image_limit > 0 && $total_images > 0) {

                foreach ($_FILES['images']['name'] as $key => $image_name) {

                    $tmp = $_FILES['images']['tmp_name'][$key];
                    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    $allowed = ['jpg', 'jpeg', 'png'];

                    if (in_array($ext, $allowed)) {

                        $new_name = time() . rand(1000,9999) . "." . $ext;
                        move_uploaded_file($tmp, "../uploads/" . $new_name);

                        $img = $pdo->prepare("
                            INSERT INTO property_images (property_id, image_path) 
                            VALUES (?, ?)
                        ");
                        $img->execute([$property_id, $new_name]);
                    }
                }
            }
        }

        // ✅ VIDEO RESTRICTION
        if (!$allow_video && !empty($_FILES['video']['name'])) {
            die("Video upload is allowed only in Platinum plan.");
        }

        // ✅ DOCUMENTS (ALL PLANS ALLOWED)
        $docs = ['sale_deed', 'title_doc', 'layout_plan', 'other_docs'];

        foreach ($docs as $doc) {
            if (!empty($_FILES[$doc]['name'])) {

                $file = $_FILES[$doc];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                $new_name = "doc_" . time() . rand(1000,9999) . "." . $ext;
                move_uploaded_file($file['tmp_name'], "../uploads/docs/" . $new_name);

                $pdo->prepare("
                    INSERT INTO property_documents 
                    (property_id, document_type, file_path, status) 
                    VALUES (?, ?, ?, 'pending')
                ")->execute([$property_id, $doc, $new_name]);
            }
        }

        echo "<div class='alert alert-success text-center'>
                Property submitted for review ✅
              </div>";
    }
}


$categories = [
    "Builder Floors",
    "Apartments",
    "Flats",
    "Independent Floors",
    "Independent Kothi",
    "Independent Villa",
    "Society Flats",
    "Commercial Building",
    "Commercial Shop",
    "Commercial Showroom",
    "Commercial Floor",
    "Land",
    "Commercial Land",
    "Agricultural Land"
];
?>


<style>
    body { background-color: #f7f7f7; }

    .form-container {
        max-width: 850px;
        margin: 40px auto;
        margin-top: 110px; /* Space for sticky navbar */
    }

    .upload-card {
        background: #ffffff;
        border: 1px solid #ebebeb;
        border-radius: 20px;
        padding: 45px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    /* Top accent bar matching register page */
    .upload-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: #2eca6a;
        border-radius: 20px 20px 0 0;
    }

    .section-header {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #2eca6a;
        font-weight: 800;
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        margin-top: 30px;
    }

    .section-header::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #f1f1f1;
        margin-left: 20px;
    }

    .form-label {
        color: #555;
        font-size: 0.8rem;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 2px solid #f1f1f1;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2eca6a;
        box-shadow: none;
        background: #fff;
    }

    .image-upload-box {
        border: 2px dashed #2eca6a;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        background: #fcfdfc;
        cursor: pointer;
        transition: 0.3s;
    }

    .image-upload-box:hover {
        background: #f0fdf4;
        border-color: #25a556;
    }

    .btn-post {
        background-color: #2eca6a;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 18px;
        font-weight: 700;
        width: 100%;
        margin-top: 40px;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
        box-shadow: 0 10px 20px rgba(46, 202, 106, 0.15);
    }

    .btn-post:hover {
        background-color: #25a556;
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(46, 202, 106, 0.25);
    }

    .doc-input-group {
        background: #f9fafb;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #eee;
    }
</style>

<div class="container form-container" data-aos="fade-up">
    <div class="upload-card">
        <div class="mb-5 text-center">
            <h2 class="fw-bold" style="color: #2b2b2b;">List Your Property</h2>
            <p class="text-muted">Enter accurate details to reach verified buyers and tenants.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="section-header">01. Basic Information</div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">Property Title</label>
                    <input name="title" class="form-control" placeholder="e.g. 3BHK Luxury Apartment with City View" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Property Category</label>
                    <select name="type" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Transaction Type</label>
                    <select name="transaction_type" class="form-select" required>
                        <option value="">Select Purpose</option>
                        <option value="Sell">For Sell</option>
                        <option value="Rent">For Rent</option>
                        <option value="Lease">For Lease</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Expected Price (₹)</label>
                    <input name="price" type="number" class="form-control" placeholder="e.g. 5000000" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Total Area (Sq Ft)</label>
                    <input name="area" type="number" class="form-control" placeholder="e.g. 1250">
                </div>
            </div>

            <div class="section-header">02. Location & Description</div>
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold">City / District</label>
                    <input name="city" class="form-control" placeholder="e.g. Noida" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Pin Code</label>
                    <input name="pincode" class="form-control" placeholder="e.g. 201301">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Full Address</label>
                    <input name="address" class="form-control" placeholder="Building name, Street, Landmark...">
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Detailed Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Mention amenities like Parking, Gym, Security, nearby Schools etc..."></textarea>
                </div>
            </div>

            <div class="section-header">03. Property Media</div>
            <div class="alert alert-info">
                <strong>Your Plan:</strong> <?= htmlspecialchars($plan_name) ?><br>

                <?php if ($image_limit == 0): ?>
                    <span class="text-danger">Text-only listing (no images allowed)</span><br>
                    <a class="btn btn-warning mt-3" href="membership.php"><strong>Upgrade to upload images</strong></a>
                <?php else: ?>
                    You can upload up to <b><?= $image_limit ?></b> images.
                <?php endif; ?>

                <?php if ($allow_video): ?>
                    <br>Video upload is enabled.
                <?php endif; ?>
            </div>
            <div class="image-upload-box" onclick="document.getElementById('imgInput').click();">
                <i class="fa-solid fa-cloud-arrow-up text-success mb-2" style="font-size: 2.5rem;"></i>
                <h5 class="fw-bold">Upload High-Quality Photos</h5>
                <p class="text-muted small mb-0">Click to select multiple images from your gallery</p>
                <input type="file" name="images[]" id="imgInput" multiple 
                    <?= $image_limit == 0 ? 'disabled' : '' ?> 
                    style="display: none;" onchange="updateLabel(this)">
                <div id="fileCount" class="badge bg-success mt-3 d-none">0 files selected</div>
            </div>

            <div class="section-header">04. Legal Documents (KYC)</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Sale Deed / Allotment Letter</label>
                    <input type="file" name="sale_deed" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Chain of Title Documents</label>
                    <input type="file" name="title_doc" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Approved Layout Plan</label>
                    <input type="file" name="layout_plan" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Other Supporting Docs</label>
                    <input type="file" name="other_docs" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn-post shadow-sm">
                Submit Listing for Verification <i class="fa-solid fa-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>

<script>
    function updateLabel(input) {
        const count = input.files.length;
        const label = document.getElementById('fileCount');
        if (count > 0) {
            label.classList.remove('d-none');
            label.innerText = count + (count === 1 ? " Photo Selected" : " Photos Selected");
        }
    }
</script>

<?php 
include('../includes/footer.php'); 
?>