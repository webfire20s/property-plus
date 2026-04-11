<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email=? AND password=?");
    $stmt->execute([$email, $password]);

    $admin = $stmt->fetch();

    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Invalid credentials";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background: #f1f5f9; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            border: 1px solid #e2e8f0;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: #2563eb;
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 20px;
        }

        .form-control {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .form-control:focus {
            background: white;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            margin-top: 10px;
            transition: 0.2s;
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .login-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.85rem;
            color: #64748b;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa-solid fa-house-shield"></i>
    </div>
    
    <div class="text-center mb-4">
        <h3 class="fw-bold text-dark">Admin Portal</h3>
        <p class="text-secondary small">Please enter your credentials to continue</p>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" name="email" class="form-control border-start-0" placeholder="admin@propertyplus.com" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-secondary">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label small text-secondary" for="remember">
                    Remember me
                </label>
            </div>
            <a href="#" class="small text-primary text-decoration-none fw-bold">Forgot?</a>
        </div>

        <button type="submit" class="btn btn-login">
            Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>
    </form>

    <div class="login-footer">
        &copy; <?= date('Y') ?> PropertyPlus System. All rights reserved.
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>