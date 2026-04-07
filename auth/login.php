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

        if ($user['status'] == 'blocked') {
            die("Your account is blocked");
        }

        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/dashboard.php");
        }

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
    <title>Login | Property Plus</title>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            /* Subtle geometric pattern background */
            background-image: radial-gradient(#e2e8f0 0.5px, transparent 0.5px);
            background-size: 24px 24px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            background: var(--slate-900);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            font-size: 1.5rem;
        }

        .login-title {
            font-weight: 700;
            color: var(--slate-900);
            text-align: center;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #64748b;
            text-align: center;
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--slate-900);
            margin-bottom: 8px;
        }

        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: #94a3b8;
            border-radius: 12px 0 0 12px;
            padding-left: 16px;
        }

        .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
            padding: 12px 16px;
            font-size: 0.95rem;
            background-color: #ffffff;
            border-color: var(--slate-200);
        }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--slate-200);
            background-color: #ffffff;
        }

        .btn-login {
            background-color: var(--slate-900);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px;
            width: 100%;
            font-weight: 700;
            margin-top: 10px;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background-color: var(--blue-600);
            transform: translateY(-1px);
        }

        .footer-link {
            text-align: center;
            margin-top: 24px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .footer-link a {
            color: var(--blue-600);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa-solid fa-house-lock"></i>
    </div>
    <h3 class="login-title">Welcome Back</h3>
    <p class="login-subtitle">Enter your credentials to access your portal</p>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Phone Number</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                <input name="phone" type="text" class="form-control" placeholder="Enter registered phone" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input name="password" type="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn-login">
            Sign In
        </button>
    </form>
    <div class="footer-link">
        Don't have an account? <a href="register.php">Create Account</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>