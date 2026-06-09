<?php
require '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

try {

    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        throw new Exception("Please verify OTP first");
    }

    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $business_name = trim($_POST['business_name'] ?? '');
    $state = trim($_POST['state'] ?? '');
    $district = trim($_POST['district'] ?? '');
    $rera = trim($_POST['rera'] ?? '');
    $gst = trim($_POST['gst'] ?? '');

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        throw new Exception("Invalid phone number");
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address");
    }

    if (strlen($password_raw) < 4) {
        throw new Exception("Password must be at least 4 characters");
    }

    $check = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
    $check->execute([$phone]);

    if ($check->fetch()) {
        throw new Exception("This phone number is already registered. Please login.");
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users
        (
            phone,
            email,
            password,
            business_name,
            state,
            district,
            rera_number,
            gst_number,
            status
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?,
            'active'
        )
    ");

    $stmt->execute([
        $phone,
        $email,
        $password,
        $business_name,
        $state,
        $district,
        $rera,
        $gst
    ]);

    $user_id = $pdo->lastInsertId();

    $_SESSION['user_id'] = $user_id;

    echo json_encode([
        'success' => true,
        'user_id' => $user_id
    ]);

} catch (PDOException $e) {

    if (($e->errorInfo[1] ?? 0) == 1062) {

        echo json_encode([
            'success' => false,
            'message' => 'This phone number is already registered.'
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred.'
        ]);
    }

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}