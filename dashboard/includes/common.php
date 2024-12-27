<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function checkAuth() {
    if (!isset($_SESSION['admin_id']) || !$_SESSION['is_admin']) {
        header("Location: login.php");
        exit();
    }
    return true;
}

function getHeader($title = 'Admin Dashboard') {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($title); ?></title>
        <link rel="stylesheet" href="../css/admin_dashboard.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>
    <body>
    <?php
}

function getSidebar() {
    ?>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <a href="../index.php">
                <h2>Elegance</h2>
            </a>
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="has-dropdown">
                <a href="#">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <div class="dropdown">
                    <a href="products.php?action=list">View All</a>
                    <a href="products.php?action=add">Add New</a>
                </div>
            </li>
            <li>
                <a href="orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li>
                <a href="customers.php">
                    <i class="fas fa-users"></i>
                    <span>Customers</span>
                </a>
            </li>
        </ul>
    </div>
    <?php
}

function getFooter() {
    ?>
    </body>
    </html>
    <?php
}
?>
