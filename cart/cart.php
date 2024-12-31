<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <!-- Custom Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- font of inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
    rel="stylesheet" />
  <style>
  

  /* closing the font of inter */

  /* -------------------- Navbar Start -------------------- */

  header { 
    width: 100%; 
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 5%;
    background-color: white;
    color: black;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .logo {
    flex: 1;
  }

  .logo img {
    height: 40px;
    width: auto;
  }

  .nav-container {
    flex: 1;
    display: flex;
    justify-content: center;
  }

  .navmenu {
    display: flex;
    align-items: center;
    list-style: none;
  }

  .navmenu li {
    margin: 0 15px;
  }

  .navmenu a {
    text-decoration: none;
    color: black;
    font-size: 22px;
    font-weight: 450;
    transition: color 0.3s ease, transform 0.3s ease;
  }

  .navmenu a:hover {
    color: #ff5733;
    transform: translateY(-2px);
  }

  /* Start of CSS for dropdown in shop */
  .navmenu>li {
    position: relative;
  }

  .dropdown {
    display: none;
    position: absolute;
    left: 0;
    top: 100%;
    width: max-content;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1;
  }

  .navmenu li:hover .dropdown {
    display: block;
  }

  .dropdown li {
    display: block;
  }

  .dropdown a {
    padding: 10px 20px;
    color: black;
    background-color: white;
    white-space: nowrap;
  }

  /* End of CSS for dropdown in shop */

  .nav-icon {
    flex: 1;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;
  }

  .nav-icon a {
    display: flex;
    align-items: center;
    text-decoration: none;
  }

  .nav-icon i {
    color: black;
    font-size: 24px;
    transition: color 0.3s ease, transform 0.3s ease;
  }

  .nav-icon i:hover {
    color: #ff5733;
    transform: scale(1.1);
  }

  #menu-icon {
    font-size: 30px;
    color: black;
    cursor: pointer;
  }

    .container {
      margin-top: 60px;
    }

    
  .cart-badge {
    position: relative;
    top: -10px;
    right: 5px;
    background-color: #ff5733;
    color: white;
    font-size: 12px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-weight: bold;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    visibility: hidden;
  }
  </style>
</head>

<body>
<header>
    <div class="logo">
      <a href="/aayush/index.php">
        <img src="../images/logo.png" alt="elegance" />
      </a>
    </div>
    <div class="nav-container">
      <ul class="navmenu">
        <li><a href="/aayush/index.php">Home</a></li>
        <li>
          <a href="shop.php">Shop</a>
          <ul class="dropdown">
            <li><a href="mens_collection.php">Men's Collection</a></li>
            <li><a href="womens_collection.php">Women's Collection</a></li>
            <li><a href="kids_collection.php">Kids' Collection</a></li>
          </ul>
        </li>
        <li><a href="/aayush/abt.php">About</a></li>
        <li><a href="/aayush/login.php">Login</a></li>
        <li><a href="/aayush/contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="nav-icon">
      <a href="#"><i class="bx bx-search"></i></a>
      <a href="#"><i class="bx bx-user"></i></a>
      <a href="/cart.php"><i class="bx bx-cart"></i>
      <span id="cart-badge" class="cart-badge">0</span>
    </a>
      <div class="bx bx-menu" id="menu-icon"></div>
    </div>
  </header>
  <!-- Cart Container -->
  <div class="container">
    <h1 class="text-center mb-4">Your Cart</h1>
    <div id="cart-container" class="row"></div>
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Cart Script -->
  <script src="addToCart.js" ></script>
  <script>
    let cart = [];

    function displayCart() {
      
      $.post('cart_handler.php', { action: 'fetch' }, function (response) {
        const cart = JSON.parse(response);
        const cartContainer = $('#cart-container');
        // const cartCount = $('#cart-count');
        // updateCartBadge();

        // cartCount.text(cart.length);
        if (cart.length === 0) {
          cartContainer.html(`<div class="col-12 text-center"><p class="text-muted">Your cart is empty.</p></div>`);
          return;
        }
       
        const cartItemsHtml = cart.map(item => `
          <div class="col-md-6">
            <div class="cart-item border p-3 mb-3">
              <h5>${item.product_name}</h5>
              <p>Price: <strong>$${parseFloat(item.price).toFixed(2)}</strong></p>
              <p>Quantity: <strong>${item.quantity}</strong></p>
              <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">Remove</button>
            </div>
          </div>
        `).join('');
        cartContainer.html(cartItemsHtml);
      });
    }

    function removeFromCart(id) {
      $.post('cart_handler.php', { action: 'remove', id: id }, function (response) {
        displayCart();
        updateCartBadge();
        alert(response);
      });
    }

    $(document).ready(displayCart);

    $.post('cart_handler.php', { action: 'count' }, function(response) {
      const totalItems = parseInt(response);
      const cartBadge = $("#cart-badge");
      if (totalItems > 0) {
          cartBadge.css("visibility", "visible").text(totalItems);
      } else {
          cartBadge.css("visibility", "hidden");
      }
    });
  </script>
</body>

</html>
