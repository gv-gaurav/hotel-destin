<?php
// submit-hourly-booking.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail-helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// CSRF Validation
$csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    echo json_encode(['success' => false, 'message' => 'Security token validation failed.']);
    exit;
}

$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$stay_date = isset($_POST['stay_date']) ? htmlspecialchars(trim($_POST['stay_date'])) : '';
$checkin_time = isset($_POST['checkin_time']) ? htmlspecialchars(trim($_POST['checkin_time'])) : '';
$hours = isset($_POST['hours']) ? intval($_POST['hours']) : 0;
$room_category = isset($_POST['room_category']) ? trim($_POST['room_category']) : '';

$allowed_packages = [3, 6, 9, 12];
if (empty($name) || empty($phone) || empty($stay_date) || empty($checkin_time) || !in_array($hours, $allowed_packages) || empty($room_category)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields. Stay duration must be 3, 6, 9, or 12 hours.']);
    exit;
}

// Map category title to settings slug
$cat_slug = 'standard';
if (strpos($room_category, 'Executive') !== false) {
    $cat_slug = 'executive';
} else if (strpos($room_category, 'Premium') !== false) {
    $cat_slug = 'premium';
}

$setting_key = "hourly_{$hours}_{$cat_slug}";

// Fallback rates matrix
$hourly_packages = [
    'hourly_3_standard' => 600.00,
    'hourly_6_standard' => 1000.00,
    'hourly_9_standard' => 1400.00,
    'hourly_12_standard' => 1800.00,
    'hourly_3_executive' => 800.00,
    'hourly_6_executive' => 1300.00,
    'hourly_9_executive' => 1800.00,
    'hourly_12_executive' => 2200.00,
    'hourly_3_premium' => 1000.00,
    'hourly_6_premium' => 1600.00,
    'hourly_9_premium' => 2200.00,
    'hourly_12_premium' => 2800.00
];

try {
    $stmt_h = $pdo->prepare("SELECT val_content FROM settings WHERE key_name = ?");
    $stmt_h->execute([$setting_key]);
    $db_val = $stmt_h->fetchColumn();
    if ($db_val !== false && $db_val !== '') {
        $total_price = floatval($db_val);
    } else {
        $total_price = $hourly_packages[$setting_key];
    }
} catch (Exception $e) {
    $total_price = $hourly_packages[$setting_key];
}

try {
    // 1. Save to enquiries log
    $requirements = "Room Category: {$room_category}\nCheck-in Time: {$checkin_time}\nPackage Total: ₹" . number_format($total_price, 2);
    
    $stmt = $pdo->prepare("
        INSERT INTO enquiries (category, name, email, phone, date, guests, requirements, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        'hourly_booking',
        $name,
        'N/A', // Email omitted for hourly stays
        $phone,
        $stay_date,
        $hours,
        $requirements,
        'pending'
    ]);

    $enquiry_id = $pdo->lastInsertId();
    // 2. Dispatch email and WhatsApp notifications to the hotel owner
    send_enquiry_alert(
        'hourly_booking',
        $name,
        'N/A',
        $phone,
        $stay_date,
        $hours,
        [
            'Room Type' => $room_category,
            'Check-in Time' => $checkin_time,
            'Package Cost' => '₹' . number_format($total_price, 2)
        ],
        $enquiry_id
    );

    echo json_encode([
        'success' => true, 
        'message' => 'Thank you! We will call you as soon as possible to confirm your hourly booking.'
    ]);
} catch (Exception $e) {
    error_log("Hourly booking submission database error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred while saving your hourly stay request. Please try again.'
    ]);
}
?>
