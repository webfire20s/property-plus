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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Property | PropertyPlus</title>
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
        }

        body { 
            background-color: var(--slate-50); 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
        }

        .form-container {
            max-width: 850px;
            margin: 60px auto;
        }

        .upload-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 30px;
            padding: 45px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
        }

        .section-header {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #94a3b8;
            font-weight: 800;
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            margin-top: 10px;
        }

        .section-header::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--slate-200);
            margin-left: 20px;
        }

        .form-control, .form-select {
            border-radius: 14px;
            padding: 14px 18px;
            border: 1px solid var(--slate-200);
            background-color: #fcfdfe;
            font-weight: 500;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-gold);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.15);
        }

        .form-label {
            margin-left: 4px;
            color: #475569;
        }

        .image-upload-box {
            border: 2px dashed var(--slate-200);
            border-radius: 20px;
            padding: 50px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload-box:hover {
            border-color: var(--brand-green);
            background: #f0fdf4;
            transform: scale(1.01);
        }

        .image-upload-box i {
            color: var(--brand-green);
            margin-bottom: 15px;
        }

        .btn-post {
            background-color: var(--brand-gold);
            color: var(--slate-900) !important;
            border: none;
            border-radius: 16px;
            padding: 18px;
            font-weight: 800;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 35px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-post:hover {
            background-color: #f59e0b;
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.2);
            transform: translateY(-2px);
        }

        #fileCount {
            background-color: var(--brand-green) !important;
            padding: 8px 16px;
            border-radius: 10px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container form-container">
    <div class="upload-card">
        <div class="mb-5 text-center">
            <h2 class="fw-extrabold" style="font-weight: 800;">List Your Property</h2>
            <p class="text-secondary">Reach verified partners and builders in your district.</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="section-header">Basic Information</div>
            <div class="row g-4 mb-5">
                <div class="col-12">
                    <label class="form-label small fw-bold">Property Title</label>
                    <input name="title" class="form-control" placeholder="e.g. 3BHK Luxury Apartment in Sector 62" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Property Type</label>
                    <select name="type" class="form-select">
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
                    <input name="price" type="number" class="form-control" placeholder="50,00,000" required>
                </div>
            </div>

            <div class="section-header">Location & Details</div>
            <div class="row g-4 mb-5">
                <div class="col-md-12">
                    <label class="form-label small fw-bold">City / District</label>
                    <input name="city" class="form-control" placeholder="e.g. Noida, Uttar Pradesh" required>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Full Description</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Describe the area, amenities, and nearby landmarks..."></textarea>
                </div>
            </div>

            <div class="section-header">Property Media</div>
            <div class="image-upload-box" onclick="document.getElementById('imgInput').click();">
                <i class="fa-solid fa-images fa-3x"></i>
                <h5 class="fw-bold">Upload Property Photos</h5>
                <p class="text-muted small mb-0">High-quality photos increase visibility by 80%</p>
                <input type="file" name="images[]" id="imgInput" multiple required style="display: none;" onchange="updateLabel(this)">
                <div id="fileCount" class="badge bg-success mt-3 d-none">0 files selected</div>
            </div>

            <button type="submit" class="btn-post">
                Publish Listing <i class="fa-solid fa-arrow-right ms-2"></i>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>