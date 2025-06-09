<?php
$page_title = 'Products';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get products
$query = "SELECT * FROM products WHERE stock > 0";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <div class="section-title">
                <h1>Our Products</h1>
                <p>Explore our complete collection of Trojan merchandise</p>
            </div>
            
            <!-- Search -->
            <div class="search-box">
                <form method="GET" action="">
                    <input type="text" name="search" id="search" class="search-input" 
                           placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>"
                           onkeyup="searchProducts()">
                </form>
            </div>
            
            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div class="text-center" style="padding: 3rem 0;">
                    <p style="font-size: 1.2rem; color: #666;">No products found.</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <img src="<?php echo $product['image_url']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                <div class="product-footer">
                                    <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                    <span class="product-stock">Stock: <?php echo $product['stock']; ?></span>
                                </div>
                                <button class="btn btn-primary" onclick="addToCart(<?php echo $product['product_id']; ?>)" style="width: 100%;">Add to Cart</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
