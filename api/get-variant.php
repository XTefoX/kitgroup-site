<?php
// api/get-variant.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$size = isset($_GET['size']) ? trim($_GET['size']) : null;
$color = isset($_GET['color']) ? trim($_GET['color']) : null;

if (!$product_id || !$size || !$color) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$variant = getVariantBySizeColor($product_id, $size, $color);

if ($variant) {
    echo json_encode([
        'success' => true,
        'sku' => $variant['sku'],
        'price' => floatval($variant['price']),
        'stock' => intval($variant['stock']),
        'compare_price' => $variant['compare_price'] ? floatval($variant['compare_price']) : null
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Variant not found']);
}
exit;
?>