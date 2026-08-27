<?php
session_start();
session_destroy();
header('Location: /kitgroup/admin/login?logout=1');
exit;
?>