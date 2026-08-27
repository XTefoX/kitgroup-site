<?php
// admin/product-delete.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id && deleteProduct($id)) {
    header('Location: products.php?msg=deleted');
} else {
    header('Location: products.php?error=delete_failed');
}
exit;
?>