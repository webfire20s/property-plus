<?php
require '../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logic remains untouched
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        die("<div class='alert alert-danger'>Please verify OTP first</div>");
    }

    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $business_name = $_POST['business_name'];
    $state = $_POST['state'];
    $district = $_POST['district'];
    $rera = $_POST['rera'];
    $gst = $_POST['gst'];

    // Validation checks remain untouched
    //if (empty($rera)) { die("RERA number is required"); }
    //if (preg_match("/^[0-9]+$/", $rera)) { die("Invalid RERA number"); }
    //if (!preg_match("/^[A-Z0-9\/\-]{8,25}$/i", $rera)) { die("Invalid RERA format"); }
    if (!empty($gst)) {
        if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/", $gst)) { die("Invalid GST number"); }
    }

    $stmt = $pdo->prepare("INSERT INTO users (phone, password, business_name, state, district, rera_number, gst_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$phone, $password, $business_name, $state, $district, $rera, $gst]);

    $user_id = $pdo->lastInsertId();

    // ✅ Set sessions for payment
    $_SESSION['user_id'] = $user_id;
    $_SESSION['temp_user_id'] = $user_id;

    // Cleanup OTP session
    unset($_SESSION['otp_verified'], $_SESSION['otp'], $_SESSION['otp_phone']);

    // ✅ Redirect to registration payment
    header("Location: ../user/registration_payment.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration | EstateAgency</title>
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
            background-image: radial-gradient(#d1d1d1 0.5px, transparent 0.5px);
            background-size: 20px 20px;
        }

        /* Logo styling */
        .brand-logo {
            display: block;
            margin: 0 auto 25px auto;
            text-align: center;
        }

        .brand-logo img {
            max-width: 180px;
            height: auto;
        }

        .reg-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #ebebeb;
            padding: 45px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            position: relative;
        }

        /* Top accent bar */
        .reg-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--theme-green);
            border-radius: 20px 20px 0 0;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 2px solid #f1f1f1;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--theme-green);
            box-shadow: none;
            background: #fff;
        }

        .form-label {
            font-weight: 700;
            font-size: 0.75rem;
            color: #555;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .step-title {
            border-left: 5px solid var(--theme-green);
            padding-left: 15px;
            margin-bottom: 30px;
            margin-top: 10px;
        }

        .btn-verify {
            background: var(--theme-dark);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
            border: none;
        }

        .btn-verify:hover {
            background: var(--theme-green);
            color: white;
        }

        .btn-register {
            background: var(--theme-green);
            color: white;
            width: 100%;
            border-radius: 10px;
            padding: 16px;
            font-weight: 700;
            border: none;
            margin-top: 25px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #25a556;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 202, 106, 0.2);
        }

        .info-box {
            background: #f0fff4;
            border-left: 4px solid var(--theme-green);
            color: #2b2b2b;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="brand-logo">
                <img src="../assets/logo.png" alt="Property Plus Logo">
            </div>

            <div class="text-center mb-5">
                <h2 class="fw-bold mb-2" style="color: var(--theme-dark);">Become a Professional Partner</h2>
                <p class="text-muted">Join the most trusted real estate network in India.</p>
            </div>

            <div class="reg-card">
                <form method="POST" id="registerForm">
                    <div class="step-title">
                        <h5 class="fw-bold mb-0">01. Identity Verification</h5>
                    </div>
                    
                    <div class="row g-3 mb-5">
                        <div class="col-md-8">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-2 border-end-0"><i class="fa-solid fa-mobile-button text-muted"></i></span>
                                <input name="phone" class="form-control border-start-0" placeholder="10 Digit Mobile Number" required>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" onclick="sendOTP()" class="btn btn-verify w-100 p-2 px-3">Send OTP</button>
                        </div>
                        
                        <div class="col-md-8">
                            <label class="form-label">Verification Code</label>
                            <input name="otp" class="form-control" placeholder="6-Digit OTP">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" onclick="verifyOTP()" class="btn btn-outline-dark w-100 p-2" style="border-radius:8px;">Verify</button>
                        </div>
                        <div id="otp_msg" class="small mt-2 ps-1"></div>
                    </div>

                    <div class="step-title">
                        <h5 class="fw-bold mb-0">02. Agency Details</h5>
                    </div>
                    
                    <div class="row g-3 mb-5">
                        <div class="col-12">
                            <label class="form-label">Business / Agency Name</label>
                            <input name="business_name" class="form-control" placeholder="Business Name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input name="state" class="form-control" placeholder="State"  required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">District</label>
                            <input name="district" class="form-control" placeholder="District/City" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RERA Number (Optional)</label>
                            <input name="rera" class="form-control" placeholder="RERA No." >
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST Number (Optional)</label>
                            <input name="gst" class="form-control" placeholder="GSTIN">
                        </div>
                    </div>

                    <div class="step-title">
                        <h5 class="fw-bold mb-0">03. Secure Your Account</h5>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <input name="password" type="password" class="form-control" placeholder="*********" required>
                    </div>

                    <div class="p-3 info-box rounded-3 small mb-4">
                        <i class="fa-solid fa-shield-halved me-2"></i> 
                        Registration requires a one-time activation fee of <b>₹1,000</b> to verify your agency status.
                    </div>

                    <button type="submit" class="btn-register shadow-sm">
                        Confirm & Proceed to Payment
                    </button>
                    <!-- <script src="https://checkout.razorpay.com/v1/checkout.js"></script> -->
                    
                    <p class="text-center mt-4 text-muted small">
                        Already have an account? <a href="login.php" class="text-success fw-bold text-decoration-none">Sign In</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- <script>
function payRegistration() {

    fetch('../user/create_order.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'amount=1000&type=registration'
    })
    .then(res => res.json())
    .then(data => {

        if (!data.order_id) {
            console.log(data);
            alert("Payment init failed: " + (data.error || "Unknown error"));
            return;
        }

        var options = {
            "key": data.key,
            "amount": data.amount * 100,
            "currency": "INR",
            "name": "PropertyPlus",
            "description": "Registration Fee",
            "order_id": data.order_id,

            // ✅ ADD THIS (VERY IMPORTANT)
            "prefill": {
                "name": document.querySelector('[name=business_name]').value || "User",
                "email": "test@example.com",
                "contact": document.querySelector('[name=phone]').value || "9999999999"
            },

            // ✅ ENABLE TEST FRIENDLY FLOW
            "config": {
                "display": {
                    "blocks": {
                        "utib": { // UPI block
                            "name": "Pay via UPI",
                            "instruments": [
                                {
                                    "method": "upi"
                                }
                            ]
                        }
                    },
                    "sequence": ["block.utib"],
                    "preferences": {
                        "show_default_blocks": true
                    }
                }
            },

            "handler": function (response) {
                fetch('../user/verify_payment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(response)
                })
                .then(res => res.text())
                .then(res => {
                    if (res === "success") {
                        document.getElementById("registerForm").submit();
                    } else {
                        alert("Payment verification failed");
                    }
                });
            },

            "theme": {
                "color": "#2eca6a"
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();

    });
}
</script> -->
<script>
function sendOTP() {
    let phone = document.querySelector('[name=phone]').value;
    document.getElementById('otp_msg').innerHTML = "<i class='fa-solid fa-circle-notch fa-spin text-success'></i> Sending code...";
    fetch('send_otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'phone=' + phone
    })
    .then(res => res.text())
    .then(data => { document.getElementById('otp_msg').innerHTML = data; });
}

function verifyOTP() {
    let otp = document.querySelector('[name=otp]').value;
    fetch('verify_otp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'otp=' + otp
    })
    .then(res => res.text())
    .then(data => { document.getElementById('otp_msg').innerHTML = data; });
}
</script>

</body>
</html>