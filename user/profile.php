<?php
require '../includes/auth_check.php';
require '../config/db.php';
include '../includes/navbar.php';

$user_id = $_SESSION['user_id'];

// ✅ Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// ================= UPDATE PROFILE =================
if (isset($_POST['update_profile'])) {

    $business_name = $_POST['business_name'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $rera = $_POST['rera'];
    $gst = $_POST['gst'];

    // GST validation
    if (!empty($gst)) {
        if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/", $gst)) {
            $error = "Invalid GST number";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET business_name=?, state=?, district=?, rera_number=?, gst_number=? 
            WHERE id=?
        ");
        $stmt->execute([$business_name, $state, $district, $rera, $gst, $user_id]);

        $success = "Profile updated successfully";
    }
}

// ================= CHANGE PASSWORD =================
if (isset($_POST['change_password'])) {

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect";
    } elseif ($new !== $confirm) {
        $error = "Passwords do not match";
    } elseif (strlen($new) < 4) {
        $error = "Password must be at least 4 characters";
    } else {
        $hashed = password_hash($new, PASSWORD_DEFAULT);

        $pdo->prepare("UPDATE users SET password=? WHERE id=?")
            ->execute([$hashed, $user_id]);

        $success = "Password changed successfully";
        header("Location: profile.php?updated=1");
        exit;
    }
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

    .card-custom {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ebebeb;
        padding: 35px;
        margin-bottom: 30px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 25px;
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

    .btn-save {
        background: #2eca6a;
        color: white;
        font-weight: 700;
        border-radius: 8px;
        padding: 12px 30px;
        border: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
    }

    .btn-save:hover {
        background: #000;
        color: #fff;
        transform: translateY(-2px);
    }

    .btn-password {
        background: #000;
        color: #fff;
        font-weight: 700;
        border-radius: 8px;
        padding: 12px 30px;
        border: none;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
    }

    .btn-password:hover {
        background: #2eca6a;
        transform: translateY(-2px);
    }

    .alert {
        border-radius: 10px;
        border: none;
        font-weight: 600;
    }
</style>

<div class="container container-box">

    <div class="row mb-4">
        <div class="col-12">
            <h3 class="fw-bold m-0" style="border-left: 5px solid #2eca6a; padding-left: 15px;">
                My Profile Settings
            </h3>
        </div>
    </div>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger shadow-sm"><?= $error ?></div>
    <?php endif; ?>

    <?php if(isset($success)): ?>
        <div class="alert alert-success shadow-sm"><?= $success ?></div>
    <?php endif; ?>

    <div class="card-custom shadow-sm">
        <h5 class="section-title">Profile Information</h5>

        <form method="POST">
            <div class="row g-4">

                <div class="col-md-6">
                    <label class="form-label">Phone (Login ID)</label>
                    <input class="form-control bg-light" value="<?= $user['phone'] ?>" disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Business Name</label>
                    <input name="business_name" class="form-control" 
                        value="<?= htmlspecialchars($user['business_name']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">State</label>
                    <input name="state" class="form-control" 
                        value="<?= htmlspecialchars($user['state']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">District</label>
                    <input name="district" class="form-control" 
                        value="<?= htmlspecialchars($user['district']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">RERA Number</label>
                    <input name="rera" class="form-control" 
                        value="<?= htmlspecialchars($user['rera_number']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">GST Number</label>
                    <input name="gst" class="form-control" 
                        value="<?= htmlspecialchars($user['gst_number']) ?>">
                </div>

            </div>

            <button name="update_profile" class="btn btn-save mt-4">
                Save Changes
            </button>
        </form>
    </div>

    <div class="card-custom shadow-sm">
        <h5 class="section-title">Security Settings</h5>

        <form method="POST">
            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                </div>

            </div>

            <button name="change_password" class="btn btn-password mt-4">
                Update Password
            </button>
        </form>
    </div>

</div>

<?php include '../includes/footer.php'; ?>