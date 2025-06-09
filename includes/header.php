<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Trojan' : 'Trojan'; ?></title>
    <link rel="stylesheet" href="./assets/css/style.css?v=<?php echo time(); ?>">
    <style>
        /* Reset & Base - ULTRA DARK THEME */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: #e2e8f0;
    background: #050505;
    min-height: 100vh;
}

/* Header - ULTRA DARK */
.header {
    background: #0a0a0a;
    border-bottom: 1px solid rgba(34, 197, 94, 0.15);
    padding: 0.75rem 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 60px;
}

/* Logo Section - ULTRA DARK */
.logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: #ffffff;
    transition: all 0.2s ease;
}

.logo:hover {
    color: #22c55e;
}

.logo-image {
    width: 40px;
    height: 40px;
    object-fit: contain;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.logo:hover .logo-image {
    transform: scale(1.05);
}

.logo-fallback {
    width: 40px;
    height: 40px;
    background: #22c55e;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #000000;
    font-weight: bold;
    font-size: 1.2rem;
    transition: all 0.2s ease;
}

.logo:hover .logo-fallback {
    background: #16a34a;
    transform: scale(1.05);
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.025em;
}

/* Navigation - ULTRA DARK */
.nav {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.nav li {
    position: relative;
}

.nav a {
    color: #9ca3af;
    text-decoration: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    font-weight: 500;
    font-size: 0.9rem;
    display: block;
    position: relative;
}

.nav a:hover {
    color: #22c55e;
    background: rgba(34, 197, 94, 0.05);
}

.nav a.active {
    color: #22c55e;
    background: rgba(34, 197, 94, 0.08);
    font-weight: 600;
}

/* Auth Section - ULTRA DARK */
.auth-section {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    transition: all 0.2s ease;
    font-size: 0.9rem;
    font-weight: 500;
    white-space: nowrap;
}

.btn-primary {
    background: #22c55e;
    color: #000000;
}

.btn-primary:hover {
    background: #16a34a;
}

.btn-outline {
    background: transparent;
    color: #9ca3af;
    border: 1px solid rgba(34, 197, 94, 0.3);
}

.btn-outline:hover {
    color: #22c55e;
    border-color: #22c55e;
    background: rgba(34, 197, 94, 0.05);
}

/* Profile Dropdown - ULTRA DARK */
.profile-dropdown {
    position: relative;
    display: inline-block;
}

.profile-trigger {
    background: #111111;
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.15s ease;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.profile-trigger:hover {
    background: #161616;
    border-color: rgba(34, 197, 94, 0.4);
}

.profile-avatar {
    width: 32px;
    height: 32px;
    background: #22c55e;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
    color: #000000;
    flex-shrink: 0;
}

.profile-name {
    font-weight: 500;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    color: #ffffff;
}

.dropdown-arrow {
    transition: transform 0.15s ease;
    color: #22c55e;
    flex-shrink: 0;
    stroke-width: 2.5;
}

.profile-trigger.active .dropdown-arrow {
    transform: rotate(180deg);
}

/* Dropdown Menu - ULTRA DARK */
.dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: #111111;
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    min-width: 240px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-8px);
    transition: all 0.15s ease;
    z-index: 1000;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-header {
    padding: 1rem;
    border-bottom: 1px solid rgba(34, 197, 94, 0.1);
    background: #0a0a0a;
}

.user-info .user-name {
    font-weight: 600;
    color: #ffffff;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.user-info .user-email {
    font-size: 0.8rem;
    color: #9ca3af;
}

.dropdown-divider {
    height: 1px;
    background: rgba(34, 197, 94, 0.1);
    margin: 0.5rem 0;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: #e5e7eb;
    text-decoration: none;
    transition: all 0.1s ease;
    font-size: 0.9rem;
    font-weight: 500;
}

.dropdown-item:hover {
    background: rgba(34, 197, 94, 0.05);
    color: #22c55e;
}

.dropdown-item svg {
    opacity: 0.7;
    transition: all 0.1s ease;
    width: 16px;
    height: 16px;
    stroke-width: 2;
}

.dropdown-item:hover svg {
    opacity: 1;
    stroke: #22c55e;
}

.dropdown-item.logout {
    color: #f87171;
    border-top: 1px solid rgba(34, 197, 94, 0.1);
    margin-top: 0.5rem;
}

.dropdown-item.logout:hover {
    background: rgba(239, 68, 68, 0.05);
    color: #ef4444;
}

.dropdown-item.logout:hover svg {
    stroke: #ef4444;
}

/* Guest Links - ULTRA DARK */
.guest-links {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

/* Main Nav */
.main-nav {
    flex-grow: 1;
}

.main-nav .nav {
    justify-content: center;
}

/* Mobile Responsive - ULTRA DARK */
@media (max-width: 768px) {
    .header-content {
        height: auto;
        flex-direction: column;
        gap: 1rem;
        padding: 0.5rem 0;
    }

    .logo-text {
        font-size: 1.25rem;
    }

    .nav {
        gap: 0.25rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .nav a {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    .auth-section {
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }

    .profile-name {
        display: none;
    }

    .profile-trigger {
        padding: 0.5rem;
        gap: 0.5rem;
    }

    .dropdown-menu {
        right: -10px;
        min-width: 200px;
    }

    .dropdown-arrow {
        width: 18px;
        height: 18px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 0.75rem;
    }

    .logo-image, .logo-fallback {
        width: 32px;
        height: 32px;
    }

    .logo-text {
        font-size: 1.1rem;
    }

    .dropdown-menu {
        right: -20px;
        min-width: 180px;
    }
}
    </style>
    <script>
        function toggleProfileMenu() {
            var dropdown = document.getElementById("profileDropdown");
            var trigger = document.querySelector(".profile-trigger");
            
            if (dropdown.classList.contains("show")) {
                dropdown.classList.remove("show");
                trigger.classList.remove("active");
            } else {
                dropdown.classList.add("show");
                trigger.classList.add("active");
            }
        }

        // Close dropdown when clicking outside - optimized
        document.addEventListener('click', function(event) {
            var dropdown = document.querySelector('.profile-dropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                var dropdownMenu = document.getElementById("profileDropdown");
                var trigger = document.querySelector(".profile-trigger");
                if (dropdownMenu) {
                    dropdownMenu.classList.remove("show");
                }
                if (trigger) {
                    trigger.classList.remove("active");
                }
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                var dropdownMenu = document.getElementById("profileDropdown");
                var trigger = document.querySelector(".profile-trigger");
                if (dropdownMenu) {
                    dropdownMenu.classList.remove("show");
                }
                if (trigger) {
                    trigger.classList.remove("active");
                }
            }
        });
    </script>
</head>
<body>
    <header class="header">
        <div class="container">
            
<div class="header-content">
    <!-- Logo Section -->
    <a href="index.php" class="logo">
        <img src="./assets/images/trojan-logo.png" alt="Trojan Logo" class="logo-image" 
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="logo-fallback" style="display:none;">T</div>
        <span class="logo-text">TROJAN</span>
    </a>
    
    <!-- Main Navigation -->
    <nav class="main-nav">
        <ul class="nav">
            <li><a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
            <li><a href="products.php" <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'class="active"' : ''; ?>>Products</a></li>
            <li><a href="social.php" <?php echo basename($_SERVER['PHP_SELF']) == 'social.php' ? 'class="active"' : ''; ?>>Info</a></li>
        </ul>
    </nav>
    
    <!-- Auth Section -->
    <div class="auth-section">
        <?php if (isLoggedIn()): ?>
            <!-- Profile Dropdown -->
            <div class="profile-dropdown">
                <button class="profile-trigger" onclick="toggleProfileMenu()">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                    </div>
                    <span class="profile-name"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <!-- Arrow yang lebih jelas -->
                    <svg class="dropdown-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>
                </button>
                
                <div class="dropdown-menu" id="profileDropdown">
                    <div class="dropdown-header">
                        <div class="user-info">
                            <div class="user-name"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                            <div class="user-email"><?php echo htmlspecialchars($_SESSION['email']); ?></div>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="profile.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Edit Profile
                    </a>
                    <a href="cart.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                        </svg>
                        Shopping Cart
                    </a>
                    <a href="my-orders.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14,2 14,8 20,8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10,9 9,9 8,9"></polyline>
                        </svg>
                        My Orders
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="logout.php" class="dropdown-item logout">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16,17 21,12 16,7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Guest Links -->
            <div class="guest-links">
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            </div>
        <?php endif; ?>
    </div>
</div>

        </div>
    </header>
    
    <!-- Main content will be inserted here by each page -->
