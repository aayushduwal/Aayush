<?php
session_start();

require_once('includes/common.php');
require_once('../database/config.php');

checkAuth();

// Get dashboard statistics
$product_count = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
$order_count = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$customer_count = $conn->query("SELECT COUNT(*) as count FROM customers")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;

// Get recent orders
$recent_orders = $conn->query("
    SELECT o.*, c.name as customer_name 
    FROM orders o 
    LEFT JOIN customers c ON o.customer_id = c.id 
    ORDER BY order_date DESC LIMIT 5
");

// Get header with title
getHeader('Dashboard - Elegance');
getSidebar();

?>

<div class="main-content">
    <div class="dashboard-header">
        <h1>Dashboard Overview</h1>
        <div class="nav-icon">
            <p>Welcome, <?php echo $_SESSION['admin_username']; ?></p>
            <a href="../logout.php">Logout</a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total Products</h3>
            <div class="number"><?php echo $product_count; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Orders</h3>
            <div class="number"><?php echo $order_count; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Customers</h3>
            <div class="number"><?php echo $customer_count; ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <div class="number">Rs. <?php echo number_format($total_revenue, 2); ?></div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="recent-orders">
        <h2>Recent Orders</h2>
        <table class="order-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $recent_orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo $order['customer_name']; ?></td>
                    <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <span class="status status-<?php echo strtolower($order['status']); ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                    <td>
                        <a href="orders/view.php?id=<?php echo $order['id']; ?>" class="btn-view">View</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php getFooter(); ?>