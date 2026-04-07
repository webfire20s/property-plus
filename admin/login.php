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

<form method="POST">
    <input name="email" placeholder="Email"><br>
    <input name="password" placeholder="Password"><br>
    <button>Login</button>
</form>