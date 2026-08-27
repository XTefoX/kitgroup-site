<?php
// products.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

// Get filter parameters
$category_slug = $_GET['category'] ?? null;
$brand_slug = $_GET['brand'] ?? null;
$search = $_GET['search'] ?? null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 15;

// Get filter arrays from GET
$selected_sizes = isset($_GET['sizes']) ? (array)$_GET['sizes'] : [];
$selected_colors = isset($_GET['colors']) ? (array)$_GET['colors'] : [];
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

// Build filters array
$filters = [
    'sizes' => $selected_sizes,
    'colors' => $selected_colors,
    'min_price' => $min_price,
    'max_price' => $max_price
];

// Get total products (for pagination)
$total_products = getTotalProducts($category_slug, $brand_slug, $filters, $search);
$total_pages = ceil($total_products / $per_page);

// Make sure page is valid
if ($page < 1) $page = 1;
if ($page > $total_pages && $total_pages > 0) $page = $total_pages;

// Get products with pagination
$products = getProductsPaginated($category_slug, $brand_slug, $filters, $search, $page, $per_page);

// Get all data for filters
$category_tree = getCategoryTree();
$brands = getBrands();
$all_sizes = getAllSizes();
$all_colors = getAllColors();
$price_range = getPriceRange();

// Set page title
$page_title = "Products – Kit Group PPE & Workwear";
$page_desc = "Browse our full range of PPE, workwear, and safety solutions.";

include 'templates/header.php';
?>

<!-- ============================================================
     PRODUCTS PAGE HERO
     ============================================================ -->
<section class="products-hero py-5" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 100%);
    color: #ffffff;
    border-bottom: 3px solid #e63946;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-2">Our Products</h1>
                <p class="lead mb-0" style="opacity: 0.8;">
                    Premium PPE and workwear solutions for Botswana's toughest workplaces
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0">
                <form action="/kitgroup/products" method="get" class="d-flex">
                    <input type="text" name="search" class="form-control form-control-lg" placeholder="Search products..." value="<?= htmlspecialchars($search ?? '') ?>" style="border-radius: 50px 0 0 50px; border: none;">
                    <button type="submit" class="btn btn-danger" style="border-radius: 0 50px 50px 0; background: #e63946; border: none; padding: 0 1.5rem;">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     FILTERS + PRODUCT GRID
     ============================================================ -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <!-- ====== SIDEBAR FILTERS ====== -->
            <div class="col-lg-3">
                <!-- Category Filter (Expandable) -->
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-grid me-2" style="color: #e63946;"></i> Categories</h5>
                        <div class="category-tree" style="font-size: 0.9rem;">
                            <a href="/kitgroup/products" class="category-link <?= (!$category_slug && !$brand_slug) ? 'active' : '' ?>" style="display: block; padding: 0.5rem 0; text-decoration: none; color: #1a1a2e; font-weight: 600;">
                                All Categories
                            </a>
                            <?php foreach ($category_tree as $parent_id => $parent): ?>
                                <div class="category-item">
                                    <div class="category-parent <?= ($category_slug === $parent['slug']) ? 'active' : '' ?>" 
                                         style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; cursor: pointer; border-bottom: 1px solid #f1f3f5;">
                                        <a href="/kitgroup/products?category=<?= urlencode($parent['slug']) ?><?= $brand_slug ? '&brand=' . urlencode($brand_slug) : '' ?>" 
                                           style="text-decoration: none; color: #1a1a2e; font-weight: 600; flex: 1;">
                                            <?= htmlspecialchars($parent['name']) ?>
                                        </a>
                                        <?php if (!empty($parent['children'])): ?>
                                            <span class="toggle-icon" style="cursor: pointer; padding: 0 0.5rem; color: #6c757d; transition: transform 0.3s;">
                                                <i class="bi bi-chevron-down"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($parent['children'])): ?>
                                        <div class="category-children" style="padding-left: 1.5rem; display: none; overflow: hidden; transition: max-height 0.3s ease;">
                                            <?php foreach ($parent['children'] as $child): ?>
                                                <a href="/kitgroup/products?category=<?= urlencode($child['slug']) ?><?= $brand_slug ? '&brand=' . urlencode($brand_slug) : '' ?>" 
                                                   class="category-link <?= ($category_slug === $child['slug']) ? 'active' : '' ?>" 
                                                   style="display: block; padding: 0.3rem 0; text-decoration: none; color: <?= ($category_slug === $child['slug']) ? '#e63946' : '#6c757d' ?>; font-size: 0.85rem; border-bottom: 1px solid #f8f9fa;">
                                                    └ <?= htmlspecialchars($child['name']) ?>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Brand Filter -->
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-tag me-2" style="color: #e63946;"></i> 
                            Brands
                            <span class="badge bg-light text-dark ms-2" style="font-size: 0.65rem; font-weight: 400;"><?= count($brands) ?></span>
                        </h5>
                        <div class="brand-list" style="font-size: 0.9rem;">
                            <a href="/kitgroup/products<?= $category_slug ? '?category=' . urlencode($category_slug) : '' ?>" 
                            class="brand-link <?= (!$brand_slug) ? 'active' : '' ?>" 
                            style="display: flex; align-items: center; padding: 0.5rem 0.75rem; text-decoration: none; color: <?= (!$brand_slug) ? '#e63946' : '#1a1a2e' ?>; border-radius: 6px; transition: all 0.2s; <?= (!$brand_slug) ? 'background: rgba(230, 57, 70, 0.08); font-weight: 600;' : '' ?>">
                                <i class="bi bi-grid-3x3-gap-fill me-2" style="font-size: 0.9rem; opacity: 0.6;"></i>
                                All Brands
                            </a>
                            <?php foreach ($brands as $brand): ?>
                                <?php
                                // Count products for this brand (for badge)
                                $pdo = getDB();
                                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM products WHERE brand_id = ? AND is_active = 1");
                                $stmt->execute([$brand['id']]);
                                $product_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
                                $is_active = ($brand_slug === $brand['slug']);
                                ?>
                                <a href="/kitgroup/products?brand=<?= urlencode($brand['slug']) ?><?= $category_slug ? '&category=' . urlencode($category_slug) : '' ?><?= $page > 1 ? '&page=' . $page : '' ?>" 
                                class="brand-link <?= $is_active ? 'active' : '' ?>" 
                                style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0.75rem; text-decoration: none; color: <?= $is_active ? '#e63946' : '#1a1a2e' ?>; border-radius: 6px; transition: all 0.2s; <?= $is_active ? 'background: rgba(230, 57, 70, 0.08); font-weight: 600;' : '' ?>">
                                    <span>
                                        <?php if ($brand['logo']): ?>
                                            <img src="<?= getBrandLogoUrl($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" style="height: 20px; width: auto; max-width: 30px; object-fit: contain; margin-right: 8px; vertical-align: middle;">
                                        <?php else: ?>
                                            <i class="bi bi-tag me-2" style="font-size: 0.8rem; opacity: 0.5;"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($brand['name']) ?>
                                    </span>
                                    <span class="badge <?= $is_active ? 'bg-danger' : 'bg-light text-dark' ?>" style="font-size: 0.6rem; font-weight: 400; border-radius: 50px;">
                                        <?= $product_count ?>
                                    </span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Size Filter -->
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-rulers me-2" style="color: #e63946;"></i> Size</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($all_sizes as $size): ?>
                                <?php
                                // Build URL with size filter
                                $url_params = $_GET;
                                if (in_array($size, $selected_sizes)) {
                                    $url_params['sizes'] = array_diff((array)($url_params['sizes'] ?? []), [$size]);
                                    if (empty($url_params['sizes'])) unset($url_params['sizes']);
                                } else {
                                    if (!isset($url_params['sizes'])) $url_params['sizes'] = [];
                                    $url_params['sizes'][] = $size;
                                }
                                unset($url_params['page']); // Reset page when filter changes
                                ?>
                                <a href="/kitgroup/products?<?= http_build_query($url_params) ?>" 
                                   class="btn btn-sm <?= in_array($size, $selected_sizes) ? 'btn-danger' : 'btn-outline-secondary' ?>" 
                                   style="border-radius: 50px; font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                                    <?= htmlspecialchars($size) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Color Filter -->
                <div class="card shadow-sm mb-4" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-palette me-2" style="color: #e63946;"></i> Color</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($all_colors as $color): ?>
                                <?php
                                $url_params = $_GET;
                                if (in_array($color, $selected_colors)) {
                                    $url_params['colors'] = array_diff((array)($url_params['colors'] ?? []), [$color]);
                                    if (empty($url_params['colors'])) unset($url_params['colors']);
                                } else {
                                    if (!isset($url_params['colors'])) $url_params['colors'] = [];
                                    $url_params['colors'][] = $color;
                                }
                                unset($url_params['page']);
                                ?>
                                <a href="/kitgroup/products?<?= http_build_query($url_params) ?>" 
                                   class="btn btn-sm <?= in_array($color, $selected_colors) ? 'btn-danger' : 'btn-outline-secondary' ?>" 
                                   style="border-radius: 50px; font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                                    <?= htmlspecialchars($color) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3"><i class="bi bi-currency-dollar me-2" style="color: #e63946;"></i> Price Range</h5>
                        <div class="mb-3">
                            <div style="display: flex; justify-content: space-between; font-size: 0.85rem;">
                                <span>P <?= number_format($price_range['min'], 0) ?></span>
                                <span>P <?= number_format($price_range['max'], 0) ?></span>
                            </div>
                            <form method="get" action="/kitgroup/products" id="priceFilterForm">
                                <?php if ($category_slug): ?>
                                    <input type="hidden" name="category" value="<?= htmlspecialchars($category_slug) ?>">
                                <?php endif; ?>
                                <?php if ($brand_slug): ?>
                                    <input type="hidden" name="brand" value="<?= htmlspecialchars($brand_slug) ?>">
                                <?php endif; ?>
                                <?php if ($search): ?>
                                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                <?php endif; ?>
                                <?php foreach ($selected_sizes as $size): ?>
                                    <input type="hidden" name="sizes[]" value="<?= htmlspecialchars($size) ?>">
                                <?php endforeach; ?>
                                <?php foreach ($selected_colors as $color): ?>
                                    <input type="hidden" name="colors[]" value="<?= htmlspecialchars($color) ?>">
                                <?php endforeach; ?>
                                <input type="hidden" name="page" value="1">
                                
                                <div style="padding: 0.5rem 0;">
                                    <label style="font-size: 0.85rem;">Min: P <span id="minPriceDisplay"><?= $min_price ?? $price_range['min'] ?></span></label>
                                    <input type="range" 
                                           name="min_price" 
                                           id="minPriceSlider" 
                                           class="form-range" 
                                           min="<?= $price_range['min'] ?>" 
                                           max="<?= $price_range['max'] ?>" 
                                           step="50" 
                                           value="<?= $min_price ?? $price_range['min'] ?>"
                                           style="padding: 0;">
                                </div>
                                <div style="padding: 0.5rem 0;">
                                    <label style="font-size: 0.85rem;">Max: P <span id="maxPriceDisplay"><?= $max_price ?? $price_range['max'] ?></span></label>
                                    <input type="range" 
                                           name="max_price" 
                                           id="maxPriceSlider" 
                                           class="form-range" 
                                           min="<?= $price_range['min'] ?>" 
                                           max="<?= $price_range['max'] ?>" 
                                           step="50" 
                                           value="<?= $max_price ?? $price_range['max'] ?>"
                                           style="padding: 0;">
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm w-100 mt-2" style="border-radius: 50px;">Apply Price Filter</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Clear All Filters -->
                <?php if ($category_slug || $brand_slug || $search || !empty($selected_sizes) || !empty($selected_colors) || $min_price || $max_price): ?>
                    <div class="mt-3">
                        <a href="/kitgroup/products" class="btn btn-outline-secondary w-100" style="border-radius: 50px;">
                            <i class="bi bi-x-circle me-1"></i> Clear All Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ====== PRODUCT GRID ====== -->
            <div class="col-lg-9">
                <!-- Result count -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span style="font-size: 0.95rem; color: #6c757d;">
                        <i class="bi bi-box-seam me-1"></i> 
                        Showing <?= count($products) ?> of <?= $total_products ?> product<?= $total_products > 1 ? 's' : '' ?> found
                    </span>
                    <?php if ($category_slug || $brand_slug || $search || !empty($selected_sizes) || !empty($selected_colors)): ?>
                        <a href="/kitgroup/products" class="btn btn-sm btn-outline-secondary" style="border-radius: 50px;">
                            <i class="bi bi-x me-1"></i> Clear filters
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($products)): ?>
                    <!-- No products found -->
                    <div class="text-center py-5">
                        <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <h4 style="color: #1a1a2e;">No products found</h4>
                        <p class="text-muted">Try adjusting your filters or search terms.</p>
                        <a href="/kitgroup/products" class="btn btn-danger" style="border-radius: 50px; background: #e63946; border: none;">
                            <i class="bi bi-arrow-left me-1"></i> View all products
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Product Grid -->
                    <div class="row g-4">
                        <?php foreach ($products as $product): 
                            $colors = !empty($product['colors']) ? explode(',', $product['colors']) : [];
                            $sizes = !empty($product['sizes']) ? explode(',', $product['sizes']) : [];
                            $default_image = $product['default_image'] ?? (count($colors) > 0 ? getColorImage($product['id'], $colors[0]) : null);
                            $in_stock = $product['total_stock'] > 0;
                        ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 shadow-sm product-card" style="border: none; border-radius: 12px; transition: all 0.3s ease; overflow: hidden;">
                                    <!-- Product Image -->
                                    <div style="position: relative; overflow: hidden; background: #f8f9fa; height: 220px;">
                                        <?php if ($default_image): ?>
                                            <img src="/kitgroup/assets/images/products/<?= htmlspecialchars($default_image) ?>" 
                                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                        <?php else: ?>
                                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #dee2e6; font-size: 3rem;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Featured badge -->
                                        <?php if ($product['featured']): ?>
                                            <span style="position: absolute; top: 10px; left: 10px; background: #e63946; color: #fff; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase;">
                                                Featured
                                            </span>
                                        <?php endif; ?>
                                        
                                        <!-- Stock badge -->
                                        <?php if ($in_stock): ?>
                                            <span style="position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.7); color: #fff; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.7rem;">
                                                <?= $product['total_stock'] ?> in stock
                                            </span>
                                        <?php else: ?>
                                            <span style="position: absolute; bottom: 10px; right: 10px; background: #dc3545; color: #fff; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.7rem;">
                                                Out of stock
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="card-body">
                                        <!-- Brand -->
                                        <span style="font-size: 0.75rem; color: #e63946; font-weight: 600; text-transform: uppercase;">
                                            <?= htmlspecialchars($product['brand_name']) ?>
                                        </span>
                                        
                                        <!-- Product name -->
                                        <h5 class="card-title mt-1" style="font-weight: 600; font-size: 1rem; line-height: 1.3;">
                                            <a href="/kitgroup/product/<?= htmlspecialchars($product['slug']) ?>" 
                                               style="color: #1a1a2e; text-decoration: none;">
                                                <?= htmlspecialchars($product['name']) ?>
                                            </a>
                                        </h5>
                                        
                                        <!-- Colors swatches -->
                                        <?php if (!empty($colors)): ?>
                                            <div class="mb-2 d-flex flex-wrap gap-1">
                                                <?php 
                                                $color_data = getProductColors($product['id']);
                                                $color_hex_map = [];
                                                foreach ($color_data as $c) {
                                                    $color_hex_map[$c['color']] = $c['color_hex'] ?? '#cccccc';
                                                }
                                                ?>
                                                <?php foreach ($colors as $color): ?>
                                                    <span style="display: inline-block; width: 18px; height: 18px; border-radius: 50%; background: <?= $color_hex_map[$color] ?? '#cccccc' ?>; border: 1px solid rgba(0,0,0,0.1);" title="<?= htmlspecialchars($color) ?>"></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Sizes badges -->
                                        <?php if (!empty($sizes)): ?>
                                            <div class="mb-2 d-flex flex-wrap gap-1">
                                                <?php foreach ($sizes as $size): ?>
                                                    <span style="font-size: 0.65rem; background: #f1f3f5; padding: 0.1rem 0.5rem; border-radius: 4px; color: #495057;"><?= htmlspecialchars($size) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Price -->
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if ($product['min_price'] === $product['max_price']): ?>
                                                <span class="fw-bold" style="color: #1a1a2e; font-size: 1.1rem;">
                                                    P <?= number_format($product['min_price'], 2) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="fw-bold" style="color: #1a1a2e; font-size: 1.1rem;">
                                                    P <?= number_format($product['min_price'], 2) ?> – P <?= number_format($product['max_price'], 2) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent border-0 pb-3">
                                        <a href="/kitgroup/product/<?= htmlspecialchars($product['slug']) ?>" 
                                           class="btn btn-outline-danger w-100" 
                                           style="border-radius: 50px; border-width: 2px; font-weight: 600;">
                                            View Details <i class="bi bi-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- ====== PAGINATION ====== -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Product pagination" class="mt-5">
                            <ul class="pagination justify-content-center">
                                <!-- Previous button -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="/kitgroup/products?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <!-- Page numbers -->
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link" href="/kitgroup/products?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <!-- Next button -->
                                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="/kitgroup/products?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        
                        <!-- Results info -->
                        <div class="text-center text-muted small mt-2">
                            Showing page <?= $page ?> of <?= $total_pages ?> (<?= $total_products ?> total products)
                        </div>
                    <?php endif; ?>
                    
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     JAVASCRIPT FOR EXPANDABLE CATEGORIES & PRICE SLIDER
     ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // EXPANDABLE CATEGORIES
    // ============================================================
    const toggleIcons = document.querySelectorAll('.toggle-icon');
    const categoryItems = document.querySelectorAll('.category-item');
    
    // Open categories that have active children or parent
    categoryItems.forEach(item => {
        const parentLink = item.querySelector('.category-parent a');
        const children = item.querySelector('.category-children');
        const toggleIcon = item.querySelector('.toggle-icon');
        
        if (children) {
            // Check if any child is active
            const hasActiveChild = children.querySelector('.category-link.active');
            const isParentActive = parentLink && parentLink.classList.contains('active');
            
            if (hasActiveChild || isParentActive) {
                children.style.display = 'block';
                if (toggleIcon) {
                    toggleIcon.querySelector('i').className = 'bi bi-chevron-up';
                }
            }
        }
    });
    
    toggleIcons.forEach(icon => {
        icon.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const parent = this.closest('.category-item');
            const children = parent.querySelector('.category-children');
            const iconElement = this.querySelector('i');
            
            if (children) {
                if (children.style.display === 'block') {
                    children.style.display = 'none';
                    if (iconElement) {
                        iconElement.className = 'bi bi-chevron-down';
                    }
                } else {
                    children.style.display = 'block';
                    if (iconElement) {
                        iconElement.className = 'bi bi-chevron-up';
                    }
                }
            }
        });
    });
    
    // Also toggle when clicking the parent link (but not the category link inside it)
    document.querySelectorAll('.category-parent').forEach(parent => {
        parent.addEventListener('click', function(e) {
            // Don't toggle if clicked directly on the category link
            if (e.target.tagName === 'A') return;
            
            const icon = this.querySelector('.toggle-icon');
            if (icon) {
                icon.click();
            }
        });
    });
    
    // ============================================================
    // PRICE RANGE SLIDER LIVE UPDATE
    // ============================================================
    const minSlider = document.getElementById('minPriceSlider');
    const maxSlider = document.getElementById('maxPriceSlider');
    const minDisplay = document.getElementById('minPriceDisplay');
    const maxDisplay = document.getElementById('maxPriceDisplay');
    
    if (minSlider && maxSlider) {
        minSlider.addEventListener('input', function() {
            minDisplay.textContent = this.value;
            if (parseInt(this.value) > parseInt(maxSlider.value)) {
                maxSlider.value = this.value;
                maxDisplay.textContent = this.value;
            }
        });
        
        maxSlider.addEventListener('input', function() {
            maxDisplay.textContent = this.value;
            if (parseInt(this.value) < parseInt(minSlider.value)) {
                minSlider.value = this.value;
                minDisplay.textContent = this.value;
            }
        });
    }
    
    // ============================================================
    // CLOSE MOBILE SIDEBAR ON FILTER CLICK (if needed)
    // ============================================================
    // Add Bootstrap collapse toggle for mobile if needed
});
</script>

<!-- ============================================================
     CSS FOR EXPANDABLE CATEGORIES
     ============================================================ -->
<style>
.category-parent.active a {
    color: #e63946 !important;
}

.category-link.active {
    color: #e63946 !important;
    font-weight: 600;
}

.category-children {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.category-parent {
    transition: background 0.2s ease;
    border-radius: 4px;
}

.category-parent:hover {
    background: #f8f9fa;
}

.toggle-icon i {
    font-size: 0.8rem;
}

/* ============================================================
   BRAND FILTER STYLES (Matches Category Filter)
   ============================================================ */
.brand-link {
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
}

.brand-link:hover {
    background: #f8f9fa;
    color: #e63946 !important;
    border-left-color: #e63946;
}

.brand-link.active {
    background: rgba(230, 57, 70, 0.08) !important;
    color: #e63946 !important;
    border-left-color: #e63946;
    font-weight: 600;
}

.brand-link .badge {
    transition: all 0.2s ease;
}

.brand-link:hover .badge:not(.bg-danger) {
    background: #e63946 !important;
    color: #fff !important;
}

.brand-link.active .badge {
    background: #e63946 !important;
    color: #fff !important;
}
</style>

<?php include 'templates/footer.php'; ?>