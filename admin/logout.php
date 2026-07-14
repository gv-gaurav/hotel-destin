<?php
require_once __DIR__ . '/../config.php';

// Unset all admin session keys
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_id']);

session_destroy();

header("Location: login.php");
exit;
?>
