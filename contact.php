<!DOCTYPE html>
<html>
  <head>
    <title>Contact us</title>
    <link rel="stylesheet" type="text/css" href="css/contact.css" />
    <link rel="stylesheet" href="css/index.css">
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
    <div class="container">
      <div class="contact-box">
        <div class="left"></div>
        <div class="right">
          <h2>Contact Us</h2>
          <input type="text" class="field" placeholder="Your Name" />
          <input type="text" class="field" placeholder="Your Email" />
          <input type="text" class="field" placeholder="Phone" />
          <textarea placeholder="Message" class="field"></textarea>
          <button class="btn">Send</button>
          <a href="index.html" class="btn1">Back to Home</a>
        </div>
      </div>
    </div>
  </body>
</html>