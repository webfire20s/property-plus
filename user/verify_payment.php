<?php
require '../config/db.php';
require '../vendor/autoload.php';
require '../config/razorpay.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

session_start();

$data = json_decode(file_get_contents("php://input"), true);

$razorpay_payment_id = $data['razorpay_payment_id'] ?? '';
$razorpay_order_id   = $data['razorpay_order_id'] ?? '';
$razorpay_signature  = $data['razorpay_signature'] ?? '';

if (
    empty($razorpay_payment_id) ||
    empty($razorpay_order_id) ||
    empty($razorpay_signature)
) {
    http_response_code(400);
    exit("Invalid payment response");
}

$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

try {

    $attributes = [
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    ];

    $api->utility->verifyPaymentSignature($attributes);

    // ✅ Update payment status
    $stmt = $pdo->prepare("
        UPDATE payments
        SET
            txn_id = ?,
            status = 'success'
        WHERE txn_id = ?
    ");

    $stmt->execute([
        $razorpay_payment_id,
        $razorpay_order_id
    ]);

    echo "success";

} catch (SignatureVerificationError $e) {

    // Mark failed
    $stmt = $pdo->prepare("
        UPDATE payments
        SET status='failed'
        WHERE txn_id=?
    ");

    $stmt->execute([$razorpay_order_id]);

    http_response_code(400);
    echo "Payment verification failed";
}
?>