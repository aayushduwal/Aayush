<?php
require_once 'database/config.php'; // Include the database connection

session_start();

// Add this missing function
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$error_message = ""; 
$success_message = ""; 
$errors = []; // To track all validation errors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
     // Server-side validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success = "Registration successful! You can now login.";
                // Optional: Automatically log in the user
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['user_name'] = $name;
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
  }
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form | Signup</title>
    <link rel="stylesheet" href="css/signup.css" />
    <link rel="stylesheet" href="css/index.css" />
    <!-- CSS-link -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <!-- font of inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
      rel="stylesheet"
    />
    <!-- closing the font of inter -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
  </head>
  <body>
    <!-- start of navbar -->
    <header>
      <div class="logo">
        <img src="images/logo.png" alt="Logo" />
      </div>
      <nav class="nav-container">
        <ul class="navmenu">
          <li><a href="index.html">Home</a></li>
          <li>
            <a href="shop.html">Shop</a>
            <ul class="dropdown-menu">
              <li><a href="mens_collection.html">Men's Collection</a></li>
              <li><a href="womens_collection.html">Women's Collection</a></li>
              <li><a href="kids_collection.html">Kid's Collection</a></li>
            </ul>
          </li>
          <li><a href="aboutus.html">AboutUs</a></li>
          <li><a href="login.html">Login</a></li>
          <li><a href="contact.html">Contact</a></li>
        </ul>
      </nav>
      <div class="nav-icon">
        <a href="#"><i class="bx bx-search"></i></a>
        <a href="#"><i class="bx bx-user"></i></a>
        <a href="#"><i class="bx bx-cart"></i></a>
      </div>
      <div id="menu-icon">
        <i class="fa fa-bars"></i>
      </div>
    </header>
    <!-- end of navbar -->
    <div class="image-background"></div>
    <div class="parent-container">
      <div class="container">
        
        <?php if ($error): ?>
          <div class="message error">
            <?php echo htmlspecialchars($error); ?>
          </div>
          <?php endif; ?>
          
          <?php if ($success): ?>
            <div class="message success">
              <?php echo htmlspecialchars($success); ?>
            </div>
            <?php endif; ?>
            
            <h1>Signup</h1>

        <form action="signup.php" method="POST">
          <div class="input-box">
            <input type="text" name="username" placeholder="username" required />
          </div>
          <div class="input-box">
            <input type="email" name="email" placeholder="email" required />
          </div>
          <div class="input-box">
            <input type="password" name="password" placeholder="password" required />
          </div>
          <div class="password-requirements">
            <h5>Password requirements:</h5>
            <ul>
              <li id="length">At least 8 characters</li>
              <li id="letter">At least one letter</li>
              <li id="number">At least one number</li>
              <li id="special">At least one special character</li>
            </ul>
          </div>
          <div class="input-box">
            <input
              type="password"
              name="confirm_password"
              placeholder="confirm your password"
              required
            />
          </div>
          <button type="submit" class="btn">Signup</button>
          <div class="signup-link">
            <p>Don't have an account? <a href="login.html">Login Here!</a></p>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>