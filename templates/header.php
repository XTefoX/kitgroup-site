<?php
// templates/header.php
// This file is included at the top of every page

// Start session if not already started (for admin login status)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine if user is logged in (admin)
$is_admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Default meta values if not set by the page
$page_title = $page_title ?? 'Kit Group – Premium PPE & Workwear Solutions';
$page_desc = $page_desc ?? 'Kit Group provides high-quality PPE, workwear, and safety solutions for mining, construction, and industry across South Africa.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="http<?= (!empty($_SERVER['HTTPS']) ? 's' : '') ?>://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
    
    <!-- Favicon -->
    <link rel="icon" href="/kitgroup/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts – Inter (clean, professional) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link href="/kitgroup/assets/css/custom.css" rel="stylesheet">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- ============================================================
         SCHEMA MARKUP (SEO – Organization)
         ============================================================ -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Kit Group",
        "url": "http://localhost:8080/kitgroup",
        "logo": "http://localhost:8080/kitgroup/assets/images/logo.png",
        "description": "Premium PPE and workwear supplier in South Africa",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Johannesburg",
            "addressCountry": "ZA"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+27-11-123-4567",
            "contactType": "Sales"
        }
    }
    </script>
</head>
<body>

<!-- ============================================================
     TOP BAR – Utility (Contact info, opening hours)
     ============================================================ -->
<div class="top-bar py-1" style="background: #0a1628; color: rgba(255,255,255,0.7); font-size: 0.8rem; border-bottom: 2px solid #e63946;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <span class="me-3"><i class="bi bi-envelope me-1"></i> sales@kitgroup.com</span>
                <span><i class="bi bi-telephone me-1"></i> +267 390 0886</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span><i class="bi bi-clock me-1"></i> Mon–Fri: 8:00am – 5:00pm</span>
                <?php if ($is_admin_logged_in): ?>
                    <span class="ms-3"><a href="/kitgroup/admin" class="text-white-50 text-decoration-none"><i class="bi bi-shield-lock"></i> Admin</a></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     MAIN HEADER / NAVIGATION
     ============================================================ -->
<header class="main-header" style="background: #ffffff; box-shadow: 0 2px 15px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 1000;">
    <nav class="navbar navbar-expand-lg navbar-light py-0">
        <div class="container">
            <!-- ====== BRAND / LOGO ====== -->
            <a class="navbar-brand py-2" href="/kitgroup/">
    <img src="/kitgroup/assets/images/kitlogo.webp" alt="Kit Group" height="70">
</a>
            
            <!-- ====== TOGGLER (Mobile) ====== -->
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- ====== NAVIGATION LINKS ====== -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0" style="font-weight: 500; font-size: 0.95rem;">
                    <li class="nav-item">
                        <a class="nav-link <?= ($_SERVER['REQUEST_URI'] == '/kitgroup/' || $_SERVER['REQUEST_URI'] == '/kitgroup/index.php') ? 'active' : '' ?>" href="/kitgroup/">
                            <i class="bi bi-house me-1"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/products') !== false) ? 'active' : '' ?>" href="/kitgroup/products">
                            <i class="bi bi-grid me-1"></i> Products
                        </a>
                    </li>
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-grid me-1"></i> Products
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="productsDropdown" style="border-radius: 0; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.12);">
                            <li><a class="dropdown-item" href="/kitgroup/products"><i class="bi bi-grid-3x3-gap me-2"></i>All Products</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/kitgroup/category/coveralls"><i class="bi bi-journal me-2"></i>Coveralls & Workwear</a></li>
                            <li><a class="dropdown-item" href="/kitgroup/category/hi-vis"><i class="bi bi-eye me-2"></i>Hi-Visibility Clothing</a></li>
                            <li><a class="dropdown-item" href="/kitgroup/category/gloves"><i class="bi bi-hand-index me-2"></i>Hand Protection</a></li>
                            <li><a class="dropdown-item" href="/kitgroup/category/footwear"><i class="bi bi-boots me-2"></i>Safety Footwear</a></li>
                            <li><a class="dropdown-item" href="/kitgroup/category/head"><i class="bi bi-shield me-2"></i>Head Protection</a></li>
                            <li><a class="dropdown-item" href="/kitgroup/category/eye"><i class="bi bi-eyeglasses me-2"></i>Eye Protection</a></li>
                        </ul>
                    </li> -->
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/about') !== false) ? 'active' : '' ?>" href="/kitgroup/about">
                            <i class="bi bi-info-circle me-1"></i> About
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/stores') !== false) ? 'active' : '' ?>" href="/kitgroup/stores">
                            <i class="bi bi-geo-alt me-1"></i> Stores
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (strpos($_SERVER['REQUEST_URI'], '/contact') !== false) ? 'active' : '' ?>" href="/kitgroup/contact">
                            <i class="bi bi-envelope me-1"></i> Contact
                        </a>
                    </li>
                </ul>
                
                <!-- ====== RIGHT SIDE: SEARCH + QUOTE ====== -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Search Form -->
                    <form class="d-none d-lg-block" action="/kitgroup/products" method="get" style="position: relative;">
                        <input class="form-control form-control-sm" type="search" name="search" placeholder="Search products..." aria-label="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="width: 160px; border-radius: 50px; padding-left: 35px; border-color: #dee2e6;">
                        <i class="bi bi-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd;"></i>
                    </form>
                    
                    <!-- Quote Icon -->
                    <?php
                    // Get quote count from session
                 
                    $quote_count = 0;
                    if (isset($_SESSION['quote']) && is_array($_SESSION['quote'])) {
                        foreach ($_SESSION['quote'] as $item) {
                            $quote_count += $item['quantity'] ?? 0;
                        }
                    }
                    ?>
                    <a href="/kitgroup/quote.php" class="btn btn-outline-danger btn-sm position-relative" style="border-radius: 50px; padding: 0.4rem 0.9rem;">
                        <i class="bi bi-file-text"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; margin-top: -5px;">
                            <?= $quote_count ?>
                            <span class="visually-hidden">items in quote</span>
                        </span>
                    </a>
                    
                    
                </div>
            </div>
        </div>
    </nav>
</header>



<!-- ============================================================
     MAIN CONTENT STARTS HERE
     ============================================================ -->
<main>