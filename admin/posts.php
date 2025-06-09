<?php
$page_title = 'Posts';
require_once '../includes/admin-header.php';

// Handle delete post
if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    
    // Check if post exists
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if ($post) {
        // Delete post
        $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->execute([$post_id]);
        
        $success_message = "Post deleted successfully!";
    } else {
        $error_message = "Post not found!";
    }
}

// Get search term
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get posts
$query = "
    SELECT p.*, u.name as admin_name 
    FROM posts p 
    JOIN users u ON p.admin_id = u.user_id
";
$params = [];

if (!empty($search)) {
    $query .= " WHERE p.caption LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll();
?>

<div class="admin-header">
    <h1 class="page-title">Posts</h1>
    <div class="admin-actions">
        <a href="add-post.php" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            Add Post
        </a>
    </div>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php endif; ?>

<!-- Search Box -->
<div class="search-box">
    <svg xmlns="http://www.w3.org/2000/svg" class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="11" cy="11" r="8"></circle>
        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
    </svg>
    <form method="GET" action="">
        <input type="text" name="search" class="search-input" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>">
    </form>
</div>

<!-- Posts Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Caption</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No posts found</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?php echo $post['post_id']; ?></td>
                                <td>
                                    <?php if ($post['image_url']): ?>
                                        <img src="<?php echo $post['image_url']; ?>" alt="Post Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <div style="width: 50px; height: 50px; background: #374151; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">No img</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $caption = htmlspecialchars($post['caption']);
                                    echo strlen($caption) > 50 ? substr($caption, 0, 50) . '...' : $caption;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($post['admin_name']); ?></td>
                                <td><?php echo date('d M Y', strtotime($post['created_at'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit-post.php?id=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-secondary">Edit</a>
                                        <a href="posts.php?delete=<?php echo $post['post_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete('Are you sure you want to delete this post?')">Delete</a>
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
