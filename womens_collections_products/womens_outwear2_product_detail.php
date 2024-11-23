<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form | Login</title>
    <link rel="stylesheet" href="../css/index.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css"
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <!-- font of inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
      rel="stylesheet"
    />
    <!-- closing the font of inter -->
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }
      /* font of inter */
      body {
        margin: 0;
        padding-top: 400px;
        font-family: "Inter", sans-serif;
        font-weight: 400;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }
      /* closing the font of inter */

      /* -------------------- Navbar Start -------------------- */
      header {
        position: fixed;
        width: 100%;
        top: 0;
        left: 0;
        z-index: 1000;
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
      .navmenu > li {
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
      /* -------------------- Navbar End -------------------- */

      /* Start of product detail */
      .product-container {
        margin-top: 450px; /* Adjust this value to match the height of your navbar */
      }
      .small-container {
        margin-top: 200px;
        max-width: 1200px;
        padding: 0 20px;
        margin-left: auto;
        margin-right: auto;
      }

      .row {
        display: flex;
        flex-wrap: wrap;
      }

      .col-2 {
        flex: 50%;
        padding: 20px;
      }

      .single-product h4 {
        margin: 20px 0;
        font-size: 22px;
        font-weight: bold;
      }

      .single-product select {
        display: block;
        padding: 10px;
        margin-top: 20px;
        margin-bottom: 20px;
        border: 1px solid #ff523b;
      }

      .single-product .fa {
        color: #ff523b;
        margin-left: 10px;
      }

      .number-input {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
      }

      .number-input input {
        text-align: center;
        width: 50px;
        height: 40px;
        font-size: 20px;
        border: 1px solid #ff523b;
        border-left: none;
        border-right: none;
        /* appearance: textfield; */
      }

      .number-input button {
        background-color: white;
        color: #ff523b;
        width: 40px;
        height: 40px;
        font-size: 20px;
        border: 1px solid #ff523b;
        cursor: pointer;
      }

      .number-input button:hover {
        background-color: #dadada;
      }

      input:focus {
        outline: none;
      }

      .btn-container {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
      }

      .btn {
        background-color: #ff523b;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
        text-align: center;
        flex: 1;
      }

      .btn.buy-now {
        background-color: #00bfff;
      }

      /* for multiple small images */
      .small-img-row {
        display: flex;
        justify-content: start;
      }
      .small-img-col {
        flex-basis: 24%;
        cursor: pointer;
        margin-right: 10px;
      }
      /* end of multiple small images */

      @media (max-width: 768px) {
        .col-2 {
          flex: 100%;
          margin-bottom: 20px;
        }
      }

      /* Related Products Section */
      .related-products {
        margin-top: 50px;
        margin-bottom: 50px;
      }

      .related-products h3 {
        margin-bottom: 20px;
      }

      .related-items {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 20px;
      }

      .related-item {
        text-align: center;
        flex: 1;
        margin-right: 20px;
        max-width: 150px;
      }

      .related-item img {
        max-width: 100%;
        border: 1px solid #ccc;
        margin-bottom: 10px;
        cursor: pointer;
        transition: ease-in-out;
      }

      .related-item .item-price {
        color: #f00;
        margin-top: 5px;
      }
      /* End of Related Products Section */

      /* css of footer */
.infos {
  background-color: var(--dark-background);
  color: #fff;
}
.contact {
  padding-top: 100px;
}

.contact-info {
  display: flex;
  gap: 3rem;
  color: #fff;
  max-width: 1200px;
  margin: 0 auto;
}

.info {
  flex: 1;
}

.first-info {
  flex: 2;
}

.contact a {
  text-decoration: none;
}

.contact-info h4 {
  color: #fff;
  font-size: 20px;
  font-weight: 600;
  text-transform: uppercase;
  margin-bottom: 10px;
}

.contact-info p {
  color: var(--dark-background-foreground);
  font-size: 14px;
  font-weight: 400;
  line-height: 1.5;
  margin-bottom: 10px;
  cursor: pointer;
}

.copyright {
  max-width: 1200px;
  margin: 0 auto;
  color: var(--dark-background-foreground);
}

.copyright a {
  text-decoration: underline;
  color: var(--dark-background-foreground);
}
.copyright a:hover {
  text-decoration: underline;
}
footer {
  width: 100%;
  background-color: var(--dark-background);
  color: #fff;
  padding: 20px;
  text-align: center;
}

.info h4 {
  margin-bottom: 14px;
}

.info {
  text-align: left;
}
.info ul {
  list-style-type: none;
}
.footer-image {
  margin-bottom: 14px;
}
/* end of css of footer */

    </style>
  </head>
  <body>
    <!-- navbar -->
    <header>
      <div class="logo">
        <img src="../images/logo.png" alt="elegance" />
      </div>
      <div class="nav-container">
        <ul class="navmenu">
          <li><a href="index.php">Home</a></li>
          <li>
            <a href="shop.php">Shop</a>
            <ul class="dropdown">
              <li><a href="mens_collection.php">Men's Collection</a></li>
              <li><a href="womens_collection.php">Women's Collection</a></li>
              <li><a href="kids_collection.php">Kids' Collection</a></li>
            </ul>
          </li>
          <li><a href="abt.php">AboutUs</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="nav-icon">
        <a href="#"><i class="bx bx-search"></i></a>
        <a href="#"><i class="bx bx-user"></i></a>
        <a href="#"><i class="bx bx-cart"></i></a>
        <div class="bx bx-menu" id="menu-icon"></div>
      </div>
    </header>
    <!-- end of navbar -->

    <!-- start of product detail -->
    <div class="product-container">
      <div class="small-container single-product">
        <div class="row">
          <div class="col-2">
            <img
              src="https://img.drz.lazcdn.com/g/kf/S518efe48d3d9422aba884338cc6cd393n.jpg_720x720q80.jpg_.webp"
              width="100%"
              id="ProductImg"
              alt="Cotton Plain Half Sleeves T-Shirt"
            />
            <div class="small-img-row">
              <div class="small-img-col">
                <img
                  src="https://img.drz.lazcdn.com/g/kf/S518efe48d3d9422aba884338cc6cd393n.jpg_720x720q80.jpg_.webp"
                  width="100%"
                  class="small-img"
                  alt="Cotton Plain Half Sleeves T-Shirt"
                />
              </div>
              <div class="small-img-col">
                <img
                  src="https://img.drz.lazcdn.com/g/kf/S2ff5ace9b3eb45efbeba4890e415461dx.jpg_720x720q80.jpg_.webp"
                  width="100%"
                  class="small-img"
                  alt="Cotton Plain Half Sleeves T-Shirt"
                />
              </div>
              <div class="small-img-col">
                <img
                  src="https://img.drz.lazcdn.com/g/kf/S8b0075900438436680a4b02f487dce96n.jpg_720x720q80.jpg_.webp"
                  width="100%"
                  class="small-img"
                  alt="Cotton Plain Half Sleeves T-Shirt"
                />
              </div>
              <div class="small-img-col">
                <img
                  src="https://img.drz.lazcdn.com/g/kf/Sdaf9afacc38943feac51ee52b6c625209.jpg_720x720q80.jpg_.webp"
                  width="100%"
                  class="small-img"
                  alt="Cotton Plain Half Sleeves T-Shirt"
                />
              </div>
              <div class="small-img-col">
                <img
                  src="https://img.drz.lazcdn.com/g/kf/S9f4a88b9cb334be98d8a5636fa82c2e8X.jpg_720x720q80.jpg_.webp"
                  width="100%"
                  class="small-img"
                  alt="Cotton Plain Half Sleeves T-Shirt"
                />
              </div>
            </div>
          </div>
          <div class="col-2">
            <p>Home / T-shirt</p>
            <h1>
              Autumn Winter Fashion Female Vintage Full Sleeve Loose Warm
              Knitwear Long Sweaters Cardigan Women Casual Sweater Coats Outwear
            </h1>
            <h4>$50.00</h4>
            <select>
              <option>Select Size</option>
              <option>XXL</option>
              <option>XL</option>
              <option>Large</option>
              <option>Medium</option>
              <option>Small</option>
            </select>
            <div class="number-input">
              <button onclick="decrement()">-</button>
              <input id="quantity" type="text" value="1" min="1" />
              <button onclick="increment()">+</button>
            </div>
            <div class="btn-container">
              <a href="#" class="btn buy-now">Buy Now</a>
              <a href="#" class="btn">Add To Cart</a>
            </div>
            <h3>Product Details <i class="fa fa-indent"></i></h3>
            <br />
            <p>
              This Cotton Plain Half Sleeves T-Shirt for men is a stylish and
              versatile summer essential. Made from 100% cotton, it offers a
              slim fit that provides comfort and breathability, making it
              perfect for warm weather. Available in multiple sizes.
            </p>
          </div>
        </div>
      </div>
      <!-- end of product detail -->

      <!-- Related Products Section -->
      <div class="related-products">
        <h3>Related Products</h3>
        <div class="related-items">
          <div class="related-item">
            <img src="image/product-1.jpg" />
            <div>Charcoal Grey & Gunmetal-Toned Analogue Watch</div>
            <div class="item-price">$59.99</div>
          </div>
          <div class="related-item">
            <img src="image/product-2.jpg" />
            <div>Running Black Shoes</div>
            <div class="item-price">$19.99</div>
          </div>
          <div class="related-item">
            <img src="image/product-3.jpg" />
            <div>Men Nordic Walking Sports Shoes</div>
            <div class="item-price">$99.99</div>
          </div>
          <div class="related-item">
            <img src="image/product-4.jpg" />
            <div>Nike Men's Track Pants</div>
            <div class="item-price">$79.99</div>
          </div>
        </div>
        <!-- </div> -->
        <!-- end of product detail -->
      </div>
    </div>

    <!-- js for quantity increment and decrement -->
    <script>
      function increment() {
        let input = document.getElementById("quantity");
        input.value = parseInt(input.value) + 1;
      }

      function decrement() {
        let input = document.getElementById("quantity");
        if (input.value > 1) {
          input.value = parseInt(input.value) - 1;
        }
      }
    </script>

    <!-- js for product gallery -->
    <script>
      var ProductImg = document.getElementById("ProductImg");
      var SmallImg = document.getElementsByClassName("small-img");

      SmallImg[0].onmouseover = function () {
        ProductImg.src = SmallImg[0].src;
      };
      SmallImg[1].onmouseover = function () {
        ProductImg.src = SmallImg[1].src;
      };
      SmallImg[2].onmouseover = function () {
        ProductImg.src = SmallImg[2].src;
      };
      SmallImg[3].onmouseover = function () {
        ProductImg.src = SmallImg[3].src;
      };
      SmallImg[4].onmouseover = function () {
        ProductImg.src = SmallImg[4].src;
      };
    </script>

   <!-- footer starts -->
    <footer class="infos">
      <section class="contact">
        <div class="contact-info">
          <div class="first-info info">
            <img
              src="../images/logo.png"
              width="80"
              class="footer-image"
              height="80"
              alt="Elegance Logo"
            />
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
                <a href="#"><p>Contact us</p></a>
              </li>
              <li>
                <a href="#"><p>About page</p></a>
              </li>
              <li>
                <a href="#"><p>Shopping & Returns</p></a>
              </li>
              <li>
                <a href="#"><p>Privacy</p></a>
              </li>
            </ul>
          </div>

          <div class="third-info info">
            <h4>Shop</h4>
            <ul>
              <li>
                <a href="#"><p>Men's Shopping</p></a>
              </li>
              <li>
                <a href="#"><p>Women's Shopping</p></a>
              </li>
              <li>
                <a href="#"><p>Kid's Shopping</p></a>
              </li>
              <li>
                <a href="#"><p>Discount</p></a>
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
  </body>
</html>
