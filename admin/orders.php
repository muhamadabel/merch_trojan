<?php
$page_title = 'Orders';
require_once '../includes/admin-header.php';

// Get status filter
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get orders
$query = "
    SELECT o.*, u.name as customer_name, p.name as product_name
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN products p ON o.product_id = p.product_id
    WHERE o.status != 'cart'
";
$params = [];

if (!empty($status_filter)) {
    $query .= " AND o.status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $query .= " AND (u.name LIKE ? OR o.order_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY o.order_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1 class="page-title">Orders</h1>
</div>

<!-- Search and Filter -->
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
    <div class="search-box" style="flex: 1;">
        <svg xmlns="http://www.w3.org/2000/svg" class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <form method="GET" action="">
            <?php if (!empty($status_filter)): ?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
            <?php endif; ?>
            <input type="text" name="search" class="search-input" placeholder="Search orders..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>
    
    <div style="display: flex; gap: 0.5rem;">
        <a href="orders.php" class="btn <?php echo empty($status_filter) ? 'btn-primary' : 'btn-secondary'; ?>">All</a>
        <a href="orders.php?status=pending<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn <?php echo $status_filter === 'pending' ? 'btn-primary' : 'btn-secondary'; ?>">Pending</a>
        <a href="orders.php?status=paid<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn <?php echo $status_filter === 'paid' ? 'btn-primary' : 'btn-secondary'; ?>">Paid</a>
        <a href="orders.php?status=shipped<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="btn <?php echo $status_filter === 'shipped' ? 'btn-primary' : 'btn-secondary'; ?>">Shipped</a>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No orders found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['product_name'] ?? 'N/A'); ?></td>
                                <td><?php echo $order['quantity']; ?></td>
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
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="btn btn-sm btn-secondary">
                                            <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="paid" <?php echo $order['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                            <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    $allowed_statuses = ['pending', 'paid', 'shipped'];
    
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        echo "<script>window.location.reload();</script>";
    }
}
?>

<?php require_once '../includes/admin-footer.php'; ?>
