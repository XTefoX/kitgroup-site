<?php
// quote-clear.php
session_start();

if (isset($_SESSION['quote'])) {
    unset($_SESSION['quote']);
}

header('Location: /kitgroup/quote.php');
exit;
?>