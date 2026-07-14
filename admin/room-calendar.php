<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

// Parse query parameters for month and year, default to current
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));

if ($month < 1 || $month > 12) $month = intval(date('m'));
if ($year < 2000 || $year > 2100) $year = intval(date('Y'));

// Calculate calendar properties
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$first_day_timestamp = mktime(0, 0, 0, $month, 1, $year);
$month_name = date('F', $first_day_timestamp);

$start_date = sprintf("%04d-%02d-01", $year, $month);
$end_date = sprintf("%04d-%02d-%02d", $year, $month, $days_in_month);

// Fetch physical rooms
$physical_rooms = [];
try {
    $physical_rooms = $pdo->query("
        SELECT pr.*, r.title as category_title 
        FROM physical_rooms pr 
        JOIN rooms r ON pr.category_id = r.id 
        ORDER BY pr.room_number ASC
    ")->fetchAll();
} catch (Exception $e) {
    error_log("Failed to fetch physical rooms for calendar: " . $e->getMessage());
}

// Fetch bookings overlapping this month stay date range
$bookings = [];
try {
    $stmt = $pdo->prepare("
        SELECT b.*, r.title as room_title, pr.room_number 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        JOIN physical_rooms pr ON b.physical_room_id = pr.id 
        WHERE b.booking_status != 'cancelled' 
          AND b.physical_room_id IS NOT NULL 
          AND b.check_in <= ? 
          AND b.check_out >= ?
    ");
    $stmt->execute([$end_date, $start_date]);
    $bookings = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to load calendar bookings: " . $e->getMessage());
}

// Map bookings to physical rooms and dates for quick lookup
$occupancy = []; // structure: [physical_room_id][date] => booking_data
foreach ($bookings as $b) {
    $pr_id = $b['physical_room_id'];
    $cin = new DateTime($b['check_in']);
    $cout = new DateTime($b['check_out']);
    
    // Loop through check-in to check-out dates (excluding check-out day since guest leaves)
    $curr = clone $cin;
    while ($curr < $cout) {
        $date_key = $curr->format('Y-m-d');
        $occupancy[$pr_id][$date_key] = $b;
        $curr->modify('+1 day');
    }
}

// Prev/Next month links
$prev_month = $month - 1;
$prev_year = $year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $month + 1;
$next_year = $year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}
?>

<style>
    .calendar-grid-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .calendar-header-strip {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    .calendar-grid-wrapper {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #ffffff;
    }
    .calendar-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1200px;
    }
    .calendar-table th, .calendar-table td {
        border: 1px solid #f1f5f9;
        text-align: center;
        padding: 0;
        height: 60px;
        vertical-align: middle;
    }
    .calendar-table th {
        background: #f8fafc;
        font-weight: 700;
        font-size: 11.5px;
        color: #475569;
        height: 40px;
        border-bottom: 2px solid #e2e8f0;
    }
    .calendar-table th.room-col-header, .calendar-table td.room-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background: #ffffff;
        border-right: 2px solid #e2e8f0;
        text-align: left;
        padding: 10px 15px;
        min-width: 180px;
        width: 180px;
        box-sizing: border-box;
    }
    .calendar-table th.room-col-header {
        background: #f8fafc;
    }
    .calendar-table td.day-cell {
        min-width: 42px;
        width: 42px;
        background: #ffffff;
    }
    .booking-block {
        display: block;
        margin: 2px 0;
        padding: 6px 4px;
        font-size: 11px;
        font-weight: 700;
        text-align: center;
        text-decoration: none;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        border-radius: 4px;
        height: 48px;
        line-height: 36px;
        transition: transform 0.15s ease;
        box-sizing: border-box;
    }
    .booking-block:hover {
        transform: scale(1.02);
    }
    .booking-block.pending {
        background-color: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }
    .booking-block.confirmed {
        background-color: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }
    .booking-block.checked_in {
        background-color: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .booking-block.checked_out {
        background-color: #f0fdf4;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .legend-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: #475569;
    }
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Hotel Occupancy Calendar</h1>
        <p class="text-sm text-neutral-500 mt-5">Visual room scheduling calendar displaying physical room bookings status and stay periods.</p>
    </div>
</div>

<div class="calendar-grid-card">
    <div class="calendar-header-strip">
        <div class="d-flex align-items-center gap-15">
            <!-- Navigate Months -->
            <a class="btn btn-outline-dark" style="padding: 6px 14px; border-radius: 8px; border-color: #ccc;" href="room-calendar.php?month=<?= $prev_month ?>&year=<?= $prev_year ?>">← Prev</a>
            <h3 class="mb-0 font-heading" style="font-size: 20px; font-weight:700; color:#0f172a; min-width: 150px; text-align: center;">
                <?= $month_name ?> <?= $year ?>
            </h3>
            <a class="btn btn-outline-dark" style="padding: 6px 14px; border-radius: 8px; border-color: #ccc;" href="room-calendar.php?month=<?= $next_month ?>&year=<?= $next_year ?>">Next →</a>
        </div>
        
        <!-- Legend definitions -->
        <div class="d-flex gap-20">
            <div class="legend-item">
                <span class="legend-color" style="background:#fffbeb; border:1px solid #fde68a;"></span>
                <span>Pending</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background:#ecfdf5; border:1px solid #a7f3d0;"></span>
                <span>Confirmed</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background:#fef2f2; border:1px solid #fecaca;"></span>
                <span>Occupied</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background:#f0fdf4; border:1px solid #bbf7d0;"></span>
                <span>Completed</span>
            </div>
        </div>
    </div>

    <div class="calendar-grid-wrapper">
        <table class="calendar-table">
            <thead>
                <tr>
                    <th class="room-col-header">Physical Room</th>
                    <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
                        <th>
                            <?= $day ?><br>
                            <span style="font-size: 9px; font-weight:normal; color:#64748b;">
                                <?= substr(date('D', mktime(0, 0, 0, $month, $day, $year)), 0, 1) ?>
                            </span>
                        </th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($physical_rooms) > 0): ?>
                    <?php foreach ($physical_rooms as $pr): ?>
                        <tr>
                            <td class="room-col">
                                <strong style="color: #9c6047; font-size:14.5px;">Room <?= htmlspecialchars($pr['room_number']) ?></strong><br>
                                <span style="font-size: 11px; color: #64748b;"><?= htmlspecialchars($pr['category_title']) ?></span>
                            </td>
                            <?php for ($day = 1; $day <= $days_in_month; $day++): 
                                $curr_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
                                $booking = isset($occupancy[$pr['id']][$curr_date]) ? $occupancy[$pr['id']][$curr_date] : null;
                            ?>
                                <td class="day-cell">
                                    <?php if ($booking): ?>
                                        <?php 
                                        $css_class = 'pending';
                                        if ($booking['booking_status'] === 'confirmed') $css_class = 'confirmed';
                                        if ($booking['booking_status'] === 'checked_in') $css_class = 'checked_in';
                                        if ($booking['booking_status'] === 'checked_out') $css_class = 'checked_out';
                                        
                                        // Show guest name on the check-in day, else output continuous empty block
                                        $show_text = '';
                                        if ($booking['check_in'] === $curr_date) {
                                            $show_text = htmlspecialchars($booking['customer_name']);
                                        }
                                        ?>
                                        <a href="booking-details.php?id=<?= $booking['id'] ?>" 
                                           class="booking-block <?= $css_class ?>" 
                                           title="Invoice: <?= htmlspecialchars($booking['invoice_no'] ?: $booking['booking_id']) ?> | Guest: <?= htmlspecialchars($booking['customer_name']) ?> (<?= $booking['check_in'] ?> to <?= $booking['check_out'] ?>)">
                                            <?= $show_text ? $show_text : '&nbsp;' ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $days_in_month + 1 ?>" class="text-center py-40 text-neutral-500">No physical rooms configured. Please add rooms in Physical Rooms tab first.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
