<!-- reset-password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/reset-pwd.css" />
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        <?php
        if (isset($_GET['token'])) {
            require_once 'config.php';
            
            $token = $_GET['token'];
            $current_time = date('Y-m-d H:i:s');
            
            // Verify token and check if it's not expired
            $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expiry > ?");
            $stmt->bind_param("ss", $token, $current_time);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                if (isset($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $user_id = $result->fetch_assoc()['id'];
                    
                    // Update password and clear reset token
                    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE id = ?");
                    $stmt->bind_param("si", $password, $user_id);
                    $stmt->execute();
                    
                    echo "<div class='message success'>Your password has been successfully reset. You can now <a href='login.php'>login</a> with your new password.</div>";
                } else {
                    ?>
                    <form action="" method="POST">
                        <div class="input-box">
                            <input type="password" name="password" placeholder="Enter new password" required>
                        </div>
                        <div class="input-box">
                            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                        <button type="submit" class="btn">Set New Password</button>
                    </form>
                    <?php
                }
            } else {
                echo "<div class='message error'>Invalid or expired reset link.</div>";
            }
            
            $stmt->close();
            $conn->close();
        } else {
            echo "<div class='message error'>Invalid reset link.</div>";
        }
        ?>
    </div>
</body>
</html>