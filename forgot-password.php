<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/forgot-password.css" />
</head>
<body>
    <div class="container">
        <h1>Forgot Password</h1>
        <?php
        if (isset($_POST['email'])) {
            require_once 'config.php'; // Database connection file
            
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            
            // Check if email exists in database
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
                $stmt->bind_param("sss", $token, $expiry, $email);
                $stmt->execute();
                
                // Send reset email
                $reset_link = "http://yourwebsite.com/reset-password.php?token=" . $token;
                $to = $email;
                $subject = "Password Reset Request";
                $message = "Click the following link to reset your password: " . $reset_link;
                $headers = "From: noreply@yourwebsite.com";
                
                mail($to, $subject, $message, $headers);
                
                echo "<div class='message success'>Password reset instructions have been sent to your email.</div>";
            } else {
                echo "<div class='message error'>Email address not found.</div>";
            }
            
            $stmt->close();
            $conn->close();
        }
        ?>
        <form action="" method="POST">
            <div class="input-box">
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <button type="submit" class="btn">Reset Password</button>
        </form>
        <a href="login.php" class="back-link">Back to Login</a>
    </div>
</body>
</html>