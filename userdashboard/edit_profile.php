<?php
session_start();
include('../database/config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user details
$stmt = $conn->prepare("
    SELECT u.*, cd.phone, cd.address, cd.city, cd.country, cd.postal_code 
    FROM users u 
    LEFT JOIN customer_details cd ON u.id = cd.user_id 
    WHERE u.id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $postal_code = $_POST['postal_code'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update users table
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $_SESSION['user_id']);
        $stmt->execute();

        // Check if customer details exist
        $stmt = $conn->prepare("SELECT id FROM customer_details WHERE user_id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update existing customer details
            $stmt = $conn->prepare("
                UPDATE customer_details 
                SET phone = ?, address = ?, city = ?, country = ?, postal_code = ?
                WHERE user_id = ?
            ");
        } else {
            // Insert new customer details
            $stmt = $conn->prepare("
                INSERT INTO customer_details (user_id, phone, address, city, country, postal_code)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
        }
        $stmt->bind_param("sssssi", $phone, $address, $city, $country, $postal_code, $_SESSION['user_id']);
        $stmt->execute();

        $conn->commit();
        $success = "Profile updated successfully!";
        
        // Refresh user data
        header("Location: edit_profile.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error updating profile: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - ELEGANCE</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../userdashboard/css/edit_profile.css">
   <!-- font of inter -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>
     <header>
        <div class="logo">
            <a href="index.php">
                <img src="../images/logo.png" alt="Logo" />
            </a>
        </div>
        <nav class="nav-container">
            <ul class="navmenu">
                <li><a href="/aayush/index.php">Home</a></li>
                <li>
                    <a href="shop.php">Shop</a>
                    <ul class="dropdown-menu">
                        <li><a href="mens_collection.php">Men's Collection</a></li>
                        <li><a href="womens_collection.php">Women's Collection</a></li>
                        <li><a href="kids_collection.php">Kid's Collection</a></li>
                    </ul>
                </li>
                <li><a href="About.php">About</a></li>
                <li><a href="/aayush/contact.php">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="user_dashboard.php"><?php echo htmlspecialchars($user['username']); ?>'s Account</a></li>
                    <li><a href="/aayush/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="nav-icon">
            <?php if (isset($_SESSION['user_id'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($user['username']); ?></p>
            <?php endif; ?>
            <a href="cart.php"><i class='bx bx-shopping-bag'></i></a>
            <div id="menu-icon"><i class='bx bx-menu'></i></div>
        </div>
    </header>


    <div class="edit-profile-container">
        <div class="edit-profile-form">
            <h2>Edit Profile</h2>
            
            <?php if (isset($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="country">Country</label>
                        <input type="text" id="country" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>">
                </div>
                
                <button type="submit" class="submit-btn">Update Profile</button>
            </form>
        </div>
    </div>
</body>
</html>