<?php
// admin/category-delete.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id && deleteCategory($id)) {
    header('Location: categories.php?msg=deleted');
} else {
    header('Location: categories.php?error=delete_failed');
}
exit;
?>