<?php
$page_title = 'My Orders';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user orders (exclude cart items)
$stmt = $pdo->prepare("
    SELECT o.*, p.name as product_name, p.image_url 
    FROM orders o 
    LEFT JOIN products p ON o.product_id = p.product_id 
    WHERE o.user_id = ? AND o.status != 'cart'
    ORDER BY o.order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <div class="section-title">
                <h1>My Orders</h1>
                <p>Track your order history and status</p>
            </div>
            
            <?php if (empty($orders)): ?>
                <div class="text-center" style="padding: 3rem 0;">
                    <p style="font-size: 1.2rem; color: #666;">You haven't placed any orders yet.</p>
                    <a href="products.php" class="btn btn-primary">Start Shopping</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div>
                                    <h3>Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></h3>
                                    <p><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="order-details">
                                <?php if ($order['product_name']): ?>
                                    <div class="order-product">
                                        <img src="<?php echo $order['image_url']; ?>" alt="<?php echo htmlspecialchars($order['product_name']); ?>" class="order-product-image">
                                        <div class="order-product-info">
                                            <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                                            <p>Qty: <?php echo $order['quantity']; ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="order-info">
                                    <span class="order-total"><strong><?php echo formatPrice($order['total_price']); ?></strong></span>
                                </div>
                                
                                <div class="order-actions">
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-danger" onclick="cancelOrder(<?php echo $order['order_id']; ?>)">
                                            Cancel Order
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
