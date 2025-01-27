<?php
session_start();
require 'database/config.php';
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    // Server-side validation
    if (empty($email)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        // Check if email exists in database
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result === false) {
            die("Query failed: " . $stmt->error);
        }
        
        if ($result->num_rows === 1) {
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Debugging output
            echo "Reset Token: " . $reset_token; // Check the generated token

            // Store reset token
            $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
            if ($update_stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $update_stmt->bind_param("sss", $reset_token, $token_expiry, $email);
            
            if ($update_stmt->execute()) {
                // Send reset link via email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                    $mail->SMTPAuth = true; // Enable SMTP authentication
                    $mail->Username = 'your-email@gmail.com'; // Your email
                    $mail->Password = 'your-email-password'; // Your email password
                    $mail->SMTPSecure = 'tls'; // Enable TLS encryption
                    $mail->Port = 587; // TCP port to connect to

                    //Recipients
                    $mail->setFrom('no-reply@yourwebsite.com', 'Your Website');
                    $mail->addAddress($email); // Add a recipient

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Instructions';
                    $mail->Body    = 'Click the link below to reset your password:<br><br>' .
                                     '<a href="https://yourwebsite.com/reset-pwd.php?token=' . $reset_token . '">Reset Password</a>';

                    $mail->send();
                    $error = "Password reset link sent to your email";
                } catch (Exception $e) {
                    $error = "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error = "Error processing your request";
            }
            $update_stmt->close();
        } else {
            $error = "Email not found";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - StaySpot</title>

  <!-- Lato Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@300;400;700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&family=Permanent+Marker&display=swap" rel="stylesheet">

  <!-- Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

  <!-- Link to CSS files -->
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="css/reset-pwd.css"> <!-- Added CSS file -->
</head>

<body>
  <div class="container">
    <a href="/elegance">
      <h1 class="logo">Elegance</h1>
    </a>

    <?php if ($error): ?>
    <div class="server-error">
      <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <h1>Reset Password</h1>

    <form id="resetPasswordForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <div class="input-group">
        <label for="email">Email address</label>
        <input type="email" name="email" id="email" placeholder="Email address" required>
        <div class="error-message" id="emailError"></div>
      </div>

      <button type="submit">Reset Password</button>
    </form>

    <p class="signup-link">
      Remember your password? <a href="login.php">Log in</a>
    </p>
  </div>

  <script>
  document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    let isValid = true;
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');

    // Reset error messages
    emailError.style.display = 'none';

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email.value)) {
      emailError.textContent = 'Please enter a valid email address';
      emailError.style.display = 'block';
      isValid = false;
    }

    if (!isValid) {
      e.preventDefault();
    }
  });
  </script>
</body>

</html>