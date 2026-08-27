<?php
// about.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "About Us – Kit Group";
$page_desc = "Kit Group has been providing premium PPE and workwear solutions in Botswana for over 30 years. Learn about our story, values, and commitment to safety.";

$breadcrumb_items = [
    ['label' => 'Home', 'url' => '/kitgroup/'],
    ['label' => 'About Us']
];

include 'templates/header.php';
?>

<!-- ============================================================
     ABOUT PAGE HERO
     ============================================================ -->
<section class="about-hero py-5" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 100%);
    color: #ffffff;
    border-bottom: 3px solid #e63946;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">About Kit Group</h1>
                <p class="lead mb-0" style="opacity: 0.9;">
                    Protecting Botswana's workforce for over 30 years with premium PPE and workwear solutions.
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0 text-end">
                <span class="badge bg-danger px-4 py-2" style="font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    <i class="bi bi-award me-2"></i> Trusted Since 1994
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     OUR STORY SECTION
     ============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge bg-danger mb-3 px-3 py-2" style="font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    Our Story
                </span>
                <h2 class="display-5 fw-bold mb-3" style="color: #1a1a2e;">A Legacy of Safety and Reliability</h2>
                <p class="lead" style="color: #495057; font-size: 1.1rem;">
                    For over three decades, Kit Group has been at the forefront of PPE and workwear manufacturing in Botswana and Southern Africa.
                </p>
                <p style="color: #6c757d;">
                    What began as a small family-owned business has grown into one of Botswana's most trusted suppliers of personal protective equipment. Our journey has been driven by a simple mission: to protect the people who build our nation.
                </p>
                <p style="color: #6c757d;">
                    From the mining fields of Jwaneng to the construction sites of Gaborone, Kit Group products are trusted by professionals who refuse to compromise on safety and quality.
                </p>
                <div class="mt-4 d-flex gap-4 flex-wrap">
                    <div>
                        <i class="bi bi-check-circle-fill" style="color: #e63946;"></i>
                        <span class="fw-bold" style="color: #1a1a2e;">30+ Years</span>
                        <span class="d-block text-muted small">Industry Experience</span>
                    </div>
                    <div>
                        <i class="bi bi-check-circle-fill" style="color: #e63946;"></i>
                        <span class="fw-bold" style="color: #1a1a2e;">3 Stores</span>
                        <span class="d-block text-muted small">Across Botswana</span>
                    </div>
                    <div>
                        <i class="bi bi-check-circle-fill" style="color: #e63946;"></i>
                        <span class="fw-bold" style="color: #1a1a2e;">BOBS Approved</span>
                        <span class="d-block text-muted small">Quality Certified</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div style="background: #f8f9fa; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                    <img src="/kitgroup/assets/images/kitGroup.webp" 
                         alt="Kit Group History" 
                         class="img-fluid w-100" 
                         style="height: 400px; object-fit: contain;">
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-4">
                        <div style="background: #f8f9fa; border-radius: 8px; overflow: hidden; height: 80px;">
                            <img src="/kitgroup/assets/images/hero2.jpg" alt="Workers" class="img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background: #f8f9fa; border-radius: 8px; overflow: hidden; height: 80px;">
                            <img src="/kitgroup/assets/images/hero5.jpg" alt="Quality" class="img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background: #f8f9fa; border-radius: 8px; overflow: hidden; height: 80px;">
                            <img src="/kitgroup/assets/images/hero4.jpg" alt="Certificate" class="img-fluid w-100 h-100" style="object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     VALUES / MISSION SECTION
     ============================================================ -->
<section class="py-5" style="background: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-danger mb-3 px-3 py-2" style="font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                Our Values
            </span>
            <h2 class="display-5 fw-bold" style="color: #1a1a2e;">What Drives Us</h2>
            <p class="text-muted" style="font-size: 1.1rem;">The principles that guide everything we do</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center p-4" style="border: none; border-radius: 12px; border-top: 4px solid #e63946;">
                    <div style="font-size: 3rem; color: #e63946; margin-bottom: 1rem;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #1a1a2e;">Quality First</h4>
                    <p class="text-muted">
                        Every product we offer meets the highest standards of quality and durability. We never compromise on safety.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center p-4" style="border: none; border-radius: 12px; border-top: 4px solid #e63946;">
                    <div style="font-size: 3rem; color: #e63946; margin-bottom: 1rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #1a1a2e;">Customer Focus</h4>
                    <p class="text-muted">
                        We understand the unique challenges of Botswana's workplaces. Our solutions are tailored to your needs.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm text-center p-4" style="border: none; border-radius: 12px; border-top: 4px solid #e63946;">
                    <div style="font-size: 3rem; color: #e63946; margin-bottom: 1rem;">
                        <i class="bi bi-award"></i>
                    </div>
                    <h4 class="fw-bold" style="color: #1a1a2e;">Innovation</h4>
                    <p class="text-muted">
                        We continuously evolve our product range to meet changing industry standards and workplace requirements.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     WHY CHOOSE US SECTION
     ============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 order-lg-2">
                <span class="badge bg-danger mb-3 px-3 py-2" style="font-size: 0.7rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    Why Choose Us
                </span>
                <h2 class="display-5 fw-bold mb-3" style="color: #1a1a2e;">Your Trusted PPE Partner in Botswana</h2>
                <ul class="list-unstyled" style="font-size: 1rem;">
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill" style="color: #e63946; font-size: 1.3rem;"></i>
                        <div>
                            <span class="fw-bold" style="color: #1a1a2e;">30+ Years of Excellence</span>
                            <p class="text-muted small mb-0">Proven track record of quality and reliability.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill" style="color: #e63946; font-size: 1.3rem;"></i>
                        <div>
                            <span class="fw-bold" style="color: #1a1a2e;">3 Convenient Locations</span>
                            <p class="text-muted small mb-0">Stores in Gaborone and Jwaneng for easy access.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill" style="color: #e63946; font-size: 1.3rem;"></i>
                        <div>
                            <span class="fw-bold" style="color: #1a1a2e;">Premium Brands</span>
                            <p class="text-muted small mb-0">Partnerships with trusted global and local brands.</p>
                        </div>
                    </li>
                    <li class="mb-3 d-flex gap-3">
                        <i class="bi bi-check-circle-fill" style="color: #e63946; font-size: 1.3rem;"></i>
                        <div>
                            <span class="fw-bold" style="color: #1a1a2e;">BOBS Approved</span>
                            <p class="text-muted small mb-0">All products meet South African Bureau of Standards requirements.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-7 order-lg-1">
                <div style="background: #f8f9fa; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.08);">
                    <img src="/kitgroup/assets/images/why-choose-us.webp" 
                         alt="Why Choose Kit Group" 
                         class="img-fluid w-100" 
                         style="height: 450px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS SECTION
     ============================================================ -->
<section class="py-5" style="background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 100%); color: #ffffff;">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-6 col-lg-3">
                <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;">30+</div>
                <div style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Years of Experience</div>
            </div>
            <div class="col-6 col-lg-3">
                <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;">3</div>
                <div style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Store Locations</div>
            </div>
            <div class="col-6 col-lg-3">
                <?php 
                $pdo = getDB();
                $stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
                $product_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                ?>
                <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;"><?= number_format($product_count) ?>+</div>
                <div style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Products Available</div>
            </div>
            <div class="col-6 col-lg-3">
                <?php 
                $stmt = $pdo->query("SELECT COUNT(DISTINCT brand_id) as total FROM products WHERE is_active = 1");
                $brand_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                ?>
                <div style="font-size: 2.5rem; font-weight: 700; color: #e63946;"><?= number_format($brand_count) ?>+</div>
                <div style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">Trusted Brands</div>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>