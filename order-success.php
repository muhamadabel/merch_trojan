<?php
$page_title = 'Order Success';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$order_id = $_GET['order_id'] ?? 0;
$user_id = $_SESSION['user_id'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.name as customer_name, p.address, p.phone
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN profiles p ON o.user_id = p.user_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}
?>
<?php include 'includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <div class="text-center" style="margin-bottom: 3rem;">
                <div style="font-size: 4rem; color: #22c55e; margin-bottom: 1rem;">✓</div>
                <h1 style="color: #22c55e; margin-bottom: 1rem;">Order Successful!</h1>
                <p style="font-size: 1.2rem; color: #666;">Thank you for your order. We'll process it shortly.</p>
            </div>
            
            <div class="order-details">
                <div class="order-info">
                    <h3>Order Information</h3>
                    <div class="info-row">
                        <span>Order ID:</span>
                        <span><strong>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></strong></span>
                    </div>
                    <div class="info-row">
                        <span>Order Date:</span>
                        <span><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span>Status:</span>
                        <span class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span>Total Amount:</span>
                        <span><strong><?php echo formatPrice($order['total_price']); ?></strong></span>
                    </div>
                </div>
                
                <div class="shipping-info">
                    <h3>Shipping Information</h3>
                    <div class="info-row">
                        <span>Customer:</span>
                        <span><?php echo htmlspecialchars($order['customer_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span>Phone:</span>
                        <span><?php echo htmlspecialchars($order['phone'] ?? 'Not provided'); ?></span>
                    </div>
                    <div class="info-row">
                        <span>Address:</span>
                        <span><?php echo nl2br(htmlspecialchars($order['address'] ?? 'Not provided')); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="payment-instructions">
                <h3>Payment Instructions</h3>
                <p>Please transfer the total amount to one of our bank accounts:</p>
                <div class="bank-accounts">
                    <div class="bank-account">
                        <strong>Bank BCA</strong><br>
                        Account: 1234567890<br>
                        Name: Trojan Merchandise
                    </div>
                    <div class="bank-account">
                        <strong>Bank Mandiri</strong><br>
                        Account: 0987654321<br>
                        Name: Trojan Merchandise
                    </div>
                </div>
                <p><strong>Important:</strong> Please include your Order ID (#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>) in the transfer description.</p>
            </div>
            
            <div class="text-center" style="margin-top: 3rem;">
                <a href="products.php" class="btn btn-primary">Continue Shopping</a>
                <a href="my-orders.php" class="btn btn-outline">View My Orders</a>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
