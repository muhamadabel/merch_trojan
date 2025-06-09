<?php
$page_title = 'Order Detail';
require_once '../includes/admin-header.php';

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if order exists
$stmt = $pdo->prepare("
    SELECT o.*, u.name as customer_name, u.email as customer_email, p.address, p.phone
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN profiles p ON o.user_id = p.user_id
    WHERE o.order_id = ? AND o.status != 'cart'
");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $allowed_statuses = ['pending', 'paid', 'shipped'];
    
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$new_status, $order_id]);
        
        // Refresh order data
        $stmt = $pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email as customer_email, p.address, p.phone
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            LEFT JOIN profiles p ON o.user_id = p.user_id
            WHERE o.order_id = ? AND o.status != 'cart'
        ");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch();
        
        $success_message = "Order status updated successfully!";
    }
}
?>

<div class="admin-header">
    <h1 class="page-title">Order #<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></h1>
    <div class="admin-actions">
        <a href="orders.php" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Orders
        </a>
    </div>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Order Information</h2>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h3 style="margin-bottom: 1rem; color: #9ca3af; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Order Details</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    <div style="color: #9ca3af;">Order ID:</div>
                    <div>#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></div>
                    
                    <div style="color: #9ca3af;">Date:</div>
                    <div><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></div>
                    
                    <div style="color: #9ca3af;">Status:</div>
                    <div>
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
                    </div>
                    
                    <div style="color: #9ca3af;">Total Amount:</div>
                    <div><strong><?php echo formatPrice($order['total_price']); ?></strong></div>
                </div>
                
                <h3 style="margin: 1.5rem 0 1rem; color: #9ca3af; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Update Status</h3>
                <form method="POST" style="display: flex; gap: 0.5rem;">
                    <select name="status" class="form-control" style="width: auto;">
                        <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="paid" <?php echo $order['status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    </select>
                    <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                </form>
            </div>
            
            <div>
                <h3 style="margin-bottom: 1rem; color: #9ca3af; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em;">Customer Information</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem;">
                    <div style="color: #9ca3af;">Name:</div>
                    <div><?php echo htmlspecialchars($order['customer_name']); ?></div>
                    
                    <div style="color: #9ca3af;">Email:</div>
                    <div><?php echo htmlspecialchars($order['customer_email']); ?></div>
                    
                    <div style="color: #9ca3af;">Phone:</div>
                    <div><?php echo htmlspecialchars($order['phone'] ?? 'Not provided'); ?></div>
                    
                    <div style="color: #9ca3af;">Address:</div>
                    <div><?php echo nl2br(htmlspecialchars($order['address'] ?? 'Not provided')); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
