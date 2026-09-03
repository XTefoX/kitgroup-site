<?php
// includes/functions.php

// ============================================================
// DATABASE CONNECTION
// ============================================================
require_once __DIR__ . '/db.php';

// ============================================================
// CATEGORY FUNCTIONS
// ============================================================

/**
 * Get all categories
 */
function getCategories() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get a category by slug
 */
function getCategoryBySlug($slug) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ============================================================
// BRAND FUNCTIONS
// ============================================================

/**
 * Get all brands
 */
function getBrands() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM brands ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get a brand by slug
 */
function getBrandBySlug($slug) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE slug = ? LIMIT 1");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ============================================================
// PRODUCT FUNCTIONS
// ============================================================

/**
 * Get products with filters and aggregate color/size info
 */
function getProducts($category_slug = null, $brand_slug = null, $limit = null) {
    $pdo = getDB();
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
                b.name as brand_name,
                b.slug as brand_slug,
                c.name as category_name,
                c.slug as category_slug,
                (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as min_price,
                (SELECT MAX(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as max_price,
                (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id AND is_active = 1) as total_stock,
                (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image,
                GROUP_CONCAT(DISTINCT pci.color ORDER BY pci.sort_order) as colors,
                GROUP_CONCAT(DISTINCT pv.size ORDER BY FIELD(pv.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', 'Universal')) as sizes
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_color_images pci ON p.id = pci.product_id
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_active = 1
            WHERE p.is_active = 1";
    
    $params = [];
    
    if ($category_slug) {
        $sql .= " AND c.slug = ?";
        $params[] = $category_slug;
    }
    
    if ($brand_slug) {
        $sql .= " AND b.slug = ?";
        $params[] = $brand_slug;
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.featured DESC, p.name ASC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get a single product with all variants and color images
 */
function getProductBySlug($slug) {
    $pdo = getDB();
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
                b.name as brand_name,
                b.slug as brand_slug,
                b.logo as brand_logo,
                c.name as category_name,
                c.slug as category_slug
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.slug = ? AND p.is_active = 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$slug]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $product['color_images'] = getProductColorImages($product['id']);
        $product['variants'] = getProductVariants($product['id']);
        $product['sizes'] = getProductSizes($product['id']);
        $product['colors'] = getProductColors($product['id']);
    }
    
    return $product;
}

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Create a URL-friendly slug
 */
function createSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Format price with P sign
 */
function formatPrice($price) {
    return 'P ' . number_format($price, 2);
}

/**
 * Check if a product has stock
 */
function hasStock($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT SUM(stock) as total FROM product_variants WHERE product_id = ? AND is_active = 1");
    $stmt->execute([$product_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result && $result['total'] > 0);
}

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
            (brand_id, category_id, name, slug, description, short_description, featured, is_active, is_made_in_botswana, is_orderable) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['brand_id'],
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['featured'] ? 1 : 0,
            $data['is_active'] ? 1 : 0,
            $data['is_made_in_botswana'] ? 1 : 0,
            $data['is_orderable'] ? 1 : 0
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
        // Update product with new fields
        $stmt = $pdo->prepare("UPDATE products SET 
            brand_id=?, category_id=?, name=?, slug=?, description=?, short_description=?, 
            featured=?, is_active=?, is_made_in_botswana=?, is_orderable=? WHERE id=?");
        $stmt->execute([
            $data['brand_id'],
            $data['category_id'],
            $data['name'],
            $data['slug'],
            $data['description'],
            $data['short_description'],
            $data['featured'] ? 1 : 0,
            $data['is_active'] ? 1 : 0,
            $data['is_made_in_botswana'] ? 1 : 0,
            $data['is_orderable'] ? 1 : 0,
            $id
        ]);

        // Delete existing colors and variants (cascade on DB, but we need to handle images)
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
        return null;
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

// ============================================================
// ADVANCED PRODUCT FILTERING FUNCTIONS
// ============================================================

/**
 * Get products with advanced filtering (size, color, price)
 */
function getFilteredProducts($category_slug = null, $brand_slug = null, $filters = [], $search = null) {
    $pdo = getDB();
    
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
                b.name as brand_name,
                b.slug as brand_slug,
                c.name as category_name,
                c.slug as category_slug,
                c.parent_id as category_parent_id,
                (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as min_price,
                (SELECT MAX(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as max_price,
                (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id AND is_active = 1) as total_stock,
                (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image,
                GROUP_CONCAT(DISTINCT pci.color ORDER BY pci.sort_order) as colors,
                GROUP_CONCAT(DISTINCT pv.size ORDER BY FIELD(pv.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', 'Universal')) as sizes,
                GROUP_CONCAT(DISTINCT pv.color) as all_colors
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_color_images pci ON p.id = pci.product_id
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_active = 1
            WHERE p.is_active = 1";
    
    $params = [];
    
    // Category filter (including parent categories)
    if ($category_slug) {
        // Check if it's a parent category
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND parent_id IS NULL");
        $stmt->execute([$category_slug]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($parent) {
            // It's a parent category – get all child IDs
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
            $stmt->execute([$parent['id']]);
            $child_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($child_ids)) {
                $placeholders = implode(',', array_fill(0, count($child_ids), '?'));
                $sql .= " AND c.id IN ($placeholders)";
                $params = array_merge($params, $child_ids);
            }
        } else {
            // It's a child category – filter directly
            $sql .= " AND c.slug = ?";
            $params[] = $category_slug;
        }
    }
    
    // Brand filter
    if ($brand_slug) {
        $sql .= " AND b.slug = ?";
        $params[] = $brand_slug;
    }
    
    // Size filter
    if (!empty($filters['sizes']) && is_array($filters['sizes'])) {
        $size_placeholders = implode(',', array_fill(0, count($filters['sizes']), '?'));
        $sql .= " AND EXISTS (
            SELECT 1 FROM product_variants 
            WHERE product_id = p.id AND size IN ($size_placeholders) AND is_active = 1
        )";
        $params = array_merge($params, $filters['sizes']);
    }
    
    // Color filter
    if (!empty($filters['colors']) && is_array($filters['colors'])) {
        $color_placeholders = implode(',', array_fill(0, count($filters['colors']), '?'));
        $sql .= " AND EXISTS (
            SELECT 1 FROM product_variants 
            WHERE product_id = p.id AND color IN ($color_placeholders) AND is_active = 1
        )";
        $params = array_merge($params, $filters['colors']);
    }
    
    // Price range filter
    if (!empty($filters['min_price'])) {
        $sql .= " AND (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) >= ?";
        $params[] = (float)$filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $sql .= " AND (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) <= ?";
        $params[] = (float)$filters['max_price'];
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.featured DESC, p.name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Apply search filter if provided
    if ($search && !empty($products)) {
        $search_lower = strtolower($search);
        $products = array_filter($products, function($p) use ($search_lower) {
            return strpos(strtolower($p['name']), $search_lower) !== false ||
                   strpos(strtolower($p['brand_name']), $search_lower) !== false ||
                   strpos(strtolower($p['category_name']), $search_lower) !== false;
        });
    }
    
    return $products;
}

// ============================================================
// COLOR & VARIANT FUNCTIONS
// ============================================================

/**
 * Get color images for a product (includes hex values)
 */
function getProductColorImages($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM product_color_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get image URL for a specific color
 */
function getColorImage($product_id, $color) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT image_url FROM product_color_images WHERE product_id = ? AND color = ? LIMIT 1");
    $stmt->execute([$product_id, $color]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['image_url'] : null;
}

/**
 * Get unique colors with hex values for a product
 * Querying from product_color_images table (where hex is stored)
 */
function getProductColors($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT DISTINCT color, color_hex FROM product_color_images WHERE product_id = ? ORDER BY sort_order, color");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all variants for a product (size, color, price, stock)
 */
function getProductVariants($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ? AND is_active = 1 ORDER BY size, color");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get a specific variant by product_id, size, and color
 */
function getVariantBySizeColor($product_id, $size, $color) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ? AND size = ? AND color = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$product_id, $size, $color]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Get variants for a specific color
 */
function getVariantsByColor($product_id, $color) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ? AND color = ? AND is_active = 1 ORDER BY size");
    $stmt->execute([$product_id, $color]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get unique sizes for a product (ordered logically)
 */
function getProductSizes($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT DISTINCT size FROM product_variants WHERE product_id = ? AND is_active = 1 ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', 'Universal')");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get all available colors from all products (for filter sidebar)
 * Querying from product_color_images table
 */
function getAllColors() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT DISTINCT color FROM product_color_images ORDER BY color");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Get all available sizes from all products (for filter sidebar)
 */
function getAllSizes() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT DISTINCT size FROM product_variants WHERE is_active = 1 ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', 'Universal')");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// ============================================================
// CATEGORY TREE & FILTER HELPERS
// ============================================================

/**
 * Get categories with parent-child relationship for display
 */
function getCategoryTree() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY parent_id, name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $tree = [];
    foreach ($categories as $cat) {
        if ($cat['parent_id'] === null) {
            $tree[$cat['id']] = $cat;
            $tree[$cat['id']]['children'] = [];
        }
    }
    foreach ($categories as $cat) {
        if ($cat['parent_id'] !== null && isset($tree[$cat['parent_id']])) {
            $tree[$cat['parent_id']]['children'][] = $cat;
        }
    }
    return $tree;
}

/**
 * Get minimum and maximum prices across all products
 */
function getPriceRange() {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT MIN(price) as min, MAX(price) as max FROM product_variants WHERE is_active = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return [
        'min' => floor($result['min'] ?? 0),
        'max' => ceil($result['max'] ?? 5000)
    ];
}

/**
 * Build filter URL for size/color toggles
 */
function buildFilterUrl($filter_type, $value) {
    $params = $_GET;
    
    // Remove current filter value if already selected
    if (isset($params[$filter_type]) && in_array($value, (array)$params[$filter_type])) {
        $params[$filter_type] = array_diff((array)$params[$filter_type], [$value]);
        if (empty($params[$filter_type])) {
            unset($params[$filter_type]);
        }
    } else {
        // Add the filter value
        if (!isset($params[$filter_type])) {
            $params[$filter_type] = [];
        }
        $params[$filter_type][] = $value;
    }
    
    // Build query string
    $query = http_build_query($params);
    return '/kitgroup/products' . ($query ? '?' . $query : '');
}

// ============================================================
// FEATURED PRODUCTS
// ============================================================

/**
 * Get featured products for homepage
 */
function getFeaturedProducts($limit = 6) {
    $pdo = getDB();
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
                b.name as brand_name,
                b.slug as brand_slug,
                c.name as category_name,
                (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.featured = 1
            ORDER BY p.created_at DESC
            LIMIT :limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// PAGINATION FUNCTIONS
// ============================================================

/**
 * Get total count of products (for pagination)
 */
function getTotalProducts($category_slug = null, $brand_slug = null, $filters = [], $search = null) {
    $pdo = getDB();
    
    $sql = "SELECT COUNT(DISTINCT p.id) as total
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_active = 1
            WHERE p.is_active = 1";
    
    $params = [];
    
    // Category filter (including parent categories)
    if ($category_slug) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND parent_id IS NULL");
        $stmt->execute([$category_slug]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($parent) {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
            $stmt->execute([$parent['id']]);
            $child_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($child_ids)) {
                $placeholders = implode(',', array_fill(0, count($child_ids), '?'));
                $sql .= " AND c.id IN ($placeholders)";
                $params = array_merge($params, $child_ids);
            }
        } else {
            $sql .= " AND c.slug = ?";
            $params[] = $category_slug;
        }
    }
    
    // Brand filter
    if ($brand_slug) {
        $sql .= " AND b.slug = ?";
        $params[] = $brand_slug;
    }
    
    // Size filter
    if (!empty($filters['sizes']) && is_array($filters['sizes'])) {
        $placeholders = implode(',', array_fill(0, count($filters['sizes']), '?'));
        $sql .= " AND EXISTS (
            SELECT 1 FROM product_variants 
            WHERE product_id = p.id AND size IN ($placeholders) AND is_active = 1
        )";
        $params = array_merge($params, $filters['sizes']);
    }
    
    // Color filter
    if (!empty($filters['colors']) && is_array($filters['colors'])) {
        $placeholders = implode(',', array_fill(0, count($filters['colors']), '?'));
        $sql .= " AND EXISTS (
            SELECT 1 FROM product_variants 
            WHERE product_id = p.id AND color IN ($placeholders) AND is_active = 1
        )";
        $params = array_merge($params, $filters['colors']);
    }
    
    // Price range filter
    if (!empty($filters['min_price'])) {
        $sql .= " AND (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) >= ?";
        $params[] = (float)$filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $sql .= " AND (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) <= ?";
        $params[] = (float)$filters['max_price'];
    }
    
    // Search filter
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR b.name LIKE ? OR c.name LIKE ?)";
        $search_term = '%' . $search . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (int)$result['total'];
}

/**
 * Get products with pagination
 */
function getProductsPaginated($category_slug = null, $brand_slug = null, $filters = [], $search = null, $page = 1, $per_page = 15) {
    $pdo = getDB();
    $offset = ($page - 1) * $per_page;
    
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
                b.name as brand_name,
                b.slug as brand_slug,
                c.name as category_name,
                c.slug as category_slug,
                c.parent_id as category_parent_id,
                (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as min_price,
                (SELECT MAX(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as max_price,
                (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id AND is_active = 1) as total_stock,
                (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image,
                GROUP_CONCAT(DISTINCT pci.color ORDER BY pci.sort_order) as colors,
                GROUP_CONCAT(DISTINCT pv.size ORDER BY FIELD(pv.size, 'XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL', 'Universal')) as sizes
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN product_color_images pci ON p.id = pci.product_id
            LEFT JOIN product_variants pv ON p.id = pv.product_id AND pv.is_active = 1
            WHERE p.is_active = 1";
    
    $params = [];
    
    // Category filter (including parent categories)
    if ($category_slug) {
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE slug = ? AND parent_id IS NULL");
        $stmt->execute([$category_slug]);
        $parent = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($parent) {
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE parent_id = ?");
            $stmt->execute([$parent['id']]);
            $child_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (!empty($child_ids)) {
                $placeholders = implode(',', array_fill(0, count($child_ids), '?'));
                $sql .= " AND c.id IN ($placeholders)";
                $params = array_merge($params, $child_ids);
            }
        } else {
            $sql .= " AND c.slug = ?";
            $params[] = $category_slug;
        }
    }
    
    // Brand filter
    if ($brand_slug) {
        $sql .= " AND b.slug = ?";
        $params[] = $brand_slug;
    }
    
    // Size filter
    if (!empty($filters['sizes']) && is_array($filters['sizes'])) {
        $placeholders = implode(',', array_fill(0, count($filters['sizes']), '?'));
        $sql .= " AND EXISTS (
            SELECT 1 FROM product_variants 
            WHERE product_id = p.id AND size IN ($placeholders) AND is_active = 1
        )";
        $params = array_merge($params, $filters['sizes']);
    }
    
    // Color filter
    if (!empty($filters['colors']) && is_array($filters['colors'])) {
        $placeholders = implode(',', array_fill(0, count($filters['colors']), '?'));
        $sql .= " AND EXISTS (
            SELECT 1 FROM product_variants 
            WHERE product_id = p.id AND color IN ($placeholders) AND is_active = 1
        )";
        $params = array_merge($params, $filters['colors']);
    }
    
    // Price range filter
    if (!empty($filters['min_price'])) {
        $sql .= " AND (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) >= ?";
        $params[] = (float)$filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $sql .= " AND (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) <= ?";
        $params[] = (float)$filters['max_price'];
    }
    
    // Search filter
    if ($search) {
        $sql .= " AND (p.name LIKE ? OR b.name LIKE ? OR c.name LIKE ?)";
        $search_term = '%' . $search . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $sql .= " GROUP BY p.id ORDER BY p.featured DESC, p.name ASC LIMIT ? OFFSET ?";
    $params[] = (int)$per_page;
    $params[] = (int)$offset;
    
    $stmt = $pdo->prepare($sql);
    
    // Bind all parameters with proper types
    foreach ($params as $key => $value) {
        // Check if this is the LIMIT or OFFSET (last two parameters)
        if ($key >= count($params) - 2) {
            $stmt->bindValue($key + 1, (int)$value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ============================================================
// RELATED PRODUCTS
// ============================================================

/**
 * Get related products for a product
 * Shows products from same category first, then same brand
 */
function getRelatedProducts($product_id, $category_id, $brand_id, $limit = 4) {
    $pdo = getDB();
    
    // First, try to get products from the same category
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
                b.name as brand_name,
                b.slug as brand_slug,
                (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as min_price,
                (SELECT MAX(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as max_price,
                (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id AND is_active = 1) as total_stock,
                (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            WHERE p.is_active = 1 
                AND p.id != ?
                AND p.category_id = ?
            ORDER BY p.featured DESC, p.name ASC
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
    // Bind with proper types
    $stmt->bindValue(1, $product_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $category_id, PDO::PARAM_INT);
    $stmt->bindValue(3, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $related = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If we don't have enough products from the same category, get from same brand
    if (count($related) < $limit) {
        $remaining = $limit - count($related);
        $existing_ids = array_column($related, 'id');
        $existing_ids[] = $product_id; // Also exclude current product
        
        $placeholders = implode(',', array_fill(0, count($existing_ids), '?'));
        
        $sql2 = "SELECT 
                    p.*,
                    p.is_made_in_botswana,
                    p.is_orderable,
                    b.name as brand_name,
                    b.slug as brand_slug,
                    (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as min_price,
                    (SELECT MAX(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as max_price,
                    (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id AND is_active = 1) as total_stock,
                    (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image
                FROM products p
                JOIN brands b ON p.brand_id = b.id
                WHERE p.is_active = 1 
                    AND p.id NOT IN ($placeholders)
                    AND p.brand_id = ?
                ORDER BY p.featured DESC, p.name ASC
                LIMIT ?";
        
        $params = array_merge($existing_ids, [$brand_id, $remaining]);
        $stmt2 = $pdo->prepare($sql2);
        
        // Bind all parameters with proper types
        $index = 1;
        foreach ($existing_ids as $id) {
            $stmt2->bindValue($index++, $id, PDO::PARAM_INT);
        }
        $stmt2->bindValue($index++, $brand_id, PDO::PARAM_INT);
        $stmt2->bindValue($index++, $remaining, PDO::PARAM_INT);
        
        $stmt2->execute();
        $brand_related = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $related = array_merge($related, $brand_related);
    }
    
    return $related;
}

// ============================================================
<<<<<<< HEAD
// MADE IN BOTSWANA / STOCK STATUS FUNCTIONS
// ============================================================

/**
 * Get stock status label for a variant
 */
function getStockStatus($stock, $is_orderable) {
    if ($stock > 0) {
        return ['label' => 'In Stock', 'class' => 'text-success', 'icon' => 'bi-check-circle-fill'];
    } elseif ($is_orderable) {
        return ['label' => 'Orderable', 'class' => 'text-warning', 'icon' => 'bi-clock-fill'];
    } else {
        return ['label' => 'Out of Stock', 'class' => 'text-danger', 'icon' => 'bi-x-circle-fill'];
    }
}

/**
 * Check if product has any in-stock variants
 */
function hasInStockVariants($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM product_variants WHERE product_id = ? AND stock > 0 AND is_active = 1");
    $stmt->execute([$product_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

/**
 * Check if product has any orderable variants
 */
function hasOrderableVariants($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM product_variants WHERE product_id = ? AND stock = 0 AND is_orderable = 1 AND is_active = 1");
    $stmt->execute([$product_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}

/**
 * Get overall product stock status
 */
function getProductStockStatus($product_id) {
    if (hasInStockVariants($product_id)) {
        return ['label' => 'In Stock', 'class' => 'text-success', 'icon' => 'bi-check-circle-fill'];
    } elseif (hasOrderableVariants($product_id)) {
        return ['label' => 'Orderable', 'class' => 'text-warning', 'icon' => 'bi-clock-fill'];
    } else {
        return ['label' => 'Out of Stock', 'class' => 'text-danger', 'icon' => 'bi-x-circle-fill'];
=======
// STOCK STATUS FUNCTIONS (Simplified)
// ============================================================

/**
 * Get product stock status tag (no quantities)
 */
function getProductStockTag($product_id) {
    $pdo = getDB();
    
    // Get product info
    $stmt = $pdo->prepare("SELECT is_orderable, is_made_in_botswana FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        return ['label' => 'Unknown', 'class' => 'text-secondary', 'icon' => 'bi-question-circle', 'bg' => 'rgba(108, 117, 125, 0.9)'];
    }
    
    // If product is orderable
    if ($product['is_orderable']) {
        return ['label' => 'Orderable', 'class' => 'text-warning', 'icon' => 'bi-clock-fill', 'bg' => 'rgba(255, 193, 7, 0.9)'];
    }
    
    // Check if there's any stock
    $stmt = $pdo->prepare("SELECT SUM(stock) as total_stock FROM product_variants WHERE product_id = ? AND is_active = 1");
    $stmt->execute([$product_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result && $result['total_stock'] > 0) {
        return ['label' => 'In Store', 'class' => 'text-success', 'icon' => 'bi-check-circle-fill', 'bg' => 'rgba(40, 167, 69, 0.9)'];
    } else {
        return ['label' => 'Orderable', 'class' => 'text-warning', 'icon' => 'bi-clock-fill', 'bg' => 'rgba(255, 193, 7, 0.9)'];
>>>>>>> 28ddb57b97b4bbfe9b7141303919f30aa214af9e
    }
}

// ============================================================
<<<<<<< HEAD
// USUALLY SOLD WITH (RELATED PRODUCTS)
// ============================================================

/**
 * Get related products for a given product
 */
function getRelatedProductsForProduct($product_id, $limit = 4) {
    $pdo = getDB();
    $sql = "SELECT 
                p.*,
=======
// USUALLY BOUGHT WITH (Explicit Pairings)
// ============================================================

/**
 * Get products explicitly paired as "Usually Bought With"
 */
function getUsuallyBoughtWith($product_id, $limit = 4) {
    $pdo = getDB();
    $sql = "SELECT 
                p.*,
                p.is_made_in_botswana,
                p.is_orderable,
>>>>>>> 28ddb57b97b4bbfe9b7141303919f30aa214af9e
                b.name as brand_name,
                b.slug as brand_slug,
                (SELECT MIN(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as min_price,
                (SELECT MAX(price) FROM product_variants WHERE product_id = p.id AND is_active = 1) as max_price,
                (SELECT SUM(stock) FROM product_variants WHERE product_id = p.id AND is_active = 1) as total_stock,
                (SELECT image_url FROM product_color_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as default_image
            FROM products p
            JOIN brands b ON p.brand_id = b.id
            JOIN product_related pr ON p.id = pr.related_product_id
            WHERE pr.product_id = ? AND p.is_active = 1
            ORDER BY pr.sort_order ASC, p.name ASC
            LIMIT ?";
    
    $stmt = $pdo->prepare($sql);
<<<<<<< HEAD
    $stmt->execute([$product_id, $limit]);
=======
    $stmt->bindValue(1, $product_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
>>>>>>> 28ddb57b97b4bbfe9b7141303919f30aa214af9e
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get all related product IDs for a product (for admin)
 */
function getRelatedProductIds($product_id) {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT related_product_id FROM product_related WHERE product_id = ? ORDER BY sort_order");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Save related products for a product
 */
function saveRelatedProducts($product_id, $related_ids) {
    $pdo = getDB();
    // Delete existing relations
    $stmt = $pdo->prepare("DELETE FROM product_related WHERE product_id = ?");
    $stmt->execute([$product_id]);
    
    // Insert new relations
    if (!empty($related_ids) && is_array($related_ids)) {
        $stmt = $pdo->prepare("INSERT INTO product_related (product_id, related_product_id, sort_order) VALUES (?, ?, ?)");
        $sort = 0;
        foreach ($related_ids as $rid) {
            $stmt->execute([$product_id, $rid, $sort++]);
        }
    }
    return true;
<<<<<<< HEAD
}
=======
}
?>
>>>>>>> 28ddb57b97b4bbfe9b7141303919f30aa214af9e
