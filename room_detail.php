<?php
// Redirect underscore format to standard hyphenated room-detail.php
$query = !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';
header("HTTP/1.1 301 Moved Permanently");
header("Location: room-detail.php" . $query);
exit;
