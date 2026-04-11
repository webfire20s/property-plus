<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require '../includes/membership_check.php';

    // Logic remains untouched
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $count = $stmt->fetchColumn();

    if ($count >= $membership['property_limit']) {
        // Styled error message for better UX
        echo "<div class='alert alert-danger border-0 rounded-0 text-center'>Limit reached. <a href='membership.php'>Upgrade your plan</a> to post more.</div>";
    } else {
        $title = htmlspecialchars($_POST['title']);
        $description = htmlspecialchars($_POST['description']);
        $price = $_POST['price'];
        $city = htmlspecialchars($_POST['city']);
        $type = $_POST['type'];

        $stmt = $pdo->prepare("INSERT INTO properties (user_id, title, description, price, city, property_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $title, $description, $price, $city, $type]);

        $property_id = $pdo->lastInsertId();

        if (empty($_FILES['images']['name'][0])) {
            die("Please upload at least one image");
        }

        foreach ($_FILES['images']['name'] as $key => $image_name) {
            $tmp = $_FILES['images']['tmp_name'][$key];
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (in_array($ext, $allowed)) {
                $new_name = time() . "_" . rand(1000,9999) . "." . $ext;
                move_uploaded_file($tmp, "../uploads/" . $new_name);
                $img = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
                $img->execute([$property_id, $new_name]);
            }
        }
        echo "<div class='alert alert-success border-0 rounded-0 text-center mb-0'>Property Added Successfully! <a href='my_properties.php' class='fw-bold'>View My Listings</a></div>";
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
    .form-container {
        max-width: 850px;
        margin: 40px auto;
        margin-top: 100px; /* Space for the sticky navbar */
    }

    .upload-card {
        background: #ffffff;
        border: 1px solid #ebebeb;
        border-radius: 20px;
        padding: 45px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .section-header {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #2eca6a; /* Theme Green */
        font-weight: 700;
        display: flex;
        align-items: center;
        margin-bottom: 25px;
        margin-top: 20px;
    }

    .section-header::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #eee;
        margin-left: 20px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: #2eca6a;
        box-shadow: 0 0 0 3px rgba(46, 202, 106, 0.1);
    }

    .image-upload-box {
        border: 2px dashed #2eca6a;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        background: #f9f9f9;
        cursor: pointer;
        transition: 0.3s;
    }

    .image-upload-box:hover {
        background: #f0fdf4;
    }

    .btn-post {
        background-color: #2eca6a;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 15px;
        font-weight: 700;
        width: 100%;
        margin-top: 30px;
        transition: 0.3s;
    }

    .btn-post:hover {
        background-color: #25a556;
        transform: translateY(-2px);
    }
</style>

<div class="container form-container" data-aos="fade-up">
    <div class="upload-card">
        <div class="mb-5 text-center">
            <h2 class="fw-bold" style="color: #000;">List Your Property</h2>
            <p class="text-secondary">Fill in the details below to publish your property listing.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="section-header">Basic Information</div>
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <label class="form-label small fw-bold">Property Title</label>
                    <input name="title" class="form-control" placeholder="e.g. 3BHK Luxury Apartment" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Property Type</label>
                    <select name="type" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= (($_GET['category'] ?? '')==$cat)?'selected':'' ?>>
                                <?= $cat ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Price (₹)</label>
                    <input name="price" type="number" class="form-control" placeholder="5000000" required>
                </div>
            </div>

            <div class="section-header">Location & Details</div>
            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <label class="form-label small fw-bold">City / District</label>
                    <input name="city" class="form-control" placeholder="e.g. Noida, Uttar Pradesh" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Full Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe amenities, landmarks..."></textarea>
                </div>
            </div>

            <div class="section-header">Property Media</div>
            <div class="image-upload-box" onclick="document.getElementById('imgInput').click();">
                <i class="bi bi-cloud-arrow-up text-success" style="font-size: 3rem;"></i>
                <h5 class="fw-bold mt-2">Upload Property Photos</h5>
                <p class="text-muted small mb-0">Click to select multiple images</p>
                <input type="file" name="images[]" id="imgInput" multiple required style="display: none;" onchange="updateLabel(this)">
                <div id="fileCount" class="badge bg-success mt-3 d-none">0 files selected</div>
            </div>

            <button type="submit" class="btn-post">
                Publish Listing <i class="bi bi-arrow-right ms-2"></i>
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
// 2. Include the footer (Handles AOS and JS scripts)
include('../includes/footer.php'); 
?>