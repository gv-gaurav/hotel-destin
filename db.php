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

/**
 * Resolve room price for a specific date considering rate calendar overrides.
 */
function get_resolved_room_price($pdo, $room_id, $date, $meal_plan, $adults, $room) {
    try {
        $stmt = $pdo->prepare("
            SELECT ep_price, cp_price, map_price 
            FROM room_rate_calendars 
            WHERE room_category_id = ? 
              AND start_date <= ? 
              AND end_date >= ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([$room_id, $date, $date]);
        $rule = $stmt->fetch();
        
        if ($rule) {
            $plan = strtolower(trim($meal_plan));
            if ($plan === 'cp') {
                return (float)$rule['cp_price'];
            } elseif ($plan === 'map') {
                return (float)$rule['map_price'];
            } else {
                return (float)$rule['ep_price'];
            }
        }
    } catch (Exception $e) {
        error_log("Rate calendar lookup error on date $date: " . $e->getMessage());
    }
    
    // Fallback: standard matrix pricing
    $occupancy = ($adults >= 2) ? 'double' : 'single';
    $plan = strtolower(trim($meal_plan));
    $column = "price_" . $occupancy . "_" . $plan;
    
    return isset($room[$column]) ? (float)$room[$column] : (float)$room['price'];
}

/**
 * Shared helper to get the icon filename for a given amenity/facility name.
 */
function get_amenity_icon($name)
{
    $n = strtolower(trim($name));
    if (strpos($n, 'ac') !== false || strpos($n, 'air') !== false || strpos($n, 'conditioner') !== false) {
        return 'assets/imgs/page/room/air-conditioner.svg';
    }
    if (strpos($n, 'wifi') !== false || strpos($n, 'wi-fi') !== false || strpos($n, 'internet') !== false) {
        return 'assets/imgs/page/room/wifi.svg';
    }
    if (strpos($n, 'laundry') !== false || strpos($n, 'wash') !== false) {
        return 'assets/imgs/page/room/loundry.svg';
    }
    if (strpos($n, 'bed') !== false) {
        return 'assets/imgs/page/room/bed.svg';
    }
    if (strpos($n, 'safe') !== false || strpos($n, 'locker') !== false || strpos($n, 'safety') !== false) {
        return 'assets/imgs/page/room/safety-box.svg';
    }
    if (strpos($n, 'airport') !== false || strpos($n, 'transfer') !== false || strpos($n, 'shuttle') !== false) {
        return 'assets/imgs/page/room/airport.svg';
    }
    if (strpos($n, 'food') !== false || strpos($n, 'meal') !== false || strpos($n, 'breakfast') !== false || strpos($n, 'dining') !== false) {
        return 'assets/imgs/page/room/food.svg';
    }
    if (strpos($n, 'living') !== false || strpos($n, 'hall') !== false || strpos($n, 'sofa') !== false) {
        return 'assets/imgs/page/room/living.svg';
    }
    return 'assets/imgs/page/room/wifi.svg';
}
?>
