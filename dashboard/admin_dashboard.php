<?php 
session_start();
include('../database/config.php');

// Function to get user details
function getUserDetails($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userDetails = null;

if ($isLoggedIn) {
    $userDetails = getUserDetails($conn, $_SESSION['user_id']);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>E-commerce Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css" />
  </head>
  <body>
    <div class="sidebar">
      <div class="sidebar-logo">
         <a href="/aayush/index.php">
           <h2>Elegance</h2>
         </a>
      </div>
      <ul class="sidebar-menu">
        <li><a href="#">Dashboard</a></li>
        <li class="has-dropdown">
          <a href="#">Products ▼</a>
          <div class="dropdown">
            <a href="#">Add Products</a>
            <a href="#">View Products</a>
          </div>
        </li>
        <li><a href="#">Orders</a></li>
        <li><a href="#">Customers</a></li>
      </ul>
    </div>

    <div class="main-content">
      <div class="dashboard-header">
        <h1>Dashboard</h1>
        <!-- <div>Welcome, Admin</div> -->

    <div class="nav-icon">
       <?php if ($isLoggedIn): ?>
       <p>
         Logged in as <u><strong><?php echo $userDetails['username']; ?></strong></u>
       </p>
       <?php else: ?>
       <a href="#"><i class="bx bx-user"></i></a>
       <?php endif; ?>
     </div>
     <div id="menu-icon">
       <i class="fa fa-bars"></i>
     </div>
     </div>

      <div class="stats-container">
        <div class="stat-card">
          <h3>Total Revenue</h3>
          <div class="number">NPR-0</div>
        </div>
        <div class="stat-card">
          <h3>Orders</h3>
          <div class="number">0</div>
        </div>
        <div class="stat-card">
          <h3>Products</h3>
          <div class="number">0</div>
        </div>
        <div class="stat-card">
          <h3>Customers</h3>
          <div class="number">0</div>
        </div>
      </div>

      <div class="recent-orders">
        <h2>Recent Orders</h2>
        <table class="order-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Status</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>#12345</td>
              <td>Ram</td>
              <td>2024-02-15</td>
              <td>Completed</td>
              <td>$247.50</td>
            </tr>
            <tr>
              <td>#12346</td>
              <td>Shyam</td>
              <td>2024-02-16</td>
              <td>Pending</td>
              <td>$189.99</td>
            </tr>
            <tr>
              <td>#12347</td>
              <td>Hari</td>
              <td>2024-02-17</td>
              <td>Processing</td>
              <td>$345.25</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>