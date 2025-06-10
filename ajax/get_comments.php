<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$post_id = intval($_GET['post_id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT c.*, u.name as user_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.user_id 
        WHERE c.post_id = ? 
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$post_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($comments);
    
} catch (Exception $e) {
    error_log("Error getting comments: " . $e->getMessage());
    echo json_encode([]);
}
?>
