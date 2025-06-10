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

// Get order details with product info
$stmt = $pdo->prepare("
    SELECT o.*, u.name as customer_name, p.address, p.phone,
           pr.name as product_name, pr.price as product_price, pr.image_url
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN profiles p ON o.user_id = p.user_id
    LEFT JOIN products pr ON o.product_id = pr.product_id
    WHERE o.order_id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}
?>
<?php include 'includes/header.php'; ?>

<style>
.success-container {
    min-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 1rem;
    background: radial-gradient(ellipse at center, rgba(34, 197, 94, 0.03) 0%, transparent 70%);
}

.success-card {
    background: linear-gradient(145deg, #111111 0%, #1a1a1a 100%);
    border-radius: 20px;
    border: 1px solid rgba(34, 197, 94, 0.3);
    max-width: 700px;
    width: 100%;
    overflow: hidden;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.5),
        0 0 0 1px rgba(34, 197, 94, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    transform: translateY(0);
    transition: all 0.3s ease;
}

.success-card:hover {
    transform: translateY(-5px);
    box-shadow: 
        0 35px 70px rgba(0, 0, 0, 0.6),
        0 0 0 1px rgba(34, 197, 94, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.success-header {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);
    padding: 3rem 2rem;
    text-align: center;
    color: #000000;
    position: relative;
    overflow: hidden;
}

.success-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: shimmer 3s infinite;
}

@keyframes shimmer {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.success-icon {
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    animation: successPulse 2s infinite;
    box-shadow: 
        0 10px 30px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 1;
}

@keyframes successPulse {
    0%, 100% { 
        transform: scale(1);
        box-shadow: 
            0 10px 30px rgba(0, 0, 0, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            0 0 0 0 rgba(255, 255, 255, 0.4);
    }
    50% { 
        transform: scale(1.05);
        box-shadow: 
            0 15px 40px rgba(0, 0, 0, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.3),
            0 0 0 10px rgba(255, 255, 255, 0.1);
    }
}

.success-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.75rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 1;
}

.success-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    font-weight: 500;
    position: relative;
    z-index: 1;
    line-height: 1.4;
}

.order-details {
    padding: 2.5rem;
    background: linear-gradient(145deg, #0f0f0f 0%, #1a1a1a 100%);
}

.detail-section {
    margin-bottom: 2.5rem;
    background: linear-gradient(145deg, #1a1a1a 0%, #111111 100%);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(34, 197, 94, 0.1);
    box-shadow: 
        0 4px 15px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.detail-section h3 {
    color: #22c55e;
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(34, 197, 94, 0.1);
    transition: all 0.2s ease;
    gap: 1rem;
}

.detail-row:hover {
    background: rgba(34, 197, 94, 0.02);
    margin: 0 -1rem;
    padding: 1rem;
    border-radius: 8px;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    color: #9ca3af;
    font-weight: 600;
    font-size: 0.95rem;
    flex-shrink: 0;
    min-width: 100px;
}

.detail-value {
    color: #ffffff;
    font-weight: 700;
    font-size: 1rem;
    text-align: right;
    word-break: break-word;
}

.order-id {
    font-family: 'Courier New', monospace;
    background: linear-gradient(145deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.3);
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
    font-weight: 800;
    display: inline-block;
}

.status-badge {
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 700;
    text-transform: uppercase;
    background: linear-gradient(145deg, rgba(234, 179, 8, 0.15) 0%, rgba(234, 179, 8, 0.05) 100%);
    color: #eab308;
    border: 1px solid rgba(234, 179, 8, 0.3);
    box-shadow: 
        0 4px 15px rgba(234, 179, 8, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
    display: inline-block;
}

.product-info {
    display: flex;
    gap: 1.5rem;
    align-items: center;
    padding: 1.5rem;
    background: linear-gradient(145deg, rgba(34, 197, 94, 0.08) 0%, rgba(34, 197, 94, 0.03) 100%);
    border-radius: 12px;
    margin-bottom: 1rem;
    border: 1px solid rgba(34, 197, 94, 0.2);
    box-shadow: 
        0 4px 15px rgba(34, 197, 94, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 12px;
    border: 2px solid rgba(34, 197, 94, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    flex-shrink: 0;
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-details h4 {
    color: #ffffff;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
    font-weight: 700;
    word-break: break-word;
}

.product-price {
    color: #22c55e;
    font-weight: 700;
    font-size: 1.1rem;
}

.payment-instructions {
    background: linear-gradient(145deg, rgba(34, 197, 94, 0.08) 0%, rgba(34, 197, 94, 0.03) 100%);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 
        0 8px 25px rgba(34, 197, 94, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.payment-instructions h3 {
    color: #22c55e;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.3rem;
    font-weight: 700;
}

.bank-accounts {
    display: grid;
    gap: 1.5rem;
    margin: 1.5rem 0;
}

.bank-account {
    background: linear-gradient(145deg, #1a1a1a 0%, #111111 100%);
    padding: 1.5rem;
    border-radius: 12px;
    border: 1px solid rgba(34, 197, 94, 0.2);
    box-shadow: 
        0 4px 15px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    transition: all 0.2s ease;
}

.bank-account:hover {
    transform: translateY(-2px);
    box-shadow: 
        0 8px 25px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.bank-account strong {
    color: #22c55e;
    display: block;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
    font-weight: 700;
}

.bank-account div {
    color: #e2e8f0;
    margin-bottom: 0.25rem;
    word-break: break-all;
}

.important-note {
    background: linear-gradient(145deg, rgba(234, 179, 8, 0.12) 0%, rgba(234, 179, 8, 0.05) 100%);
    border: 1px solid rgba(234, 179, 8, 0.4);
    border-radius: 12px;
    padding: 1.5rem;
    margin-top: 1.5rem;
    box-shadow: 
        0 4px 15px rgba(234, 179, 8, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
}

.important-note strong {
    color: #eab308;
    font-weight: 700;
}

.action-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    padding: 2.5rem;
    border-top: 1px solid rgba(34, 197, 94, 0.1);
    background: linear-gradient(145deg, #0f0f0f 0%, #1a1a1a 100%);
}

.btn-success {
    background: linear-gradient(145deg, #22c55e 0%, #16a34a 50%, #15803d 100%);
    color: #000000;
    padding: 1rem 2.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 
        0 8px 25px rgba(34, 197, 94, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    font-size: 1rem;
    text-align: center;
    justify-content: center;
    white-space: nowrap;
}

.btn-success:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 12px 35px rgba(34, 197, 94, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.3);
}

.btn-outline-success {
    background: linear-gradient(145deg, transparent 0%, rgba(34, 197, 94, 0.05) 100%);
    color: #22c55e;
    border: 2px solid #22c55e;
    padding: 1rem 2.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 
        0 4px 15px rgba(34, 197, 94, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.05);
    font-size: 1rem;
    text-align: center;
    justify-content: center;
    white-space: nowrap;
}

.btn-outline-success:hover {
    background: linear-gradient(145deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
    transform: translateY(-3px);
    box-shadow: 
        0 8px 25px rgba(34, 197, 94, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

/* ===== RESPONSIVE DESIGN ===== */

/* Tablet Landscape (1024px and below) */
@media (max-width: 1024px) {
    .success-container {
        padding: 2rem 1rem;
    }
    
    .success-card {
        max-width: 600px;
    }
    
    .success-header {
        padding: 2.5rem 1.5rem;
    }
    
    .order-details {
        padding: 2rem;
    }
    
    .detail-section {
        padding: 1.25rem;
    }
}

/* Tablet Portrait (768px and below) */
@media (max-width: 768px) {
    .success-container {
        padding: 1.5rem 0.75rem;
        min-height: 100vh;
        align-items: flex-start;
    }
    
    .success-card {
        margin: 0;
        border-radius: 16px;
        max-width: 100%;
    }
    
    .success-header {
        padding: 2rem 1.5rem;
    }
    
    .success-icon {
        width: 80px;
        height: 80px;
        margin-bottom: 1rem;
    }
    
    .success-title {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .success-subtitle {
        font-size: 1rem;
    }
    
    .order-details {
        padding: 1.5rem;
    }
    
    .detail-section {
        margin-bottom: 2rem;
        padding: 1.25rem;
    }
    
    .detail-section h3 {
        font-size: 1.1rem;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.75rem 0;
    }
    
    .detail-label {
        min-width: auto;
        font-size: 0.9rem;
    }
    
    .detail-value {
        text-align: left;
        font-size: 0.95rem;
    }
    
    .product-info {
        flex-direction: column;
        text-align: center;
        padding: 1.25rem;
        gap: 1rem;
    }
    
    .product-image {
        width: 100px;
        height: 100px;
        align-self: center;
    }
    
    .payment-instructions {
        padding: 1.5rem;
    }
    
    .payment-instructions h3 {
        font-size: 1.1rem;
        gap: 0.5rem;
    }
    
    .bank-account {
        padding: 1.25rem;
    }
    
    .action-buttons {
        flex-direction: column;
        padding: 2rem 1.5rem;
        gap: 1rem;
    }
    
    .btn-success,
    .btn-outline-success {
        width: 100%;
        padding: 1rem 1.5rem;
        font-size: 0.95rem;
    }
}

/* Mobile Large (480px and below) */
@media (max-width: 480px) {
    .success-container {
        padding: 1rem 0.5rem;
    }
    
    .success-card {
        border-radius: 12px;
    }
    
    .success-header {
        padding: 1.5rem 1rem;
    }
    
    .success-icon {
        width: 70px;
        height: 70px;
    }
    
    .success-title {
        font-size: 1.75rem;
    }
    
    .success-subtitle {
        font-size: 0.95rem;
    }
    
    .order-details {
        padding: 1rem;
    }
    
    .detail-section {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
    }
    
    .detail-section h3 {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .detail-section h3 svg {
        width: 16px;
        height: 16px;
    }
    
    .detail-row {
        padding: 0.5rem 0;
    }
    
    .detail-label {
        font-size: 0.85rem;
    }
    
    .detail-value {
        font-size: 0.9rem;
    }
    
    .order-id {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
    
    .product-info {
        padding: 1rem;
    }
    
    .product-image {
        width: 80px;
        height: 80px;
    }
    
    .product-details h4 {
        font-size: 1rem;
    }
    
    .product-price {
        font-size: 1rem;
    }
    
    .payment-instructions {
        padding: 1.25rem;
        border-radius: 12px;
    }
    
    .payment-instructions h3 {
        font-size: 1rem;
    }
    
    .bank-account {
        padding: 1rem;
    }
    
    .bank-account strong {
        font-size: 1rem;
    }
    
    .important-note {
        padding: 1.25rem;
    }
    
    .action-buttons {
        padding: 1.5rem 1rem;
    }
    
    .btn-success,
    .btn-outline-success {
        padding: 0.875rem 1.25rem;
        font-size: 0.9rem;
        gap: 0.5rem;
    }
    
    .btn-success svg,
    .btn-outline-success svg {
        width: 14px;
        height: 14px;
    }
}

/* Mobile Small (360px and below) */
@media (max-width: 360px) {
    .success-container {
        padding: 0.75rem 0.25rem;
    }
    
    .success-header {
        padding: 1.25rem 0.75rem;
    }
    
    .success-icon {
        width: 60px;
        height: 60px;
    }
    
    .success-title {
        font-size: 1.5rem;
    }
    
    .success-subtitle {
        font-size: 0.9rem;
    }
    
    .order-details {
        padding: 0.75rem;
    }
    
    .detail-section {
        padding: 0.875rem;
    }
    
    .payment-instructions {
        padding: 1rem;
    }
    
    .bank-account {
        padding: 0.875rem;
    }
    
    .action-buttons {
        padding: 1.25rem 0.75rem;
    }
    
    .btn-success,
    .btn-outline-success {
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }
}

/* Landscape orientation adjustments */
@media (max-height: 600px) and (orientation: landscape) {
    .success-container {
        min-height: auto;
        padding: 1rem;
    }
    
    .success-header {
        padding: 1.5rem;
    }
    
    .success-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 0.75rem;
    }
    
    .success-title {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }
    
    .order-details {
        padding: 1.5rem;
    }
    
    .detail-section {
        margin-bottom: 1.5rem;
    }
}

/* High DPI displays */
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
    .success-card {
        border-width: 0.5px;
    }
    
    .detail-section {
        border-width: 0.5px;
    }
    
    .bank-account {
        border-width: 0.5px;
    }
}

/* Touch device optimizations */
@media (hover: none) and (pointer: coarse) {
    .success-card:hover {
        transform: none;
    }
    
    .detail-row:hover {
        background: none;
        margin: 0;
        padding: 1rem 0;
    }
    
    .bank-account:hover {
        transform: none;
    }
    
    .btn-success:hover,
    .btn-outline-success:hover {
        transform: none;
    }
    
    /* Increase touch targets */
    .btn-success,
    .btn-outline-success {
        min-height: 48px;
        padding: 1rem 1.5rem;
    }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
    .success-card,
    .success-icon,
    .detail-row,
    .bank-account,
    .btn-success,
    .btn-outline-success {
        transition: none;
        animation: none;
    }
    
    .success-header::before {
        animation: none;
    }
    
    @keyframes successPulse {
        0%, 100% { transform: scale(1); }
    }
    
    @keyframes shimmer {
        0%, 100% { transform: rotate(0deg); }
    }
}
</style>

<div class="success-container">
    <div class="container">
        <div class="success-card">
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20,6 9,17 4,12"></polyline>
                    </svg>
                </div>
                <h1 class="success-title">Order Successful!</h1>
                <p class="success-subtitle">Thank you for your purchase. We'll process your order shortly.</p>
            </div>

            <!-- Order Details -->
            <div class="order-details">
                <!-- Order Information -->
                <div class="detail-section">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                        </svg>
                        Order Information
                    </h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Order ID</span>
                        <span class="detail-value order-id">#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Order Date</span>
                        <span class="detail-value"><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Status</span>
                        <span class="status-badge"><?php echo ucfirst($order['status']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Total Amount</span>
                        <span class="detail-value" style="color: #22c55e; font-size: 1.2rem;">
                            <?php echo formatPrice($order['total_price']); ?>
                        </span>
                    </div>
                </div>

                <!-- Product Information -->
                <?php if ($order['product_name']): ?>
                <div class="detail-section">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="9" cy="9" r="2"></circle>
                            <path d="M21 15l-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>
                        </svg>
                        Product Details
                    </h3>
                    
                    <div class="product-info">
                        <?php if ($order['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($order['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($order['product_name']); ?>" 
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image" style="background: #22c55e; display: flex; align-items: center; justify-content: center; color: #000; font-weight: bold;">
                                <?php echo strtoupper(substr($order['product_name'], 0, 1)); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-details">
                            <h4><?php echo htmlspecialchars($order['product_name']); ?></h4>
                            <div class="product-price"><?php echo formatPrice($order['product_price']); ?></div>
                            <div style="color: #9ca3af; font-size: 0.9rem;">Quantity: <?php echo $order['quantity']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Customer Information -->
                <div class="detail-section">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Customer Information
                    </h3>
                    
                    <div class="detail-row">
                        <span class="detail-label">Name</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value"><?php echo htmlspecialchars($order['phone'] ?? 'Not provided'); ?></span>
                    </div>
                    
                    <div class="detail-row">
                        <span class="detail-label">Address</span>
                        <span class="detail-value"><?php echo nl2br(htmlspecialchars($order['address'] ?? 'Not provided')); ?></span>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div class="payment-instructions">
                    <h3>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                            <line x1="1" y1="10" x2="23" y2="10"></line>
                        </svg>
                        Payment Instructions
                    </h3>
                    
                    <p style="margin-bottom: 1rem; color: #e2e8f0;">Please transfer the total amount to one of our bank accounts:</p>
                    
                    <div class="bank-accounts">
                        <div class="bank-account">
                            <strong>Bank BCA</strong>
                            <div>Account: 1234567890</div>
                            <div>Name: Trojan Merchandise</div>
                        </div>
                        
                        <div class="bank-account">
                            <strong>Bank Mandiri</strong>
                            <div>Account: 0987654321</div>
                            <div>Name: Trojan Merchandise</div>
                        </div>
                    </div>
                    
                    <div class="important-note">
                        <strong>Important:</strong> Please include your Order ID 
                        <span class="order-id">#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></span> 
                        in the transfer description.
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="products.php" class="btn-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Continue Shopping
                </a>
                
                <a href="my-orders.php" class="btn-outline-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14,2 14,8 20,8"></polyline>
                    </svg>
                    View My Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
