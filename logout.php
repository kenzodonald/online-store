<?php
require_once 'config.php';
unset($_SESSION['user']);
unset($_SESSION['user_id']);
unset($_SESSION['username']);
// keep persisted cart in DB; just clear session copy
unset($_SESSION['cart']);
header('Location: index.php'); exit;
?>