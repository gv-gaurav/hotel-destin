<?php
echo "<pre>";
require_once __DIR__ . '/config.php';
echo "=== Database Config on server (Active) ===\n";
echo "DB_HOST: " . DB_HOST . "\n";
echo "DB_NAME: " . DB_NAME . "\n";
echo "DB_USER: " . DB_USER . "\n";

echo "\n=== Reading server database settings for banquet ===\n";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Successfully connected to server DB: " . DB_NAME . "\n";
    
    $stmt = $pdo->query("SELECT key_name, val_content FROM settings WHERE key_name LIKE 'banquet%' OR key_name LIKE 'hotel_address'");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($settings);
} catch (Exception $e) {
    echo "Connection/Query Error: " . $e->getMessage() . "\n";
}
echo "</pre>";
