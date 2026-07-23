<?php
// submit-general-enquiry.php
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
    echo json_encode(['success' => false, 'message' => 'Security token validation failed. Please refresh the page and try again.']);
    exit;
}

$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
$enquiry_type = isset($_POST['enquiry_type']) ? trim($_POST['enquiry_type']) : ''; // 'rooms' or 'banquet'

if (empty($name) || empty($phone) || empty($enquiry_type)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

try {
    if ($enquiry_type === 'rooms') {
        $checkin = isset($_POST['checkin']) ? trim($_POST['checkin']) : '';
        $checkout = isset($_POST['checkout']) ? trim($_POST['checkout']) : '';
        if (empty($checkin) || empty($checkout)) {
            echo json_encode(['success' => false, 'message' => 'Please select both Check-in and Check-out dates.']);
            exit;
        }

        // Save to database as category 'long_stay' (Rooms & Long Stay)
        $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, requirements, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'long_stay', 
            $name, 
            'no-email@hoteldestin.in', // placeholder email since email was not in required form inputs
            $phone, 
            $checkin, 
            "General Room Enquiry via header. Check-out Date: $checkout", 
            'pending'
        ]);

        // Send email alert to owner
        send_enquiry_alert(
            'long_stay', 
            $name, 
            'no-email@hoteldestin.in', 
            $phone, 
            $checkin, 
            1, 
            [
                'Check-out Date' => $checkout,
                'Source' => 'Header Enquiry Button'
            ]
        );

    } else if ($enquiry_type === 'banquet') {
        $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
        if (empty($event_date)) {
            echo json_encode(['success' => false, 'message' => 'Please select the Event Date.']);
            exit;
        }

        // Save to database as category 'banquet'
        $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, requirements, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'banquet', 
            $name, 
            'no-email@hoteldestin.in', 
            $phone, 
            $event_date, 
            "General Banquet Hall Enquiry via header.", 
            'pending'
        ]);

        // Send email alert to owner
        send_enquiry_alert(
            'banquet', 
            $name, 
            'no-email@hoteldestin.in', 
            $phone, 
            $event_date, 
            1, 
            [
                'Source' => 'Header Enquiry Button'
            ]
        );
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid enquiry type selected.']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Your enquiry has been logged successfully! We will connect with you shortly.']);
} catch (Exception $e) {
    error_log("General enquiry submission error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while saving your request. Please try again.']);
}
?>
