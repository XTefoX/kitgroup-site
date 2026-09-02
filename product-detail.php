<?php
// product-detail.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: /kitgroup/products');
    exit;
}

$product = getProductBySlug($slug);
if (!$product) {
    http_response_code(404);
    $page_title = "Product Not Found – Kit Group";
    include 'templates/header.php';
    echo '<div class="container py-5 text-center">';
    echo '  <h1 class="display-4">404</h1>';
    echo '  <p class="lead">Product not found.</p>';
    echo '  <a href="/kitgroup/products" class="btn btn-danger">Browse Products</a>';
    echo '</div>';
    include 'templates/footer.php';
    exit;
}

// Set page title and breadcrumbs
$page_title = $product['name'] . ' – Kit Group PPE';
$page_desc = strip_tags($product['short_description'] ?? $product['description'] ?? '');
$breadcrumb_items = [
    ['label' => 'Home', 'url' => '/kitgroup/'],
    ['label' => 'Products', 'url' => '/kitgroup/products'],
    ['label' => $product['category_name'], 'url' => '/kitgroup/products?category=' . $product['category_slug']],
    ['label' => $product['name']]
];

include 'templates/header.php';
?>

<!-- ============================================================
     PRODUCT DETAIL
     ============================================================ -->
<section class="py-4">
    <div class="container">
        <div class="row g-5">
            <!-- ====== PRODUCT IMAGES ====== -->
            <div class="col-lg-6">
                <!-- Main image container -->
                <div id="main-image-container" style="background: #f8f9fa; border-radius: 12px; overflow: hidden; height: 450px; position: relative;">
                    <?php 
                    // Get primary image from color_images
                    $primary_image = null;
                    if (!empty($product['color_images'])) {
                        foreach ($product['color_images'] as $img) {
                            if ($img['is_primary']) {
                                $primary_image = $img;
                                break;
                            }
                        }
                        if (!$primary_image) {
                            $primary_image = $product['color_images'][0];
                        }
                    }
                    $default_image = $primary_image ? $primary_image['image_url'] : 'placeholder.jpg';
                    ?>
                    <img id="main-product-image" 
                         src="/kitgroup/assets/images/products/<?= htmlspecialchars($default_image) ?>" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         style="width: 100%; height: 100%; object-fit: contain; transition: opacity 0.3s ease;">
                </div>
                
                <!-- Color selector thumbnails -->
                <?php if (!empty($product['color_images']) && count($product['color_images']) > 1): ?>
                    <div class="d-flex gap-2 mt-3 flex-wrap" id="color-thumbnails">
                        <?php foreach ($product['color_images'] as $image): ?>
                            <div class="color-thumbnail" 
                                 data-color="<?= htmlspecialchars($image['color']) ?>"
                                 data-image="/kitgroup/assets/images/products/<?= htmlspecialchars($image['image_url']) ?>"
                                 style="
                                     width: 80px; 
                                     height: 80px; 
                                     border-radius: 8px; 
                                     overflow: hidden; 
                                     cursor: pointer; 
                                     border: 3px solid <?= ($image['is_primary']) ? '#e63946' : 'transparent' ?>;
                                     transition: border-color 0.3s ease;
                                 ">
                                <img src="/kitgroup/assets/images/products/<?= htmlspecialchars($image['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($image['alt_text'] ?? $image['color']) ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                                <div style="text-align: center; font-size: 0.6rem; padding: 2px; background: #fff;">
                                    <?= htmlspecialchars($image['color']) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- ============================================================
                     USUALLY BOUGHT WITH (Under Product Images)
                     ============================================================ -->
                <?php 
                $usually_bought_with = getUsuallyBoughtWith($product['id'], 4);
                if (!empty($usually_bought_with)): 
                ?>
                <div class="mt-4">
                    <div class="card shadow-sm" style="border: none; border-radius: 12px; background: #f8f9fa; border-left: 4px solid #e63946;">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: #1a1a2e;">
                                <i class="bi bi-link-45deg me-2" style="color: #e63946;"></i> Usually Bought With
                            </h6>
                            <div class="row g-2">
                                <?php foreach ($usually_bought_with as $related): 
                                    $rel_image = $related['default_image'] ?? null;
                                    $rel_stock_tag = getProductStockTag($related['id']);
                                ?>
                                    <div class="col-6 col-md-3">
                                        <a href="/kitgroup/product/<?= htmlspecialchars($related['slug']) ?>" class="text-decoration-none">
                                            <div class="card h-100" style="border: none; border-radius: 8px; overflow: hidden; transition: transform 0.2s;">
                                                <div style="background: #fff; height: 100px; overflow: hidden; position: relative;">
                                                    <?php if ($rel_image): ?>
                                                        <img src="/kitgroup/assets/images/products/<?= htmlspecialchars($rel_image) ?>" 
                                                             alt="<?= htmlspecialchars($related['name']) ?>" 
                                                             style="width: 100%; height: 100%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #dee2e6;">
                                                            <i class="bi bi-image" style="font-size: 2rem;"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if ($related['is_made_in_botswana']): ?>
                                                        <span style="position: absolute; top: 4px; right: 4px; background: #28a745; color: #fff; padding: 0.1rem 0.4rem; border-radius: 50px; font-size: 0.5rem; font-weight: 600;">
                                                            <i class="bi bi-flag"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="card-body p-2">
                                                    <p class="card-text small mb-0" style="font-weight: 500; color: #1a1a2e; line-height: 1.2;">
                                                        <?= htmlspecialchars($related['name']) ?>
                                                    </p>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <span class="small fw-bold" style="color: #1a1a2e;">
                                                            P <?= number_format($related['min_price'] ?? 0, 2) ?>
                                                        </span>
                                                        <span class="small" style="color: <?= $rel_stock_tag['class'] === 'text-success' ? '#28a745' : '#ffc107' ?>;">
                                                            <i class="bi <?= $rel_stock_tag['icon'] ?>"></i>
                                                            <?= $rel_stock_tag['label'] ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- ====== PRODUCT INFO ====== -->
            <div class="col-lg-6">
                <!-- Brand -->
                <span style="font-size: 0.8rem; color: #e63946; font-weight: 600; text-transform: uppercase;">
                    <?= htmlspecialchars($product['brand_name']) ?>
                </span>
                
                <!-- Product Name -->
                <h1 class="display-6 fw-bold mt-1" style="color: #1a1a2e;">
                    <?= htmlspecialchars($product['name']) ?>
                </h1>
                
                <!-- Price -->
                <div class="mb-3">
                    <?php 
                    if (!empty($product['variants'])) {
                        $prices = array_column($product['variants'], 'price');
                        $min_price = min($prices);
                        $max_price = max($prices);
                    } else {
                        $min_price = 0;
                        $max_price = 0;
                    }
                    ?>
                    <?php if ($min_price === $max_price): ?>
                        <span class="display-6 fw-bold" style="color: #1a1a2e;">
                            P <?= number_format($min_price, 2) ?>
                        </span>
                    <?php else: ?>
                        <span class="display-6 fw-bold" style="color: #1a1a2e;">
                            P <?= number_format($min_price, 2) ?> – P <?= number_format($max_price, 2) ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Short Description -->
                <?php if ($product['short_description']): ?>
                    <p class="lead" style="font-size: 1rem; color: #495057;">
                        <?= htmlspecialchars($product['short_description']) ?>
                    </p>
                <?php endif; ?>
                
                <!-- Description -->
                <?php if ($product['description']): ?>
                    <div class="mb-4" style="color: #6c757d; font-size: 0.95rem;">
                        <?= nl2br(htmlspecialchars($product['description'])) ?>
                    </div>
                <?php endif; ?>
                
                <!-- ====== VARIANT SELECTOR ====== -->
                <div class="mb-4">
                    <!-- Color Selection -->
                    <?php if (!empty($product['colors'])): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Color</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $first_color = true;
                                foreach ($product['colors'] as $color): 
                                ?>
                                    <button class="btn variant-color <?= $first_color ? 'active' : '' ?>" 
                                            data-color="<?= htmlspecialchars($color['color']) ?>"
                                            style="
                                                border-radius: 50px; 
                                                padding: 0.35rem 1.2rem; 
                                                font-size: 0.85rem; 
                                                border: 2px solid <?= $first_color ? '#e63946' : '#dee2e6' ?>;
                                                background: <?= $first_color ? '#e63946' : '#fff' ?>;
                                                color: <?= $first_color ? '#fff' : '#1a1a2e' ?>;
                                                transition: all 0.3s ease;
                                            ">
                                        <?php if (!empty($color['color_hex'])): ?>
                                            <span style="display: inline-block; width: 12px; height: 12px; border-radius: 50%; background: <?= htmlspecialchars($color['color_hex']) ?>; margin-right: 6px; border: 1px solid rgba(0,0,0,0.1);"></span>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($color['color']) ?>
                                    </button>
                                <?php 
                                $first_color = false;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No colors available for this product.</div>
                    <?php endif; ?>
                    
                    <!-- Size Selection -->
                    <?php if (!empty($product['sizes'])): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Size</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php 
                                $first_size = true;
                                foreach ($product['sizes'] as $size): 
                                ?>
                                    <button class="btn variant-size <?= $first_size ? 'active' : '' ?>" 
                                            data-size="<?= htmlspecialchars($size) ?>"
                                            style="
                                                border-radius: 50px; 
                                                padding: 0.35rem 1.2rem; 
                                                font-size: 0.85rem; 
                                                min-width: 50px;
                                                border: 2px solid <?= $first_size ? '#e63946' : '#dee2e6' ?>;
                                                background: <?= $first_size ? '#e63946' : '#fff' ?>;
                                                color: <?= $first_size ? '#fff' : '#1a1a2e' ?>;
                                                transition: all 0.3s ease;
                                            ">
                                        <?= htmlspecialchars($size) ?>
                                    </button>
                                <?php 
                                $first_size = false;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No sizes available for this product.</div>
                    <?php endif; ?>
                    
                    <!-- Variant info display -->
                    <div id="variant-info" class="mt-3 p-3" style="background: #f8f9fa; border-radius: 8px; display: none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">Stock:</span>
                                <span id="variant-stock" style="color: #28a745;">In stock</span>
                            </div>
                            <div>
                                <span class="fw-bold">Price:</span>
                                <span id="variant-price" class="text-danger fw-bold">P 0.00</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="fw-bold">SKU:</span>
                            <span id="variant-sku" style="font-size: 0.85rem; color: #6c757d;">-</span>
                        </div>
                    </div>
                    
                    <!-- Add to Quote -->
                    <button class="btn btn-danger btn-lg w-100 mt-3" 
                            id="add-to-quote-btn"
                            style="border-radius: 50px; background: #e63946; border: none; font-weight: 600; padding: 0.8rem;">
                        <i class="bi bi-file-text me-2"></i> Add to Quote
                    </button>
                    
                    <div class="mt-3 text-center">
                        <small class="text-muted">
                            <i class="bi bi-truck me-1"></i> Available for delivery or in-store pickup
                        </small>
                    </div>
                </div>
                
                <!-- Additional info -->
                <div class="row g-3 mt-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2" style="color: #6c757d; font-size: 0.9rem;">
                            <i class="bi bi-shield-check" style="color: #e63946; font-size: 1.2rem;"></i>
                            <span>BOBS Approved</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2" style="color: #6c757d; font-size: 0.9rem;">
                            <i class="bi bi-award" style="color: #e63946; font-size: 1.2rem;"></i>
                            <span>Quality Guaranteed</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2" style="color: #6c757d; font-size: 0.9rem;">
                            <i class="bi bi-arrow-repeat" style="color: #e63946; font-size: 1.2rem;"></i>
                            <span>Easy Returns</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center gap-2" style="color: #6c757d; font-size: 0.9rem;">
                            <i class="bi bi-clock-history" style="color: #e63946; font-size: 1.2rem;"></i>
                            <span>In-Stock Ready</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
       
        <!-- ============================================================
        RELATED PRODUCTS
        ============================================================ -->
        <div class="mt-5 pt-5 border-top">
            <h3 class="fw-bold mb-4">You May Also Like</h3>
            
            <?php 
            // Get related products
            $related_products = getRelatedProducts($product['id'], $product['category_id'], $product['brand_id'], 4);
            ?>
            
            <?php if (empty($related_products)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-boxes" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #dee2e6;"></i>
                    <p>No related products found.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($related_products as $related): 
                        $default_image = $related['default_image'] ?? null;
                        $stock_tag = getProductStockTag($related['id']);
                    ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 shadow-sm product-card" style="border: none; border-radius: 12px; transition: all 0.3s ease; overflow: hidden;">
                                <!-- Product Image -->
                                <div style="position: relative; overflow: hidden; background: #f8f9fa; height: 180px;">
                                    <?php if ($default_image): ?>
                                        <img src="/kitgroup/assets/images/products/<?= htmlspecialchars($default_image) ?>" 
                                            alt="<?= htmlspecialchars($related['name']) ?>" 
                                            style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
                                    <?php else: ?>
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #dee2e6; font-size: 3rem;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Featured badge -->
                                    <?php if ($related['featured']): ?>
                                        <span style="position: absolute; top: 10px; left: 10px; background: #e63946; color: #fff; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; z-index: 2;">
                                            Featured
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Made in Botswana badge -->
                                    <?php if ($related['is_made_in_botswana']): ?>
                                        <span style="position: absolute; top: 10px; right: 10px; background: #28a745; color: #fff; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.6rem; font-weight: 600; z-index: 2;">
                                            <i class="bi bi-flag"></i> Local
                                        </span>
                                    <?php endif; ?>
                                    
                                    <!-- Stock badge -->
                                    <span style="position: absolute; bottom: 10px; right: 10px; background: <?= $stock_tag['bg'] ?>; color: #fff; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.6rem; z-index: 2;">
                                        <i class="bi <?= $stock_tag['icon'] ?> me-1"></i>
                                        <?= $stock_tag['label'] ?>
                                    </span>
                                </div>
                                
                                <div class="card-body">
                                    <!-- Brand -->
                                    <span style="font-size: 0.7rem; color: #e63946; font-weight: 600; text-transform: uppercase;">
                                        <?= htmlspecialchars($related['brand_name']) ?>
                                    </span>
                                    
                                    <!-- Product name -->
                                    <h6 class="card-title mt-1" style="font-weight: 600; font-size: 0.9rem; line-height: 1.3; min-height: 40px;">
                                        <a href="/kitgroup/product/<?= htmlspecialchars($related['slug']) ?>" 
                                        style="color: #1a1a2e; text-decoration: none; transition: color 0.2s;">
                                            <?= htmlspecialchars($related['name']) ?>
                                        </a>
                                    </h6>
                                    
                                    <!-- Price -->
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($related['min_price'] === $related['max_price']): ?>
                                            <span class="fw-bold" style="color: #1a1a2e; font-size: 1rem;">
                                                P <?= number_format($related['min_price'], 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="fw-bold" style="color: #1a1a2e; font-size: 1rem;">
                                                P <?= number_format($related['min_price'], 2) ?> – P <?= number_format($related['max_price'], 2) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent border-0 pb-3 pt-0">
                                    <a href="/kitgroup/product/<?= htmlspecialchars($related['slug']) ?>" 
                                    class="btn btn-outline-danger w-100" 
                                    style="border-radius: 50px; border-width: 2px; font-weight: 600; font-size: 0.85rem; padding: 0.4rem;">
                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     JAVASCRIPT – COLOR/SIZE SELECTION
     ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    const colorButtons = document.querySelectorAll('.variant-color');
    const sizeButtons = document.querySelectorAll('.variant-size');
    const variantInfo = document.getElementById('variant-info');
    const variantStock = document.getElementById('variant-stock');
    const variantPrice = document.getElementById('variant-price');
    const variantSku = document.getElementById('variant-sku');
    const mainImage = document.getElementById('main-product-image');
    const colorThumbnails = document.querySelectorAll('.color-thumbnail');
    const productId = <?= (int)$product['id'] ?>;
    
    let selectedColor = colorButtons.length > 0 ? colorButtons[0].dataset.color : null;
    let selectedSize = sizeButtons.length > 0 ? sizeButtons[0].dataset.size : null;
    
    // ============================================================
    // COLOR SELECTION
    // ============================================================
    colorButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            colorButtons.forEach(b => {
                b.style.borderColor = '#dee2e6';
                b.style.background = '#fff';
                b.style.color = '#1a1a2e';
            });
            this.style.borderColor = '#e63946';
            this.style.background = '#e63946';
            this.style.color = '#fff';
            
            selectedColor = this.dataset.color;
            
            // Update main image based on selected color
            const colorImage = document.querySelector(`.color-thumbnail[data-color="${selectedColor}"]`);
            if (colorImage) {
                const imageUrl = colorImage.dataset.image;
                mainImage.src = imageUrl;
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.style.opacity = '1';
                }, 100);
            }
            
            // Update variant info
            updateVariantInfo();
        });
    });
    
    // ============================================================
    // SIZE SELECTION
    // ============================================================
    sizeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            sizeButtons.forEach(b => {
                b.style.borderColor = '#dee2e6';
                b.style.background = '#fff';
                b.style.color = '#1a1a2e';
            });
            this.style.borderColor = '#e63946';
            this.style.background = '#e63946';
            this.style.color = '#fff';
            
            selectedSize = this.dataset.size;
            updateVariantInfo();
        });
    });
    
    // ============================================================
    // THUMBNAIL CLICK (also updates main image)
    // ============================================================
    colorThumbnails.forEach(thumb => {
        thumb.addEventListener('click', function() {
            const color = this.dataset.color;
            const imageUrl = this.dataset.image;
            
            // Update main image
            mainImage.src = imageUrl;
            mainImage.style.opacity = '0';
            setTimeout(() => {
                mainImage.style.opacity = '1';
            }, 100);
            
            // Update active color button
            colorButtons.forEach(btn => {
                btn.style.borderColor = '#dee2e6';
                btn.style.background = '#fff';
                btn.style.color = '#1a1a2e';
                if (btn.dataset.color === color) {
                    btn.style.borderColor = '#e63946';
                    btn.style.background = '#e63946';
                    btn.style.color = '#fff';
                }
            });
            
            selectedColor = color;
            updateVariantInfo();
        });
    });
    
    // ============================================================
    // UPDATE VARIANT INFO
    // ============================================================
    function updateVariantInfo() {
        if (!selectedColor || !selectedSize) {
            variantInfo.style.display = 'none';
            return;
        }
        
        // Show loading state
        variantInfo.style.display = 'block';
        variantStock.textContent = 'Loading...';
        variantStock.style.color = '#6c757d';
        variantPrice.textContent = 'P --.--';
        variantSku.textContent = '--';
        
        // Build API URL
        const apiUrl = `/kitgroup/api/get-variant.php?product_id=${productId}&size=${encodeURIComponent(selectedSize)}&color=${encodeURIComponent(selectedColor)}`;
        
        fetch(apiUrl)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const stock = data.stock;
                    variantStock.textContent = stock > 0 ? `${stock} in stock` : 'Out of stock';
                    variantStock.style.color = stock > 0 ? '#28a745' : '#dc3545';
                    variantPrice.textContent = `P ${data.price.toFixed(2)}`;
                    variantSku.textContent = data.sku;
                } else {
                    variantStock.textContent = 'Not available';
                    variantStock.style.color = '#dc3545';
                    variantPrice.textContent = 'P --.--';
                    variantSku.textContent = '--';
                }
            })
            .catch(() => {
                // Fallback – show placeholder when API fails
                variantStock.textContent = 'Check in store';
                variantStock.style.color = '#6c757d';
                variantPrice.textContent = 'P --.--';
                variantSku.textContent = 'Contact us';
            });
    }
    
    // Initial update if both color and size are selected
    if (selectedColor && selectedSize) {
        updateVariantInfo();
    }
});

// ============================================================
// ADD TO QUOTE
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    const addToQuoteBtn = document.getElementById('add-to-quote-btn');

    if (addToQuoteBtn) {
        addToQuoteBtn.addEventListener('click', function() {
            // Get selected color and size
            const activeColor = document.querySelector('.variant-color.active');
            const activeSize = document.querySelector('.variant-size.active');
            
            if (!activeColor || !activeSize) {
                alert('Please select a color and size.');
                return;
            }
            
            const color = activeColor.dataset.color;
            const size = activeSize.dataset.size;
            const productId = <?= (int)$product['id'] ?>;
            
            // Optional: Ask for quantity
            const quantity = prompt('Enter quantity:', '1');
            if (quantity === null) return; // User cancelled
            if (isNaN(quantity) || parseInt(quantity) < 1) {
                alert('Please enter a valid quantity.');
                return;
            }
            
            // Build URL
            const url = `/kitgroup/quote-add.php?product_id=${productId}&size=${encodeURIComponent(size)}&color=${encodeURIComponent(color)}&quantity=${parseInt(quantity)}`;
            
            // Redirect to add to quote
            window.location.href = url;
        });
    }
});
</script>

<?php include 'templates/footer.php'; ?>