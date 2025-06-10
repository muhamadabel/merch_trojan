<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

// Debug: Log all incoming data
error_log("POST data: " . print_r($_POST, true));
error_log("Session data: " . print_r($_SESSION, true));

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

// Debug: Log processed data
error_log("Processed - post_id: $post_id, content: $content, user_id: $user_id");

if ($post_id <= 0 || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID or empty comment', 'debug' => ['post_id' => $post_id, 'content' => $content]]);
    exit;
}

try {
    // Check if post exists
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Post not found', 'post_id' => $post_id]);
        exit;
    }
    
    // Add comment
    $stmt = $pdo->prepare("
        INSERT INTO comments (post_id, user_id, content, created_at) 
        VALUES (?, ?, ?, NOW())
    ");
    $result = $stmt->execute([$post_id, $user_id, $content]);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert comment', 'error' => $stmt->errorInfo()]);
        exit;
    }
    
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
    error_log("Error adding comment: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error adding comment: ' . $e->getMessage()]);
}
?>
