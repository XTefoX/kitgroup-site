<?php
// admin/category-edit.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';



$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: categories.php');
    exit;
}

$category = getCategory($id);
if (!$category) {
    header('Location: categories.php');
    exit;
}

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $slug = trim($_POST['slug']);
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    
    // Prevent self-parenting
    if ($parent_id == $id) {
        $error = 'A category cannot be its own parent.';
    } else {
        if (updateCategory($id, $name, $slug, $description, $parent_id)) {
            $success = true;
            $category = getCategory($id);
        } else {
            $error = 'Failed to update category.';
        }
    }
}

$categories = getCategories();
$page_title = "Edit Category – Admin";

require_once __DIR__ . '/templates/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Category</h2>
    <a href="categories.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back to Categories</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success">Category updated successfully!</div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="admin-card">
    <div class="card-body">
        <form method="post">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Category Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($category['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug']) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($category['description']) ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Parent Category</label>
                    <select name="parent_id" class="form-select">
                        <option value="">None (Top Level)</option>
                        <?php foreach ($categories as $cat): ?>
                            <?php if ($cat['id'] == $id) continue; // skip self ?>
                            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category['parent_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Category</button>
                    <a href="categories.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>