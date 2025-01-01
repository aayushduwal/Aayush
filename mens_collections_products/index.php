<?php
session_start();
require_once('../database/config.php');
require_once('../includes/functions.php');

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check login status
$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['admin_id']);
$userDetails = null;

if (isset($_SESSION['user_id'])) {
    $userDetails = getUserDetails($conn, $_SESSION['user_id']);
}

// Get subcategory from URL if provided
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : null;

// Prepare the base query
$base_query = "SELECT * FROM products WHERE category = 'Men'";
if ($subcategory) {
    $base_query .= " AND subcategory = ?";
}

// Fetch products based on subcategory
$stmt = $conn->prepare($base_query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if ($subcategory) {
    $stmt->bind_param("s", $subcategory);
}
$stmt->execute();
$products = $stmt->get_result();

// Fetch distinct subcategories for navigation
$subcategories_stmt = $conn->prepare("SELECT DISTINCT subcategory FROM products WHERE category = 'Men' AND subcategory IS NOT NULL");
$subcategories_stmt->execute();
$subcategories = $subcategories_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Men's Collection - Elegance</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/collection.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>
  <style>
    .hero {
  background: url("https://www.shaahidamir.co.in/images/mens-banner.jpg")
    no-repeat center center/cover;
  height: 60vh;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
  text-align: center;
  overflow: hidden;
}

.hero h1 {
  font-size: 48px;
  letter-spacing: 3px;
  margin-bottom: 10px;
  text-transform: uppercase;
}

.hero p {
  font-size: 20px;
  font-style: italic;
}

.categories {
  display: flex;
  justify-content: center;
  gap: 20px;
  padding: 20px;
  font-size: 18px;
}

.categories a {
  text-decoration: none;
  color: #333;
  font-weight: bold;
}
  </style>
    <?php include('../includes/header.php'); ?>

    <section class="hero">
      <div class="herotxt">
        <h5>ELEGANCE</h5>
      </div>
    </section>

    <div class="categories">
      <a href="#products">Shirts</a>
      <a href="#jackets">Jackets</a>
      <a href="#hoodies">Hoodies</a>
      <a href="#sweatshirts">Sweatshirts</a>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav">
        <h2>Men's Collection</h2>
        <ul>
            <li><a href="index.php" <?php echo !$subcategory ? 'class="active"' : ''; ?>>All</a></li>
            <?php while($sub = $subcategories->fetch_assoc()): ?>
                <li>
                    <a href="index.php?subcategory=<?php echo urlencode($sub['subcategory']); ?>"
                       <?php echo ($subcategory == $sub['subcategory']) ? 'class="active"' : ''; ?>>
                        <?php echo htmlspecialchars($sub['subcategory']); ?>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>

    <!-- Products display section -->
    <div class="container">
        <section class="collection">
            <div class="collection-wrapper">
                <?php 
                if ($products->num_rows > 0) {
                    while($product = $products->fetch_assoc()): 
                ?>
                    <div class="collection-wrapper-child">
                        <a href="details.php?id=<?php echo $product['id']; ?>">
                            <img src="../uploads/products/<?php echo htmlspecialchars($product['images']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" />
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="rating-wrapper">
                                <i class="fa-regular fa-star"></i>
                                <?php echo number_format($product['rating'], 1); ?>
                            </div>
                            <p>रु. <?php echo number_format($product['price'], 2); ?></p>
                        </a>
                    </div>
                <?php 
                    endwhile;
                } else {
                    echo "<p>No products found in this category.</p>";
                }
                ?>
            </div>
        </section>
    </div>

    <?php include('../includes/footer.php'); ?>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../cart/addToCart.js"></script>
</body>
</html>