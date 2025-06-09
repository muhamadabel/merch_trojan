<?php
$page_title = 'My Profile';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get profile data
$stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch();

// Create profile if doesn't exist
if (!$profile) {
    $stmt = $pdo->prepare("INSERT INTO profiles (user_id) VALUES (?)");
    $stmt->execute([$user_id]);
    
    $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $profile = $stmt->fetch();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $photo_url = $profile['photo_url']; // Keep existing photo by default
    
    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/uploads/profiles/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['photo']['name']);
        $extension = strtolower($file_info['extension']);
        
        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($extension, $allowed_types)) {
            $error = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        } else {
            // Validate file size (max 5MB)
            if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
                $error = 'File size must be less than 5MB.';
            } else {
                // Generate unique filename
                $filename = 'profile_' . $user_id . '_' . time() . '.' . $extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                    // Delete old photo if exists
                    if ($profile['photo_url'] && file_exists($profile['photo_url'])) {
                        unlink($profile['photo_url']);
                    }
                    $photo_url = $upload_path;
                } else {
                    $error = 'Failed to upload photo.';
                }
            }
        }
    }
    
    if (empty($error)) {
        try {
            $pdo->beginTransaction();
            
            // Update user name
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE user_id = ?");
            $stmt->execute([$name, $user_id]);
            
            // Update profile
            $stmt = $pdo->prepare("UPDATE profiles SET photo_url = ?, address = ?, phone = ? WHERE user_id = ?");
            $stmt->execute([$photo_url, $address, $phone, $user_id]);
            
            $pdo->commit();
            
            // Update session
            $_SESSION['name'] = $name;
            
            $message = 'Profile updated successfully!';
            
            // Refresh profile data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT * FROM profiles WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $profile = $stmt->fetch();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to update profile. Please try again.';
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

<section class="section">
    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h1>My Profile</h1>
                <p>Manage your account information and preferences</p>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="profile-content">
                <div class="profile-sidebar">
                    <div class="profile-photo-section">
                        <div class="current-photo">
                            <?php if ($profile['photo_url'] && file_exists($profile['photo_url'])): ?>
                                <img src="<?php echo $profile['photo_url']; ?>" alt="Profile Photo" class="profile-photo">
                            <?php else: ?>
                                <div class="profile-photo-placeholder">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="photo-info">
                            <h3><?php echo htmlspecialchars($user['name']); ?></h3>
                            <p class="user-role"><?php echo ucfirst($user['role']); ?></p>
                            <p class="user-email"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                    </div>
                    
                    <div class="profile-stats">
                        <?php
                        // Get user stats
                        $stmt = $pdo->prepare("SELECT COUNT(*) as order_count FROM orders WHERE user_id = ? AND status != 'cart'");
                        $stmt->execute([$user_id]);
                        $order_count = $stmt->fetchColumn();
                        
                        $stmt = $pdo->prepare("SELECT COUNT(*) as comment_count FROM comments WHERE user_id = ?");
                        $stmt->execute([$user_id]);
                        $comment_count = $stmt->fetchColumn();
                        ?>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $order_count; ?></span>
                            <span class="stat-label">Orders</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo $comment_count; ?></span>
                            <span class="stat-label">Comments</span>
                        </div>
                    </div>
                </div>
                
                <div class="profile-form-section">
                    <form method="POST" enctype="multipart/form-data" class="profile-form">
                        <div class="form-section">
                            <h3>Personal Information</h3>
                            
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                <small class="form-text">Email cannot be changed</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>" 
                                       placeholder="e.g., +62 812-3456-7890">
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea id="address" name="address" class="form-control" rows="3" 
                                          placeholder="Enter your full address"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-section">
                            <h3>Profile Photo</h3>
                            
                            <div class="form-group">
                                <label for="photo">Upload New Photo</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="photo" name="photo" class="file-input" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif">
                                    <label for="photo" class="file-upload-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                            <polyline points="17,8 12,3 7,8"></polyline>
                                            <line x1="12" y1="3" x2="12" y2="15"></line>
                                        </svg>
                                        Choose Photo
                                    </label>
                                </div>
                                <small class="form-text">Max file size: 5MB. Supported formats: JPG, PNG, GIF</small>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17,21 17,13 7,13 7,21"></polyline>
                                    <polyline points="7,3 7,8 15,8"></polyline>
                                </svg>
                                Save Changes
                            </button>
                            <a href="index.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Profile Page Styling */
.profile-container {
    max-width: 1000px;
    margin: 0 auto;
}

.profile-header {
    text-align: center;
    margin-bottom: 3rem;
}

.profile-header h1 {
    font-size: 2.5rem;
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.profile-header p {
    color: #6b7280;
    font-size: 1.1rem;
}

.profile-content {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 3rem;
    margin-top: 2rem;
}

/* Profile Sidebar */
.profile-sidebar {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    height: fit-content;
    position: sticky;
    top: 100px;
}

.profile-photo-section {
    text-align: center;
    margin-bottom: 2rem;
}

.current-photo {
    margin-bottom: 1rem;
}

.profile-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #22c55e;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.2);
}

.profile-photo-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 3rem;
    font-weight: bold;
    margin: 0 auto;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.2);
}

.photo-info h3 {
    color: #1f2937;
    margin-bottom: 0.5rem;
    font-size: 1.3rem;
    font-weight: 600;
}

.user-role {
    color: #22c55e;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.user-email {
    color: #6b7280;
    font-size: 0.9rem;
}

.profile-stats {
    display: flex;
    justify-content: space-around;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #22c55e;
}

.stat-label {
    font-size: 0.8rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Profile Form */
.profile-form-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.form-section {
    margin-bottom: 2.5rem;
}

.form-section h3 {
    color: #1f2937;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
    font-weight: 600;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #22c55e;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #1f2937;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

.form-control:disabled {
    background: #f3f4f6;
    color: #6b7280;
    cursor: not-allowed;
}

.form-text {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

/* File Upload */
.file-upload-wrapper {
    position: relative;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.file-upload-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #f8fafc;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #6b7280;
    font-weight: 500;
}

.file-upload-label:hover {
    border-color: #22c55e;
    background: rgba(34, 197, 94, 0.05);
    color: #22c55e;
}

.file-input:focus + .file-upload-label {
    border-color: #22c55e;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 600;
    white-space: nowrap;
}

.btn-primary {
    background: #22c55e;
    color: white;
}

.btn-primary:hover {
    background: #16a34a;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
}

.btn-outline {
    background: transparent;
    color: #6b7280;
    border: 2px solid #d1d5db;
}

.btn-outline:hover {
    color: #22c55e;
    border-color: #22c55e;
    background: rgba(34, 197, 94, 0.05);
}

/* Alerts */
.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    text-align: center;
    font-weight: 500;
}

.alert-success {
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .profile-sidebar {
        position: static;
    }
    
    .profile-stats {
        justify-content: space-between;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>

<script>
// File upload preview
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const currentPhoto = document.querySelector('.current-photo');
            const existingImg = currentPhoto.querySelector('.profile-photo');
            const placeholder = currentPhoto.querySelector('.profile-photo-placeholder');
            
            if (existingImg) {
                existingImg.src = e.target.result;
            } else if (placeholder) {
                placeholder.style.display = 'none';
                const newImg = document.createElement('img');
                newImg.src = e.target.result;
                newImg.className = 'profile-photo';
                newImg.alt = 'Profile Photo';
                currentPhoto.appendChild(newImg);
            }
        };
        reader.readAsDataURL(file);
        
        // Update file label
        const label = document.querySelector('.file-upload-label');
        label.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2 4-4"></path>
                <path d="M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9c2.12 0 4.07.74 5.61 1.98"></path>
            </svg>
            ${file.name}
        `;
    }
});

// Form validation
document.querySelector('.profile-form').addEventListener('submit', function(e) {
    const name = document.getElementById('name').value.trim();
    const phone = document.getElementById('phone').value.trim();
    
    if (!name) {
        e.preventDefault();
        showNotification('Name is required', 'error');
        return;
    }
    
    if (phone && !/^[\+]?[0-9\s\-$$$$]+$/.test(phone)) {
        e.preventDefault();
        showNotification('Please enter a valid phone number', 'error');
        return;
    }
});

// Show notification function
function showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    `;

    switch (type) {
        case "success":
            notification.style.backgroundColor = "#22c55e";
            break;
        case "error":
            notification.style.backgroundColor = "#374151";
            break;
        default:
            notification.style.backgroundColor = "#22c55e";
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = "slideOut 0.3s ease";
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement("style");
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>
