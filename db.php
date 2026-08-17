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
    
    // Ensure cancellation_reason column exists in bookings table
    try {
        $col_check = $pdo->query("SHOW COLUMNS FROM `bookings` LIKE 'cancellation_reason'");
        if ($col_check->rowCount() === 0) {
            $pdo->exec("ALTER TABLE `bookings` ADD COLUMN `cancellation_reason` TEXT DEFAULT NULL");
        }
    } catch (Exception $col_e) {
        error_log("Migration error (cancellation_reason): " . $col_e->getMessage());
    }

    // Ensure struck_price column exists in room_rate_calendars table
    try {
        $col_check = $pdo->query("SHOW COLUMNS FROM `room_rate_calendars` LIKE 'struck_price'");
        if ($col_check->rowCount() === 0) {
            $pdo->exec("ALTER TABLE `room_rate_calendars` ADD COLUMN `struck_price` DECIMAL(10,2) DEFAULT NULL");
        }
    } catch (Exception $col_e) {
        error_log("Migration error (room_rate_calendars struck_price): " . $col_e->getMessage());
    }

    // Ensure the default settings address value is updated
    try {
        $pdo->exec("UPDATE `settings` SET `val_content` = 'Hotel  destin Gwalior Sachin Tendulkar road Near Ram Vatika marriage garden Govindpuri Gwalior' WHERE `key_name` = 'hotel_address'");
    } catch (Exception $settings_e) {
        error_log("Migration error (settings hotel_address): " . $settings_e->getMessage());
    }

    // Ensure restaurant and cafe settings are updated from Bar to Cafe By Soul
    try {
        $updates = [
            'restaurant_hero_title' => 'Cafe By Soul, Rooftop Cafe',
            'restaurant_hero_tagline' => 'Elevated Gastronomy & Refreshing Brews',
            'restaurant_food_types' => 'We have both veg and non-veg food available at our rooftop cafe',
            'restaurant_facility_1_title' => 'Rooftop Cafe',
            'restaurant_facility_1_desc' => 'Unwind under the stars with our premium coffees, mocktails, and ambient tunes at Gwalior\'s premier rooftop cafe.',
            'restaurant_ambience_desc' => 'Take a visual tour through our celestial rooftop cafe and warm indoor dining halls.',
            'restaurant_ambience_2_title' => 'Rooftop Cafe Lounge'
        ];
        $stmt = $pdo->prepare("UPDATE `settings` SET `val_content` = ? WHERE `key_name` = ?");
        foreach ($updates as $key => $val) {
            $stmt->execute([$val, $key]);
        }
    } catch (Exception $settings_e) {
        error_log("Migration error (restaurant settings to cafe): " . $settings_e->getMessage());
    }

    // Ensure start_date column exists in coupons table
    try {
        $col_check = $pdo->query("SHOW COLUMNS FROM `coupons` LIKE 'start_date'");
        if ($col_check->rowCount() === 0) {
            $pdo->exec("ALTER TABLE `coupons` ADD COLUMN `start_date` DATE DEFAULT NULL");
            $pdo->exec("UPDATE `coupons` SET `start_date` = IFNULL(DATE(`created_at`), CURDATE()) WHERE `start_date` IS NULL");
            $pdo->exec("ALTER TABLE `coupons` MODIFY COLUMN `start_date` DATE NOT NULL");
        }
    } catch (Exception $col_e) {
        error_log("Migration error (coupons start_date): " . $col_e->getMessage());
    }
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
        return ($val !== false && $val !== '') ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Resolve room struck price (crossed-out price) for a specific date considering rate calendar overrides.
 */
function get_resolved_room_struck_price($pdo, $room_id, $date, $room) {
    try {
        $stmt = $pdo->prepare("
            SELECT struck_price 
            FROM room_rate_calendars 
            WHERE room_category_id = ? 
              AND start_date <= ? 
              AND end_date >= ? 
            ORDER BY DATEDIFF(end_date, start_date) ASC, id DESC 
            LIMIT 1
        ");
        $stmt->execute([$room_id, $date, $date]);
        $rule_struck_price = $stmt->fetchColumn();
        
        if ($rule_struck_price !== false && (float)$rule_struck_price > 0) {
            return (float)$rule_struck_price;
        }
    } catch (Exception $e) {
        error_log("Rate calendar struck price lookup error: " . $e->getMessage());
    }
    
    return isset($room['struck_price']) ? (float)$room['struck_price'] : 0.00;
}

/**
 * Resolve room price for a specific date considering rate calendar overrides.
 */
function get_resolved_room_price($pdo, $room_id, $date, $meal_plan, $adults, $room, $children = 0) {
    try {
        $stmt = $pdo->prepare("
            SELECT * 
            FROM room_rate_calendars 
            WHERE room_category_id = ? 
              AND start_date <= ? 
              AND end_date >= ? 
            ORDER BY DATEDIFF(end_date, start_date) ASC, id DESC 
            LIMIT 1
        ");
        $stmt->execute([$room_id, $date, $date]);
        $rule = $stmt->fetch();
        
        if ($rule) {
            $plan = strtolower(trim($meal_plan));
            
            // Check occupancy (single vs double vs triple)
            if ($adults >= 3) {
                $col_name = "price_triple_" . $plan;
                if (isset($rule[$col_name]) && (float)$rule[$col_name] > 0) {
                    $base_price = (float)$rule[$col_name];
                } else {
                    // Fallback to Double rate
                    $base_price = (float)$rule[$plan . '_price'];
                }
            } elseif ($adults === 1) {
                $col_name = "price_single_" . $plan;
                if (isset($rule[$col_name]) && (float)$rule[$col_name] > 0) {
                    $base_price = (float)$rule[$col_name];
                } else {
                    // Fallback to Double rate
                    $base_price = (float)$rule[$plan . '_price'];
                }
            } else {
                // Double rate (or fallback)
                $base_price = (float)$rule[$plan . '_price'];
            }

            // Add extra child charge if children > 0
            if ($children > 0) {
                $child_charge = 0.00;
                if (isset($rule['extra_child_price']) && (float)$rule['extra_child_price'] > 0) {
                    $child_charge = (float)$rule['extra_child_price'];
                } else {
                    $child_charge = isset($room['extra_adult_price']) ? (float)$room['extra_adult_price'] : 1000.00;
                }
                $base_price += $children * $child_charge;
            }
            return $base_price;
        }
    } catch (Exception $e) {
        error_log("Rate calendar lookup error on date $date: " . $e->getMessage());
    }
    
    // Fallback: standard matrix pricing
    if ($adults >= 3) {
        $occupancy = 'triple';
    } elseif ($adults === 2) {
        $occupancy = 'double';
    } else {
        $occupancy = 'single';
    }
    
    $plan = strtolower(trim($meal_plan));
    $column = "price_" . $occupancy . "_" . $plan;
    
    $base_price = isset($room[$column]) ? (float)$room[$column] : (float)$room['price'];

    // Add extra child charge if children > 0
    if ($children > 0) {
        $child_charge = isset($room['extra_adult_price']) ? (float)$room['extra_adult_price'] : 1000.00;
        $base_price += $children * $child_charge;
    }
    
    return $base_price;
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
