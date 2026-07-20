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
$active_type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'all';

// Define lead categories mapping for display names
$lead_titles = [
    'contact' => 'Contact Us',
    'restaurant' => 'Restaurant',
    'banquet' => 'Banquet & Events',
    'corporate' => 'Corporate',
    'airport_transfer' => 'Airport Transfer',
    'long_stay' => 'Long Stay'
];

// Build SQL query
$query = "SELECT * FROM enquiries";
$conditions = [];
$params = [];

if ($start_date !== '') {
    $conditions[] = "created_at >= :start_date";
    $params['start_date'] = $start_date . " 00:00:00";
}
if ($end_date !== '') {
    $conditions[] = "created_at <= :end_date";
    $params['end_date'] = $end_date . " 23:59:59";
}

// Filter by category slug (handle 'wedding' mapping to 'banquet' as done in frontend)
if ($active_type !== 'all' && $active_type !== '') {
    if ($active_type === 'banquet') {
        $conditions[] = "(LOWER(category) = 'banquet' OR LOWER(category) = 'wedding')";
    } else {
        $conditions[] = "LOWER(category) = :category";
        $params['category'] = $active_type;
    }
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $enquiries = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Export enquiries DB error: " . $e->getMessage());
    exit("Database query failed.");
}

// Clean output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Configure download headers
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=enquiries_export_' . date('Y-m-d') . '.csv');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility with special characters
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add headers
fputcsv($output, [
    'Date Received',
    'Category',
    'Customer Name',
    'Email',
    'Phone',
    'Booking Date',
    'Guests',
    'Requirements / Message',
    'Status'
]);

// Write rows
foreach ($enquiries as $e) {
    $cat_slug = strtolower($e['category']);
    if ($cat_slug === 'wedding') {
        $cat_slug = 'banquet';
    }
    $display_cat = isset($lead_titles[$cat_slug]) ? $lead_titles[$cat_slug] : ucfirst($e['category']);

    $booking_date = ($e['date'] && $e['date'] !== '0000-00-00') ? $e['date'] : 'N/A';
    $guests = $e['guests'] ?: 'N/A';

    fputcsv($output, [
        $e['created_at'],
        $display_cat,
        $e['name'],
        $e['email'],
        $e['phone'],
        $booking_date,
        $guests,
        $e['requirements'],
        ucfirst($e['status'])
    ]);
}

fclose($output);
exit;
