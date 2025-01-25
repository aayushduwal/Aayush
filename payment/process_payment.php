<?php
session_start();
require_once('../database/config.php');
require_once('payment_config.php');

// Enhanced error reporting and logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// NEW: Database connection verification
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}
error_log("Database connection successful");

// Basic session and request validation
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../cart/checkout.php');
    exit();
}

// Get and validate form data
$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$phone = $_POST['phone'] ?? '';
$city = $_POST['city'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$delivery_zone = $_POST['delivery_zone'] ?? '';
$detailed_address = $_POST['detailed_address'] ?? '';
$email = $_SESSION['email'] ?? 'example@gmail.com';

error_log("Processing order for user_id: $user_id, email: $email");

// Validate required fields
if (!$payment_method || !$full_name || !$phone || !$city || !$delivery_zone) {
    error_log("Missing required fields in form submission");
    $_SESSION['error'] = "Please fill all required fields";
    header('Location: ../cart/checkout.php');
    exit();
}

// Get cart items and calculate total
try {
    $stmt = $conn->prepare("SELECT c.*, p.name, p.price FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?");

    if (!$stmt) {
        throw new Exception("Cart query preparation failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Cart query execution failed: " . $stmt->error);
    }

    $cart_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($cart_items)) {
        throw new Exception("Your cart is empty");
    }

    // Calculate total amount
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Add delivery charge
    $delivery_charges = [
        'inside_ring' => 85,
        'outside_ring' => 100,
        'outside_valley' => 150
    ];
    $delivery_charge = $delivery_charges[$delivery_zone] ?? 0;
    $total_amount += $delivery_charge;

    error_log("Cart total: $total_amount (including $delivery_charge delivery charge)");

} catch (Exception $e) {
    error_log("Cart processing error: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header('Location: ../cart/checkout.php');
    exit();
}

// Customer creation/update with proper error handling
try {
    // First check if customer exists
    $check_stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
    if (!$check_stmt) {
        throw new Exception("Failed to prepare customer check: " . $conn->error);
    }

    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $existing_customer = $result->fetch_assoc();

    if ($existing_customer) {
        // Update existing customer
        $stmt = $conn->prepare("UPDATE customers SET 
            name = ?,
            phone = ?,
            address = ?,
            city = ?,
            postal_code = ?
            WHERE email = ?");

        if (!$stmt) {
            throw new Exception("Failed to prepare customer update: " . $conn->error);
        }

        $stmt->bind_param("ssssss", 
            $full_name,
            $phone,
            $detailed_address,
            $city,
            $postal_code,
            $email
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update customer: " . $stmt->error);
        }

        $customer_id = $existing_customer['id'];
        error_log("Updated existing customer with ID: $customer_id");
    } else {
        // Create new customer
        $stmt = $conn->prepare("INSERT INTO customers (
            name,
            email,
            phone,
            address,
            city,
            postal_code,
            country
        ) VALUES (?, ?, ?, ?, ?, ?, 'Nepal')");

        if (!$stmt) {
            throw new Exception("Failed to prepare customer creation: " . $conn->error);
        }

        $stmt->bind_param("ssssss",
            $full_name,
            $email,
            $phone,
            $detailed_address,
            $city,
            $postal_code
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create customer: " . $stmt->error);
        }

        $customer_id = $stmt->insert_id;
        error_log("Created new customer with ID: $customer_id");
    }

    if (!$customer_id) {
        throw new Exception("Failed to get valid customer ID");
    }

} catch (Exception $e) {
    error_log("Customer processing error: " . $e->getMessage());
    $_SESSION['error'] = "Failed to process customer information: " . $e->getMessage();
    header('Location: ../cart/checkout.php');
    exit();
}

// Prepare shipping address
$shipping_address = "Name: $full_name\n";
$shipping_address .= "Phone: $phone\n";
$shipping_address .= "Address: $detailed_address\n";
$shipping_address .= "City: $city\n";
$shipping_address .= "Postal Code: $postal_code\n";
$shipping_address .= "Delivery Zone: $delivery_zone";

// Create order with enhanced error handling
try {
    error_log("Creating order for customer_id: $customer_id");

    $stmt = $conn->prepare("INSERT INTO orders (
        user_id,
        customer_id,
        total_amount,
        status,
        shipping_address,
        payment_method,
        delivery_zone,
        order_date
    ) VALUES (
        ?,
        ?,
        ?,
        'pending',
        ?,
        ?,
        ?,
        CURRENT_TIMESTAMP
    )");

    if (!$stmt) {
        throw new Exception("Failed to prepare order creation: " . $conn->error);
    }

    $bind_result = $stmt->bind_param("iidsss",
        $user_id,
        $customer_id,
        $total_amount,
        $shipping_address,
        $payment_method,
        $delivery_zone
    );

    if (!$bind_result) {
        throw new Exception("Failed to bind order parameters: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        throw new Exception("Failed to create order: " . $stmt->error);
    }

    $order_id = $stmt->insert_id;
    error_log("Order created successfully with ID: $order_id");

    // Create order items
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                           VALUES (?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Failed to prepare order items creation");
    }

    foreach ($cart_items as $item) {
        error_log("Adding item to order: Product ID: {$item['product_id']}, Quantity: {$item['quantity']}");

        if (!$stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price'])) {
            throw new Exception("Failed to bind order item parameters");
        }

        if (!$stmt->execute()) {
            throw new Exception("Failed to create order item");
        }
    }

    error_log("All order items created successfully");

} catch (Exception $e) {
    error_log("Order creation failed: " . $e->getMessage());
    if (isset($order_id)) {
        // Rollback order if it was created
        $conn->query("DELETE FROM orders WHERE id = " . $order_id);
        error_log("Rolled back order #$order_id");
    }
    $_SESSION['error'] = "Failed to create order: " . $e->getMessage();
    header('Location: ../cart/checkout.php');
    exit();
}

// Process payment based on method
switch ($payment_method) {
    case 'cod':
        error_log("Processing COD payment for order #$order_id");

        // Clear cart
        try {
            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            error_log("Cart cleared for user #$user_id");
        } catch (Exception $e) {
            error_log("Failed to clear cart: " . $e->getMessage());
        }

        $_SESSION['success'] = "Order placed successfully! We will contact you soon.";
        header('Location: ../userdashboard/order_history.php');
        break;

    case 'esewa':
        error_log("Initiating eSewa payment for order #$order_id");

        $esewa_url = getEsewaUrl();
        $success_url = "http://" . $_SERVER['HTTP_HOST'] . "/aayush/payment/verify_payment.php?payment_method=esewa&oid=" . $order_id;
        $failure_url = "http://" . $_SERVER['HTTP_HOST'] . "/aayush/payment/payment_failure.php?oid=" . $order_id;

        $amount = number_format($total_amount, 2, '.', '');
        $unique_pid = 'ESEWA_' . $order_id . '_' . time();

        error_log("eSewa Payment Details - Amount: $amount, PID: $unique_pid");
        ?>
        <form action="<?php echo $esewa_url; ?>" method="POST" id="esewaForm">
            <input value="<?php echo $amount; ?>" name="tAmt" type="hidden">
            <input value="<?php echo $amount; ?>" name="amt" type="hidden">
            <input value="0" name="txAmt" type="hidden">
            <input value="0" name="psc" type="hidden">
            <input value="0" name="pdc" type="hidden">
            <input value="<?php echo ESEWA_MERCHANT_ID; ?>" name="scd" type="hidden">
            <input value="<?php echo $unique_pid; ?>" name="pid" type="hidden">
            <input value="<?php echo $success_url; ?>" type="hidden" name="su">
            <input value="<?php echo $failure_url; ?>" type="hidden" name="fu">
            <input value="Pay with eSewa" type="submit" style="display:none;">
        </form>
        <script>
           window.onload = function() {
            console.log('Submitting to eSewa...');
            document.getElementById('esewaForm').submit();
        }
        <?php
        break;

    case 'khalti':
        error_log("Initiating Khalti payment for order #$order_id");

        $khalti_url = getKhaltiUrl();
        $success_url = "http://" . $_SERVER['HTTP_HOST'] . "/aayush/payment/verify_payment.php?payment_method=khalti&oid=" . $order_id;
        $failure_url = "http://" . $_SERVER['HTTP_HOST'] . "/aayush/payment/payment_failure.php?oid=" . $order_id;

        $data = [
            "return_url" => $success_url,
            "website_url" => "http://" . $_SERVER['HTTP_HOST'] . "/aayush",
            "amount" => intval($total_amount * 100),
            "purchase_order_id" => strval($order_id),
            "purchase_order_name" => "Order #$order_id",
            "customer_info" => [
                "name" => $full_name,
                "email" => $email,
                "phone" => $phone
            ]
        ];

        error_log("Khalti Request Data: " . json_encode($data));

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $khalti_url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Authorization: Key ' . KHALTI_SECRET_KEY,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        error_log("Khalti Response - Status: $status_code, Response: $response");

        if (curl_errno($ch)) {
            error_log("Khalti Curl Error: " . curl_error($ch));
        }

        curl_close($ch);
        $response_data = json_decode($response, true);

        if ($status_code === 200 && isset($response_data['payment_url'])) {
            header('Location: ' . $response_data['payment_url']);
            exit();
        } else {
            error_log("Khalti Error Response: " . json_encode($response_data));
            $_SESSION['error'] = "Failed to initialize Khalti payment: " . 
                (isset($response_data['detail']) ? $response_data['detail'] : 'Unknown error');
            header('Location: ../cart/checkout.php');
            exit();
        }
        break;

    default:
        error_log("Invalid payment method: $payment_method");
        $_SESSION['error'] = "Invalid payment method";
        header('Location: ../cart/checkout.php');
        exit();
}
?>
