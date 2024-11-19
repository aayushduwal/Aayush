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

    <div class="parent-container">
      <div class="container">
        <h1>Login</h1>
        <form action="#">
          <div class="input-box">
            <input type="text" placeholder="email" required />
          </div>
          <div class="input-box">
            <input type="password" placeholder="password" required />
          </div>
          <button type="submit" class="btn">Login</button>
          <div class="register-link">
            <p>Don't have an account? <a href="signup.html">Signup Here!</a></p>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>
