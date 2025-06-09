<?php
$page_title = 'Login';
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$debug_info = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            // Debug info
            $debug_info = "Trying to login with email: " . $email;
            
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Debug info
                $debug_info .= "<br>User found in database";
                
                // For testing, allow direct login with password123
                if ($password === 'password123' || password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Debug info
                    $debug_info .= "<br>Login successful, redirecting...";
                    
                    redirect('index.php');
                } else {
                    $error = 'Invalid password';
                    // Debug info
                    $debug_info .= "<br>Password verification failed";
                }
            } else {
                $error = 'Email not found';
                // Debug info
                $debug_info .= "<br>User not found in database";
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="form-container">
            <h1 style="text-align: center; margin-bottom: 2rem;">Login</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            
            <p style="text-align: center; margin-top: 1rem;">
                Don't have an account? <a href="register.php" style="color: #dc2626;">Register here</a>
            </p>
            
            <div style="margin-top: 1rem; padding: 1rem; background: #f0f0f0; border-radius: 5px; font-size: 0.9rem;">
                <strong>Demo Accounts:</strong><br>
                Admin: admin@trojan.ac.id / password123<br>
                Customer: john@student.ac.id / password123
            </div>
            
            <?php if (!empty($debug_info) && isset($_GET['debug'])): ?>
                <div style="margin-top: 1rem; padding: 1rem; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; font-size: 0.9rem;">
                    <strong>Debug Info:</strong><br>
                    <?php echo $debug_info; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
