<?php
$page_title = 'Shopping Cart';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $order_id = $_POST['order_id'];
                $quantity = intval($_POST['quantity']);
                
                if ($quantity <= 0) {
                    // Remove item if quantity is 0 or less
                    $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ? AND user_id = ? AND status = 'cart'");
                    $stmt->execute([$order_id, $user_id]);
                } else {
                    // Get product price
                    $stmt = $pdo->prepare("
                        SELECT p.price 
                        FROM orders o 
                        JOIN products p ON o.product_id = p.product_id 
                        WHERE o.order_id = ? AND o.user_id = ? AND o.status = 'cart'
                    ");
                    $stmt->execute([$order_id, $user_id]);
                    $product_price = $stmt->fetchColumn();
                    
                    if ($product_price) {
                        // Update quantity and total price
                        $new_total = $product_price * $quantity;
                        $stmt = $pdo->prepare("
                            UPDATE orders 
                            SET quantity = ?, total_price = ? 
                            WHERE order_id = ? AND user_id = ? AND status = 'cart'
                        ");
                        $stmt->execute([$quantity, $new_total, $order_id, $user_id]);
                    }
                }
                
                $message = 'Cart updated successfully!';
                break;
                
            case 'remove':
                $order_id = $_POST['order_id'];
                
                $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ? AND user_id = ? AND status = 'cart'");
                $stmt->execute([$order_id, $user_id]);
                $message = 'Item removed from cart!';
                break;
        }
    }
}

// Get cart items with product details
$stmt = $pdo->prepare("
    SELECT o.*, p.name, p.price as unit_price, p.image_url, p.stock
    FROM orders o 
    JOIN products p ON o.product_id = p.product_id 
    WHERE o.user_id = ? AND o.status = 'cart'
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['total_price'];
}
?>
<?php include 'includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <div class="section-title">
                <h1>Shopping Cart</h1>
                <p>Review your items before checkout</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if (empty($cart_items)): ?>
                <div class="text-center" style="padding: 3rem 0;">
                    <p style="font-size: 1.2rem; color: #666;">Your cart is empty.</p>
                    <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-container">
                    <div class="cart-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item">
                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-item-image">
                                
                                <div class="cart-item-details">
                                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="cart-item-price"><?php echo formatPrice($item['unit_price']); ?> each</p>
                                    <p class="cart-item-stock">Stock: <?php echo $item['stock']; ?></p>
                                </div>
                                
                                <div class="cart-item-actions">
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <div class="quantity-controls">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                            <button type="submit" class="btn btn-sm">Update</button>
                                        </div>
                                    </form>
                                    
                                    <form method="POST" style="display: inline-block;">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="order_id" value="<?php echo $item['order_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('Remove this item from cart?')">Remove</button>
                                    </form>
                                </div>
                                
                                <div class="cart-item-subtotal">
                                    <?php echo formatPrice($item['total_price']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Subtotal:</span>
                            <span><?php echo formatPrice($total); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span>Free</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span><?php echo formatPrice($total); ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                            Proceed to Checkout
                        </a>
                        <a href="products.php" class="btn btn-outline" style="width: 100%; margin-top: 0.5rem;">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
