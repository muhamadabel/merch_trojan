<?php
$page_title = 'Checkout';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$error = '';

// Get cart items
$stmt = $pdo->prepare("
    SELECT o.*, p.name, p.price as unit_price, p.image_url 
    FROM orders o 
    JOIN products p ON o.product_id = p.product_id 
    WHERE o.user_id = ? AND o.status = 'cart'
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Redirect if cart is empty
if (empty($cart_items)) {
    redirect('cart.php');
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['total_price'];
}

// Get user profile
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $phone = trim($_POST['phone']);
    $payment_method = $_POST['payment_method'];
    
    if (empty($shipping_address) || empty($phone)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Update user profile with shipping info
            if ($profile) {
                $stmt = $pdo->prepare("UPDATE profiles SET address = ?, phone = ? WHERE user_id = ?");
                $stmt->execute([$shipping_address, $phone, $user_id]);
            } else {
                // Create profile if doesn't exist
                $stmt = $pdo->prepare("INSERT INTO profiles (user_id, address, phone) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $shipping_address, $phone]);
            }
            
            // Update all cart items to pending status
            $stmt = $pdo->prepare("UPDATE orders SET status = 'pending' WHERE user_id = ? AND status = 'cart'");
            $stmt->execute([$user_id]);
            
            // Update product stock
            foreach ($cart_items as $item) {
                $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            $pdo->commit();
            
            // Get the first order ID for redirect
            $order_id = $cart_items[0]['order_id'];
            
            // Redirect to success page
            redirect('order-success.php?order_id=' . $order_id);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Order processing failed. Please try again. Error: ' . $e->getMessage();
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <div class="section-title">
                <h1>Checkout</h1>
                <p>Complete your order</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="checkout-container">
                <div class="checkout-form">
                    <h3>Shipping Information</h3>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="shipping_address">Shipping Address *</label>
                            <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" required><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" class="form-control" required 
                                   value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                        </div>
                        
                        <h3 style="margin-top: 2rem;">Payment Method</h3>
                        
                        <div class="payment-methods">
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="bank_transfer" required checked>
                                <div class="payment-option">
                                    <strong>Bank Transfer</strong>
                                    <p>Transfer to our bank account</p>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="cod" required>
                                <div class="payment-option">
                                    <strong>Cash on Delivery (COD)</strong>
                                    <p>Pay when you receive the item</p>
                                </div>
                            </label>
                            
                            <label class="payment-method">
                                <input type="radio" name="payment_method" value="ewallet" required>
                                <div class="payment-option">
                                    <strong>E-Wallet</strong>
                                    <p>OVO, GoPay, DANA</p>
                                </div>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 2rem;">
                            Place Order
                        </button>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h3>Order Summary</h3>
                    
                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-item">
                                <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                                <span><?php echo formatPrice($item['total_price']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
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
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
