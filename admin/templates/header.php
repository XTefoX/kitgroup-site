<?php
// admin/templates/header.php
// Admin Dashboard Header with Sidebar

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /kitgroup/admin/login');
    exit;
}

$admin_name = $_SESSION['admin_full_name'] ?? 'Admin';
$admin_username = $_SESSION['admin_username'] ?? 'admin';
$admin_role = $_SESSION['admin_role'] ?? 'editor';

$page_title = $page_title ?? 'Dashboard – Kit Group Admin';

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

$admin_base = '/kitgroup/admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f0f2f5;
            overflow-x: hidden;
        }
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 260px;
            background: linear-gradient(180deg, #0a1628 0%, #1a2a4a 100%);
            color: #ffffff;
            z-index: 1050;
            transition: transform 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 20px rgba(0,0,0,0.15);
        }
        .admin-sidebar::-webkit-scrollbar { width: 4px; }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .sidebar-brand .brand-icon { font-size: 1.8rem; color: #e63946; }
        .sidebar-brand .brand-text { font-weight: 800; font-size: 1.3rem; letter-spacing: -0.5px; color: #ffffff; }
        .sidebar-brand .brand-text span { color: #e63946; }
        .sidebar-brand .brand-sub { font-size: 0.65rem; color: rgba(255,255,255,0.4); display: block; font-weight: 400; letter-spacing: 1px; text-transform: uppercase; }
        .sidebar-nav { padding: 1.25rem 0; }
        .sidebar-nav .nav-section-title { padding: 0.5rem 1.5rem; font-size: 0.7rem; text-transform: uppercase; color: rgba(255,255,255,0.3); letter-spacing: 1.5px; font-weight: 600; }
        .sidebar-nav .nav-item {
            display: flex;
            align-items: center;
            padding: 0.65rem 1.5rem;
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .sidebar-nav .nav-item:hover { background: rgba(255,255,255,0.05); color: #ffffff; border-left-color: #e63946; }
        .sidebar-nav .nav-item.active { background: rgba(230, 57, 70, 0.15); color: #ffffff; border-left-color: #e63946; }
        .sidebar-nav .nav-item i { font-size: 1.2rem; width: 24px; text-align: center; color: rgba(255,255,255,0.4); }
        .sidebar-nav .nav-item.active i { color: #e63946; }
        .sidebar-nav .nav-item .badge { margin-left: auto; background: #e63946; font-size: 0.65rem; padding: 0.2rem 0.6rem; }
        .admin-main { margin-left: 260px; min-height: 100vh; display: flex; flex-direction: column; }
        .admin-topbar {
            background: #ffffff;
            padding: 0.75rem 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1040;
            border-bottom: 1px solid #e9ecef;
        }
        .admin-topbar .topbar-left { display: flex; align-items: center; gap: 1rem; }
        .admin-topbar .topbar-left .menu-toggle { display: none; background: none; border: none; font-size: 1.5rem; color: #1a1a2e; cursor: pointer; padding: 0.25rem 0.5rem; }
        .admin-topbar .topbar-left .page-title { font-size: 1.1rem; font-weight: 600; color: #1a1a2e; margin: 0; }
        .admin-topbar .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .admin-topbar .user-info { display: flex; align-items: center; gap: 0.75rem; }
        .admin-topbar .user-avatar { width: 36px; height: 36px; border-radius: 50%; background: #e63946; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; }
        .admin-topbar .user-name { font-weight: 600; font-size: 0.9rem; color: #1a1a2e; }
        .admin-topbar .user-role { font-size: 0.7rem; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; }
        .admin-topbar .logout-btn { background: transparent; border: 1px solid #dee2e6; border-radius: 50px; padding: 0.35rem 1rem; color: #6c757d; font-size: 0.85rem; font-weight: 500; text-decoration: none; transition: all 0.2s ease; }
        .admin-topbar .logout-btn:hover { background: #e63946; border-color: #e63946; color: #ffffff; }
        .admin-content { padding: 1.5rem; flex: 1; }
        @media (max-width: 992px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-main { margin-left: 0; }
            .admin-topbar .topbar-left .menu-toggle { display: block; }
        }
        @media (max-width: 576px) {
            .admin-topbar .user-name { display: none; }
            .admin-topbar .user-role { display: none; }
            .admin-topbar .logout-btn span { display: none; }
            .admin-content { padding: 1rem; }
        }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 1045; }
        .sidebar-overlay.show { display: block; }
        .admin-card { background: #ffffff; border-radius: 12px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: box-shadow 0.3s ease; }
        .admin-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .admin-card .card-header { background: transparent; border-bottom: 1px solid #e9ecef; padding: 1rem 1.25rem; font-weight: 600; }
        .text-kit-red { color: #e63946; }
        .bg-kit-red { background: #e63946; }
        .border-kit-red { border-color: #e63946; }

        /* Admin category dropdown styling */
        optgroup {
            font-weight: 700;
            color: #0a1628;
            background: #f8f9fa;
        }

        optgroup option {
            font-weight: 400;
            padding-left: 1.5rem;
            background: #ffffff;
        }

        optgroup option:before {
            content: "└ ";
        }
    </style>
</head>
<body>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <span class="brand-icon"><i class="bi bi-shield-check"></i></span>
        <div>
            <span class="brand-text"><span>KIT</span> GROUP</span>
            <span class="brand-sub">Admin Panel</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-title">Main</div>
        <a href="<?= $admin_base ?>/index.php" class="nav-item <?= $current_page === 'index.php' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>
        <a href="<?= $admin_base ?>/products.php" class="nav-item <?= strpos($current_page, 'product') !== false ? 'active' : '' ?>">
            <i class="bi bi-box-seam"></i> Products
            <?php
            // Count products for badge (only if function exists)
            if (function_exists('getProducts')) {
                $prod_count = count(getProducts());
                echo '<span class="badge">' . $prod_count . '</span>';
            }
            ?>
        </a>
        <a href="<?= $admin_base ?>/brands.php" class="nav-item <?= strpos($current_page, 'brand') !== false ? 'active' : '' ?>">
            <i class="bi bi-tag"></i> Brands
        </a>
        <a href="<?= $admin_base ?>/categories.php" class="nav-item <?= strpos($current_page, 'category') !== false ? 'active' : '' ?>">
            <i class="bi bi-grid"></i> Categories
        </a>
        <div class="nav-section-title mt-3">E-Commerce</div>
        <a href="#" class="nav-item" style="opacity: 0.6; cursor: default;">
            <i class="bi bi-cart"></i> Orders
            <span class="badge bg-secondary" style="background: rgba(255,255,255,0.2) !important;">Soon</span>
        </a>
        <a href="#" class="nav-item" style="opacity: 0.6; cursor: default;">
            <i class="bi bi-people"></i> Customers
            <span class="badge bg-secondary" style="background: rgba(255,255,255,0.2) !important;">Soon</span>
        </a>
        <div class="nav-section-title mt-3">System</div>
        <a href="#" class="nav-item" style="opacity: 0.6; cursor: default;">
            <i class="bi bi-gear"></i> Settings
            <span class="badge bg-secondary" style="background: rgba(255,255,255,0.2) !important;">Soon</span>
        </a>
        <a href="<?= $admin_base ?>/logout.php" class="nav-item" style="border-left-color: transparent; color: rgba(255,255,255,0.4);">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </nav>
</aside>

<div class="admin-main">
    <header class="admin-topbar">
        <div class="topbar-left">
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="page-title"><?= htmlspecialchars($page_title) ?></h1>
        </div>
        <div class="topbar-right">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($admin_name, 0, 1)) ?></div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($admin_name) ?></div>
                    <div class="user-role"><?= htmlspecialchars($admin_role) ?></div>
                </div>
            </div>
            <a href="<?= $admin_base ?>/logout.php" class="logout-btn">
                <i class="bi bi-box-arrow-right"></i> <span>Logout</span>
            </a>
        </div>
    </header>
    <main class="admin-content">