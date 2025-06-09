<?php
$page_title = 'Edit Post';
require_once '../includes/admin-header.php';

$error = '';
$success = '';

// Get post ID
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Check if post exists
$stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

if (!$post) {
    redirect('posts.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim($_POST['caption']);
    $image_url = trim($_POST['image_url']) ?: $post['image_url']; // Keep existing image if not provided
    
    if (empty($caption)) {
        $error = 'Please enter a caption for the post.';
    } else {
        try {
            // Handle file upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/uploads/posts/';
                
                // Create directory if it doesn't exist
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_info = pathinfo($_FILES['image']['name']);
                $extension = strtolower($file_info['extension']);
                
                // Validate file type
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($extension, $allowed_types)) {
                    $error = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
                } else {
                    // Generate unique filename
                    $filename = 'post_' . time() . '_' . uniqid() . '.' . $extension;
                    $upload_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        // Delete old image if exists and is a local file
                        if ($post['image_url'] && file_exists($post['image_url']) && strpos($post['image_url'], 'assets/') === 0) {
                            unlink($post['image_url']);
                        }
                        
                        // Simpan path relatif dari root (tanpa ../)
                        $image_url = 'assets/uploads/posts/' . $filename;
                    } else {
                        $error = 'Failed to upload image.';
                    }
                }
            }
            
            if (empty($error)) {
                // Update post
                $stmt = $pdo->prepare("
                    UPDATE posts 
                    SET caption = ?, image_url = ? 
                    WHERE post_id = ?
                ");
                $stmt->execute([$caption, $image_url, $post_id]);
                
                $success = 'Post updated successfully!';
                
                // Refresh post data
                $stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
                $stmt->execute([$post_id]);
                $post = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<div class="admin-header">
    <h1 class="page-title">Edit Post</h1>
    <div class="admin-actions">
        <a href="posts.php" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Posts
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Edit Post</h2>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="caption">Caption *</label>
                <textarea id="caption" name="caption" class="form-control" rows="4" required><?php echo htmlspecialchars($post['caption']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Current Image</label>
                <?php if ($post['image_url']): ?>
                    <div>
                        <img src="<?php echo $post['image_url']; ?>" alt="Post Image" class="image-preview">
                    </div>
                <?php else: ?>
                    <p>No image available</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="image">Change Post Image</label>
                <div class="file-input-wrapper">
                    <input type="file" id="image" name="image" class="file-input" accept="image/jpeg,image/jpg,image/png,image/gif" onchange="previewImage(this, 'imagePreview')">
                    <label for="image" class="file-input-label">Choose File</label>
                </div>
                <small>Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                <div style="margin-top: 10px;">
                    <img id="imagePreview" src="#" alt="Image Preview" style="max-width: 200px; max-height: 200px; display: none;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="image_url">Or Image URL</label>
                <input type="text" id="image_url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($post['image_url']); ?>" placeholder="https://example.com/image.jpg">
                <small>If you don't upload an image, you can provide a URL to an existing image</small>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Update Post
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
