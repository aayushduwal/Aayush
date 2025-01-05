<?php
session_start();
require_once('../database/config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items and subtotal
$stmt = $conn->prepare("SELECT c.*, p.name, p.images FROM cart c 
                      LEFT JOIN products p ON c.product_id = p.id 
                      WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
$subtotal = 0;

while ($item = $result->fetch_assoc()) {
    $items[] = $item;
    $subtotal += $item['price'] * $item['quantity'];
}

// Delivery zones and charges
$delivery_zones = [
    'inside_ring_road' => ['name' => 'Inside Ring Road', 'charge' => 85],
    'outside_ring_road' => ['name' => 'Outside Ring Road', 'charge' => 150],
    'outside_valley' => ['name' => 'Outside Valley', 'charge' => 250]
];
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
            display: flex;
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .shipping-details {
            flex: 2;
        }
        .order-summary {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            position: sticky;
            top: 20px;
            height: fit-content;
        }
        .item-list {
            margin-bottom: 20px;
        }
        .item {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }
        .delivery-options {
            margin: 20px 0;
        }
        .delivery-option {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        .delivery-option.selected {
            border-color: #4CAF50;
            background: #f0f9f0;
        }
        .proceed-btn {
            background: #ff5722;
            color: white;
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .proceed-btn:hover {
            background: #f4511e;
        }
    </style>
</head>
<body>
    <?php include('../includes/header.php'); ?>

    <div class="checkout-container">
        <div class="shipping-details">
            <h2>Shipping Information</h2>
            <form id="checkoutForm" action="process_payment.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Delivery Zone</label>
                    <?php foreach ($delivery_zones as $key => $zone): ?>
                    <div class="delivery-option" data-charge="<?php echo $zone['charge']; ?>">
                        <label>
                            <input type="radio" name="delivery_zone" value="<?php echo $key; ?>" required>
                            <?php echo $zone['name']; ?>
                        </label>
                        <span>रु. <?php echo number_format($zone['charge'], 0); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="form-group">
                    <label>Detailed Address</label>
                    <textarea name="address" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control" required>
                        <option value="">Select Payment Method</option>
                        <option value="cod">Cash on Delivery</option>
                        <option value="esewa">eSewa</option>
                        <option value="khalti">Khalti</option>
                    </select>
                </div>
            </form>
        </div>

        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="item-list">
                <?php foreach ($items as $item): ?>
                <div class="item">
                    <img src="../uploads/products/<?php echo $item['images']; ?>" alt="<?php echo $item['name']; ?>">
                    <div>
                        <h4><?php echo $item['name']; ?></h4>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>रु. <?php echo number_format($item['price'], 0); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="price-details">
                <div class="d-flex justify-content-between mb-2">
                    <span>Items Total (<?php echo count($items); ?> Items)</span>
                    <span>रु. <?php echo number_format($subtotal, 0); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Delivery Fee</span>
                    <span id="delivery-fee">रु. 0</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total:</strong>
                    <strong id="total-amount">रु. <?php echo number_format($subtotal, 0); ?></strong>
                </div>
            </div>

            <button type="submit" form="checkoutForm" class="proceed-btn">Proceed to Pay</button>
        </div>
    </div>

    <script>
    document.querySelectorAll('.delivery-option').forEach(option => {
        option.addEventListener('click', function() {
            // Update selected style
            document.querySelectorAll('.delivery-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            // Update radio button
            this.querySelector('input[type="radio"]').checked = true;
            
            // Update delivery fee and total
            const deliveryCharge = parseInt(this.dataset.charge);
            const subtotal = <?php echo $subtotal; ?>;
            
            document.getElementById('delivery-fee').textContent = 'रु. ' + deliveryCharge.toLocaleString();
            document.getElementById('total-amount').textContent = 'रु. ' + (subtotal + deliveryCharge).toLocaleString();
        });
    });
    </script>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
