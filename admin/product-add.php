<?php
// admin/product-add.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$brands = getBrands();
$categories = getCategories();

$error = '';
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Basic product data
        $product_data = [
            'brand_id' => (int)$_POST['brand_id'],
            'category_id' => (int)$_POST['category_id'],
            'name' => trim($_POST['name']),
            'slug' => !empty($_POST['slug']) ? trim($_POST['slug']) : createSlug($_POST['name']),
            'description' => trim($_POST['description']),
            'short_description' => trim($_POST['short_description']),
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_made_in_botswana' => isset($_POST['is_made_in_botswana']) ? 1 : 0,
            'is_orderable' => isset($_POST['is_orderable']) ? 1 : 0,
            'colors' => []
        ];

        // Process colors
        $color_names = $_POST['color_name'] ?? [];
        $color_hexes = $_POST['color_hex'] ?? [];
        $color_primary = $_POST['color_primary'] ?? [];
        $color_images = $_FILES['color_image'] ?? [];
        $color_sort = $_POST['color_sort'] ?? [];

        for ($i = 0; $i < count($color_names); $i++) {
            if (empty($color_names[$i])) continue;
            
            $image_file = null;
            if (isset($color_images['tmp_name'][$i]) && $color_images['error'][$i] === UPLOAD_ERR_OK) {
                $image_file = uploadImage(
                    ['name' => $color_images['name'][$i], 'tmp_name' => $color_images['tmp_name'][$i], 'error' => $color_images['error'][$i]],
                    'products',
                    'product_' . $i
                );
            }
            
            $color = [
                'name' => $color_names[$i],
                'hex' => $color_hexes[$i] ?? null,
                'image' => $image_file,
                'is_primary' => isset($color_primary[$i]) ? 1 : 0,
                'sort_order' => $color_sort[$i] ?? 0,
                'sizes' => []
            ];

            // Process sizes for this color
            $size_names = $_POST['size_name'][$i] ?? [];
            $size_skus = $_POST['size_sku'][$i] ?? [];
            $size_prices = $_POST['size_price'][$i] ?? [];
            $size_compare = $_POST['size_compare'][$i] ?? [];
            $size_stock = $_POST['size_stock'][$i] ?? [];
            $size_active = $_POST['size_active'][$i] ?? [];

            for ($j = 0; $j < count($size_names); $j++) {
                if (empty($size_names[$j])) continue;
                $color['sizes'][] = [
                    'size' => $size_names[$j],
                    'sku' => $size_skus[$j] ?? '',
                    'price' => (float)$size_prices[$j],
                    'compare_price' => !empty($size_compare[$j]) ? (float)$size_compare[$j] : null,
                    'stock' => (int)$size_stock[$j],
                    'is_active' => isset($size_active[$j]) ? 1 : 0
                ];
            }
            $product_data['colors'][] = $color;
        }

        // Save product
        $product_id = addProduct($product_data);
        if ($product_id) {
            // ===== SAVE USUALLY BOUGHT WITH =====
            if (isset($_POST['related_products']) && is_array($_POST['related_products'])) {
                $related_ids = array_map('intval', $_POST['related_products']);
                saveRelatedProducts($product_id, $related_ids);
            }
            
            $success = true;
            header('Location: product-edit.php?id=' . $product_id . '&msg=added');
            exit;
        } else {
            $error = 'Failed to add product.';
        }
    } catch (Exception $e) {
        $error = 'Error: ' . $e->getMessage();
    }
}

// Set page title
$page_title = "Add Product – Admin";
$hide_breadcrumb = true;
require_once __DIR__ . '/templates/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Add New Product</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <!-- ===== BASIC INFO ===== -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">Basic Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Product Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" class="form-control" placeholder="Leave blank to auto-generate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Brand *</label>
                        <select name="brand_id" class="form-select" required>
                            <option value="">Select Brand</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?= $brand['id'] ?>"><?= htmlspecialchars($brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <!-- ===== CATEGORY DROPDOWN WITH OPTGROUP ===== -->
                    <div class="col-md-6">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php 
                            $category_tree = getCategoryTree();
                            foreach ($category_tree as $parent_id => $parent): 
                            ?>
                                <optgroup label="<?= htmlspecialchars($parent['name']) ?>">
                                    <?php if (!empty($parent['children'])): ?>
                                        <?php foreach ($parent['children'] as $child): ?>
                                            <option value="<?= $child['id'] ?>">
                                                └ <?= htmlspecialchars($child['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="<?= $parent['id'] ?>">
                                            <?= htmlspecialchars($parent['name']) ?>
                                        </option>
                                    <?php endif; ?>
                                </optgroup>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Products should be assigned to the most specific (child) category.</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Short Description</label>
                        <input type="text" name="short_description" class="form-control" maxlength="255">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Full Description</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                    
                    <!-- ===== CHECKBOXES ROW ===== -->
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="featured" class="form-check-input" id="featured">
                            <label class="form-check-label" for="featured">Featured Product</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" checked>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
<<<<<<< HEAD
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_made_in_botswana" class="form-check-input" id="is_made_in_botswana" <?= isset($product) && $product['is_made_in_botswana'] ? 'checked' : '' ?>>
=======
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_made_in_botswana" class="form-check-input" id="is_made_in_botswana">
>>>>>>> 28ddb57b97b4bbfe9b7141303919f30aa214af9e
                            <label class="form-check-label" for="is_made_in_botswana">
                                <span class="badge bg-success"><i class="bi bi-flag"></i> Made in Botswana</span>
                            </label>
                        </div>
                    </div>
<<<<<<< HEAD
=======
                    <div class="col-md-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_orderable" class="form-check-input" id="is_orderable">
                            <label class="form-check-label" for="is_orderable">
                                <span class="badge bg-warning text-dark"><i class="bi bi-clock"></i> Orderable (No Stock Kept)</span>
                            </label>
                            <div class="form-text">Check this if the product is only available on order.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== USUALLY BOUGHT WITH ====== -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <i class="bi bi-link-45deg me-2"></i> Usually Bought With
            </div>
            <div class="card-body">
                <p class="text-muted small">Select products that are <strong>explicitly paired</strong> with this product (e.g., a shoe with a specific sock).</p>
                <div class="row">
                    <div class="col-md-12">
                        <select name="related_products[]" class="form-select" multiple style="height: 200px;">
                            <?php 
                            $all_products = getProducts();
                            foreach ($all_products as $p):
                            ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['brand_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple products.</small>
                    </div>
>>>>>>> 28ddb57b97b4bbfe9b7141303919f30aa214af9e
                </div>
            </div>
        </div>

        <!-- ===== COLORS & VARIANTS ===== -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span>Colors & Variants</span>
                <button type="button" class="btn btn-light btn-sm" id="addColorBtn">+ Add Color</button>
            </div>
            <div class="card-body" id="colorsContainer">
                <!-- Color blocks will be added here via JavaScript -->
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Save Product</button>
        <a href="products.php" class="btn btn-secondary btn-lg">Cancel</a>
    </form>
</div>

<!-- ====== JAVASCRIPT FOR DYNAMIC COLOR/VARIANT WITH BULK SIZE GENERATOR ====== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('colorsContainer');
    const addColorBtn = document.getElementById('addColorBtn');
    let colorIndex = 0;

    // ============================================================
    // SIZE RANGE PRESETS
    // ============================================================
    const SIZE_RANGES = {
        'clothing': {
            label: 'Clothing Sizes (XS - 4XL)',
            sizes: ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL']
        },
        'numeric-even': {
            label: 'Numeric Sizes (26 - 64, even)',
            generate: function() {
                const sizes = [];
                for (let i = 26; i <= 64; i += 2) {
                    sizes.push(i.toString());
                }
                return sizes;
            }
        },
        'shoes': {
            label: 'Shoe Sizes (2 - 14)',
            generate: function() {
                const sizes = [];
                for (let i = 2; i <= 14; i++) {
                    sizes.push(i.toString());
                }
                return sizes;
            }
        },
        'universal': {
            label: 'Universal (One Size Fits All)',
            sizes: ['Universal']
        }
    };

    // ============================================================
    // MAIN FUNCTION – Add Color Block with Bulk Size Generator
    // ============================================================
    function addColorBlock(colorData = null) {
        const block = document.createElement('div');
        block.className = 'color-block border rounded p-3 mb-3';
        block.dataset.index = colorIndex;

        const colorName = colorData?.name || '';
        const colorHex = colorData?.hex || '#cccccc';
        const isPrimary = colorData?.is_primary || false;

        block.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <h6 class="fw-bold">Color #${colorIndex + 1}</h6>
                <button type="button" class="btn btn-danger btn-sm remove-color-btn">×</button>
            </div>
            <div class="row g-2">
                <div class="col-md-3">
                    <label class="form-label">Color Name *</label>
                    <input type="text" name="color_name[]" class="form-control color-name" value="${colorName}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Hex</label>
                    <input type="color" name="color_hex[]" class="form-control form-control-color color-hex" value="${colorHex}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="color_image[]" class="form-control" accept="image/*">
                    ${colorData?.image ? `<small>Current: ${colorData.image}</small>` : ''}
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="color_sort[]" class="form-control" value="${colorData?.sort_order || 0}" min="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check">
                        <input type="checkbox" name="color_primary[]" class="form-check-input primary-check" ${isPrimary ? 'checked' : ''}>
                        <label class="form-check-label">Primary</label>
                    </div>
                </div>
            </div>
            
            <!-- ====== BULK SIZE GENERATOR ====== -->
            <div class="mt-3 p-3 bg-light rounded">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Bulk Add Sizes</label>
                        <select class="form-select form-select-sm size-range-type">
                            <option value="">Select Range Type...</option>
                            <option value="clothing">Clothing Sizes (XS - 4XL)</option>
                            <option value="numeric-even">Numeric Sizes (26 - 64, even)</option>
                            <option value="shoes">Shoe Sizes (2 - 14)</option>
                            <option value="universal">Universal (One Size Fits All)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control form-control-sm bulk-price" placeholder="e.g. 289.00">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Compare Price</label>
                        <input type="number" step="0.01" class="form-control form-control-sm bulk-compare" placeholder="e.g. 349.00">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control form-control-sm bulk-stock" placeholder="e.g. 10" value="0">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary btn-sm w-100 generate-sizes-btn">
                            <i class="bi bi-lightning-fill me-1"></i> Generate Sizes
                        </button>
                    </div>
                </div>
                <div class="form-text small mt-1">Select a range type, set price/stock, then click Generate Sizes.</div>
            </div>

            <div class="mt-2">
                <label class="form-label">Sizes / Variants</label>
                <div class="size-container">
                    <!-- Size rows will be added here -->
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm mt-2 add-size-btn">+ Add Individual Size</button>
            </div>
        `;

        container.appendChild(block);
        colorIndex++;

        // Attach event listeners
        attachColorBlockEvents(block);

        // Reindex color numbers
        reindexColors();

        // Initialize with one size row if no sizes provided
        if (!colorData?.sizes || colorData.sizes.length === 0) {
            addSizeRow(block);
        } else {
            colorData.sizes.forEach(size => addSizeRow(block, size));
        }
    }

    // ============================================================
    // ATTACH EVENTS TO A COLOR BLOCK
    // ============================================================
    function attachColorBlockEvents(block) {
        // Remove color block
        block.querySelector('.remove-color-btn').addEventListener('click', function() {
            if (confirm('Remove this color block?')) {
                block.remove();
                reindexColors();
            }
        });

        // Add individual size
        block.querySelector('.add-size-btn').addEventListener('click', function() {
            addSizeRow(block);
        });

        // Primary checkbox exclusive
        block.querySelector('.primary-check').addEventListener('change', function() {
            if (this.checked) {
                document.querySelectorAll('.primary-check').forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
            }
        });

        // ====== BULK SIZE GENERATOR ======
        const generateBtn = block.querySelector('.generate-sizes-btn');
        const rangeType = block.querySelector('.size-range-type');
        const bulkPrice = block.querySelector('.bulk-price');
        const bulkCompare = block.querySelector('.bulk-compare');
        const bulkStock = block.querySelector('.bulk-stock');

        generateBtn.addEventListener('click', function() {
            const type = rangeType.value;
            if (!type) {
                alert('Please select a size range type.');
                return;
            }

            const price = parseFloat(bulkPrice.value);
            if (isNaN(price) || price <= 0) {
                alert('Please enter a valid price.');
                return;
            }

            const compare = bulkCompare.value ? parseFloat(bulkCompare.value) : null;
            const stock = parseInt(bulkStock.value) || 0;

            // Get sizes based on range type
            let sizes = [];
            if (type === 'clothing') {
                sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'];
            } else if (type === 'numeric-even') {
                for (let i = 26; i <= 64; i += 2) {
                    sizes.push(i.toString());
                }
            } else if (type === 'shoes') {
                for (let i = 2; i <= 14; i++) {
                    sizes.push(i.toString());
                }
            } else if (type === 'universal') {
                sizes = ['Universal'];
            }

            if (sizes.length === 0) {
                alert('No sizes generated for the selected range.');
                return;
            }

            // Clear existing sizes
            const sizeContainer = block.querySelector('.size-container');
            sizeContainer.innerHTML = '';

            // Generate size rows
            sizes.forEach(size => {
                addSizeRow(block, {
                    size: size,
                    sku: '',  // Will be auto-generated
                    price: price,
                    compare_price: compare,
                    stock: stock,
                    is_active: true
                });
            });

            // Auto-generate SKUs based on product slug (if available)
            const productSlug = document.querySelector('input[name="slug"]').value || 
                               document.querySelector('input[name="name"]').value?.toLowerCase().replace(/[^a-z0-9-]/g, '-') || 
                               'product';
            const colorName = block.querySelector('.color-name').value.toLowerCase().replace(/\s+/g, '-');
            
            block.querySelectorAll('.size-row').forEach(row => {
                const sizeInput = row.querySelector('input[name*="size_name"]');
                const skuInput = row.querySelector('input[name*="size_sku"]');
                if (sizeInput && skuInput && !skuInput.value) {
                    const size = sizeInput.value.toLowerCase();
                    skuInput.value = `${productSlug}-${size}-${colorName}`;
                }
            });

            // Show success message
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = `<i class="bi bi-check-circle me-1"></i> ${sizes.length} sizes generated!`;
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 3000);
        });
    }

    // ============================================================
    // ADD A SINGLE SIZE ROW
    // ============================================================
    function addSizeRow(block, sizeData = null) {
        const container = block.querySelector('.size-container');
        const row = document.createElement('div');
        row.className = 'size-row row g-2 mb-2 align-items-end';

        const sizeName = sizeData?.size || '';
        const sku = sizeData?.sku || '';
        const price = sizeData?.price || '';
        const compare = sizeData?.compare_price || '';
        const stock = sizeData?.stock || 0;
        const isActive = sizeData?.is_active !== undefined ? sizeData.is_active : true;

        const colorIndex = block.dataset.index;
        row.innerHTML = `
            <div class="col-md-2">
                <label class="form-label">Size *</label>
                <input type="text" name="size_name[${colorIndex}][]" class="form-control form-control-sm size-input" value="${sizeName}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">SKU</label>
                <input type="text" name="size_sku[${colorIndex}][]" class="form-control form-control-sm sku-input" value="${sku}" placeholder="Auto-generated">
            </div>
            <div class="col-md-2">
                <label class="form-label">Price *</label>
                <input type="number" step="0.01" name="size_price[${colorIndex}][]" class="form-control form-control-sm price-input" value="${price}" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Compare Price</label>
                <input type="number" step="0.01" name="size_compare[${colorIndex}][]" class="form-control form-control-sm compare-input" value="${compare}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Stock</label>
                <input type="number" name="size_stock[${colorIndex}][]" class="form-control form-control-sm stock-input" value="${stock}">
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input type="checkbox" name="size_active[${colorIndex}][]" class="form-check-input" ${isActive ? 'checked' : ''}>
                    <label class="form-check-label">Active</label>
                </div>
            </div>
            <div class="col-md-1">
                <div class="form-check">
                    <input type="checkbox" name="size_orderable[<?= $colorIndex ?>][]" class="form-check-input" <?= $sizeData['is_orderable'] ?? false ? 'checked' : '' ?>>
                    <label class="form-check-label" title="Orderable when out of stock">Orderable</label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm remove-size-btn">×</button>
            </div>
        `;

        container.appendChild(row);

        // Remove size row
        row.querySelector('.remove-size-btn').addEventListener('click', function() {
            row.remove();
        });

        // Auto-generate SKU when size or color changes
        const sizeInput = row.querySelector('.size-input');
        const skuInput = row.querySelector('.sku-input');
        const colorNameInput = block.querySelector('.color-name');
        
        function autoGenerateSku() {
            if (!skuInput.value) {
                const productSlug = document.querySelector('input[name="slug"]').value || 
                                   document.querySelector('input[name="name"]').value?.toLowerCase().replace(/[^a-z0-9-]/g, '-') || 
                                   'product';
                const size = sizeInput.value.toLowerCase();
                const color = colorNameInput.value.toLowerCase().replace(/\s+/g, '-');
                if (size) {
                    skuInput.value = `${productSlug}-${size}-${color}`;
                }
            }
        }

        sizeInput.addEventListener('blur', autoGenerateSku);
        colorNameInput.addEventListener('change', function() {
            block.querySelectorAll('.sku-input').forEach(input => {
                if (!input.value) {
                    const size = input.closest('.size-row').querySelector('.size-input').value.toLowerCase();
                    const color = this.value.toLowerCase().replace(/\s+/g, '-');
                    const productSlug = document.querySelector('input[name="slug"]').value || 
                                       document.querySelector('input[name="name"]').value?.toLowerCase().replace(/[^a-z0-9-]/g, '-') || 
                                       'product';
                    if (size) {
                        input.value = `${productSlug}-${size}-${color}`;
                    }
                }
            });
        });

        // Also auto-generate on product name/slug change
        document.querySelector('input[name="name"]').addEventListener('blur', function() {
            block.querySelectorAll('.sku-input').forEach(input => {
                if (!input.value) {
                    const size = input.closest('.size-row').querySelector('.size-input').value.toLowerCase();
                    const color = colorNameInput.value.toLowerCase().replace(/\s+/g, '-');
                    const slug = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '-');
                    if (size) {
                        input.value = `${slug}-${size}-${color}`;
                    }
                }
            });
        });
    }

    // ============================================================
    // REINDEX COLOR BLOCKS
    // ============================================================
    function reindexColors() {
        const blocks = container.querySelectorAll('.color-block');
        blocks.forEach((block, index) => {
            block.dataset.index = index;
            const h6 = block.querySelector('h6');
            if (h6) h6.textContent = `Color #${index + 1}`;
            block.querySelectorAll('.size-row').forEach(row => {
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                    }
                });
            });
        });
    }

    // ============================================================
    // INITIALIZE
    // ============================================================
    addColorBlock();

    addColorBtn.addEventListener('click', function() {
        addColorBlock();
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const colorBlocks = container.querySelectorAll('.color-block');
        if (colorBlocks.length === 0) {
            e.preventDefault();
            alert('Please add at least one color.');
            return;
        }
        let valid = true;
        colorBlocks.forEach(block => {
            const sizes = block.querySelectorAll('.size-row');
            if (sizes.length === 0) {
                alert('Each color must have at least one size.');
                valid = false;
            }
        });
        if (!valid) e.preventDefault();
    });

});
</script>

<style>
.color-block {
    background: #f8f9fa;
    border-left: 4px solid #28a745;
}
.color-block .size-row {
    background: #fff;
    padding: 8px;
    border-radius: 5px;
    border: 1px solid #dee2e6;
}
.bulk-size-generator {
    background: #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
}
</style>

<?php require_once __DIR__ . '/templates/footer.php'; ?>