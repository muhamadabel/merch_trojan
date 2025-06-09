<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

// Redirect jika tidak login atau bukan admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get current page for active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - Admin Trojan' : 'Admin Trojan'; ?></title>
    <style>
        /* Admin Panel - ULTRA DARK THEME */
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
            display: flex;
        }

        /* Admin Sidebar */
        .admin-sidebar {
            width: 250px;
            background: #0a0a0a;
            border-right: 1px solid rgba(34, 197, 94, 0.15);
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(34, 197, 94, 0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            object-fit: contain;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .logo-fallback {
            width: 32px;
            height: 32px;
            background: #22c55e;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #000000;
        }

        .logo-text {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-category {
            color: #9ca3af;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem 0.5rem;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: #e2e8f0;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
        }

        .menu-item:hover {
            background: rgba(34, 197, 94, 0.05);
            color: #22c55e;
        }

        .menu-item.active {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            font-weight: 500;
        }

        .menu-item.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #22c55e;
        }

        .menu-icon {
            width: 18px;
            height: 18px;
            opacity: 0.8;
        }

        .menu-item:hover .menu-icon,
        .menu-item.active .menu-icon {
            opacity: 1;
        }

        /* Admin Content */
        .admin-content {
            flex: 1;
            margin-left: 250px;
            padding: 2rem;
            max-width: calc(100% - 250px);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(34, 197, 94, 0.15);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
        }

        .admin-actions {
            display: flex;
            gap: 0.75rem;
        }

        /* Cards & Tables */
        .card {
            background: #111111;
            border-radius: 8px;
            border: 1px solid rgba(34, 197, 94, 0.15);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(34, 197, 94, 0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #ffffff;
        }

        .card-body {
            padding: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: #1a1a1a;
            border-radius: 8px;
            padding: 1.5rem;
            border: 1px solid rgba(34, 197, 94, 0.1);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(34, 197, 94, 0.3);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #22c55e;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #9ca3af;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(34, 197, 94, 0.1);
        }

        th {
            font-weight: 600;
            color: #9ca3af;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody tr {
            transition: all 0.2s ease;
        }

        tbody tr:hover {
            background: rgba(34, 197, 94, 0.05);
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #ffffff;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            background: #1a1a1a;
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 6px;
            color: #ffffff;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: rgba(34, 197, 94, 0.5);
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.1);
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: #22c55e;
            color: #000000;
        }

        .btn-primary:hover {
            background: #16a34a;
        }

        .btn-secondary {
            background: #374151;
            color: #ffffff;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: #dc2626;
            color: #ffffff;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        /* Utilities */
        .text-success {
            color: #22c55e;
        }

        .text-danger {
            color: #dc2626;
        }

        .text-warning {
            color: #eab308;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .badge-warning {
            background: rgba(234, 179, 8, 0.1);
            color: #eab308;
        }

        .badge-danger {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
        }

        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #dc2626;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 70px;
                overflow: hidden;
            }

            .sidebar-header {
                padding: 1rem;
                justify-content: center;
            }

            .logo-text, .menu-text, .menu-category {
                display: none;
            }

            .menu-item {
                padding: 0.75rem;
                justify-content: center;
            }

            .admin-content {
                margin-left: 70px;
                max-width: calc(100% - 70px);
            }
        }

        @media (max-width: 480px) {
            .admin-content {
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Image Preview */
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 0.5rem;
            border-radius: 4px;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        /* File Input */
        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .file-input-label {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: #374151;
            color: #ffffff;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-input-label:hover {
            background: #4b5563;
        }

        /* Status Pills */
        .status-pill {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .status-pending {
            background: rgba(234, 179, 8, 0.1);
            color: #eab308;
        }

        .status-inactive {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }

        .pagination-item {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: #1a1a1a;
            color: #e2e8f0;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .pagination-item:hover {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .pagination-item.active {
            background: #22c55e;
            color: #000000;
        }

        /* Search Box */
        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            background: #1a1a1a;
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 6px;
            color: #ffffff;
            font-size: 0.9rem;
        }

        .search-input:focus {
            outline: none;
            border-color: rgba(34, 197, 94, 0.5);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            width: 16px;
            height: 16px;
        }
    </style>
</head>
<body>
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <img src="../assets/images/trojan-logo.png" alt="Trojan Logo" class="logo-icon"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="logo-fallback" style="display:none;">T</div>
                <span class="logo-text">TROJAN ADMIN</span>
            </a>
        </div>
        
        <nav class="sidebar-menu">
            <div class="menu-category">Dashboard</div>
            <a href="index.php" class="menu-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"></rect>
                    <rect x="14" y="3" width="7" height="7"></rect>
                    <rect x="14" y="14" width="7" height="7"></rect>
                    <rect x="3" y="14" width="7" height="7"></rect>
                </svg>
                <span class="menu-text">Dashboard</span>
            </a>
            
            <div class="menu-category">Products</div>
            <a href="products.php" class="menu-item <?php echo $current_page == 'products.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span class="menu-text">All Products</span>
            </a>
            <a href="add-product.php" class="menu-item <?php echo $current_page == 'add-product.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span class="menu-text">Add Product</span>
            </a>
            
            <div class="menu-category">Orders</div>
            <a href="orders.php" class="menu-item <?php echo $current_page == 'orders.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span class="menu-text">All Orders</span>
            </a>
            
            <div class="menu-category">Social</div>
            <a href="posts.php" class="menu-item <?php echo $current_page == 'posts.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                </svg>
                <span class="menu-text">Posts</span>
            </a>
            <a href="add-post.php" class="menu-item <?php echo $current_page == 'add-post.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                </svg>
                <span class="menu-text">Add Post</span>
            </a>
            <a href="comments.php" class="menu-item <?php echo $current_page == 'comments.php' ? 'active' : ''; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                <span class="menu-text">Comments</span>
            </a>
            
            <div class="menu-category">Settings</div>
            <a href="../index.php" class="menu-item">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="menu-text">Back to Site</span>
            </a>
            <a href="../logout.php" class="menu-item">
                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="menu-text">Logout</span>
            </a>
        </nav>
    </aside>
    
    <!-- Admin Content -->
    <main class="admin-content">
