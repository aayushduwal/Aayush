<?php
session_start();
require_once('../database/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['oid']) ? $_GET['oid'] : null;
$payment_method = isset($_GET['payment_method']) ? $_GET['payment_method'] : null;

if (!$order_id || !$payment_method) {
    $_SESSION['error'] = "Invalid order reference";
    header('Location: ../userdashboard/order_history.php');
    exit();
}

// eSewa Payment Verification
if ($payment_method === 'esewa') {
    $response = file_get_contents(
        "https://esewa.com.np/epay/transrec" .
        "?amt=YOUR_ORDER_AMOUNT" . // Replace with the actual amount
        "&rid=" . urlencode($_GET['refId']) . // Reference ID from eSewa
        "&pid=" . urlencode($_GET['pid']) . // Unique PID sent during request
        "&scd=" . urlencode(ESEWA_MERCHANT_ID) // Merchant ID
    );

    if (strpos($response, "Success") === false) {
        $_SESSION['error'] = "Payment verification failed. Order not confirmed.";
        header('Location: ../userdashboard/order_history.php');
        exit();
    }
}

// Khalti Payment Verification
if ($payment_method === 'khalti') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://khalti.com/api/v2/payment/verify/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'token' => $_GET['token'], // Token from Khalti response
        'amount' => $_GET['amount'] // The amount sent for verification
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Key ' . KHALTI_SECRET_KEY
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $response_data = json_decode($response, true);

    if ($status_code !== 200 || $response_data['state']['name'] !== 'Completed') {
        $_SESSION['error'] = "Payment verification failed. Order not confirmed.";
        header('Location: ../userdashboard/order_history.php');
        exit();
    }
}

// If payment verified, update order status
$stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect to success page
    header("Location: ../payment/payment_success_page.php?oid=$order_id");
    exit();
} else {
    $_SESSION['error'] = "Could not confirm order. Please contact support.";
    header('Location: ../userdashboard/order_history.php');
    exit();
}
?>
