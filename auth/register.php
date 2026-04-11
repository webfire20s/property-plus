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
    if (empty($rera)) { die("RERA number is required"); }
    if (preg_match("/^[0-9]+$/", $rera)) { die("Invalid RERA number"); }
    if (!preg_match("/^[A-Z0-9\/\-]{8,25}$/i", $rera)) { die("Invalid RERA format"); }
    if (!empty($gst)) {
        if (!preg_match("/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[A-Z0-9]{3}$/", $gst)) { die("Invalid GST number"); }
    }

    $stmt = $pdo->prepare("INSERT INTO users (phone, password, business_name, state, district, rera_number, gst_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$phone, $password, $business_name, $state, $district, $rera, $gst]);
    $user_id = $pdo->lastInsertId();

    unset($_SESSION['otp_verified'], $_SESSION['otp'], $_SESSION['otp_phone']);
    header("Location: ../user/registration_payment.php?user_id=$user_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration | PropertyPlus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --slate-50: #f8fafc; --slate-900: #0f172a; --blue-600: #2563eb; }
        body { background-color: var(--slate-50); font-family: 'Plus Jakarta Sans', sans-serif; }
        .reg-card { background: white; border-radius: 24px; border: 1px solid #e2e8f0; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .form-control { border-radius: 12px; padding: 12px; border: 1px solid #e2e8f0; background: #fcfdfe; }
        .form-label { font-weight: 700; font-size: 0.85rem; color: #64748b; text-transform: uppercase; }
        .step-title { border-left: 4px solid var(--blue-600); padding-left: 15px; margin-bottom: 25px; }
        .btn-verify { background: var(--slate-900); color: white; border-radius: 10px; font-weight: 600; }
        .btn-register { background: var(--blue-600); color: white; width: 100%; border-radius: 12px; padding: 15px; font-weight: 700; border: none; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="text-center mb-4">
                <h2 class="fw-bold">Become a Partner</h2>
                <p class="text-secondary">Join Uttar Pradesh's exclusive real estate network.</p>
            </div>

            <div class="reg-card">
                <form method="POST">
                    <div class="step-title"><h5 class="fw-bold mb-0">Step 1: Phone Verification</h5></div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-phone text-muted"></i></span>
                                <input name="phone" class="form-control border-start-0" placeholder="10 Digit Mobile Number" required>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" onclick="sendOTP()" class="btn btn-verify w-100 p-2 px-3">Send OTP</button>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Enter 6-Digit OTP</label>
                            <input name="otp" class="form-control" placeholder="000000">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" onclick="verifyOTP()" class="btn btn-outline-dark w-100 p-2">Verify</button>
                        </div>
                        <div id="otp_msg" class="small mt-2"></div>
                    </div>

                    <div class="step-title"><h5 class="fw-bold mb-0">Step 2: Business & Compliance</h5></div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Business / Agency Name</label>
                            <input name="business_name" class="form-control" placeholder="e.g. Bansal Realty Group" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State</label>
                            <input name="state" class="form-control" value="Uttar Pradesh" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">District</label>
                            <input name="district" class="form-control" placeholder="e.g. Firozabad" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">RERA Number</label>
                            <input name="rera" class="form-control" placeholder="UPRERA..." required>
                            <div class="text-muted" style="font-size:0.7rem;">Must include letters (e.g., UPRERAPRJ1234)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST Number (Optional)</label>
                            <input name="gst" class="form-control" placeholder="09AAAAA0000A1Z5">
                        </div>
                    </div>

                    <div class="step-title"><h5 class="fw-bold mb-0">Step 3: Security</h5></div>
                    <div class="mb-4">
                        <label class="form-label">Account Password</label>
                        <input name="password" type="password" class="form-control" placeholder="Create a strong password" required>
                    </div>

                    <div class="p-3 bg-light rounded-3 small text-secondary mb-3">
                        <i class="fa-solid fa-circle-info me-2 text-primary"></i> 
                        After registration, you will be redirected to complete the one-time registration fee of <b>₹1,000</b>.
                    </div>

                    <button type="submit" class="btn-register shadow-sm">
                        Verify & Pay Registration Fee
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Logic remains untouched
function sendOTP() {
    let phone = document.querySelector('[name=phone]').value;
    document.getElementById('otp_msg').innerHTML = "<i class='fa-solid fa-spinner fa-spin'></i> Sending...";
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