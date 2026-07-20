<?php
require_once __DIR__ . '/../db.php';

// Verify session authentication
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Get active filters
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

// Build SQL query
$query = "
    SELECT b.*, r.title as room_title 
    FROM bookings b 
    LEFT JOIN rooms r ON b.room_id = r.id
";

$conditions = [];
$params = [];

if ($start_date !== '') {
    $conditions[] = "b.created_at >= :start_date";
    $params['start_date'] = $start_date . " 00:00:00";
}
if ($end_date !== '') {
    $conditions[] = "b.created_at <= :end_date";
    $params['end_date'] = $end_date . " 23:59:59";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY b.id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Export bookings DB error: " . $e->getMessage());
    exit("Database query failed.");
}

// Clean output buffer to prevent issues with leading whitespaces
if (ob_get_level()) {
    ob_end_clean();
}

// Configure download headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=bookings_export_' . date('Y-m-d') . '.csv');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility with special characters
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add headers
fputcsv($output, [
    'Ref ID',
    'Invoice No',
    'Guest Name',
    'Email',
    'Phone',
    'Room Reserved',
    'Check-in',
    'Check-out',
    'Total Nights',
    'Base Amount',
    'Coupon Code',
    'Total Paid',
    'Payment Method',
    'Razorpay ID',
    'Payment Status',
    'Booked On'
]);

// Write rows
foreach ($bookings as $b) {
    fputcsv($output, [
        $b['booking_id'],
        $b['invoice_no'] ?: 'N/A',
        $b['customer_name'],
        $b['customer_email'],
        $b['customer_phone'],
        $b['room_title'] ?: 'Deluxe Room',
        $b['check_in'],
        $b['check_out'],
        $b['total_nights'],
        $b['base_amount'],
        $b['coupon_code'] ?: 'None',
        $b['total_amount'],
        $b['payment_method'] ?: 'Razorpay',
        $b['razorpay_payment_id'] ?: 'N/A',
        ucfirst($b['payment_status']),
        $b['created_at']
    ]);
}

fclose($output);
exit;
