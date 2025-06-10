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
        /* Mobile-First Responsive Header */
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
            position: relative;
        }

        /* Logo Section */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
            transition: all 0.2s ease;
            z-index: 1001;
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

        .logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.025em;
        }

        /* Hamburger Menu Button */
        .hamburger {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 1001;
            background: none;
            border: none;
            transition: all 0.3s ease;
        }

        .hamburger span {
            width: 25px;
            height: 3px;
            background: #ffffff;
            margin: 3px 0;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
            background: #22c55e;
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
            background: #22c55e;
        }

        /* Navigation */
        .main-nav {
            flex-grow: 1;
        }

        .nav {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            margin: 0;
            padding: 0;
            justify-content: center;
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

        /* Auth Section */
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

        /* Profile Dropdown */
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

        /* Dropdown Menu */
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

        /* Guest Links */
        .guest-links {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-overlay.show {
            display: block;
            opacity: 1;
        }

        /* ===== RESPONSIVE BREAKPOINTS ===== */

        /* Tablet and Mobile (768px and below) */
        @media (max-width: 768px) {
            .container {
                padding: 0 0.75rem;
            }

            .header-content {
                height: 56px;
            }

            .logo-image, .logo-fallback {
                width: 36px;
                height: 36px;
            }

            .logo-text {
                font-size: 1.25rem;
            }

            /* Show hamburger menu */
            .hamburger {
                display: flex;
                order: 3;
            }

            /* Hide desktop navigation */
            .main-nav {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                background: linear-gradient(180deg, #0a0a0a 0%, #111111 100%);
                border-right: 1px solid rgba(34, 197, 94, 0.2);
                transition: left 0.3s ease;
                z-index: 1000;
                overflow-y: auto;
                box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
            }

            .main-nav.active {
                left: 0;
            }

            .nav {
                flex-direction: column;
                padding: 80px 0 2rem 0;
                gap: 0;
                justify-content: flex-start;
            }

            .nav li {
                width: 100%;
            }

            .nav a {
                padding: 1rem 1.5rem;
                border-radius: 0;
                font-size: 1rem;
                border-bottom: 1px solid rgba(34, 197, 94, 0.1);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .nav a:hover {
                background: rgba(34, 197, 94, 0.1);
                padding-left: 2rem;
            }

            .nav a.active {
                background: rgba(34, 197, 94, 0.15);
                border-left: 4px solid #22c55e;
            }

            /* Auth section in mobile menu */
            .auth-section {
                position: fixed;
                bottom: 0;
                left: -100%;
                width: 280px;
                background: #0a0a0a;
                border-top: 1px solid rgba(34, 197, 94, 0.2);
                border-right: 1px solid rgba(34, 197, 94, 0.2);
                padding: 1.5rem;
                transition: left 0.3s ease;
                z-index: 1000;
                flex-direction: column;
                gap: 1rem;
            }

            .auth-section.active {
                left: 0;
            }

            .guest-links {
                flex-direction: column;
                width: 100%;
                gap: 1rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
                padding: 0.875rem 1rem;
                font-size: 1rem;
            }

            /* Profile dropdown in mobile */
            .profile-dropdown {
                width: 100%;
            }

            .profile-trigger {
                width: 100%;
                justify-content: space-between;
                padding: 1rem 1.5rem;
                background: rgba(34, 197, 94, 0.1);
                border: 1px solid rgba(34, 197, 94, 0.3);
            }

            .profile-name {
                max-width: none;
                display: block;
            }

            .dropdown-menu {
                position: static;
                width: 100%;
                margin-top: 1rem;
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                border: none;
                background: rgba(17, 17, 17, 0.8);
            }

            .dropdown-header {
                background: rgba(10, 10, 10, 0.8);
            }
        }

        /* Mobile Small (480px and below) */
        @media (max-width: 480px) {
            .container {
                padding: 0 0.5rem;
            }

            .header-content {
                height: 52px;
            }

            .logo-image, .logo-fallback {
                width: 32px;
                height: 32px;
            }

            .logo-text {
                font-size: 1.1rem;
            }

            .main-nav,
            .auth-section {
                width: 100%;
                left: -100%;
            }

            .main-nav.active,
            .auth-section.active {
                left: 0;
            }

            .hamburger span {
                width: 22px;
                height: 2px;
            }
        }

        /* Mobile Extra Small (360px and below) */
        @media (max-width: 360px) {
            .container {
                padding: 0 0.25rem;
            }

            .logo-text {
                font-size: 1rem;
            }

            .nav a {
                padding: 0.875rem 1rem;
                font-size: 0.95rem;
            }

            .auth-section {
                padding: 1rem;
            }
        }

        /* Landscape orientation on mobile */
        @media (max-height: 600px) and (orientation: landscape) {
            .nav {
                padding: 60px 0 1rem 0;
            }

            .nav a {
                padding: 0.75rem 1.5rem;
            }

            .auth-section {
                padding: 1rem 1.5rem;
            }
        }

        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .hamburger span {
                height: 2px;
            }
        }

        /* Touch device optimizations */
        @media (hover: none) and (pointer: coarse) {
            .nav a:hover {
                background: none;
                padding-left: 1.5rem;
            }

            .nav a:active {
                background: rgba(34, 197, 94, 0.1);
            }

            .btn:hover {
                transform: none;
            }

            .btn:active {
                transform: scale(0.98);
            }
        }

        /* Accessibility improvements */
        @media (prefers-reduced-motion: reduce) {
            .main-nav,
            .auth-section,
            .hamburger span,
            .dropdown-arrow {
                transition: none;
            }

            .mobile-overlay {
                transition: none;
            }
        }

        /* Focus states for accessibility */
        .hamburger:focus {
            outline: 2px solid #22c55e;
            outline-offset: 2px;
        }

        .nav a:focus {
            outline: 2px solid #22c55e;
            outline-offset: 2px;
        }

        /* Cart badge for mobile */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #22c55e;
            color: #000000;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        /* Mobile menu icons */
        .nav-icon {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }
    </style>
    <script>
        // Mobile menu functionality
        document.addEventListener('DOMContentLoaded', function() {
        // Hamburger menu toggle
        const hamburger = document.querySelector('.hamburger');
        const mainNav = document.querySelector('.main-nav');
        const authSection = document.querySelector('.auth-section');
        const overlay = document.querySelector('.mobile-overlay');
        
        if (hamburger) {
            hamburger.addEventListener('click', function() {
                this.classList.toggle('active');
                mainNav.classList.toggle('active');
                authSection.classList.toggle('active');
                overlay.classList.toggle('show');
                
                // Prevent body scroll when menu is open
                document.body.style.overflow = this.classList.contains('active') ? 'hidden' : '';
            });
        }
        
        // Close mobile menu when clicking overlay
        if (overlay) {
            overlay.addEventListener('click', function() {
                hamburger.classList.remove('active');
                mainNav.classList.remove('active');
                authSection.classList.remove('active');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        }
        
        // Close mobile menu when clicking nav links
        const navLinks = document.querySelectorAll('.nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    hamburger.classList.remove('active');
                    mainNav.classList.remove('active');
                    authSection.classList.remove('active');
                    overlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        });
        
        // Profile dropdown functionality
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
        
        // Setup profile dropdown toggle
        const profileTrigger = document.querySelector(".profile-trigger");
        if (profileTrigger) {
            profileTrigger.addEventListener('click', function(event) {
                event.stopPropagation();
                toggleProfileMenu();
            });
        }
        
        // Close dropdown when clicking outside
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
                hamburger.classList.remove('active');
                mainNav.classList.remove('active');
                authSection.classList.remove('active');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
                
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
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                hamburger.classList.remove('active');
                mainNav.classList.remove('active');
                authSection.classList.remove('active');
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });
</script>
</head>
<body>
    <!-- Mobile Overlay -->
    <div class="mobile-overlay" onclick="closeMobileMenu()"></div>
    
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
                        <li>
                            <a href="index.php" <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'class="active"' : ''; ?>>
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9,22 9,12 15,12 15,22"></polyline>
                                </svg>
                                Home
                            </a>
                        </li>
                        <li>
                            <a href="products.php" <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'class="active"' : ''; ?>>
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                Products
                            </a>
                        </li>
                        <li>
                            <a href="social.php" <?php echo basename($_SERVER['PHP_SELF']) == 'social.php' ? 'class="active"' : ''; ?>>
                                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                                </svg>
                                Info
                            </a>
                        </li>
                    </ul>
                </nav>
                
                <!-- Hamburger Menu Button -->
                <button class="hamburger" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                
                <!-- Auth Section -->
                <div class="auth-section">
                    <?php if (isLoggedIn()): ?>
                        <!-- Profile Dropdown -->
                        <div class="profile-dropdown">
                            <button class="profile-trigger">
                                <div class="profile-avatar">
                                    <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                                </div>
                                <span class="profile-name"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
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
                                <?php if ($_SESSION['role'] == 'admin'): ?>
                                <a href="admin/index.php" class="dropdown-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <path d="M9 9h6v6H9z"></path>
                                    </svg>
                                    Admin Dashboard
                                </a>
                                <?php endif; ?>
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
