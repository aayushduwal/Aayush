<?php
require_once 'database/config.php';
session_start();

// Initialize the user role variable
$user_role = '';

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize validation and errors
$validation = true;
$errors = [
    'name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
    'role' => '', 
    'general' => ''
];

// Initialize isSubmitted flag
$isSubmitted = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isSubmitted = true;  // Set flag when form is submitted
    
    // Get and sanitize data from form
    $username = test_input($_POST['username'] ?? '');
    $email = test_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_role = test_input($_POST['user_role'] ?? '');

        // Username validation
if (empty($username)) {
        $errors['name'] = "Username is required.";
        $validation = false;
    } elseif (!preg_match("/^[a-zA-Z0-9_-]{3,50}$/", $username)) {
        $errors['name'] = "Only letters and whitespaces allowed.";
        $validation = false;
    } elseif (strlen($username) > 50) { // Add maximum length check
        $errors['name'] = "Username cannot exceed 50 characters.";
        $validation = false;
    }

    // Email validation with additional checks
    if (empty($email)) {
        $errors['email'] = "Email is required.";
        $validation = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
        $validation = false;
    } elseif (strlen($email) > 255) { // Add maximum length check
        $errors['email'] = "Email cannot exceed 255 characters.";
        $validation = false;
    }



    // Password validation - only run if form is submitted
    if (empty($password)) {
        $errors['password'] = "Password is required.";
        $validation = false;
    }elseif(strlen($password)<8){
 $errors['password'] = "Must be 8 char";
        $validation = false;
    } elseif (!preg_match('/[a-zA-Z]/', $password)) {
        $errors['password'] = "Must include one letter";
        $validation = false;
    }elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Must include one number";
        $validation = false;
    }elseif (!preg_match('/[@$!%*?&]/', $password)) {
        $errors['password'] = "Must include one special char";
        $validation = false;
    }

      // Confirm password validation
    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password.";
        $validation = false;
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
        $validation = false;
    }

    // Role validation
   if (!isset($_POST['user_role']) || $_POST['user_role'] === '') {
        $errors['role'] = "Role selection is required.";
        $validation = false;
    } elseif (!in_array($user_role, ['guest', 'host'])) {
        $errors['role'] = "Invalid role selected.";
        $validation = false;
    }

    // Database interaction if validation passes
    if ($validation) {
        try {
            // Check for existing user
            $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $errors['general'] = "Email or username already exists.";
            } else {
                // Hash password and insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password, user_role, created_at) VALUES (?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssss", $username, $email, $hashed_password, $user_role);
                
                if ($stmt->execute()) {
                    // Success - set session and redirect
                    $_SESSION['user_id'] = mysqli_insert_id($conn);
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    $_SESSION['user_role'] = $user_role;

                     // Redirect based on role
                    if ($user_role === 'host') {
                        header("Location: admin_dashboard.php");
                    } elseif ($user_role === 'guest'){
                        header("Location: index.php");
                    }
                    exit();
                } else {
                    $errors['general'] = "Error: " . $stmt->error;
                }
            }
        } catch (Exception $e) {
            $errors['general'] = "An error occurred. Please try again later.";
            // Log the error securely
            error_log("Signup error: " . $e->getMessage());
        }
    }
}

$conn->close();
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
        <a href="index.php">
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
          <li><a href="login.php">Login</a></li>
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
    <div class="image-background"></div>
     <div class="parent-container">
        <div class="container">
            <h1>Signup</h1>

            <?php if (!empty($errors['general'])): ?>
                <p style="color: red"><?php echo $errors['general']; ?></p>
            <?php endif; ?>

            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="signup-form">
                <div class="input-box">
                    <input type="text" name="username" 
                           placeholder="Username" 
                           value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                           required />
                    <?php if (!empty($errors['name'])): ?>
                        <p style="color: red"><?php echo $errors['name']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="input-box">
                    <input type="email" name="email" 
                           placeholder="Email" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
                           required />
                    <?php if (!empty($errors['email'])): ?>
                        <p style="color: red"><?php echo $errors['email']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="input-box">
                    <input type="password" name="password" 
                           placeholder="Password" 
                           required />
                    <?php if ($isSubmitted && !empty($errors['password'])): ?>
                        <p style="color: red"><?php echo $errors['password']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Password requirements - show always -->
                <div class="password-requirements">
                    <h5>Password requirements:</h5>
                    <ul>
                        <li>At least 8 characters</li>
                        <li>At least one letter</li>
                        <li>At least one number</li>
                        <li>At least one special character</li>
                    </ul>
                </div>

                <div class="input-box">
                    <input type="password" name="confirm_password" 
                           placeholder="Confirm your password" 
                           required />
                    <?php if ($isSubmitted && !empty($errors['confirm_password'])): ?>
                        <p style="color: red"><?php echo $errors['confirm_password']; ?></p>
                    <?php endif; ?>
                </div>

          <div class="input-box">
    <select name="user_role" class="role-select" required>
        <option value="">Select your role</option>
        <option value="guest" <?php echo ($user_role === 'guest') ? 'selected' : ''; ?>>Guest</option>
        <option value="host" <?php echo ($user_role === 'host') ? 'selected' : ''; ?>>Host</option>
    </select>
    <?php if (!empty($errors['role'])): ?>
        <p style="color: red"><?php echo $errors['role']; ?></p>
    <?php endif; ?>
</div>
                <button type="submit" class="btn">Signup</button>
            </form>
        </div>
    </div>
  </body>
</html>
