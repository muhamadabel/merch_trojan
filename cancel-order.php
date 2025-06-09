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

$order_id = intval($_POST['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($order_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

try {
    // Check if order exists and belongs to user
    $stmt = $pdo->prepare("
        SELECT o.*, p.product_id 
        FROM orders o 
        LEFT JOIN products p ON o.product_id = p.product_id 
        WHERE o.order_id = ? AND o.user_id = ? AND o.status = 'pending'
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();
    
    if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found or cannot be cancelled']);
        exit;
    }
    
    $pdo->beginTransaction();
    
    // Restore product stock if product exists
    if ($order['product_id']) {
        $stmt = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE product_id = ?");
        $stmt->execute([$order['quantity'], $order['product_id']]);
    }
    
    // Delete the order
    $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully']);
    
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error cancelling order: ' . $e->getMessage()]);
}
?>
