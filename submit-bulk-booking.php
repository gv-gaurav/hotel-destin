<?php
// submit-bulk-booking.php
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
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$checkin = isset($_POST['checkin']) ? htmlspecialchars(trim($_POST['checkin'])) : '';
$checkout = isset($_POST['checkout']) ? htmlspecialchars(trim($_POST['checkout'])) : '';
$guests = isset($_POST['guests']) ? intval($_POST['guests']) : 0;
$requirements = isset($_POST['requirements']) ? htmlspecialchars(trim($_POST['requirements'])) : '';

if (empty($name) || empty($email) || empty($phone) || empty($checkin) || empty($checkout) || $guests <= 0) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

try {
    // 1. Save to database
    $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, guests, requirements, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'bulk_booking', 
        $name, 
        $email, 
        $phone, 
        $checkin, 
        $guests, 
        "Check-out Date: $checkout\nSpecial Requirements: $requirements", 
        'pending'
    ]);

    // 2. Send email notification to owner (info@hoteldestin.in)
    $email_sent = send_enquiry_alert(
        'bulk_booking', 
        $name, 
        $email, 
        $phone, 
        $checkin, 
        $guests, 
        [
            'Check-out Date' => $checkout,
            'Special Requirements' => $requirements
        ]
    );

    echo json_encode(['success' => true, 'message' => 'Your bulk booking request has been submitted successfully! We will get in touch with you shortly.']);
} catch (Exception $e) {
    error_log("Bulk booking submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while saving your request. Please try again.']);
}
?>
