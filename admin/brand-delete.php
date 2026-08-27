<?php
// admin/brand-delete.php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if ($id && deleteBrand($id)) {
    header('Location: brands.php?msg=deleted');
} else {
    header('Location: brands.php?error=delete_failed');
}
exit;
?>