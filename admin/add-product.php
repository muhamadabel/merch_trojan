<?php
$page_title = 'Add Product';
require_once '../includes/admin-header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    
    if (empty($name) || $price <= 0 || $stock < 0) {
        $error = 'Please fill in all required fields correctly.';
    } else {
        try {
            // Handle file upload if provided
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/uploads/products/';
                
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
                    $filename = 'product_' . time() . '_' . uniqid() . '.' . $extension;
                    $upload_path = $upload_dir . $filename;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        // Simpan path relatif dari root (tanpa ../)
                        $image_url = 'assets/uploads/products/' . $filename;
                    } else {
                        $error = 'Failed to upload image.';
                    }
                }
            }
            
            if (empty($error)) {
                // Insert product
                $stmt = $pdo->prepare("
                    INSERT INTO products (name, price, stock, description, image_url) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $price, $stock, $description, $image_url]);
                
                $success = 'Product added successfully!';
                
                // Clear form data
                $name = $description = $image_url = '';
                $price = $stock = 0;
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<div class="admin-header">
    <h1 class="page-title">Add Product</h1>
    <div class="admin-actions">
        <a href="products.php" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Products
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
        <h2 class="card-title">Product Information</h2>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name *</label>
                <input type="text" id="name" name="name" class="form-control" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="price">Price (Rp) *</label>
                <input type="number" id="price" name="price" class="form-control" min="0" step="1000" required value="<?php echo $price ?? 0; ?>">
            </div>
            
            <div class="form-group">
                <label for="stock">Stock *</label>
                <input type="number" id="stock" name="stock" class="form-control" min="0" required value="<?php echo $stock ?? 0; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
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
                <input type="text" id="image_url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($image_url ?? ''); ?>" placeholder="https://example.com/image.jpg">
                <small>If you don't upload an image, you can provide a URL to an existing image</small>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
