<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

$user_id = $_SESSION['user_id'];
$property_id = $_GET['id'] ?? null;

if (!$property_id) {
    die("Invalid Request");
}

// ✅ Fetch property (ownership check)
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id=? AND user_id=?");
$stmt->execute([$property_id, $user_id]);
$property = $stmt->fetch();

if (!$property) {
    die("Unauthorized access");
}

// ✅ Get membership (same logic as add_property)
$stmt = $pdo->prepare("
    SELECT um.*, m.name 
    FROM user_memberships um
    JOIN memberships m ON um.membership_id = m.id
    WHERE um.user_id=? AND um.status='active'
    ORDER BY um.id DESC LIMIT 1
");
$stmt->execute([$user_id]);
$membership = $stmt->fetch();

$plan_name = $membership['name'] ?? 'Listing';
$image_limit = 0;
$allow_video = false;

switch (strtolower($plan_name)) {
    case 'basic': $image_limit = 5; break;
    case 'silver': $image_limit = 10; break;
    case 'gold': $image_limit = 20; break;
    case 'platinum':
        $image_limit = 999;
        $allow_video = true;
        break;
}

// ✅ Fetch existing images
$imgs = $pdo->prepare("SELECT * FROM property_images WHERE property_id=?");
$imgs->execute([$property_id]);
$images = $imgs->fetchAll();


// ================= UPDATE PROPERTY =================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Update main details
    $stmt = $pdo->prepare("
        UPDATE properties SET 
        title=?, description=?, price=?, city=?, property_type=?, 
        area=?, purpose=?, address=?, pincode=?, status='pending'
        WHERE id=? AND user_id=?
    ");

    $stmt->execute([
        $_POST['title'],
        $_POST['description'],
        $_POST['price'],
        $_POST['city'],
        $_POST['type'],
        $_POST['area'],
        $_POST['transaction_type'],
        $_POST['address'],
        $_POST['pincode'],
        $property_id,
        $user_id
    ]);

    // ================= DELETE IMAGES =================
    if (!empty($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $img_id) {

            // Get file path
            $img = $pdo->prepare("SELECT image_path FROM property_images WHERE id=?");
            $img->execute([$img_id]);
            $file = $img->fetch();

            if ($file) {
                unlink("../uploads/" . $file['image_path']);
                $pdo->prepare("DELETE FROM property_images WHERE id=?")->execute([$img_id]);
            }
        }
    }

    // ================= ADD NEW IMAGES =================
    if (!empty($_FILES['images']['name'][0])) {

        $current_count = $pdo->query("SELECT COUNT(*) FROM property_images WHERE property_id=$property_id")->fetchColumn();
        $new_count = count(array_filter($_FILES['images']['name']));

        if ($current_count + $new_count > $image_limit) {
            die("Image limit exceeded for your plan.");
        }

        if ($image_limit == 0) {
            die("Your plan does not allow image uploads.");
        }

        foreach ($_FILES['images']['name'] as $key => $name) {

            $tmp = $_FILES['images']['tmp_name'][$key];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg','jpeg','png'])) {

                $new_name = time() . rand(1000,9999) . "." . $ext;
                move_uploaded_file($tmp, "../uploads/" . $new_name);

                $pdo->prepare("
                    INSERT INTO property_images (property_id, image_path)
                    VALUES (?, ?)
                ")->execute([$property_id, $new_name]);
            }
        }
    }

    header("Location: my_properties.php");
    exit;
}
?>

<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }

    .container-box {
        padding: 120px 0 60px;
    }

    .card-box {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ebebeb;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 30px;
        color: #000;
        position: relative;
        padding-bottom: 10px;
        border-left: 5px solid #2eca6a;
        padding-left: 15px;
    }

    .form-label {
        font-weight: 600;
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #2eca6a;
        box-shadow: 0 0 0 0.2rem rgba(46, 202, 106, 0.1);
    }

    /* Image Management Styling */
    .img-wrapper {
        display: inline-block;
        text-align: center;
        margin: 10px;
        padding: 10px;
        border: 1px solid #eee;
        border-radius: 10px;
        background: #fdfdfd;
    }

    .img-thumb {
        height: 100px;
        width: 100px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1px solid #ddd;
    }

    .btn-update {
        background: #2eca6a;
        color: white;
        font-weight: 700;
        border-radius: 8px;
        padding: 15px;
        border: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-update:hover {
        background: #000;
        color: #fff;
        transform: translateY(-2px);
    }

    h5 {
        font-weight: 700;
        color: #000;
        margin-bottom: 20px;
        font-size: 1.1rem;
    }

    .remove-text {
        font-size: 0.8rem;
        font-weight: 600;
        color: #dc3545;
    }
</style>

<div class="container container-box">
    <div class="card-box">
        
        <h3 class="section-title">Edit Property Listing</h3>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-4">
                
                <div class="col-12">
                    <label class="form-label">Property Title</label>
                    <input name="title" class="form-control" value="<?= htmlspecialchars($property['title']) ?>" required>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($property['description']) ?></textarea>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Price (INR)</label>
                    <input name="price" type="number" class="form-control" value="<?= $property['price'] ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input name="city" class="form-control" value="<?= htmlspecialchars($property['city']) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Property Type</label>
                    <input name="type" class="form-control" value="<?= htmlspecialchars($property['property_type']) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Area (sq.ft)</label>
                    <input name="area" class="form-control" value="<?= $property['area'] ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Transaction Type</label>
                    <input name="transaction_type" class="form-control" value="<?= $property['purpose'] ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Pincode</label>
                    <input name="pincode" class="form-control" value="<?= $property['pincode'] ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Full Address</label>
                    <input name="address" class="form-control" value="<?= htmlspecialchars($property['address']) ?>">
                </div>

            </div>

            <hr class="my-5 opacity-25">

            <h5><i class="bi bi-images me-2 text-success"></i>Existing Images</h5>
            <div class="d-flex flex-wrap">
                <?php foreach ($images as $img): ?>
                    <div class="img-wrapper shadow-sm">
                        <img src="../uploads/<?= $img['image_path'] ?>" class="img-thumb">
                        <br>
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input me-1" type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" id="img_<?= $img['id'] ?>">
                            <label class="remove-text" for="img_<?= $img['id'] ?>">Remove</label>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr class="my-5 opacity-25">

            <h5><i class="bi bi-cloud-upload me-2 text-success"></i>Add More Images</h5>
            <div class="mb-4">
                <input type="file" name="images[]" multiple class="form-control">
                <small class="text-muted mt-2 d-block">You can select multiple files to upload additional gallery images.</small>
            </div>

            <button type="submit" class="btn btn-update w-100 mt-3">
                Update Property Details
            </button>

        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>