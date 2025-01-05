<?php
session_start();
require_once('../database/config.php');

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$username = $loggedIn ? $_SESSION['username'] : '';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT SUM(price * quantity) as total FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$total = $result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .order-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 30px;
        }
        
        .place-order-btn {
            background: #0d6efd;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .place-order-btn:hover {
            background: #0b5ed7;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="checkout-container">
        <h1>Checkout</h1>
        
        <div class="order-summary">
            <h2>Order Summary</h2>
            <p>Total Amount: रु. <?php echo number_format($total, 0); ?></p>
        </div>
        
        <form action="process_payment.php" method="POST">
            <h2>Shipping Information</h2>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" required>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" required>
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" required>
            </div>
            
            <h2>Payment Method</h2>
            <div class="form-group">
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="cod">Cash on Delivery</option>
                    <option value="esewa">eSewa</option>
                    <option value="khalti">Khalti</option>
                </select>
            </div>
            
            <button type="submit" class="place-order-btn">Place Order</button>
        </form>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
