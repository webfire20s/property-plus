<?php
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // sanitize
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);

    // validate phone
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        die("Invalid Phone Number");
    }

    // check duplicate
    $check = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $check->execute([$phone]);

    if ($check->rowCount() > 0) {
        die("Phone already registered");
    }
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rera = trim($_POST['rera']);
    $gst  = trim($_POST['gst']);


    // ❌ Empty check
    if (empty($rera)) {
        die("RERA number is required");
    }

    // ❌ Reject only numbers (your current bug)
    if (preg_match("/^[0-9]+$/", $rera)) {
        die("Invalid RERA number (cannot be only digits)");
    }

    // ✅ Proper format (flexible but realistic)
    if (!preg_match("/^[A-Z0-9\/\-]{8,25}$/i", $rera)) {
        die("Invalid RERA format");
    }

    // 🟡 GST VALIDATION (optional but strict if entered)
    if (!empty($gst)) {
        if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/", $gst)) {
            die("Invalid GST number");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password, rera_number, gst_number) VALUES (?, ?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$name, $phone, $email, $password, $rera, $gst]);
        echo "Registration Successful";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration | Property Plus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --slate-50: #f8fafc;
            --slate-200: #e2e8f0;
            --slate-900: #0f172a;
            --blue-600: #2563eb;
        }

        body {
            background-color: var(--slate-50);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
            background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }

        .reg-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        .reg-header { text-align: center; margin-bottom: 35px; }
        
        .brand-icon {
            width: 50px;
            height: 50px;
            background: var(--slate-900);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.4rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--slate-900);
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid var(--slate-200);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--blue-600);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-register {
            background-color: var(--slate-900);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            width: 100%;
            font-weight: 700;
            margin-top: 20px;
            transition: 0.2s;
        }

        .btn-register:hover {
            background-color: var(--blue-600);
        }

        .section-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--slate-200);
            margin-left: 15px;
        }
    </style>
</head>
<body>

<div class="reg-card">
    <div class="reg-header">
        <div class="brand-icon"><i class="fa-solid fa-user-plus"></i></div>
        <h3 class="fw-bold text-dark">Partner Registration</h3>
        <p class="text-muted small">Join our verified network of real estate professionals</p>
    </div>

    <form method="POST">
        
        <div class="section-title">Personal Details</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">Full Name</label>
                <input name="name" class="form-control" placeholder="Full Name" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone Number</label>
                <input name="phone" class="form-control" placeholder="+91..." required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email Address</label>
                <input name="email" type="email" class="form-control" placeholder="name@email.com">
            </div>
            <div class="col-md-6">
                <label class="form-label">Account Password</label>
                <input name="password" type="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <div class="section-title">Business Verification</div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label">RERA Number</label>
                <input name="rera" class="form-control" placeholder="Registration No." required>
            </div>
            <div class="col-md-6">
                <label class="form-label">GST Number (Optional)</label>
                <input name="gst" class="form-control" placeholder="22AAAAA0000A1Z5">
            </div>
        </div>

        <button type="submit" class="btn-register">
            Create Partner Account
        </button>
    </form>
    <p class="text-center mt-4 mb-0 text-muted small">
        Already registered? <a href="login.php" class="text-primary fw-bold text-decoration-none">Sign In</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>