<?php
$page_title = 'Social';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get posts
$stmt = $pdo->prepare("
    SELECT p.*, u.name as admin_name 
    FROM posts p 
    JOIN users u ON p.admin_id = u.user_id 
    ORDER BY p.created_at DESC
");
$stmt->execute();
$posts = $stmt->fetchAll();
?>
<?php include 'includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <div class="section-title">
                <h1>Trojan Community</h1>
                <p>Stay updated with the latest news and updates from Trojan community</p>
            </div>
            
            <div class="posts-container">
                <?php if (empty($posts)): ?>
                    <div class="text-center" style="padding: 3rem 0;">
                        <p style="font-size: 1.2rem; color: #666;">No posts available yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="post">
                            <div class="post-header">
                                <div class="post-avatar">
                                    <img src="./assets/images/trojan-logo.png" alt="Trojan Logo" 
                                         style="width: 40px; height: 40px; object-fit: contain; border-radius: 50%;"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display:none; width: 40px; height: 40px; background: #22c55e; border-radius: 50%; align-items: center; justify-content: center; font-weight: bold; color: #000000;">T</div>
                                </div>
                                <div>
                                    <div class="post-author"><?php echo htmlspecialchars($post['admin_name']); ?></div>
                                    <div class="post-date"><?php echo timeAgo($post['created_at']); ?></div>
                                </div>
                            </div>
                            
                            <?php if ($post['image_url']): ?>
                                <img src="<?php echo $post['image_url']; ?>" alt="Post image" class="post-image">
                            <?php endif; ?>
                            
                            <div class="post-content">
                                <p><?php echo nl2br(htmlspecialchars($post['caption'])); ?></p>
                            </div>
                            
                            <div class="post-actions">
                                <button class="post-action comment-button" onclick="toggleComments(<?php echo $post['post_id']; ?>)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                    </svg>
                                    <span>Comment</span>
                                </button>
                            </div>
                            
                            <div id="comments-<?php echo $post['post_id']; ?>" class="comments">
                                <div id="comments-list-<?php echo $post['post_id']; ?>" class="comments-list">
                                    <!-- Comments will be loaded here -->
                                </div>
                                
                                <?php if (isLoggedIn()): ?>
                                    <div class="comment-form">
                                        <div class="comment-avatar"><?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?></div>
                                        <form onsubmit="addComment(event, <?php echo $post['post_id']; ?>)">
                                            <input type="text" placeholder="Write a comment..." class="comment-input" id="comment-input-<?php echo $post['post_id']; ?>" required maxlength="500">
                                            <button type="submit" class="btn btn-sm btn-primary">Post</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <div class="login-to-comment">
                                        <a href="login.php" class="btn btn-sm btn-outline">Login to comment</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
