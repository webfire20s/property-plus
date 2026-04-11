<?php
session_start();

$phone = $_POST['phone'];

if (empty($phone)) {
    die("Enter phone number first");
}

// Generate OTP
$otp = rand(100000,999999);

// Store in session
$_SESSION['otp'] = $otp;
$_SESSION['otp_phone'] = $phone;
$_SESSION['otp_verified'] = false;

// For testing
echo "Your OTP is: $otp";