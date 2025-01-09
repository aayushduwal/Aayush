<?php
session_start();
require_once('../database/config.php');
require_once('payment_config.php');

function verifyEsewaPayment($data) {
    $url = getEsewaVerifyUrl();
    
    $params = [
        'amt' => $data['amt'],
        'rid' => $data['refId'],
        'pid' => $data['oid'],
        'scd' => ESEWA_MERCHANT_ID
    ];
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    if(strpos($response, "Success") !== false) {
        return true;
    }
    return false;
}

function verifyKhaltiPayment($token, $amount) {
    $url = getKhaltiVerifyUrl();
    
    $params = [
        'token' => $token,
        'amount' => $amount
    ];
    
    $headers = [
        'Authorization: Key ' . KHALTI_SECRET_KEY,
        'Content-Type: application/json'
    ];
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($curl);
    $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    return $status_code === 200;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$payment_method = $_GET['payment_method'] ?? '';
$order_id = $_GET['oid'] ?? '';

if (!$order_id || !$payment_method) {
    $_SESSION['error'] = "Invalid payment verification request";
    header('Location: ../cart/checkout.php');
    exit();
}

// Get order details
$stmt = $conn->prepare("SELECT o.*, c.name, c.email, c.phone FROM orders o 
                       LEFT JOIN customers c ON o.customer_id = c.id 
                       WHERE o.id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    $_SESSION['error'] = "Order not found";
    header('Location: ../cart/checkout.php');
    exit();
}

$payment_verified = false;

switch ($payment_method) {
    case 'esewa':
        if (isset($_GET['refId'])) {
            $payment_data = [
                'amt' => $order['total_amount'],
                'refId' => $_GET['refId'],
                'oid' => $order_id
            ];
            $payment_verified = verifyEsewaPayment($payment_data);
        }
        break;
        
    case 'khalti':
        if (isset($_GET['token'])) {
            $payment_verified = verifyKhaltiPayment($_GET['token'], $order['total_amount'] * 100);
        }
        break;
}

if ($payment_verified) {
    // Update order status
    $stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    
    $_SESSION['success'] = "Payment verified successfully! Your order has been confirmed.";
    header('Location: ../userdashboard/order_history.php');
} else {
    // Update order status to failed
    $stmt = $conn->prepare("UPDATE orders SET status = 'failed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    $_SESSION['error'] = "Payment verification failed. Please try again.";
    header('Location: ../cart/checkout.php');
}
exit();
?>
