<?php
require_once __DIR__ . '/includes/functions.php';
// index.php
$page_title = "Kit Group – Premium PPE & Workwear Solutions | Botswana";
$page_desc = "Kit Group provides high-quality PPE, workwear, and safety solutions for mining, construction, and industry. Shop our range of durable, SABS-approved protective clothing.";

include 'templates/header.php';
?>
<!-- Swiper JS (Add before </body> tag) -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.featuredSwiper', {
        slidesPerView: 1,
        spaceBetween: 20,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            576: {
                slidesPerView: 2,
                spaceBetween: 20,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 24,
            },
            1200: {
                slidesPerView: 4,
                spaceBetween: 24,
            }
        }
    });
});
</script>
<style>
    /* ============================================================
   FEATURED PRODUCT CARDS
   ============================================================ */
.product-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.12) !important;
}

.product-card .card-body h5 a {
    transition: color 0.2s ease;
}

.product-card .card-body h5 a:hover {
    color: #e63946 !important;
}

/* Featured badge pulse animation */
.product-card .featured-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}
</style>
<!-- ============================================================
     HERO SECTION
     ============================================================ -->

<!-- ============================================================
     HERO SECTION – CHARACTER BOTTOM-ALIGNED
     ============================================================ -->
<section class="hero-section" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 50%, #0d1b2a 100%);
    color: #ffffff;
    padding: 40px 0 0 0;
    position: relative;
    overflow: hidden;
    min-height: 92vh;
    display: flex;
    align-items: flex-end;
">
    <!-- Subtle background pattern overlay -->
    <div style="
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('/kitgroup/assets/images/pattern-geometric.jpg') repeat;
        opacity: 0.05;
        pointer-events: none;
    "></div>

    <div class="container position-relative" style="padding-bottom: 0;">
        <div class="row align-items-end g-0">
            <!-- ====== LEFT COLUMN – TEXT CONTENT ====== -->
            <div class="col-lg-5" style="padding-bottom: 60px;">
                <!-- Badge -->
                <span class="badge bg-danger mb-3 px-3 py-2" style="font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    Proudly Botswana Store
                </span>
                <h1 class="display-4 fw-bold mb-3" style="line-height: 1.2;">
                    It's a Matter of Identity with Kit.<br>
                    <span style="color: #e63946;">Safety You Deserve.</span>
                </h1>
                <p class="lead mb-4" style="font-size: 1.15rem; opacity: 0.9;">
                    For over 30 years, Kit Group has been designing and manufacturing 
                    workwear and PPE to withstand the demands of Africa's toughest 
                    workplaces.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="/kitgroup/products" class="btn btn-danger btn-lg px-5 py-3" style="font-weight: 600; background: #e63946; border: none; transition: all 0.3s ease;">
                        Shop Now
                    </a>
                    <a href="/kitgroup/about" class="btn btn-outline-light btn-lg px-5 py-3" style="font-weight: 600; border-width: 2px; transition: all 0.3s ease;">
                        Our Story
                    </a>
                </div>
                <!-- Trust indicators -->
                <div class="mt-4 d-flex gap-4 flex-wrap">
                    <div><i class="bi bi-shield-check" style="color: #e63946;"></i> BOBS Approved</div>
                    <div><i class="bi bi-award" style="color: #e63946;"></i> 30+ Years Experience</div>
                    <div><i class="bi bi-truck" style="color: #e63946;"></i> Nationwide Delivery</div>
                </div>
            </div>
            
            <!-- ====== RIGHT COLUMN – CHARACTER (BOTTOM-ALIGNED) ====== -->
            <div class="col-lg-7 text-center" style="
                padding: 0;
                margin: 0;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                min-height: 500px;
                position: relative;
            ">
                <div class="hero-image-wrapper" style="
                    position: relative;
                    width: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: flex-end;
                    overflow: visible;
                    margin-bottom: -8px;
                ">
                    <img src="/kitgroup/assets/images/hero1.png" 
                         alt="Kit Group PPE and Workwear Collection" 
                         class="hero-character img-fluid" 
                         style="
                             max-height: 92vh;
                             width: auto;
                             max-width: 130%;
                             object-fit: contain;
                             object-position: bottom center;
                             filter: drop-shadow(0 40px 80px rgba(0,0,0,0.7));
                             animation: slideUpFade 1.2s cubic-bezier(0.22, 1, 0.36, 1) forwards;
                             transform-origin: bottom center;
                             display: block;
                             margin: 0 auto;
                         ">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     TRUST BAR
     ============================================================ -->
<section class="py-4 border-top border-bottom bg-light" style="border-top: 3px solid #e63946 !important;">
    <div class="container">
        <div class="row g-4 text-center justify-content-center">
            
            <!-- Item 1: Mining & Industry -->
            <div class="col-lg-3 col-6">
                <div class="p-3 rounded-3 h-100 transition-all hover-shadow-sm">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2 p-3 rounded-circle" style="background-color: rgba(230, 57, 70, 0.1); color: #e63946;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M14.763.075A.5.5 0 0 0 14.5 0H3a.5.5 0 0 0-.5.5v3h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1v3h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1v3h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1v-3h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1v-3h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1v-3h1a.5.5 0 0 0 .237-.925zM3.5 1h10v3h-10V1zm0 4h10v3h-10V5zm0 4h10v3h-10V9zm0 4h10v2h-10v-2z"/>
                        </svg>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Mining & Industry</h6>
                    <span class="text-muted small d-block">Trusted PPE Solutions</span>
                </div>
            </div>

            <!-- Item 2: Construction -->
            <div class="col-lg-3 col-6">
                <div class="p-3 rounded-3 h-100 transition-all hover-shadow-sm">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2 p-3 rounded-circle" style="background-color: rgba(230, 57, 70, 0.1); color: #e63946;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M14.763.075A.5.5 0 0 0 15 0H1a.5.5 0 0 0-.5.5v15a.5.5 0 0 0 .5.5h14a.5.5 0 0 0 .5-.5V.5a.5.5 0 0 0-.237-.425zM2 1h12v14H2V1zm2 2h3v3H4V3zm5 0h3v3H9V3zM4 8h3v3H4V8zm5 0h3v3H9V8z"/>
                        </svg>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Construction</h6>
                    <span class="text-muted small d-block">Built to Last</span>
                </div>
            </div>

            <!-- Item 3: Safety Certified -->
            <div class="col-lg-3 col-6">
                <div class="p-3 rounded-3 h-100 transition-all hover-shadow-sm">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2 p-3 rounded-circle" style="background-color: rgba(230, 57, 70, 0.1); color: #e63946;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M5.338 1.59a1 1 0 0 1 1.32-.083l.094.083 2.517 2.517a1 1 0 0 1 .083 1.32l-.083.094-1.125 1.125a1 1 0 0 1-1.32.083l-.094-.083-2.517-2.517a1 1 0 0 1-.083-1.32l.083-.094L5.338 1.59z"/>
                            <path d="M5.072 1.293a1 1 0 0 0-1.414 0l-2.36 2.36a1 1 0 0 0 0 1.414l8.486 8.485a1 1 0 0 0 1.414 0l2.36-2.36a1 1 0 0 0 0-1.414L5.072 1.293z"/>
                        </svg>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Safety Certified</h6>
                    <span class="text-muted small d-block">BOBS Standards</span>
                </div>
            </div>

            <!-- Item 4: Nationwide -->
            <div class="col-lg-3 col-6">
                <div class="p-3 rounded-3 h-100 transition-all hover-shadow-sm">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2 p-3 rounded-circle" style="background-color: rgba(230, 57, 70, 0.1); color: #e63946;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H6a2 2 0 1 1-4 0H1.5A1.5 1.5 0 0 1 0 10.5v-7zm1.5-.5a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h.382a2 2 0 0 1 3.736 0h4.264a2 2 0 0 1 3.736 0h.882a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12V3.5a.5.5 0 0 0-.5-.5h-10z"/>
                        </svg>
                    </div>
                    <h6 class="fw-bold mb-1 text-dark">Nationwide</h6>
                    <span class="text-muted small d-block">Delivered Across Region</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ============================================================
     PRODUCT CATEGORIES
     ============================================================ -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Our Product Range</h2>
            <p class="text-muted">Comprehensive PPE and workwear solutions for every industry</p>
        </div>
        
        <div class="row g-4">
            
            <!-- Category 1 -->
            <div class="col-lg-3 col-md-6 col-6">
                <div class="card h-100 border-0 shadow-sm category-card">
                    <div class="category-img-wrapper">
                        <img src="/kitgroup/assets/images/categories/cat3.webp" alt="Workwear & Coveralls" class="category-img" loading="lazy">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-1">Workwear & Coveralls</h5>
                        <p class="card-text text-muted small flex-grow-1">Durable, comfortable, built for the job</p>
                        <a href="/category/coveralls" class="stretched-link text-decoration-none fw-semibold" style="color: #e63946;">
                            Browse Category <span class="ms-1">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category 2 -->
            <div class="col-lg-3 col-md-6 col-6">
                <div class="card h-100 border-0 shadow-sm category-card">
                    <div class="category-img-wrapper">
                        <img src="/kitgroup/assets/images/categories/cat5.webp" alt="Hi-Visibility Gear" class="category-img" loading="lazy">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-1">Hi-Visibility Gear</h5>
                        <p class="card-text text-muted small flex-grow-1">Stay seen, stay safe on site</p>
                        <a href="/category/hi-vis" class="stretched-link text-decoration-none fw-semibold" style="color: #e63946;">
                            Browse Category <span class="ms-1">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category 3 -->
            <div class="col-lg-3 col-md-6 col-6">
                <div class="card h-100 border-0 shadow-sm category-card">
                    <div class="category-img-wrapper">
                        <img src="/kitgroup/assets/images/categories/cat2.webp" alt="Hand Protection" class="category-img" loading="lazy">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-1">Hand Protection</h5>
                        <p class="card-text text-muted small flex-grow-1">Gloves for every site hazard</p>
                        <a href="/category/gloves" class="stretched-link text-decoration-none fw-semibold" style="color: #e63946;">
                            Browse Category <span class="ms-1">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Category 4 -->
            <div class="col-lg-3 col-md-6 col-6">
                <div class="card h-100 border-0 shadow-sm category-card">
                    <div class="category-img-wrapper">
                        <img src="/kitgroup/assets/images/categories/cat4.webp" alt="Safety Footwear" class="category-img" loading="lazy">
                        <div class="category-overlay"></div>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title fw-bold text-dark mb-1">Safety Footwear</h5>
                        <p class="card-text text-muted small flex-grow-1">Protection from the ground up</p>
                        <a href="/category/footwear" class="stretched-link text-decoration-none fw-semibold" style="color: #e63946;">
                            Browse Category <span class="ms-1">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-5">
            <a href="/products" class="btn btn-outline-dark btn-lg px-4 py-2 fs-6 fw-semibold">
                View All Products &rarr;
            </a>
        </div>
    </div>
</section>



<!-- ============================================================
     FEATURED PRODUCTS (CAROUSEL)
     ============================================================ -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Top Picks</h2>
            <p class="text-muted">Our most trusted and popular workwear solutions</p>
        </div>
        
        <?php 
        // Get featured products from database
        $featured_products = getFeaturedProducts(10);
        ?>
        
        <?php if (empty($featured_products)): ?>
            <!-- No featured products found -->
            <div class="text-center py-5">
                <div style="font-size: 3rem; color: #dee2e6; margin-bottom: 1rem;">
                    <i class="bi bi-star"></i>
                </div>
                <p class="text-muted">No featured products available right now.</p>
                <a href="/kitgroup/products" class="btn btn-outline-primary">Browse All Products</a>
            </div>
        <?php else: ?>
            <!-- Featured Products Carousel -->
            <div class="swiper featuredSwiper position-relative pb-5">
                <div class="swiper-wrapper">
                    <?php foreach ($featured_products as $product): 
                        $default_image = $product['default_image'] ?? null;
                        $colors = !empty($product['colors']) ? explode(',', $product['colors']) : [];
                        $sizes = !empty($product['sizes']) ? explode(',', $product['sizes']) : [];
                    ?>
                        <div class="swiper-slide h-auto">
                            <div class="card h-100 shadow-sm product-card" style="transition: transform 0.3s; overflow: hidden; border: none; border-radius: 12px;">
                                <!-- Product Image -->
                                <div style="position: relative; overflow: hidden; background: #ffffff; height: 240px;">
                                    <?php if ($default_image): ?>
                                        <img src="/kitgroup/assets/images/products/<?= htmlspecialchars($default_image) ?>" 
                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                             style="width: 100%; height: 100%; object-fit: contain; padding: 10px; transition: transform 0.3s ease;">
                                    <?php else: ?>
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #dee2e6; font-size: 3rem;">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Featured Badge -->
                                    <span style="position: absolute; top: 10px; left: 10px; background: #e63946; color: #fff; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; z-index: 2;">
                                        <i class="bi bi-star-fill me-1"></i> Featured
                                    </span>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <!-- Brand -->
                                    <span style="font-size: 0.75rem; color: #e63946; font-weight: 600; text-transform: uppercase;">
                                        <?= htmlspecialchars($product['brand_name'] ?? 'KIT GROUP') ?>
                                    </span>
                                    
                                    <!-- Product Name -->
                                    <h5 class="card-title mt-1" style="font-weight: 600; font-size: 1rem; line-height: 1.3;">
                                        <a href="/kitgroup/product/<?= htmlspecialchars($product['slug']) ?>" 
                                           style="color: #1a1a2e; text-decoration: none;">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h5>
                                    
                                    <!-- Category -->
                                    <p class="card-text text-muted small mb-2"><?= htmlspecialchars($product['category_name'] ?? '') ?></p>
                                    
                                    <!-- Colors swatches -->
                                    <?php if (!empty($colors)): ?>
                                        <div class="mb-3 d-flex flex-wrap gap-1 mt-auto">
                                            <?php 
                                            $color_data = getProductColors($product['id']);
                                            $color_hex_map = [];
                                            foreach ($color_data as $c) {
                                                $color_hex_map[$c['color']] = $c['color_hex'] ?? '#cccccc';
                                            }
                                            ?>
                                            <?php foreach ($colors as $color): ?>
                                                <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background: <?= $color_hex_map[$color] ?? '#cccccc' ?>; border: 1px solid rgba(0,0,0,0.15);" title="<?= htmlspecialchars($color) ?>"></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div> <!-- Fixed: Properly closed card-body -->
                                
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

                <!-- Carousel Pagination & Navigation Controls -->
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next text-danger"></div>
                <div class="swiper-button-prev text-danger"></div>
            </div>
            
            <!-- View All Products Button -->
            <div class="text-center mt-4">
                <a href="/kitgroup/products" class="btn btn-danger btn-lg px-5" style="border-radius: 50px; background: #e63946; border: none; font-weight: 600;">
                    View All Products <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>



<!-- ============================================================
     ABOUT / BRAND STORY SECTION
     ============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-danger mb-3 px-3 py-2" style="font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase;">
                    Our Legacy
                </span>
                <h2 class="display-6 fw-bold mb-3">Built for Africa's Toughest Workplaces</h2>
                <p class="text-muted" style="font-size: 1.1rem;">
                    Since 1994, Kit Group has been at the forefront of workwear and PPE 
                    in Botswana. The brands we offer combine practical design, 
                    lasting comfort, and dependable protection.
                </p>
                <p class="text-muted">
                    From mining and construction to agriculture and hospitality, 
                    we provide solutions that professionals trust. 
                    Every product is engineered to BOBS specifications with reinforced 
                    stitching and practical features that ensure durability.
                </p>
                <a href="/about" class="btn btn-primary mt-2">Learn More About Us →</a>
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="bg-light p-4 text-center rounded-3" style="border-left: 4px solid #e63946;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;">30+</div>
                            <div class="text-muted small">Years of Excellence</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-4 text-center rounded-3" style="border-left: 4px solid #e63946;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;">100+</div>
                            <div class="text-muted small">Trusted Brands</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-4 text-center rounded-3" style="border-left: 4px solid #e63946;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;">2</div>
                            <div class="text-muted small">Store Locations</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="bg-light p-4 text-center rounded-3" style="border-left: 4px solid #e63946;">
                            <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;">BOBS</div>
                            <div class="text-muted small">Approved Quality</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CALL TO ACTION – STORES / CONTACT (BOTSWANA)
     ============================================================ -->
<section class="stores-section py-5" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 60%, #0d1b2a 100%);
    color: #ffffff;
    border-top: 3px solid #e63946;
    margin: 0;
    padding: 60px 0 0 0;
">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-5">
            <span class="badge bg-danger mb-3 px-3 py-2" style="
                font-size: 0.7rem; 
                letter-spacing: 2px; 
                text-transform: uppercase; 
                background: #e63946 !important;
                border-radius: 50px;
            ">
                <i class="bi bi-geo-alt me-1"></i> Find Us in Botswana
            </span>
            <h2 class="display-5 fw-bold mb-2" style="color: #ffffff;">
                Visit Our Stores
            </h2>
            <p class="lead" style="color: rgba(255,255,255,0.75); font-size: 1.1rem;">
                Three convenient locations across Botswana – come visit us today
            </p>
        </div>

        <!-- Store Cards Row -->
        <div class="row g-4 justify-content-center">
            <!-- Store 1 – Block 3, Gaborone -->
            <div class="col-lg-4 col-md-6">
                <div class="store-card" style="
                    background: rgba(255,255,255,0.08);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255,255,255,0.12);
                    border-radius: 16px;
                    padding: 2rem 1.5rem;
                    height: 100%;
                    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
                    cursor: default;
                    text-align: left;
                ">
                    <div class="store-icon" style="
                        font-size: 2.2rem;
                        margin-bottom: 1rem;
                        display: inline-block;
                        background: rgba(230, 57, 70, 0.2);
                        padding: 0.6rem 0.9rem;
                        border-radius: 12px;
                        color: #e63946;
                    ">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h5 style="color: #ffffff; font-weight: 700; margin-bottom: 0.25rem;">
                        Block 3
                    </h5>
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; font-weight: 500; margin-bottom: 0.75rem;">
                        <i class="bi bi-geo-alt me-1" style="color: #e63946;"></i> Gaborone
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0.25rem;">
                        <i class="bi bi-building me-2" style="color: #e63946;"></i> Plot 123, Block 3
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0.25rem;">
                        <i class="bi bi-telephone me-2" style="color: #e63946;"></i> +267 31 234 567
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0;">
                        <i class="bi bi-clock me-2" style="color: #e63946;"></i> Mon–Fri: 8am – 5pm
                    </p>
                </div>
            </div>

            <!-- Store 2 – Commerce Park, Gaborone -->
            <div class="col-lg-4 col-md-6">
                <div class="store-card" style="
                    background: rgba(255,255,255,0.08);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255,255,255,0.12);
                    border-radius: 16px;
                    padding: 2rem 1.5rem;
                    height: 100%;
                    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
                    cursor: default;
                    text-align: left;
                ">
                    <div class="store-icon" style="
                        font-size: 2.2rem;
                        margin-bottom: 1rem;
                        display: inline-block;
                        background: rgba(230, 57, 70, 0.2);
                        padding: 0.6rem 0.9rem;
                        border-radius: 12px;
                        color: #e63946;
                    ">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h5 style="color: #ffffff; font-weight: 700; margin-bottom: 0.25rem;">
                        Commerce Park
                    </h5>
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; font-weight: 500; margin-bottom: 0.75rem;">
                        <i class="bi bi-geo-alt me-1" style="color: #e63946;"></i> Gaborone
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0.25rem;">
                        <i class="bi bi-building me-2" style="color: #e63946;"></i> Unit 5, Commerce Park
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0.25rem;">
                        <i class="bi bi-telephone me-2" style="color: #e63946;"></i> +267 31 234 568
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0;">
                        <i class="bi bi-clock me-2" style="color: #e63946;"></i> Mon–Fri: 8am – 5pm
                    </p>
                </div>
            </div>

            <!-- Store 3 – Jwaneng Mall, Jwaneng -->
            <div class="col-lg-4 col-md-6">
                <div class="store-card" style="
                    background: rgba(255,255,255,0.08);
                    backdrop-filter: blur(12px);
                    -webkit-backdrop-filter: blur(12px);
                    border: 1px solid rgba(255,255,255,0.12);
                    border-radius: 16px;
                    padding: 2rem 1.5rem;
                    height: 100%;
                    transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
                    cursor: default;
                    text-align: left;
                ">
                    <div class="store-icon" style="
                        font-size: 2.2rem;
                        margin-bottom: 1rem;
                        display: inline-block;
                        background: rgba(230, 57, 70, 0.2);
                        padding: 0.6rem 0.9rem;
                        border-radius: 12px;
                        color: #e63946;
                    ">
                        <i class="bi bi-shop"></i>
                    </div>
                    <h5 style="color: #ffffff; font-weight: 700; margin-bottom: 0.25rem;">
                        Jwaneng Mall
                    </h5>
                    <p style="color: rgba(255,255,255,0.6); font-size: 0.85rem; font-weight: 500; margin-bottom: 0.75rem;">
                        <i class="bi bi-geo-alt me-1" style="color: #e63946;"></i> Jwaneng
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0.25rem;">
                        <i class="bi bi-building me-2" style="color: #e63946;"></i> Shop 12, Jwaneng Mall
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0.25rem;">
                        <i class="bi bi-telephone me-2" style="color: #e63946;"></i> +267 31 234 569
                    </p>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem; margin-bottom: 0;">
                        <i class="bi bi-clock me-2" style="color: #e63946;"></i> Mon–Sat: 8am – 6pm
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom CTA -->
        <div class="text-center mt-5" style="padding-bottom: 10px;">
            <a href="/kitgroup/contact" class="btn btn-danger btn-lg px-5 py-3" style="
                background: #e63946;
                border: none;
                border-radius: 50px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 8px 25px rgba(230, 57, 70, 0.3);
            ">
                <i class="bi bi-envelope me-2"></i> Contact Us
                <i class="bi bi-arrow-right ms-2"></i>
            </a>
            <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-top: 1.5rem;">
                <i class="bi bi-clock-history me-1"></i> Same-day service for bulk orders
            </p>
        </div>
    </div>
</section>

<?php
include 'templates/footer.php';
?>