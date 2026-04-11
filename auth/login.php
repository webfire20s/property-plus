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
    <title>Login | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --brand-gold: #fbbf24;
            --brand-blue: #2563eb;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-900: #0f172a;
        }

        body {
            background-color: var(--slate-50);
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            /* Enhanced geometric background */
            background-image: radial-gradient(var(--slate-200) 1px, transparent 1px);
            background-size: 32px 32px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid var(--slate-200);
            border-radius: 32px;
            padding: 48px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.08);
            position: relative;
            overflow: hidden;
        }

        /* Subtle gold accent at the top of the card */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--brand-gold);
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            background: var(--slate-900);
            color: var(--brand-gold);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px auto;
            font-size: 1.8rem;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }

        .login-title {
            font-weight: 800;
            color: var(--slate-900);
            text-align: center;
            margin-bottom: 8px;
            letter-spacing: -1px;
            font-size: 1.75rem;
        }

        .login-subtitle {
            color: var(--slate-400);
            text-align: center;
            font-size: 0.95rem;
            margin-bottom: 35px;
            font-weight: 500;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-group {
            background-color: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 14px;
            transition: all 0.2s ease;
        }

        .input-group:focus-within {
            border-color: var(--brand-gold);
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1);
        }

        .input-group-text {
            background-color: transparent;
            border: none;
            color: var(--slate-400);
            padding-left: 18px;
        }

        .form-control {
            border: none;
            background-color: transparent;
            padding: 14px 18px 14px 10px;
            font-size: 1rem;
            font-weight: 600;
            color: var(--slate-900);
        }

        .form-control:focus {
            box-shadow: none;
            background-color: transparent;
        }

        .form-control::placeholder {
            color: var(--slate-400);
            font-weight: 400;
        }

        .btn-login {
            background-color: var(--slate-900);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 16px;
            width: 100%;
            font-weight: 800;
            margin-top: 15px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-login:hover {
            background-color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.2);
            color: var(--brand-gold);
        }

        .footer-link {
            text-align: center;
            margin-top: 30px;
            font-size: 0.95rem;
            color: var(--slate-400);
            font-weight: 500;
        }

        .footer-link a {
            color: var(--slate-900);
            text-decoration: none;
            font-weight: 700;
            border-bottom: 2px solid var(--brand-gold);
            padding-bottom: 2px;
            transition: all 0.2s;
        }

        .footer-link a:hover {
            color: var(--brand-blue);
            border-color: var(--brand-blue);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa-solid fa-house-chimney-user"></i>
    </div>
    <h3 class="login-title">Partner Portal</h3>
    <p class="login-subtitle">Sign in to manage your premium listings</p>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Phone Identity</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone-flip"></i></span>
                <input name="phone" type="text" class="form-control" placeholder="Enter mobile number" required>
            </div>
        </div>

        <div class="mb-4">
            <div class="d-flex justify-content-between">
                <label class="form-label">Security Key</label>
            </div>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-shield-halved"></i></span>
                <input name="password" type="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btn-login">
            Secure Sign In
        </button>
    </form>
    
    <div class="footer-link">
        New to the platform? <a href="register.php">Get Started</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>