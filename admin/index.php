<?php
$page_title = 'Dashboard';
require_once '../includes/admin-header.php';

// Get stats
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$product_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status != 'cart'");
$order_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM posts");
$post_count = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'");
$user_count = $stmt->fetchColumn();

// Get recent orders
$stmt = $pdo->query("
    SELECT o.*, u.name as customer_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    WHERE o.status != 'cart' 
    ORDER BY o.order_date DESC 
    LIMIT 5
");
$recent_orders = $stmt->fetchAll();

// Get low stock products
$stmt = $pdo->query("
    SELECT * FROM products 
    WHERE stock < 10 
    ORDER BY stock ASC 
    LIMIT 5
");
$low_stock = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1 class="page-title">Dashboard</h1>
    <div class="admin-actions">
        <a href="add-product.php" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Add Product
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?php echo $product_count; ?></div>
        <div class="stat-label">Products</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value"><?php echo $order_count; ?></div>
        <div class="stat-label">Orders</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value"><?php echo $post_count; ?></div>
        <div class="stat-label">Posts</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value"><?php echo $user_count; ?></div>
        <div class="stat-label">Customers</div>
    </div>
</div>

<!-- Recent Orders -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Orders</h2>
        <a href="orders.php" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_orders)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No orders found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                <td><?php echo formatPrice($order['total_price']); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    switch ($order['status']) {
                                        case 'pending':
                                            $status_class = 'badge-warning';
                                            break;
                                        case 'paid':
                                            $status_class = 'badge-success';
                                            break;
                                        case 'shipped':
                                            $status_class = 'badge-success';
                                            break;
                                        default:
                                            $status_class = '';
                                    }
                                    ?>
                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Low Stock Products -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Low Stock Products</h2>
        <a href="products.php" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($low_stock)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No low stock products</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($low_stock as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo formatPrice($product['price']); ?></td>
                                <td>
                                    <span class="<?php echo $product['stock'] < 5 ? 'text-danger' : 'text-warning'; ?>">
                                        <?php echo $product['stock']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
