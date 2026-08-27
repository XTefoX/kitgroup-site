<?php
// admin/brands.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/templates/header.php';



$brands = getBrands();

$page_title = "Manage Brands – Admin";
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Brands</h2>
    <a href="brand-add.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add New Brand
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th style="width:80px;">Logo</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($brands)): ?>
                        <tr><td colspan="5" class="text-center py-3 text-muted">No brands found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($brands as $b): ?>
                        <tr>
                            <td><?= $b['id'] ?></td>
                            <td>
                                <?php if ($b['logo']): ?>
                                    <img src="<?= getBrandLogoUrl($b['logo']) ?>" alt="<?= htmlspecialchars($b['name']) ?>" style="height:40px; width:auto; max-width:60px; object-fit:contain;">
                                <?php else: ?>
                                    <span class="text-muted">No logo</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($b['name']) ?></td>
                            <td><?= htmlspecialchars($b['slug']) ?></td>
                            <td>
                                <a href="brand-edit.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i> Edit</a>
                                <a href="brand-delete.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-danger delete-confirm"><i class="bi bi-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>