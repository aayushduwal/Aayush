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

// Update order status to failed
$stmt = $conn->prepare("UPDATE orders SET status = 'failed' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

$_SESSION['error'] = "Payment failed. Please try again or choose a different payment method.";
header('Location: ../cart/checkout.php');
exit();
?>
