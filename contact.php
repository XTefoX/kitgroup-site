<?php
// contact.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = "Contact Us – Kit Group";
$page_desc = "Contact Kit Group for all your PPE and workwear needs. Reach us at our stores in Gaborone and Jwaneng.";

$breadcrumb_items = [
    ['label' => 'Home', 'url' => '/kitgroup/'],
    ['label' => 'Contact']
];

include 'templates/header.php';
?>

<!-- ============================================================
     CONTACT PAGE HERO
     ============================================================ -->
<section class="contact-hero py-5" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 100%);
    color: #ffffff;
    border-bottom: 3px solid #e63946;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                <p class="lead mb-0" style="opacity: 0.9;">
                    Get in touch with our team for enquiries, orders, or assistance.
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0 text-end">
                <span class="badge bg-danger px-4 py-2" style="font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    <i class="bi bi-envelope me-2"></i> We're Here to Help
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     CONTACT INFORMATION
     ============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Store 1 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm" style="border: none; border-radius: 12px; border-top: 4px solid #e63946; transition: transform 0.3s; hover: transform: translateY(-4px);">
                    <div class="card-body">
                        <div style="font-size: 2rem; color: #e63946; margin-bottom: 0.5rem;">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h5 class="fw-bold" style="color: #1a1a2e;">Block 3 Store</h5>
                        <p class="text-muted small mb-2">Plot 123, Block 3, Gaborone, Botswana</p>
                        <ul class="list-unstyled" style="font-size: 0.9rem;">
                            <li class="mb-2">
                                <i class="bi bi-person me-2" style="color: #e63946;"></i>
                                <strong>Manager:</strong> Mr. Thabo Molefe
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-telephone me-2" style="color: #e63946;"></i>
                                <a href="tel:+26731234567" class="text-decoration-none" style="color: #1a1a2e;">+267 31 234 567</a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-envelope me-2" style="color: #e63946;"></i>
                                <a href="mailto:info@kitgroup.co.bw" class="text-decoration-none" style="color: #1a1a2e;">info@kitgroup.co.bw</a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-clock me-2" style="color: #e63946;"></i>
                                Mon–Fri: 8:00am – 5:00pm
                            </li>
                        </ul>
                        <a href="https://www.google.com/maps/search/?api=1&query=Plot+123+Block+3+Gaborone+Botswana" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-danger w-100" 
                           style="border-radius: 50px; border-width: 2px; font-weight: 600;">
                            <i class="bi bi-map me-1"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Store 2 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm" style="border: none; border-radius: 12px; border-top: 4px solid #e63946; transition: transform 0.3s; hover: transform: translateY(-4px);">
                    <div class="card-body">
                        <div style="font-size: 2rem; color: #e63946; margin-bottom: 0.5rem;">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h5 class="fw-bold" style="color: #1a1a2e;">Commerce Park Store</h5>
                        <p class="text-muted small mb-2">Unit 5, Commerce Park, Gaborone, Botswana</p>
                        <ul class="list-unstyled" style="font-size: 0.9rem;">
                            <li class="mb-2">
                                <i class="bi bi-shop me-2" style="color: #e63946;"></i>
                                <strong>Manager:</strong> Ms. Kelebogile Ntswe
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-telephone me-2" style="color: #e63946;"></i>
                                <a href="tel:+26731234568" class="text-decoration-none" style="color: #1a1a2e;">+267 31 234 568</a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-envelope me-2" style="color: #e63946;"></i>
                                <a href="mailto:info@kitgroup.co.bw" class="text-decoration-none" style="color: #1a1a2e;">info@kitgroup.co.bw</a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-clock me-2" style="color: #e63946;"></i>
                                Mon–Fri: 8:00am – 5:00pm
                            </li>
                        </ul>
                        <a href="https://www.google.com/maps/search/?api=1&query=Unit+5+Commerce+Park+Gaborone+Botswana" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-danger w-100" 
                           style="border-radius: 50px; border-width: 2px; font-weight: 600;">
                            <i class="bi bi-map me-1"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>

            <!-- Store 3 -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm" style="border: none; border-radius: 12px; border-top: 4px solid #e63946; transition: transform 0.3s; hover: transform: translateY(-4px);">
                    <div class="card-body">
                        <div style="font-size: 2rem; color: #e63946; margin-bottom: 0.5rem;">
                            <i class="bi bi-shop"></i>
                        </div>
                        <h5 class="fw-bold" style="color: #1a1a2e;">Jwaneng Mall Store</h5>
                        <p class="text-muted small mb-2">Shop 12, Jwaneng Mall, Jwaneng, Botswana</p>
                        <ul class="list-unstyled" style="font-size: 0.9rem;">
                            <li class="mb-2">
                                <i class="bi bi-person me-2" style="color: #e63946;"></i>
                                <strong>Manager:</strong> Mr. Olebile Dikolobe
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-telephone me-2" style="color: #e63946;"></i>
                                <a href="tel:+26731234569" class="text-decoration-none" style="color: #1a1a2e;">+267 31 234 569</a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-envelope me-2" style="color: #e63946;"></i>
                                <a href="mailto:info@kitgroup.co.bw" class="text-decoration-none" style="color: #1a1a2e;">info@kitgroup.co.bw</a>
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-clock me-2" style="color: #e63946;"></i>
                                Mon–Sat: 8:00am – 6:00pm
                            </li>
                        </ul>
                        <a href="https://www.google.com/maps/search/?api=1&query=Shop+12+Jwaneng+Mall+Jwaneng+Botswana" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-danger w-100" 
                           style="border-radius: 50px; border-width: 2px; font-weight: 600;">
                            <i class="bi bi-map me-1"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Enquiry -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm" style="border: none; border-radius: 12px; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                    <div class="card-body text-center p-5">
                        <div style="font-size: 3rem; color: #e63946; margin-bottom: 1rem;">
                            <i class="bi bi-envelope-paper"></i>
                        </div>
                        <h4 style="color: #1a1a2e;">General Enquiries</h4>
                        <p class="text-muted">For all other enquiries, please email us and we'll respond within 24 hours.</p>
                        <a href="mailto:info@kitgroup.co.bw" class="btn btn-danger btn-lg px-5" style="border-radius: 50px; background: #e63946; border: none; font-weight: 600;">
                            <i class="bi bi-envelope me-2"></i> info@kitgroup.co.bw
                        </a>
                        <div class="mt-3">
                            <span class="badge bg-light text-dark me-2" style="font-size: 0.8rem; padding: 0.5rem 1rem;">
                                <i class="bi bi-whatsapp me-1" style="color: #25D366;"></i> +267 71 234 567
                            </span>
                            <span class="badge bg-light text-dark" style="font-size: 0.8rem; padding: 0.5rem 1rem;">
                                <i class="bi bi-clock me-1" style="color: #e63946;"></i> 24/7 Support
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.php'; ?>