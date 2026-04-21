
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

require '../config/razorpay.php';
$key_id = RAZORPAY_KEY_ID;
$key_secret = RAZORPAY_KEY_SECRET;

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
            SET status = 'active' 
            WHERE id = ?
        ")->execute([$payment['user_id']]);

    } elseif ($payment['type'] === 'membership') {

        $expiry = date('Y-m-d', strtotime('+1 year'));
        $today = date('Y-m-d');

        // 1. Update users table
        $pdo->prepare("
            UPDATE users 
            SET membership_plan = ?, membership_status = 'active'
            WHERE id = ?
        ")->execute([
            $payment['plan_id'],
            $payment['user_id']
        ]);

        // 2. Insert/Update user_memberships table
        $pdo->prepare("
            INSERT INTO user_memberships (user_id, membership_id, start_date, expiry_date, status)
            VALUES (?, ?, ?, ?, 'active')
        ")->execute([
            $payment['user_id'],
            $payment['plan_id'],
            $today,
            $expiry
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