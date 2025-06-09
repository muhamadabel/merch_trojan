<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 1);
$user_id = $_SESSION['user_id'];

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity']);
    exit;
}

try {
    // Check if product exists and has stock
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND stock >= ?");
    $stmt->execute([$product_id, $quantity]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not available or insufficient stock']);
        exit;
    }
    
    // Calculate total price
    $total_price = $product['price'] * $quantity;
    
    // Check if this exact product is already in cart
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE user_id = ? AND status = 'cart' AND product_id = ?
    ");
    $stmt->execute([$user_id, $product_id]);
    $existing_cart_item = $stmt->fetch();
    
    if ($existing_cart_item) {
        // Update existing cart item - add to quantity and update total price
        $new_quantity = $existing_cart_item['quantity'] + $quantity;
        $new_total = $product['price'] * $new_quantity;
        
        // Check if new quantity doesn't exceed stock
        if ($new_quantity > $product['stock']) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            exit;
        }
        
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET quantity = ?, total_price = ? 
            WHERE order_id = ?
        ");
        $stmt->execute([$new_quantity, $new_total, $existing_cart_item['order_id']]);
    } else {
        // Add new item to cart
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, product_id, quantity, status, total_price, order_date) 
            VALUES (?, ?, ?, 'cart', ?, NOW())
        ");
        $stmt->execute([$user_id, $product_id, $quantity, $total_price]);
    }
    
    // Get cart count (number of different products in cart)
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'cart'");
    $stmt->execute([$user_id]);
    $cart_count = $stmt->fetchColumn() ?: 0;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Product added to cart successfully!',
        'cart_count' => $cart_count
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding product to cart: ' . $e->getMessage()]);
}
?>
