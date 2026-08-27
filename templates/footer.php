<?php
// templates/footer.php
?>
    </main>
    
    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="bg-dark text-white py-5 mt-0" style="background: #0a1628 !important;">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="mb-3">
                        <img src="/kitgroup/assets/images/kitGroup.webp" alt="KIT Group" style="max-width: 180px; height: auto;">
                    </div>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">
                        Premium PPE and workwear solutions for Botswana's toughest workplaces.
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white-50" style="font-size: 1.2rem;"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white-50" style="font-size: 1.2rem;"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="text-white-50" style="font-size: 1.2rem;"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3" style="color: #ffffff;">Quick Links</h6>
                    <ul class="list-unstyled" style="font-size: 0.9rem;">
                        <li class="mb-2"><a href="/kitgroup/products" class="text-white-50 text-decoration-none">Products</a></li>
                        <li class="mb-2"><a href="/kitgroup/about" class="text-white-50 text-decoration-none">About Us</a></li>
                        <li class="mb-2"><a href="/kitgroup/stores" class="text-white-50 text-decoration-none">Stores</a></li>
                        <li class="mb-2"><a href="/kitgroup/contact" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3" style="color: #ffffff;">Contact</h6>
                    <ul class="list-unstyled" style="font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                        <li class="mb-2"><i class="bi bi-envelope me-2"></i> sales@kitgroup.com</li>
                        <li class="mb-2"><i class="bi bi-telephone me-2"></i> +267 390 0886</li>
                        <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Gaborone, BW</li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3" style="color: #ffffff;">Store Hours</h6>
                    <ul class="list-unstyled" style="font-size: 0.9rem; color: rgba(255,255,255,0.7);">
                        <li class="mb-1">Mon–Fri: 8:00am – 5:00pm</li>
                        <li class="mb-1">Sat: 9:00am – 1:00pm</li>
                        <li class="mb-1">Sun: Closed</li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 2rem 0 1rem 0;">
            <div class="text-center" style="font-size: 0.85rem; color: rgba(255,255,255,0.5);">
                &copy; <?= date('Y') ?> Kit Group. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS (required for hamburger menu, dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/kitgroup/assets/js/scripts.js"></script>
</body>
</html>