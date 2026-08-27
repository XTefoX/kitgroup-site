<?php
// quote-add.php
// Add item to quote session

session_start();

// Get parameters
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$size = isset($_GET['size']) ? trim($_GET['size']) : null;
$color = isset($_GET['color']) ? trim($_GET['color']) : null;
$quantity = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;

// Validate
if (!$product_id || !$size || !$color) {
    header('Location: /kitgroup/products');
    exit;
}

// Ensure quote session exists
if (!isset($_SESSION['quote'])) {
    $_SESSION['quote'] = [];
}

// Create unique key for this item (product_id + size + color)
$key = $product_id . '|' . $size . '|' . $color;

// Check if item already exists in quote
if (isset($_SESSION['quote'][$key])) {
    // Increment quantity
    $_SESSION['quote'][$key]['quantity'] += $quantity;
} else {
    // Add new item
    $_SESSION['quote'][$key] = [
        'product_id' => $product_id,
        'size' => $size,
        'color' => $color,
        'quantity' => $quantity
    ];
}

// Redirect back to product page or quote page
$redirect = $_GET['redirect'] ?? 'product';
if ($redirect === 'product') {
    // Get product slug from database
    require_once __DIR__ . '/includes/db.php';
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT slug FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        header('Location: /kitgroup/product/' . $product['slug'] . '?added=1');
    } else {
        header('Location: /kitgroup/products');
    }
} else {
    header('Location: /kitgroup/quote.php');
}
exit;
?>