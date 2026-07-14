<?php
require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Write error logs to file securely (avoid printing details to users)
    error_log("Database connection error: " . $e->getMessage());
    http_response_code(500);
    exit("<h2>500 Internal Server Error</h2><p>Database connection failure. Please try again later.</p>");
}

/**
 * Retrieve site settings dynamically from the database.
 *
 * @param string $key Setting key name
 * @param string $default Fallback value if setting not found
 * @return string Setting value content
 */
function get_setting($key, $default = '') {
    global $pdo;
    if (!isset($pdo)) {
        return $default;
    }
    try {
        $stmt = $pdo->prepare("SELECT val_content FROM settings WHERE key_name = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}
?>
