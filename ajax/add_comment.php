<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'];

if ($post_id <= 0 || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID or empty comment']);
    exit;
}

try {
    // Check if post exists
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post not found']);
        exit;
    }
    
    // Add comment
    $stmt = $pdo->prepare("
        INSERT INTO comments (post_id, user_id, content) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$post_id, $user_id, $content]);
    
    // Get the new comment with user info
    $comment_id = $pdo->lastInsertId();
    $stmt = $pdo->prepare("
        SELECT c.*, u.name as user_name 
        FROM comments c 
        JOIN users u ON c.user_id = u.user_id 
        WHERE c.comment_id = ?
    ");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Comment added successfully',
        'comment' => $comment
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding comment']);
}
?>
