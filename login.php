<?php
require_once 'database/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize user input to prevent SQL injection
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password using password_verify (recommended for modern PHP)
        if (password_verify($password, $user['password'])) {
            // Successful login
            session_start();
            $_SESSION['email'] = $email;
            header("Location: home.php"); // Redirect to dashboard
            exit();
        } else {
            // Incorrect password
            $error = "Invalid email or password";
        }
    } else {
        // User not found
        $error = "Invalid email or password";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form | Login</title>
    <link rel="stylesheet" href="css/login.css" />
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
    <!-- navbar -->
    <header>
      <div class="logo">
        <a href="home.php">
          <img src="images/logo.png" alt="Logo" />
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
          
          <li><a href="contact.php">Contact</a></li>
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

    <div class="parent-container">
      <div class="container">
        <h1>Login</h1>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <div class="input-box">
        <input type="text" name="email" placeholder="email" required />
    </div>
    <div class="input-box">
        <input type="password" name="password" placeholder="password" required />
        <?php 
        if (isset($error)) {
            echo "<p style='color: red;'>$error</p>";
        }
        ?>
        <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
    </div>
    <button type="submit" class="btn">Login</button>
          <div class="register-link">
            <p>Don't have an account? <a href="signup.php">Signup Here!</a></p>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
