<?php
session_start();

$entered_otp = $_POST['otp'];

if (!isset($_SESSION['otp'])) {
    die("OTP expired. Please request again.");
}

if ($entered_otp == $_SESSION['otp']) {
    $_SESSION['otp_verified'] = true;
    echo "OTP Verified. You can now register.";
} else {
    echo "Invalid OTP";
}