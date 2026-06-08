<?php

require '../config/db.php';

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

try{

    if(
        !isset($_SESSION['otp_verified']) ||
        $_SESSION['otp_verified'] !== true
    ){
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

    $check = $pdo->prepare(
        "SELECT id FROM users WHERE phone=?"
    );

    $check->execute([$phone]);

    if($check->rowCount() > 0){
        throw new Exception(
            "Phone number already registered"
        );
    }

    $password =
        password_hash(
            $password_raw,
            PASSWORD_DEFAULT
        );

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
            'inactive'
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

    $_SESSION['temp_user_id'] = $user_id;

    header(
        "Location: ../user/registration_payment.php"
    );
    exit;

}
catch(Exception $e){

    die($e->getMessage());

}