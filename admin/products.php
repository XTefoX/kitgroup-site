<?php
// admin/products.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$products = getProducts(); // Reuse existing function

$page_title = "Manage Products – Admin";
$hide_breadcrumb = true;
require_once __DIR__ . '/templates/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Manage Products</h1>
        <a href="product-add.php" class="btn btn-primary">+ Add New Product</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Price Range</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td>
                        <?php if ($p['default_image']): ?>
                            <img src="/kitgroup/assets/images/products/<?= htmlspecialchars($p['default_image']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:5px;">
                        <?php else: ?>
                            <span class="text-muted">No image</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['brand_name']) ?></td>
                    <td><?= htmlspecialchars($p['category_name']) ?></td>
                    <td>
                        <?php if ($p['min_price'] && $p['max_price']): ?>
                            <?php if ($p['min_price'] == $p['max_price']): ?>
                                P <?= number_format($p['min_price'], 2) ?>
                            <?php else: ?>
                                P <?= number_format($p['min_price'], 2) ?> – P <?= number_format($p['max_price'], 2) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">–</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['total_stock'] ?? 0 ?></td>
                    <td>
                        <span class="badge <?= $p['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $p['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="product-delete.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                    <tr><td colspan="9" class="text-center">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>