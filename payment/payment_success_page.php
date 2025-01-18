<?php
session_start();
require_once('../database/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['oid']) ? $_GET['oid'] : null;

if (!$order_id) {
    $_SESSION['error'] = "Invalid order reference.";
    header('Location: ../userdashboard/order_history.php');
    exit();
}

// Fetch order details
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $order = $result->fetch_assoc();
} else {
    $_SESSION['error'] = "Order not found.";
    header('Location: ../userdashboard/order_history.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
</head>
<body>
    <h1>Thank You for Your Purchase!</h1>
    <p>Your order has been successfully confirmed.</p>
    <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
    <p><strong>Total Amount:</strong> Rs. <?php echo $order['total_amount']; ?></p>
    <p>We will deliver your order by <strong><?php echo $order['delivery_date']; ?></strong>.</p>

    <a href="../userdashboard/order_history.php">Go to My Orders</a> | 
    <a href="../index.php">Continue Shopping</a>
</body>
</html>
