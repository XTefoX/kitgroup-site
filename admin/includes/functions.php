<?php
// ============================================================
// ADMIN CRUD FUNCTIONS
// ============================================================

// ===== BRANDS =====
function addBrand($name, $slug, $description, $logo_file = null) {
    $pdo = getDB();
    $logo = $logo_file ?: null;
    $stmt = $pdo->prepare("INSERT INTO brands (name, slug, description, logo) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $slug, $description, $logo]);
}

function updateBrand($id, $name, $slug, $description, $logo_file = null) {
    $pdo = getDB();
    if ($logo_file) {
        $stmt = $pdo->prepare("UPDATE brands SET name=?, slug=?, description=?, logo=? WHERE id=?");
        return $stmt->execute([$name, $slug, $description, $logo_file, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE brands SET name=?, slug=?, description=? WHERE id=?");
        return $stmt->execute([$name, $slug, $description, $id]);
    }
}

function deleteBrand($id) {
    $pdo = getDB();
    // First get logo path to delete file
    $stmt = $pdo->prepare("SELECT logo FROM brands WHERE id=?");
    $stmt->execute([$id]);
    $brand = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($brand && $brand['logo']) {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/brands/' . $brand['logo'];
        if (file_exists($file)) unlink($file);
    }
    $stmt = $pdo->prepare("DELETE FROM brands WHERE id=?");
    return $stmt->execute([$id]);
}

function getBrand($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===== CATEGORIES =====
function addCategory($name, $slug, $description, $parent_id = null) {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, parent_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $slug, $description, $parent_id]);
}

function updateCategory($id, $name, $slug, $description, $parent_id = null) {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE categories SET name=?, slug=?, description=?, parent_id=? WHERE id=?");
    return $stmt->execute([$name, $slug, $description, $parent_id, $id]);
}

function deleteCategory($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    return $stmt->execute([$id]);
}

function getCategory($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===== PRODUCTS =====
function addProduct($data) {
    $pdo = getDB();
    $pdo->beginTransaction();
    try {
        // Insert product
        $stmt = $pdo->prepare("INSERT INTO products 
            (brand_id, category_id, name, slug, description, short_description, featured, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['brand_id'],
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['featured'] ? 1 : 0,
            $data['is_active'] ? 1 : 0
        ]);
        $product_id = $pdo->lastInsertId();

        // Insert color images
        if (isset($data['colors']) && is_array($data['colors'])) {
            foreach ($data['colors'] as $color) {
                $stmt = $pdo->prepare("INSERT INTO product_color_images 
                    (product_id, color, color_hex, image_url, is_primary, sort_order) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $product_id,
                    $color['name'],
                    $color['hex'] ?? null,
                    $color['image'] ?? null,
                    $color['is_primary'] ? 1 : 0,
                    $color['sort_order'] ?? 0
                ]);
                $color_id = $pdo->lastInsertId();

                // Insert variants for this color
                if (isset($color['sizes']) && is_array($color['sizes'])) {
                    foreach ($color['sizes'] as $size) {
                        $stmt = $pdo->prepare("INSERT INTO product_variants 
                            (product_id, sku, size, color, price, compare_price, stock, is_active) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $product_id,
                            $size['sku'],
                            $size['size'],
                            $color['name'],
                            $size['price'],
                            $size['compare_price'] ?? null,
                            $size['stock'],
                            $size['is_active'] ? 1 : 0
                        ]);
                    }
                }
            }
        }
        $pdo->commit();
        return $product_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function updateProduct($id, $data) {
    $pdo = getDB();
    $pdo->beginTransaction();
    try {
        // Update product
        $stmt = $pdo->prepare("UPDATE products SET 
            brand_id=?, category_id=?, name=?, slug=?, description=?, short_description=?, 
            featured=?, is_active=? WHERE id=?");
        $stmt->execute([
            $data['brand_id'],
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['featured'] ? 1 : 0,
            $data['is_active'] ? 1 : 0,
            $id
        ]);

        // Delete existing colors and variants (cascade on DB, but we need to handle images)
        // We'll delete image files manually later
        // First get existing color images to delete files
        $stmt = $pdo->prepare("SELECT image_url FROM product_color_images WHERE product_id=?");
        $stmt->execute([$id]);
        $old_images = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($old_images as $img) {
            if ($img) {
                $file = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/products/' . $img;
                if (file_exists($file)) unlink($file);
            }
        }

        // Delete variants and color images
        $stmt = $pdo->prepare("DELETE FROM product_variants WHERE product_id=?");
        $stmt->execute([$id]);
        $stmt = $pdo->prepare("DELETE FROM product_color_images WHERE product_id=?");
        $stmt->execute([$id]);

        // Re-insert colors and variants
        if (isset($data['colors']) && is_array($data['colors'])) {
            foreach ($data['colors'] as $color) {
                $stmt = $pdo->prepare("INSERT INTO product_color_images 
                    (product_id, color, color_hex, image_url, is_primary, sort_order) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $id,
                    $color['name'],
                    $color['hex'] ?? null,
                    $color['image'] ?? null,
                    $color['is_primary'] ? 1 : 0,
                    $color['sort_order'] ?? 0
                ]);
                $color_id = $pdo->lastInsertId();

                if (isset($color['sizes']) && is_array($color['sizes'])) {
                    foreach ($color['sizes'] as $size) {
                        $stmt = $pdo->prepare("INSERT INTO product_variants 
                            (product_id, sku, size, color, price, compare_price, stock, is_active) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $id,
                            $size['sku'],
                            $size['size'],
                            $color['name'],
                            $size['price'],
                            $size['compare_price'] ?? null,
                            $size['stock'],
                            $size['is_active'] ? 1 : 0
                        ]);
                    }
                }
            }
        }
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function deleteProduct($id) {
    $pdo = getDB();
    // Get all color images to delete files
    $stmt = $pdo->prepare("SELECT image_url FROM product_color_images WHERE product_id=?");
    $stmt->execute([$id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($images as $img) {
        if ($img) {
            $file = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/products/' . $img;
            if (file_exists($file)) unlink($file);
        }
    }
    // Delete product (cascade will delete variants and color_images)
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    return $stmt->execute([$id]);
}

function getProduct($id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ===== IMAGE UPLOAD HELPER =====
function uploadImage($file, $target_dir, $prefix = '') {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return null;
    
    // Generate unique filename
    $filename = $prefix . '_' . uniqid() . '.' . $ext;
    $destination = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/' . $target_dir . '/' . $filename;
    
    // Ensure directory exists
    $dir = dirname($destination);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    return null;
}
// ============================================================
// BRAND & CATEGORY IMAGE HELPERS
// ============================================================

/**
 * Get the full URL path for a brand logo
 */
function getBrandLogoUrl($logo_filename) {
    if (empty($logo_filename)) return null;
    return '/kitgroup/assets/images/brands/' . $logo_filename;
}

/**
 * Get the full URL path for a category icon (if used)
 */
function getCategoryIconUrl($icon_filename) {
    if (empty($icon_filename)) return null;
    return '/kitgroup/assets/images/categories/' . $icon_filename;
}

/**
 * Validate image file type (only WEBP and PNG allowed)
 */
function validateImageType($file) {
    $allowed = ['image/webp', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    return in_array($mime, $allowed);
}

/**
 * Upload image with type validation
 */
function uploadBrandImage($file, $prefix = 'brand') {
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    
    // Validate type
    if (!validateImageType($file)) {
        return null; // or throw exception
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    // Only allow webp and png
    if (!in_array($ext, ['webp', 'png'])) {
        return null;
    }
    
    $filename = $prefix . '_' . uniqid() . '.' . $ext;
    $destination = $_SERVER['DOCUMENT_ROOT'] . '/kitgroup/assets/images/brands/' . $filename;
    
    // Ensure directory exists
    $dir = dirname($destination);
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    return null;
}