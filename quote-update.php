<?php
// quote-update.php
session_start();

$key = $_POST['key'] ?? null;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($key && isset($_SESSION['quote'][$key])) {
    if ($quantity > 0) {
        $_SESSION['quote'][$key]['quantity'] = $quantity;
    } else {
        // Remove if quantity is 0 or negative
        unset($_SESSION['quote'][$key]);
    }
}

header('Location: /kitgroup/quote.php');
exit;
?>