<?php
$page_title = 'Home';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get real stats from database
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$customer_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock > 0");
$available_products = $stmt->fetchColumn();

// Get featured products
$stmt = $pdo->prepare("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 3");
$stmt->execute();
$featured_products = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>TROJAN MERCHANDISE</h1>
            <p>Koleksi merchandise eksklusif dari HIMA terbaik se-kampus. Tunjukkan kebanggaan dan solidaritas mahasiswa dengan style yang keren!</p>
            <div class="hero-buttons">
                <a href="products.php" class="btn btn-primary">Shop Now</a>
                <a href="social.php" class="btn btn-outline">Event</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat">
                    <h3><?php echo $customer_count; ?>+</h3>
                    <p>Registered Customers</p>
                </div>
                <div class="stat">
                    <h3><?php echo $available_products; ?></h3>
                    <p>Products Available</p>
                </div>
                <div class="stat">
                    <h3>Premium</h3>
                    <p>Quality Products</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="section">
        <div class="container">
            <div class="section-title">
                <h2>Featured Products</h2>
                <p>Discover our most popular merchandise that represents the spirit of Trojan community</p>
            </div>
            
            <div class="products-grid">
                <?php foreach ($featured_products as $product): ?>
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
            
            <div class="text-center">
                <a href="products.php" class="btn btn-primary">View All Products</a>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
