<?php
require '../config/db.php';
require '../vendor/autoload.php';

use Razorpay\Api\Api;

$data = json_decode(file_get_contents("php://input"), true);

// 🔐 Validate data
if (
    empty($data['razorpay_order_id']) ||
    empty($data['razorpay_payment_id']) ||
    empty($data['razorpay_signature'])
) {
    http_response_code(400);
    echo "Invalid payment data";
    exit;
}

$key_id = "rzp_live_ScvB0Ti18LUIK2";
$key_secret = "EQPJZ3hP1YTnQrfprAr1W7dg";

$api = new Api($key_id, $key_secret);

try {

    // 🔐 Verify signature (CRITICAL)
    $api->utility->verifyPaymentSignature([
        'razorpay_order_id'   => $data['razorpay_order_id'],
        'razorpay_payment_id' => $data['razorpay_payment_id'],
        'razorpay_signature'  => $data['razorpay_signature']
    ]);

    // ✅ Find payment using ORDER ID
    $stmt = $pdo->prepare("SELECT * FROM payments WHERE txn_id = ?");
    $stmt->execute([$data['razorpay_order_id']]);
    $payment = $stmt->fetch();

    if (!$payment) {
        throw new Exception("Payment record not found");
    }

    // ✅ Update payment record
    $update = $pdo->prepare("
        UPDATE payments 
        SET status = 'success', txn_id = ?
        WHERE txn_id = ?
    ");

    $update->execute([
        $data['razorpay_payment_id'], // replace with actual payment id
        $data['razorpay_order_id']
    ]);

    // 🔥 ACTIVATE FEATURES BASED ON TYPE

    if ($payment['type'] === 'registration') {

        $pdo->prepare("
            UPDATE users 
            SET is_registered = 1 
            WHERE id = ?
        ")->execute([$payment['user_id']]);

    } elseif ($payment['type'] === 'membership') {

        // Example: 1 year validity
        $expiry = date('Y-m-d', strtotime('+1 year'));

        $pdo->prepare("
            UPDATE users 
            SET membership_plan = ?, membership_expiry = ?
            WHERE id = ?
        ")->execute([
            $payment['plan_id'],
            $expiry,
            $payment['user_id']
        ]);
    }

    echo "success";

} catch (Exception $e) {

    // ❌ Mark as failed (optional but recommended)
    $pdo->prepare("
        UPDATE payments 
        SET status = 'failed' 
        WHERE txn_id = ?
    ")->execute([$data['razorpay_order_id']]);

    http_response_code(400);
    echo "verification failed";
}