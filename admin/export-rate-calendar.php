<?php
require_once __DIR__ . '/../db.php';

// Verify session authentication
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Get active filters from GET parameters
$filter_category = isset($_GET['filter_category']) ? intval($_GET['filter_category']) : 0;
$filter_date = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : '';

// Build SQL query
$query = "
    SELECT rrc.*, r.title as category_title 
    FROM room_rate_calendars rrc 
    JOIN rooms r ON rrc.room_category_id = r.id
    WHERE 1=1
";
$params = [];

if ($filter_category > 0) {
    $query .= " AND rrc.room_category_id = ? ";
    $params[] = $filter_category;
}

if (!empty($filter_date)) {
    $query .= " AND rrc.start_date <= ? AND rrc.end_date >= ? ";
    $params[] = $filter_date;
    $params[] = $filter_date;
}

$query .= " ORDER BY rrc.start_date DESC, rrc.id DESC ";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $rules = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Export rate calendar DB error: " . $e->getMessage());
    exit("Database query failed.");
}

// Clean output buffer to prevent issues with formatting
if (ob_get_level()) {
    ob_end_clean();
}

// Configure download headers for Excel compatibility
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=rate_calendar_rules_export_' . date('Y-m-d') . '.csv');
header('Pragma: no-cache');
header('Expires: 0');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility with special characters
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add column headers
fputcsv($output, [
    'Room Category',
    'Start Date',
    'End Date',
    'Single EP (₹)',
    'Single CP (₹)',
    'Single MAP (₹)',
    'Double EP (₹)',
    'Double CP (₹)',
    'Double MAP (₹)',
    'Struck Price (₹)',
    'Triple EP (₹)',
    'Triple CP (₹)',
    'Triple MAP (₹)',
    'Extra Child Price (₹)',
    'Reason / Notes'
]);

// Write data rows
foreach ($rules as $rule) {
    fputcsv($output, [
        $rule['category_title'],
        $rule['start_date'],
        $rule['end_date'],
        (float)$rule['price_single_ep'] > 0 ? $rule['price_single_ep'] : '0.00',
        (float)$rule['price_single_cp'] > 0 ? $rule['price_single_cp'] : '0.00',
        (float)$rule['price_single_map'] > 0 ? $rule['price_single_map'] : '0.00',
        (float)$rule['ep_price'] > 0 ? $rule['ep_price'] : '0.00',
        (float)$rule['cp_price'] > 0 ? $rule['cp_price'] : '0.00',
        (float)$rule['map_price'] > 0 ? $rule['map_price'] : '0.00',
        (float)$rule['struck_price'] > 0 ? $rule['struck_price'] : '0.00',
        (float)$rule['price_triple_ep'] > 0 ? $rule['price_triple_ep'] : '0.00',
        (float)$rule['price_triple_cp'] > 0 ? $rule['price_triple_cp'] : '0.00',
        (float)$rule['price_triple_map'] > 0 ? $rule['price_triple_map'] : '0.00',
        (float)$rule['extra_child_price'] > 0 ? $rule['extra_child_price'] : '0.00',
        $rule['reason'] ? $rule['reason'] : 'None'
    ]);
}

fclose($output);
exit;
?>
