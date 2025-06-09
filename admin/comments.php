<?php
$page_title = 'Comments';
require_once '../includes/admin-header.php';

// Handle delete comment
if (isset($_GET['delete'])) {
    $comment_id = (int)$_GET['delete'];
    
    // Check if comment exists
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE comment_id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();
    
    if ($comment) {
        // Delete comment
        $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = ?");
        $stmt->execute([$comment_id]);
        
        $success_message = "Comment deleted successfully!";
    } else {
        $error_message = "Comment not found!";
    }
}

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get post filter
$post_filter = isset($_GET['post_id']) ? (int)$_GET['post_id'] : 0;

// Get comments
$query = "
    SELECT c.*, u.name as user_name, p.caption as post_caption 
    FROM comments c 
    JOIN users u ON c.user_id = u.user_id 
    JOIN posts p ON c.post_id = p.post_id
    WHERE 1=1
";
$params = [];

if (!empty($search)) {
    $query .= " AND (c.content LIKE ? OR u.name LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($post_filter > 0) {
    $query .= " AND c.post_id = ?";
    $params[] = $post_filter;
}

$query .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$comments = $stmt->fetchAll();

// Get all posts for filter dropdown
$stmt = $pdo->query("SELECT post_id, caption FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1 class="page-title">Comments</h1>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<!-- Search and Filter -->
<div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
    <div class="search-box" style="flex: 1;">
        <svg xmlns="http://www.w3.org/2000/svg" class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle>
            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
        </svg>
        <form method="GET" action="">
            <?php if ($post_filter > 0): ?>
                <input type="hidden" name="post_id" value="<?php echo $post_filter; ?>">
            <?php endif; ?>
            <input type="text" name="search" class="search-input" placeholder="Search comments..." value="<?php echo htmlspecialchars($search); ?>">
        </form>
    </div>
    
    <div>
        <form method="GET" action="" style="display: flex; gap: 0.5rem;">
            <?php if (!empty($search)): ?>
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
            <?php endif; ?>
            <select name="post_id" class="form-control" onchange="this.form.submit()">
                <option value="0">All Posts</option>
                <?php foreach ($posts as $post): ?>
                    <?php 
                    $caption = htmlspecialchars($post['caption']);
                    $caption = strlen($caption) > 30 ? substr($caption, 0, 30) . '...' : $caption;
                    ?>
                    <option value="<?php echo $post['post_id']; ?>" <?php echo $post_filter === $post['post_id'] ? 'selected' : ''; ?>>
                        Post #<?php echo $post['post_id']; ?>: <?php echo $caption; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($post_filter > 0): ?>
                <a href="comments.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">Clear</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Comments Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Comment</th>
                        <th>Post</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No comments found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <tr>
                                <td><?php echo $comment['comment_id']; ?></td>
                                <td><?php echo htmlspecialchars($comment['user_name']); ?></td>
                                <td>
                                    <?php 
                                    $content = htmlspecialchars($comment['content']);
                                    echo strlen($content) > 50 ? substr($content, 0, 50) . '...' : $content;
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    $post_caption = htmlspecialchars($comment['post_caption']);
                                    echo strlen($post_caption) > 30 ? substr($post_caption, 0, 30) . '...' : $post_caption;
                                    ?>
                                </td>
                                <td><?php echo date('d M Y', strtotime($comment['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="comments.php?delete=<?php echo $comment['comment_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete('Are you sure you want to delete this comment?')">Delete</a>
                                    </div>
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
