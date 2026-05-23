<?php
session_start();

require '../config/db.php';

if (
    !isset($_SESSION['reset_verified']) ||
    $_SESSION['reset_verified'] !== true
) {
    die("Unauthorized");
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $password = $_POST['password'];

    if (strlen($password) < 4) {

        $error = "Password too short.";

    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password=?
            WHERE id=?
        ");

        $stmt->execute([
            $hash,
            $_SESSION['reset_user_id']
        ]);

        unset(
            $_SESSION['reset_user_id'],
            $_SESSION['reset_email'],
            $_SESSION['reset_otp'],
            $_SESSION['reset_expiry'],
            $_SESSION['reset_verified']
        );

        $success = "Password changed successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | EstateAgency</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --theme-green: #2eca6a;
            --theme-dark: #2b2b2b;
            --bg-light: #f7f7f7;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background-image: radial-gradient(#d1d1d1 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #ebebeb;
            border-radius: 20px;
            padding: 50px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        /* Top accent bar to match EstateAgency style */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--theme-green);
            border-radius: 20px 20px 0 0;
        }

        .brand-logo {
            display: block;
            margin: 0 auto 25px auto;
            text-align: center;
        }

        .brand-logo img {
            max-width: 180px;
            height: auto;
        }

        .login-title {
            font-weight: 700;
            color: #000;
            text-align: center;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            color: #888;
            text-align: center;
            font-size: 0.9rem;
            margin-bottom: 35px;
        }

        .form-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .input-group {
            border: 2px solid #f1f1f1;
            border-radius: 10px;
            overflow: hidden;
            transition: 0.3s;
        }

        .input-group:focus-within {
            border-color: var(--theme-green);
            box-shadow: 0 0 10px rgba(46, 202, 106, 0.1);
        }

        .input-group-text {
            background: #fff;
            border: none;
            color: #aaa;
            padding-left: 20px;
        }

        .form-control {
            border: none;
            padding: 15px 15px 15px 10px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .form-control:focus {
            box-shadow: none;
            background: transparent;
        }

        .btn-action {
            background-color: var(--theme-dark);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 16px;
            width: 100%;
            font-weight: 700;
            margin-top: 10px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        .btn-action:hover {
            background-color: var(--theme-green);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 202, 106, 0.2);
        }

        .btn-success-redirect {
            background-color: var(--theme-green);
        }
        
        .btn-success-redirect:hover {
            background-color: #000;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        .alert {
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            border: none;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <img src="../assets/logo.png" alt="Property Plus Logo">
    </div>

    <h3 class="login-title">Create Password</h3>
    <p class="login-subtitle">Configure your brand new structural key settings</p>

    <?php if($error): ?>
        <div class="alert alert-danger mb-4 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="alert alert-success mb-4 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($success) ?>
        </div>

        <a href="login.php" class="btn btn-action btn-success-redirect text-center d-block text-decoration-none">
            Proceed to Sign In
        </a>

    <?php else: ?>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label">New Security Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input 
                        type="password"
                        name="password"
                        class="form-control"
                        id="passwordField"
                        placeholder="••••••••"
                        required
                    >
                    <button 
                        type="button"
                        class="input-group-text bg-white"
                        onclick="togglePassword()"
                        style="cursor:pointer;"
                    >
                        <i class="fa-solid fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-action">
                Update Password
            </button>
        </form>

    <?php endif; ?>
</div>

<script>
function togglePassword() {
    let passwordField = document.getElementById('passwordField');
    let toggleIcon = document.getElementById('toggleIcon');

    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>