<?php
// quote.php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

session_start();

// Get quote items
$quote_items = [];
$total = 0;

if (isset($_SESSION['quote']) && !empty($_SESSION['quote'])) {
    $pdo = getDB();
    foreach ($_SESSION['quote'] as $key => $item) {
        // Get product details
        $stmt = $pdo->prepare("SELECT p.*, b.name as brand_name FROM products p JOIN brands b ON p.brand_id = b.id WHERE p.id = ? AND p.is_active = 1");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Get variant price for this specific size/color
            $stmt = $pdo->prepare("SELECT price, sku FROM product_variants WHERE product_id = ? AND size = ? AND color = ? AND is_active = 1 LIMIT 1");
            $stmt->execute([$item['product_id'], $item['size'], $item['color']]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($variant) {
                $quote_items[] = [
                    'key' => $key,
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

$page_title = "My Quote – Kit Group";
$page_desc = "Review your quote request for PPE and workwear products.";

include 'templates/header.php';
?>

<!-- ============================================================
     QUOTE PAGE HERO
     ============================================================ -->
<section class="quote-hero py-5" style="
    background: linear-gradient(135deg, #0a1628 0%, #1a2a4a 100%);
    color: #ffffff;
    border-bottom: 3px solid #e63946;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">My Quote</h1>
                <p class="lead mb-0" style="opacity: 0.9;">
                    Review your selected items and print your quote.
                </p>
            </div>
            <div class="col-lg-4 mt-3 mt-lg-0 text-end">
                <span class="badge bg-danger px-4 py-2" style="font-size: 0.9rem; letter-spacing: 2px; text-transform: uppercase; background: #e63946 !important;">
                    <i class="bi bi-file-text me-2"></i> <?= count($quote_items) ?> Item<?= count($quote_items) > 1 ? 's' : '' ?>
                </span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     QUOTE CONTENT
     ============================================================ -->
<section class="py-4">
    <div class="container">
        <?php if (empty($quote_items)): ?>
            <!-- Empty quote -->
            <div class="text-center py-5">
                <div style="font-size: 4rem; color: #dee2e6; margin-bottom: 1rem;">
                    <i class="bi bi-file-text"></i>
                </div>
                <h4 style="color: #1a1a2e;">Your quote is empty</h4>
                <p class="text-muted">Start adding products to your quote from our product catalog.</p>
                <a href="/kitgroup/products" class="btn btn-danger btn-lg" style="border-radius: 50px; background: #e63946; border: none; font-weight: 600;">
                    <i class="bi bi-arrow-left me-2"></i> Browse Products
                </a>
            </div>
        <?php else: ?>
            <!-- Quote items -->
            <div class="table-responsive">
                <table class="table table-hover" id="quoteTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>SKU</th>
                            <th style="width:100px;">Price</th>
                            <th style="width:120px;">Quantity</th>
                            <th style="width:100px;">Subtotal</th>
                            <th style="width:60px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quote_items as $index => $item): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($item['product']['name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($item['product']['brand_name']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($item['color']) ?></td>
                                <td><?= htmlspecialchars($item['size']) ?></td>
                                <td><?= htmlspecialchars($item['sku']) ?></td>
                                <td class="item-price">P <?= number_format($item['price'], 2) ?></td>
                                <td>
                                    <form action="quote-update.php" method="post" class="d-flex gap-1">
                                        <input type="hidden" name="key" value="<?= htmlspecialchars($item['key']) ?>">
                                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="0" max="999" class="form-control form-control-sm quantity-input" style="width: 70px;" data-price="<?= $item['price'] ?>">
                                        <button type="submit" class="btn btn-sm btn-primary update-quantity-btn" title="Update quantity">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="item-subtotal">P <?= number_format($item['subtotal'], 2) ?></td>
                                <td>
                                    <a href="quote-remove.php?key=<?= urlencode($item['key']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?')">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold">Total</td>
                            <td colspan="2" class="fw-bold" id="grandTotal">P <?= number_format($total, 2) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Actions -->
            <div class="d-flex flex-wrap gap-3 mt-4">
                <a href="/kitgroup/products" class="btn btn-outline-secondary" style="border-radius: 50px;">
                    <i class="bi bi-arrow-left me-2"></i> Continue Shopping
                </a>
                <a href="quote-clear.php" class="btn btn-outline-danger" style="border-radius: 50px;" onclick="return confirm('Clear all items from your quote?')">
                    <i class="bi bi-trash me-2"></i> Clear Quote
                </a>
                <a href="quote-print.php" target="_blank" class="btn btn-primary" style="border-radius: 50px; background: #0a1628; border: none;">
                    <i class="bi bi-printer me-2"></i> Print Quote
                </a>
            </div>
            
            <!-- Quote notes -->
            <div class="mt-4">
                <div class="card shadow-sm" style="border: none; border-radius: 12px;">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2">Additional Notes</h6>
                        <textarea class="form-control" rows="3" placeholder="Add any special requirements or delivery instructions..."></textarea>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================
     JAVASCRIPT – LIVE QUANTITY UPDATE
     ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============================================================
    // LIVE QUANTITY UPDATE (without page reload)
    // ============================================================
    const quantityInputs = document.querySelectorAll('.quantity-input');
    const updateButtons = document.querySelectorAll('.update-quantity-btn');
    
    // Auto-update when quantity changes (on blur)
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateQuantity(this);
        });
        
        // Also update on Enter key
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                updateQuantity(this);
            }
        });
    });
    
    // Also handle button clicks
    updateButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const input = form.querySelector('.quantity-input');
            updateQuantity(input);
        });
    });
    
    function updateQuantity(input) {
        const form = input.closest('form');
        const key = form.querySelector('input[name="key"]').value;
        const quantity = parseInt(input.value) || 0;
        const price = parseFloat(input.dataset.price) || 0;
        const row = input.closest('tr');
        const subtotalCell = row.querySelector('.item-subtotal');
        const grandTotalCell = document.getElementById('grandTotal');
        
        if (quantity < 0) {
            alert('Quantity cannot be negative.');
            input.value = 0;
            return;
        }
        
        // Update subtotal for this row
        const subtotal = price * quantity;
        subtotalCell.textContent = 'P ' + subtotal.toFixed(2);
        
        // Update grand total
        let newTotal = 0;
        document.querySelectorAll('.item-subtotal').forEach(cell => {
            const val = cell.textContent.replace('P ', '').replace(',', '');
            newTotal += parseFloat(val) || 0;
        });
        grandTotalCell.textContent = 'P ' + newTotal.toFixed(2);
        
        // Submit the form to update session (background AJAX)
        const formData = new FormData(form);
        fetch('/kitgroup/quote-update.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                // Session updated successfully
                console.log('Quote updated');
            }
        })
        .catch(error => {
            console.error('Error updating quote:', error);
        });
    }
});
</script>

<?php include 'templates/footer.php'; ?>