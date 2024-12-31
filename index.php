<?php
session_start();
include('database/config.php');

// Function to get user details
function getUserDetails($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
$userDetails = null;

if (isset($_SESSION['user_id'])) {
    $userDetails = getUserDetails($conn, $_SESSION['user_id']);
}
?>


 <!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Ecommerce Website - Elegance</title>
   <!-- CSS-link -->
   <link rel="stylesheet" href="css/style.css" />
   <link rel="stylesheet" href="css/index.css" />
   <link rel="stylesheet" href="css/collection.css" />
   <link rel="preconnect" href="https://fonts.googleapis.com" />
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
   <!-- font of inter -->
   <link
     href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
     rel="stylesheet" />
   <!-- icon links -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
     integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
     crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css"
     crossorigin="anonymous" referrerpolicy="no-referrer" />
 </head>

 <body>
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
         <li><a href="/aayush/contact.php">Contact</a></li>

        
   <?php if ($isLoggedIn): ?>
    <?php if (isset($_SESSION['is_admin'])): ?>
        <li><a href="dashboard/admin_dashboard.php" class="dashboard-btn">Dashboard</a></li>
    <?php else: ?>
        <li><a href="userdashboard/user_dashboard.php"><?php echo htmlspecialchars($userDetails['username']); ?>'s Account</a></li>
    <?php endif; ?>
    <li><a href="logout.php">Logout</a></li>
<?php else: ?>
    <li><a href="login.php">Login</a></li>
<?php endif; ?>
       </ul>
     </nav>

    <div class="nav-icon">
    <?php if ($isLoggedIn): ?>
        <p>
            Logged in as <u><strong>
            <?php 
                if (isset($_SESSION['is_admin'])) {
                    echo htmlspecialchars($_SESSION['admin_username']);
                } elseif ($userDetails && isset($userDetails['username'])) {
                    echo htmlspecialchars($userDetails['username']);
                }
            ?></strong></u>
        </p>
    <?php else: ?>
        <a href="login.php"><i class="bx bx-user"></i></a>
    <?php endif; ?>
    <a href="/aayush/cart/cart.php"><i class="bx bx-cart"></i>
      <span id="cart-badge" class="cart-badge">0</span>
    </a>
      <div class="bx bx-menu" id="menu-icon"></div>
    </div>
   </header>

   <!-- Hero section -->
   <section class="main-home">
     <div class="main-home-overlay"></div>
     <div class="main-text">
       <h2>Collection</h2>
       <h1>Summer Collection</h1>
       <h3>Up to 50% Off</h3>
       <p>Discover the latest trends and styles.</p>
       <a href="#mens-collection" class="main-btn">Shop Now <i class="fa fa-arrow-right"></i></a>
     </div>
   </section>

   <!-- Men's collection -->
   <section id="mens-collection" class="collection">
     <div class="container">
       <h1 class="">Men's Collection</h1>

       <div class="collection-wrapper">
         <div class="collection-wrapper-child">
           <a href="home_collections_products/mens_jacket1_product_detail.php">
             <img src="images/index-page_images/mens_collection/jacket.png" alt="" />
             <h2>jacket</h2>
             <div class="rating-wrapper">
               <i class="fa-regular fa-star"></i>
               4.5
             </div>
             <p>रु.1500</p>
           </a>
         </div>
         <div class="collection-wrapper-child">
           <a href="home_collections_products/mens_boxpant1_product_detail.php">
             <img src="images/index-page_images/mens_collection/pant.png" alt="" />
             <h2>Cargo Pant</h2>
             <div class="rating-wrapper">
               <i class="fa-regular fa-star"></i>
               4.5
             </div>
             <p>रु. 1200</p>
           </a>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/mens_collection/vans.png" alt="" />
           <h2>Vans</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 2000</p>
         </div>
         <div class="collection-wrapper-child">
          <a href="home_collections_products/mens_jacket2_product_detail.php">
           <img src="images/index-page_images/mens_collection/jacket1.png" alt="" />
           <h2>Jacket</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 99.99</p>
         </div>
       </div>
     </div>
   </section>
   <!-- Womens's collection -->
   <section class="collection">
     <div class="container">
       <h1 class="">Womens's Collection</h1>

       <div class="collection-wrapper">
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/womens_collection/jacket.png" alt="" />
           <h2>Jacket</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 800</p>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/womens_collection/shoes.png" alt="" />
           <h2>Shoes</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 50</p>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/womens_collection/pant.png" alt="" />
           <h2>Pant</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>

             4.5
           </div>
           <p>रु. 200</p>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/womens_collection/top.png" alt="" />
           <h2>Top</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 150</p>
         </div>
       </div>
     </div>
   </section>
   <!-- Child's collection -->
   <section class="collection">
     <div class="container">
       <h1 class="">Child's Collection</h1>

       <div class="collection-wrapper">
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/kids_collection/tshirt.png" alt="" />
           <h2>Tshirt</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 600</p>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/kids_collection/jacket.png" />
           <h2>Jacket</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 1800</p>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/kids_collection/shoes.png" />
           <h2>Shoes</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 2100</p>
         </div>
         <div class="collection-wrapper-child">
           <img src="images/index-page_images/kids_collection/jacket1.png" alt="" />
           <h2>Jacket</h2>
           <div class="rating-wrapper">
             <i class="fa-regular fa-star"></i>
             4.5
           </div>
           <p>रु. 1500</p>
         </div>
       </div>
     </div>
   </section>

   <!-- footer starts -->
   <footer class="infos">
     <section class="contact">
       <div class="contact-info">
         <div class="first-info info">
            <a href="index.php">
         <img src="images/logo.png" width="80" class="footer-image" height="80" alt="Elegance Logo" />
            </a>
           <ul>
             <li>
               <p>Kathmandu, Nepal</p>
             </li>
             <li>
               <p>0160-5462-8214</p>
             </li>
             <li>
               <p>elegance2024@gmail.com</p>
             </li>
           </ul>
         </div>

         <div class="second-info info">
           <h4>Support</h4>
           <ul>
             <li>
               <a href="#">
                 <p>Contact us</p>
               </a>
             </li>
             <li>
               <a href="#">
                 <p>About page</p>
               </a>
             </li>
             <li>
               <a href="#">
                 <p>Shopping & Returns</p>
               </a>
             </li>
             <li>
               <a href="#">
                 <p>Privacy</p>
               </a>
             </li>
           </ul>
         </div>

         <div class="third-info info">
           <h4>Shop</h4>
           <ul>
             <li>
               <a href="#">
                 <p>Men's Shopping</p>
               </a>
             </li>
             <li>
               <a href="#">
                 <p>Women's Shopping</p>
               </a>
             </li>
             <li>
               <a href="#">
                 <p>Kid's Shopping</p>
               </a>
             </li>
             <li>
               <a href="#">
                 <p>Discount</p>
               </a>
             </li>
           </ul>
         </div>
       </div>
     </section>
     <div class="copyright">
       <hr style="width: 100%; margin: 20px; border-top: 1px solid #000" />
       <p>
         &copy; 2024 ELEGANCE. All rights reserved.
         <a href="#">Privacy Policy</a>
       </p>
     </div>
   </footer>
   <!-- footer ends -->

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="cart/addToCart.js"></script>
   <script>
   const menuIcon = document.getElementById("menu-icon");
   const navMenu = document.querySelector(".navmenu");

   menuIcon.addEventListener("click", () => {
     navMenu.classList.toggle("active");
   });

   $(document).ready(function() {
       updateCartBadge();
   });
   </script>
 </body>

 </html>