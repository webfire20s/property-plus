<?php
require '../config/db.php';
require '../vendor/autoload.php';



use Razorpay\Api\Api;

session_start();

// Allow guest (registration flow)
$user_id = $_SESSION['user_id'] ?? null;


// 🔐 Validate input
$type    = $_POST['type'] ?? null;       // registration / membership
$amount  = $_POST['amount'] ?? null;
$plan_id = $_POST['plan_id'] ?? null;

if (!$type || !$amount || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

// 🔑 Razorpay Keys
require '../config/razorpay.php';
$key_id = RAZORPAY_KEY_ID;
$key_secret = RAZORPAY_KEY_SECRET;

try {

    $api = new Api($key_id, $key_secret);

    // Create Razorpay Order
    $order = $api->order->create([
        'amount' => $amount * 100, // convert to paise
        'currency' => 'INR'
    ]);

    $order_id = $order['id'];

    // 💾 Store in DB (PENDING)
    $stmt = $pdo->prepare("
        INSERT INTO payments (user_id, amount, type, txn_id, status, plan_id)
        VALUES (?, ?, ?, ?, 'pending', ?)
    ");

    $stmt->execute([
        $user_id,
        $amount,
        $type,
        $order_id,   // storing order_id temporarily
        $plan_id
    ]);

    // 📤 Response to frontend
    echo json_encode([
        'status'   => 'success',
        'order_id' => $order_id,
        'amount'   => $amount,
        'key'      => $key_id
    ]);

} catch (Exception $e) {

    http_response_code(500);
    echo json_encode([
        'error' => 'Order creation failed',
        'details' => $e->getMessage()
    ]);
}