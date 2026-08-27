<?php
// quote-submit.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

$quote_items = [];
$total = 0;

if (isset($_SESSION['quote']) && !empty($_SESSION['quote'])) {
    $pdo = getDB();
    foreach ($_SESSION['quote'] as $key => $item) {
        $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name FROM products p JOIN brands b ON p.brand_id = b.id WHERE p.id = ? AND p.is_active = 1");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $stmt = $pdo->prepare("SELECT price, sku FROM product_variants WHERE product_id = ? AND size = ? AND color = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$item['product_id'], $item['size'], $item['color']]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($variant) {
                $quote_items[] = [
                    'product' => $product,
                    'size' => $item['size'],
                    'color' => $item['color'],
                    'quantity' => $item['quantity'],
                    'price' => $variant['price'],
                    'sku' => $variant['sku'],
                    'subtotal' => $variant['price'] * $item['quantity']
                ];
                $total += $variant['price'] * $item['quantity'];
            }
        }
    }
}

$page_title = "Quote Submitted – Kit Group";
include 'templates/header.php';
?>

<!-- ============================================================
     QUOTE SUBMITTED CONFIRMATION
     ============================================================ -->
<section class="py-5">
    <div class="container">
        <div class="text-center py-5">
            <div style="font-size: 5rem; color: #28a745; margin-bottom: 1rem;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1 class="display-4 fw-bold mb-3" style="color: #1a1a2e;">Quote Submitted!</h1>
            <p class="lead" style="color: #495057;">
                Thank you for your quote request. Our team will review it and get back to you shortly.
            </p>
            <div class="card shadow-sm mx-auto" style="max-width: 500px; border: none; border-radius: 12px; border-top: 4px solid #e63946;">
                <div class="card-body">
                    <p class="mb-1"><strong>Quote Items:</strong> <?= count($quote_items) ?></p>
                    <p class="mb-1"><strong>Total:</strong> P <?= number_format($total, 2) ?></p>
                    <p class="mb-0"><strong>Reference:</strong> Q-<?= date('Ymd') ?>-<?= str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) ?></p>
                </div>
            </div>
            <div class="mt-4 d-flex flex-wrap gap-3 justify-content-center">
                <a href="/kitgroup/products" class="btn btn-outline-secondary" style="border-radius: 50px;">
                    <i class="bi bi-arrow-left me-2"></i> Continue Shopping
                </a>
                <a href="/kitgroup/quote-print.php" target="_blank" class="btn btn-primary" style="border-radius: 50px; background: #0a1628; border: none;">
                    <i class="bi bi-printer me-2"></i> Print Quote
                </a>
            </div>
        </div>
    </div>
</section>

<?php 
// Clear quote after submission
unset($_SESSION['quote']);
include 'templates/footer.php'; 
?>