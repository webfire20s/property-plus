<?php
session_start();

require '../config/mail.php';

if (
    !isset($_SESSION['reset_user_id']) ||
    !isset($_SESSION['reset_email'])
) {
    die("Unauthorized");
}

$otp = rand(100000, 999999);

$_SESSION['reset_otp'] = $otp;
$_SESSION['reset_expiry'] = time() + 300;

try {

    $mail = getMailer();

    $mail->addAddress($_SESSION['reset_email']);

    $mail->Subject = "Password Reset OTP";

    $mail->Body = "
        Your OTP for password reset is:

        $otp

        This OTP will expire in 5 minutes.
    ";

    $mail->send();

    header("Location: verify_reset_otp.php");
    exit;

} catch (Exception $e) {

    die("Mail Error: " . $e->getMessage());
}