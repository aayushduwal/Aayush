<?php
session_start();
require_once('../database/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['oid']) ? $_GET['oid'] : (isset($_GET['pid']) ? $_GET['pid'] : null);

if (!$order_id) {
    $_SESSION['error'] = "Invalid order reference";
    header('Location: ../userdashboard/order_history.php');
    exit();
}

// Update order status to confirmed
$stmt = $conn->prepare("UPDATE orders SET status = 'confirmed' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    // Clear cart
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Redirect to a new payment success page with order details
    header("Location: ../payment/payment_success_page.php?oid=$order_id");
    exit();
} else {
    $_SESSION['error'] = "Could not confirm order. Please contact support.";
    header('Location: ../userdashboard/order_history.php');
    exit();
}
?>
