<?php
// admin/brand-edit.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';



$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: brands.php');
    exit;
}

$brand = getBrand($id);
if (!$brand) {
    header('Location: brands.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = trim($_POST['slug']);
    $logo_filename = $brand['logo']; // keep existing
    
    // Handle new logo upload (replace)
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $new_logo = uploadBrandImage($_FILES['logo'], 'brand');
        if ($new_logo) {
            // Delete old logo file
            if ($brand['logo']) {
                $old_file = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/brands/' . $brand['logo'];
                if (file_exists($old_file)) unlink($old_file);
            }
            $logo_filename = $new_logo;
        } else {
            $error = 'Logo must be a WEBP or PNG image.';
        }
    }
    
    // Handle remove logo checkbox
    if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
        if ($brand['logo']) {
            $old_file = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/brands/' . $brand['logo'];
            if (file_exists($old_file)) unlink($old_file);
        }
        $logo_filename = null;
    }
    
    if (!$error) {
        if (updateBrand($id, $name, $slug, $description, $logo_filename)) {
            $success = true;
            // Refresh brand data
            $brand = getBrand($id);
        } else {
            $error = 'Failed to update brand.';
        }
    }
}

$page_title = "Edit Brand – Admin";

require_once __DIR__ . '/templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Brand</h2>
    <a href="brands.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Brands</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">Brand updated successfully!</div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Brand Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($brand['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($brand['slug']) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($brand['description']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Current Logo</label>
                    <?php if ($brand['logo']): ?>
                        <div class="mb-2">
                            <img src="<?= getBrandLogoUrl($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>" style="height:80px; border:1px solid #dee2e6; border-radius:8px; padding:4px;">
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="remove_logo" value="1" class="form-check-input" id="removeLogo">
                            <label class="form-check-label" for="removeLogo">Remove existing logo</label>
                        </div>
                    <?php else: ?>
                        <span class="text-muted">No logo uploaded</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Upload New Logo (WEBP or PNG)</label>
                    <input type="file" name="logo" class="form-control" accept=".webp,.png,image/webp,image/png">
                    <div class="form-text">Leave empty to keep current logo (unless checked remove).</div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Brand</button>
                    <a href="brands.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>