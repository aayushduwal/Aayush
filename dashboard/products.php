<?php
require_once('includes/common.php');
require_once('../database/config.php');

checkAuth();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// For search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Add this at the top where you handle actions
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // First, try to delete the product image
    $stmt = $conn->prepare("SELECT images FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product && $product['images']) {
        $image_path = "../uploads/products/" . $product['images'];
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }
    }
    
    // Then delete the product from database
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    
    if ($stmt->execute()) {
        // Redirect back to products page with success message
        header("Location: products.php?msg=Product deleted successfully");
        exit();
    } else {
        // Redirect back with error message
        header("Location: products.php?error=Failed to delete product");
        exit();
    }
}

getHeader('Products Management');
getSidebar();
?>

<div class="main-content">
    <?php
    switch($action) {
        case 'add':
            // Add New Product Form
            ?>
            <div class="dashboard-header">
                <h1>Add New Product</h1>
                <div class="nav-icon">
                    <a href="?action=list" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="form-container">
                <form action="?action=create" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Price</label>
                        <input type="number" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <option value="Men">Men</option>
                            <option value="Women">Women</option>
                            <option value="Kids">Kids</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="images">Main Product Image</label>
                        <input type="file" name="images" id="images" required accept="image/*" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="additional_images">Additional Images</label>
                        <input type="file" name="additional_images[]" id="additional_images" multiple accept="image/*" class="form-control">
                        <small class="text-muted">Hold Ctrl to select multiple images</small>
                    </div>
                    <div class="form-group">
                        <label>Inventory</label>
                        <input type="number" name="inventory" required>
                    </div>
                    <div class="form-group">
                        <label>Sizes Available</label>
                        <input type="text" name="sizes" required placeholder="Example: S,M,L or 40,41,42">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
            <?php
            break;

        case 'create':
            // Handle Add Product Form Submission
            if($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $price = floatval($_POST['price']);
                $category = mysqli_real_escape_string($conn, $_POST['category']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $inventory = intval($_POST['inventory']);
                $sizes = mysqli_real_escape_string($conn, $_POST['sizes']);
                
                // Generate slug from name
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
                
                // Handle image upload
                $image = '';
                if(isset($_FILES['images'])) {
                    $main_image = time() . '_' . $_FILES['images']['name'];
                    move_uploaded_file($_FILES['images']['tmp_name'], "../uploads/products/" . $main_image);
                    
                    // Handle additional images if any
                    $additional_images = array();
                    if(isset($_FILES['additional_images'])) {
                        foreach($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                            if($_FILES['additional_images']['error'][$key] == 0) {
                                $filename = time() . '_' . $_FILES['additional_images']['name'][$key];
                                move_uploaded_file($tmp_name, "../uploads/products/" . $filename);
                                $additional_images[] = $filename;
                            }
                        }
                    }
                    
                    $additional_images_json = json_encode($additional_images);
                    
                    // Update your SQL query to include additional_images
                    $stmt = $conn->prepare("INSERT INTO products (name, slug, images, additional_images, price, description, sizes, inventory, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssdssss", $name, $slug, $main_image, $additional_images_json, $price, $description, $sizes, $inventory, $category);
                }
                
                if($stmt->execute()) {
                    echo "<script>alert('Product added successfully!'); window.location='?action=list';</script>";
                } else {
                    echo "<script>alert('Error adding product: " . $stmt->error . "'); window.location='?action=add';</script>";
                }
                $stmt->close();
            }
            break;

        case 'edit':
            // Edit Product Form
            $id = isset($_GET['id']) ? $_GET['id'] : 0;
            $product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
            if($product) {
                ?>
                <div class="dashboard-header">
                    <h1>Edit Product</h1>
                    <div class="nav-icon">
                        <a href="?action=list" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="form-container">
                    <form action="?action=update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" value="<?php echo $product['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Price</label>
                            <input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" name="category" value="<?php echo $product['category']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="4"><?php echo $product['description']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Current Image</label>
                            <?php if($product['images']): ?>
                                <img src="../uploads/products/<?php echo $product['images']; ?>" class="product-thumbnail">
                            <?php endif; ?>
                            <input type="file" name="image" accept="image/*">
                            <small>Leave empty to keep current image</small>
                        </div>
                        <div class="form-group">
                            <label>Inventory</label>
                            <input type="number" name="inventory" value="<?php echo $product['inventory']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </form>
                </div>
                <?php
            }
            break;

        case 'list':
        default:
            // View All Products (Default View)
            ?>
            <div class="dashboard-header">
                <h1>Products Management</h1>
                <div class="nav-icon">
                    <a href="?action=add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="filter-section">
                <form method="GET" class="filter-form">
                    <input type="hidden" name="action" value="list">
                    <div class="form-group">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
                    </div>
                    <div class="form-group">
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php
                            $categories = $conn->query("SELECT DISTINCT category FROM products");
                            while($cat = $categories->fetch_assoc()):
                            ?>
                            <option value="<?php echo $cat['category']; ?>" <?php echo $category == $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo $cat['category']; ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="?action=list" class="btn btn-secondary">Reset</a>
                </form>
            </div>

            <!-- Products List -->
            <div class="products-list">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Inventory</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Build query with search/filter
                        $query = "SELECT * FROM products WHERE 1=1";
                        if($search) {
                            $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
                        }
                        if($category) {
                            $query .= " AND category = '$category'";
                        }
                        $query .= " ORDER BY created_at DESC";
                        
                        $products = $conn->query($query);
                        while($product = $products->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td>
                                <img src="../uploads/products/<?php echo $product['images']; ?>" 
                                     alt="<?php echo $product['name']; ?>" 
                                     class="product-thumbnail">
                            </td>
                            <td><?php echo $product['name']; ?></td>
                            <td>Rs. <?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['category']; ?></td>
                            <td><?php echo $product['inventory']; ?></td>
                            <td class="actions">
                                <a href="?action=edit&id=<?php echo $product['id']; ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?php echo $product['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this product?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php
    }
    ?>
</div>

<?php getFooter(); ?>
