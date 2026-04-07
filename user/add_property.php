<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require '../includes/membership_check.php';

    // Count properties
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM properties WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $count = $stmt->fetchColumn();

    // Check plan limit
    if ($count >= $membership['property_limit']) {
        die("Your plan limit reached. Upgrade your plan.");
    }

    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $price = $_POST['price'];
    $city = htmlspecialchars($_POST['city']);
    $type = $_POST['type'];

    // Insert property
    $stmt = $pdo->prepare("INSERT INTO properties (user_id, title, description, price, city, property_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $title, $description, $price, $city, $type]);

    $property_id = $pdo->lastInsertId();

    if (empty($_FILES['images']['name'][0])) {
        die("Please upload at least one image");
    }

    foreach ($_FILES['images']['name'] as $key => $image_name) {

        $tmp = $_FILES['images']['tmp_name'][$key];

        // Get extension
        $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            die("Invalid file type");
        }

        // Create unique name
        $new_name = time() . "_" . rand(1000,9999) . "." . $ext;

        move_uploaded_file($tmp, "../uploads/" . $new_name);

        // Save to DB
        $img = $pdo->prepare("INSERT INTO property_images (property_id, image_path) VALUES (?, ?)");
        $img->execute([$property_id, $new_name]);
    }

    echo "Property Added Successfully!";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input name="title" placeholder="Title" required><br>
    <textarea name="description" placeholder="Description"></textarea><br>
    <input name="price" placeholder="Price" required><br>
    <input name="city" placeholder="City" required><br>

    <select name="type">
        <option>Apartment</option>
        <option>Villa</option>
        <option>Plot</option>
    </select><br>

    <input type="file" name="images[]" multiple required><br>

    <button type="submit">Add Property</button>
</form>