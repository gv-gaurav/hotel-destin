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

if (empty($name) || empty($phone) || empty($stay_date) || empty($checkin_time) || $hours < 4 || empty($room_category)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields. Stay duration must be at least 4 hours.']);
    exit;
}

// Fetch rates from DB settings for security (prevents client price manipulation)
$hourly_rates = [
    'Standard Room - Hotel Destin' => 250.00,
    'Executive Room - Hotel Destin' => 350.00,
    'Premium Room - Hotel Destin' => 500.00
];

try {
    $stmt_h = $pdo->query("SELECT key_name, val_content FROM settings WHERE key_name IN ('hourly_rate_standard', 'hourly_rate_executive', 'hourly_rate_premium')");
    while ($row_h = $stmt_h->fetch()) {
        if ($row_h['key_name'] === 'hourly_rate_standard') {
            $hourly_rates['Standard Room - Hotel Destin'] = floatval($row_h['val_content']);
        }
        if ($row_h['key_name'] === 'hourly_rate_executive') {
            $hourly_rates['Executive Room - Hotel Destin'] = floatval($row_h['val_content']);
        }
        if ($row_h['key_name'] === 'hourly_rate_premium') {
            $hourly_rates['Premium Room - Hotel Destin'] = floatval($row_h['val_content']);
        }
    }
} catch (Exception $e) {
    // Fall back to defaults
}

$rate = isset($hourly_rates[$room_category]) ? $hourly_rates[$room_category] : 250.00;
$total_price = $rate * $hours;

try {
    // 1. Save to enquiries log
    $requirements = "Room Category: {$room_category}\nCheck-in Time: {$checkin_time}\nHourly Rate: ₹" . number_format($rate, 2) . "/hr\nTotal Calculated Price: ₹" . number_format($total_price, 2);
    
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
            'Hourly Rate' => '₹' . number_format($rate, 2) . '/hr',
            'Total Price' => '₹' . number_format($total_price, 2)
        ]
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
