<?php
// admin/brand-add.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';



$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = !empty($_POST['slug']) ? trim($_POST['slug']) : createSlug($name);
    
    // Handle logo upload
    $logo_filename = null;
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $logo_filename = uploadBrandImage($_FILES['logo'], 'brand');
        if (!$logo_filename) {
            $error = 'Logo must be a WEBP or PNG image.';
        }
    }
    
    if (!$error) {
        if (addBrand($name, $slug, $description, $logo_filename)) {
            header('Location: brands.php?msg=added');
            exit;
        } else {
            $error = 'Failed to add brand.';
        }
    }
}

$page_title = "Add Brand – Admin";

require_once __DIR__ . '/templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add New Brand</h2>
    <a href="brands.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Brands</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Brand Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-control" placeholder="Auto-generated if empty" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo (WEBP or PNG only)</label>
                    <input type="file" name="logo" class="form-control" accept=".webp,.png,image/webp,image/png">
                    <div class="form-text">Max size: 2MB. Recommended: 200x200px.</div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Save Brand</button>
                    <a href="brands.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>