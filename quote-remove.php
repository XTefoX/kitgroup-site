<?php
// quote-remove.php
session_start();

$key = $_GET['key'] ?? null;

if ($key && isset($_SESSION['quote'][$key])) {
    unset($_SESSION['quote'][$key]);
}

header('Location: /kitgroup/quote.php');
exit;
?>