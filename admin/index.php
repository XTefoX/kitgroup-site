<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$page_title = "Dashboard – Kit Group Admin";

// Include admin header
require_once __DIR__ . '/templates/header.php';

// Get counts for dashboard stats
$pdo = getDB();

// Count products
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products WHERE is_active = 1");
$product_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Count brands
$stmt = $pdo->query("SELECT COUNT(*) as total FROM brands");
$brand_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Count categories
$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$category_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Count variants (total stock)
$stmt = $pdo->query("SELECT SUM(stock) as total FROM product_variants WHERE is_active = 1");
$stock_total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// Recent products
$stmt = $pdo->query("SELECT p.*, b.name as brand_name 
                     FROM products p 
                     JOIN brands b ON p.brand_id = b.id 
                     WHERE p.is_active = 1 
                     ORDER BY p.created_at DESC 
                     LIMIT 5");
$recent_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Dashboard Stats -->
<div class="row g-4">
    <div class="col-xl-3 col-md-6">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase small fw-bold">Products</span>
                    <h2 class="fw-bold mt-1 mb-0"><?= number_format($product_count) ?></h2>
                </div>
                <div style="font-size: 2.5rem; color: #e63946; opacity: 0.5;">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
            <a href="products.php" class="small text-decoration-none mt-2 d-inline-block">View all products →</a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase small fw-bold">Brands</span>
                    <h2 class="fw-bold mt-1 mb-0"><?= number_format($brand_count) ?></h2>
                </div>
                <div style="font-size: 2.5rem; color: #0d6efd; opacity: 0.5;">
                    <i class="bi bi-tag"></i>
                </div>
            </div>
            <a href="brands.php" class="small text-decoration-none mt-2 d-inline-block">Manage brands →</a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase small fw-bold">Categories</span>
                    <h2 class="fw-bold mt-1 mb-0"><?= number_format($category_count) ?></h2>
                </div>
                <div style="font-size: 2.5rem; color: #198754; opacity: 0.5;">
                    <i class="bi bi-grid"></i>
                </div>
            </div>
            <a href="categories.php" class="small text-decoration-none mt-2 d-inline-block">Manage categories →</a>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="admin-card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted text-uppercase small fw-bold">Total Stock</span>
                    <h2 class="fw-bold mt-1 mb-0"><?= number_format($stock_total) ?></h2>
                </div>
                <div style="font-size: 2.5rem; color: #ffc107; opacity: 0.5;">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <span class="small text-muted">Items across all variants</span>
        </div>
    </div>
</div>

<!-- Recent Products -->
<div class="admin-card mt-4">
    <div class="card-header">
        <span>Recent Products</span>
        <a href="products.php" class="btn btn-sm btn-outline-primary float-end">View All</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_products)): ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">No products added yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['brand_name']) ?></td>
                            <td>
                                <span class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mt-3">
    <div class="col-md-4">
        <a href="product-add.php" class="text-decoration-none">
            <div class="admin-card p-4 text-center hover-card" style="transition: all 0.3s ease; cursor: pointer;">
                <div style="font-size: 2rem; color: #e63946; margin-bottom: 0.5rem;">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <h6 class="fw-bold mb-0">Add New Product</h6>
                <small class="text-muted">Create a new product with colors & sizes</small>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="brand-add.php" class="text-decoration-none">
            <div class="admin-card p-4 text-center hover-card" style="transition: all 0.3s ease; cursor: pointer;">
                <div style="font-size: 2rem; color: #0d6efd; margin-bottom: 0.5rem;">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <h6 class="fw-bold mb-0">Add New Brand</h6>
                <small class="text-muted">Create a new brand with logo</small>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="category-add.php" class="text-decoration-none">
            <div class="admin-card p-4 text-center hover-card" style="transition: all 0.3s ease; cursor: pointer;">
                <div style="font-size: 2rem; color: #198754; margin-bottom: 0.5rem;">
                    <i class="bi bi-plus-circle"></i>
                </div>
                <h6 class="fw-bold mb-0">Add New Category</h6>
                <small class="text-muted">Create a new product category</small>
            </div>
        </a>
    </div>
</div>

<!-- Add hover style -->
<style>
.hover-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.08) !important;
}
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>