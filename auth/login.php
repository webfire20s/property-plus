<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        if ($user['status'] == 'pending') {
            die("Complete your registration payment first.");
        }

        if ($user['status'] == 'blocked') {
            die("Your account has been blocked by admin.");
        }

        // ✅ Only active users
        $_SESSION['user_id'] = $user['id'];

        header("Location: ../user/dashboard.php");
        exit;
        

    } else {
        echo "Invalid Credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login | EstateAgency</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
            width: 70px;
            height: 70px;
            background: var(--theme-dark);
            color: var(--theme-green);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px auto;
            font-size: 2rem;
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

        .btn-login {
            background-color: var(--theme-dark);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 16px;
            width: 100%;
            font-weight: 700;
            margin-top: 20px;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-login:hover {
            background-color: var(--theme-green);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 202, 106, 0.2);
        }

        .footer-link {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #888;
        }

        .footer-link a {
            color: var(--theme-green);
            text-decoration: none;
            font-weight: 700;
            transition: 0.2s;
        }

        .footer-link a:hover {
            color: #000;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa-solid fa-house-lock"></i>
    </div>
    <h3 class="login-title">Partner Portal</h3>
    <p class="login-subtitle">Access your listing management dashboard</p>

    <form method="POST">
        <div class="mb-4">
            <label class="form-label">Phone Number</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-mobile-screen-button"></i></span>
                <input name="phone" type="text" class="form-control" placeholder="9876543210" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Security Key</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                <input name="password" type="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn-login">
            Secure Sign In
        </button>
    </form>
    
    <div class="footer-link">
        Not a partner yet? <a href="register.php">Create Account</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>