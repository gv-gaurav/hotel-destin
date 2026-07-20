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

// Fetch ALL bookings (active & cancelled) overlapping this month stay date range
$all_month_bookings = [];
try {
    $stmt = $pdo->prepare("
        SELECT b.*, r.title as room_title, pr.room_number 
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        LEFT JOIN physical_rooms pr ON b.physical_room_id = pr.id 
        WHERE b.check_in <= ? 
          AND b.check_out >= ?
    ");
    $stmt->execute([$end_date, $start_date]);
    $all_month_bookings = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to load month summary bookings: " . $e->getMessage());
}

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

// Filter active physical room bookings for timeline/Gantt view
$bookings = [];
foreach ($all_month_bookings as $b) {
    if ($b['booking_status'] !== 'cancelled' && !empty($b['physical_room_id'])) {
        $bookings[] = $b;
    }
}

// Map bookings to physical rooms and dates for timeline view
$occupancy = []; // structure: [physical_room_id][date] => booking_data
foreach ($bookings as $b) {
    $pr_id = $b['physical_room_id'];
    $cin = new DateTime($b['check_in']);
    $cout = new DateTime($b['check_out']);
    
    $curr = clone $cin;
    while ($curr < $cout) {
        $date_key = $curr->format('Y-m-d');
        $occupancy[$pr_id][$date_key] = $b;
        $curr->modify('+1 day');
    }
}

// Calculate total capacity of the hotel
$total_rooms = count($physical_rooms);
if ($total_rooms === 0) {
    $total_rooms = (int) $pdo->query("SELECT SUM(inventory) FROM rooms")->fetchColumn();
}
if ($total_rooms <= 0) $total_rooms = 10;

// Build day-by-day stats summary for Monthly Grid View
$summary = []; // [day] => ['booked' => X, 'cancelled' => Y, 'available' => Z, 'bookings' => [...], 'cancelled_bookings' => [...]]
for ($day = 1; $day <= $days_in_month; $day++) {
    $curr_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
    $booked_count = 0;
    $cancelled_count = 0;
    $day_bookings = [];
    $day_cancelled_bookings = [];
    
    foreach ($all_month_bookings as $b) {
        if ($b['check_in'] <= $curr_date && $b['check_out'] > $curr_date) {
            if ($b['booking_status'] === 'cancelled') {
                $cancelled_count++;
                $day_cancelled_bookings[] = $b;
            } else {
                $booked_count++;
                $day_bookings[] = $b;
            }
        }
    }
    
    $available_count = max(0, $total_rooms - $booked_count);
    $summary[$day] = [
        'booked' => $booked_count,
        'cancelled' => $cancelled_count,
        'available' => $available_count,
        'bookings' => $day_bookings,
        'cancelled_bookings' => $day_cancelled_bookings
    ];
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
    
    /* Summary Calendar Grid Styles */
    .summary-calendar-grid {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
        margin-top: 15px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
    }
    .calendar-days-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
        font-weight: 700;
        font-size: 13.5px;
        color: #475569;
        padding: 12px 0;
    }
    .calendar-days-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }
    .calendar-day-cell {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        min-height: 120px;
        padding: 12px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s ease;
        border-top: 4px solid transparent;
    }
    .calendar-day-cell:nth-child(7n) {
        border-right: none;
    }
    .calendar-day-cell:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        z-index: 2;
    }
    .calendar-day-cell.blank {
        background: #f8fafc;
        cursor: default;
        border-top-color: transparent !important;
    }
    .calendar-day-cell.blank:hover {
        transform: none;
        box-shadow: none;
    }
    
    /* Available State (Blue) */
    .calendar-day-cell.cell-available {
        background-color: #f0f7ff;
        border-top-color: #3b82f6;
    }
    .calendar-day-cell.cell-available:hover {
        background-color: #e0f0ff;
    }
    
    /* Booked State (Green) */
    .calendar-day-cell.cell-booked {
        background-color: #f0fdf4;
        border-top-color: #22c55e;
    }
    .calendar-day-cell.cell-booked:hover {
        background-color: #dcfce7;
    }
    
    /* Cancelled State (Red) */
    .calendar-day-cell.cell-cancelled {
        background-color: #fef2f2;
        border-top-color: #ef4444;
    }
    .calendar-day-cell.cell-cancelled:hover {
        background-color: #fee2e2;
    }

    .day-number {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        text-align: right;
    }
    .status-container {
        display: flex;
        flex-direction: column;
        gap: 4px;
        align-items: flex-start;
    }
    .status-item {
        font-size: 11.5px;
        line-height: 1.4;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Hotel Booking & Occupancy Calendar</h1>
        <p class="text-sm text-neutral-500 mt-5">Visual calendar to track booked, available, and cancelled rooms daily. Click on any date to manage guests.</p>
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
        <div class="d-flex gap-20 align-items-center">
            <div class="legend-item">
                <span class="legend-color" style="background:#eff6ff; border:1.5px solid #3b82f6;"></span>
                <span>All Rooms Free</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background:#f0fdf4; border:1.5px solid #22c55e;"></span>
                <span>Active Booking</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background:#fef2f2; border:1.5px solid #ef4444;"></span>
                <span>Cancelled Stays (No active bookings)</span>
            </div>
        </div>
    </div>

    <!-- Monthly Summary Grid View -->
    <?php
    $first_day_of_week = intval(date('w', $first_day_timestamp));
    ?>
    <div class="summary-calendar-grid">
        <div class="calendar-days-header">
            <div>Sunday</div>
            <div>Monday</div>
            <div>Tuesday</div>
            <div>Wednesday</div>
            <div>Thursday</div>
            <div>Friday</div>
            <div>Saturday</div>
        </div>
        <div class="calendar-days-body">
            <!-- Padding cells for previous month -->
            <?php for ($i = 0; $i < $first_day_of_week; $i++): ?>
                <div class="calendar-day-cell blank"></div>
            <?php endfor; ?>
            
            <!-- Days of current month -->
            <?php for ($day = 1; $day <= $days_in_month; $day++): 
                $day_data = $summary[$day];
                $curr_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
                
                // Class and HTML variables
                $cell_class = 'cell-available';
                $status_html = '';
                
                if ($day_data['booked'] > 0) {
                    $cell_class = 'cell-booked';
                    $status_html .= '<div class="status-item text-success" style="font-weight:700; font-size:12.5px;">🛌 ' . $day_data['booked'] . ' Booked</div>';
                    $status_html .= '<div class="status-item text-primary" style="font-size:11.5px;">🔑 ' . $day_data['available'] . ' Vacant</div>';
                    if ($day_data['cancelled'] > 0) {
                        $status_html .= '<div class="status-item text-danger" style="font-size:11px;">❌ ' . $day_data['cancelled'] . ' Cancelled</div>';
                    }
                } elseif ($day_data['cancelled'] > 0) {
                    $cell_class = 'cell-cancelled';
                    $status_html .= '<div class="status-item text-danger" style="font-weight:700; font-size:12.5px;">❌ ' . $day_data['cancelled'] . ' Cancelled</div>';
                    $status_html .= '<div class="status-item text-primary" style="font-size:11.5px;">🔑 ' . $day_data['available'] . ' Vacant</div>';
                } else {
                    $cell_class = 'cell-available';
                    $status_html .= '<div class="status-item text-primary" style="font-weight:700; font-size:12.5px;">🔑 ' . $day_data['available'] . ' Vacant</div>';
                    $status_html .= '<div class="status-item text-muted" style="font-size:11.5px; font-weight: normal;">All Rooms Free</div>';
                }
            ?>
                <div class="calendar-day-cell <?= $cell_class ?>" data-date="<?= $curr_date ?>" data-day="<?= $day ?>">
                    <div class="day-number"><?= $day ?></div>
                    <div class="status-container mt-2">
                        <?= $status_html ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Collapsible Advanced Timeline Grid section -->
<div class="card mt-35 border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background: #ffffff; border: 1px solid #e2e8f0;">
    <div class="card-header bg-light border-0 py-15 px-20 d-flex justify-content-between align-items-center" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#timelineSection" aria-expanded="false" aria-controls="timelineSection">
        <h5 class="mb-0 font-heading" style="font-size: 15px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 8px;">
            <span>📋</span> Advanced Room-wise Stay Chart (Timeline View)
        </h5>
        <span class="text-neutral-500" style="font-size: 12px; font-weight: 600;">Click to Open / Close</span>
    </div>
    <div class="collapse" id="timelineSection">
        <div class="card-body p-20 border-top" style="background: #fafafa;">
            <p class="text-muted text-xs mb-15">This advanced chart maps individual active bookings directly to configured physical rooms (Room 101, 102 etc.). Hover over blocks to view guest details, or click on a block to inspect stays.</p>
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
                                                
                                                // Show guest name on check-in day
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
    </div>
</div>

<!-- Date Details Modal Container -->
<div class="modal fade" id="dayDetailsModal" tabindex="-1" aria-labelledby="dayDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
            <div class="modal-header bg-dark text-white" style="border-bottom: none; padding: 18px 24px;">
                <h5 class="modal-title font-heading" id="dayDetailsModalLabel" style="font-weight: 700; font-size:18px;">Stay Bookings</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: invert(1) grayscale(1) brightness(2);"></button>
            </div>
            <div class="modal-body" style="padding: 24px; background: #fafafa;">
                <div class="mb-25">
                    <h6 class="font-heading text-success mb-12" style="font-weight: 700; font-size: 15px; border-bottom: 2px solid #a7f3d0; padding-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <span style="width: 10px; height: 10px; background-color: #198754; border-radius: 50%;"></span>
                        Active & Checked-in Bookings
                    </h6>
                    <div id="activeBookingsList" class="table-responsive" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 5px;">
                        <!-- Dynamic active bookings injected here -->
                    </div>
                </div>
                <div>
                    <h6 class="font-heading text-danger mb-12" style="font-weight: 700; font-size: 15px; border-bottom: 2px solid #fecaca; padding-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                        <span style="width: 10px; height: 10px; background-color: #dc3545; border-radius: 50%;"></span>
                        Cancelled Bookings
                    </h6>
                    <div id="cancelledBookingsList" class="table-responsive" style="background: #ffffff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 5px;">
                        <!-- Dynamic cancelled bookings injected here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 15px 24px; background: #f1f5f9;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 20px;">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    var calendarSummaryData = <?= json_encode($summary) ?>;
    
    document.addEventListener("DOMContentLoaded", function() {
        // Delegate click events on calendar cells
        var cells = document.querySelectorAll('.calendar-day-cell:not(.blank)');
        cells.forEach(function(cell) {
            cell.addEventListener('click', function() {
                var day = this.getAttribute('data-day');
                var dateStr = this.getAttribute('data-date');
                var data = calendarSummaryData[day];
                
                // Format the title date
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                var dateObj = new Date(dateStr + "T00:00:00");
                var formattedDate = dateObj.toLocaleDateString('en-US', options);
                
                document.getElementById('dayDetailsModalLabel').innerText = 'Stay Bookings on ' + formattedDate;
                
                // Build Active Stays
                var activeHtml = '';
                if (data.bookings && data.bookings.length > 0) {
                    activeHtml += '<table class="table table-hover align-middle mb-0" style="font-size:13.2px;">';
                    activeHtml += '<thead><tr style="background:#f8fafc;"><th style="padding:10px 12px;">Guest Name</th><th style="padding:10px 12px;">Allocated Room</th><th style="padding:10px 12px;">Stay Interval</th><th style="padding:10px 12px; text-align:center;">Status</th><th style="padding:10px 12px; text-align:center;">Action</th></tr></thead>';
                    activeHtml += '<tbody>';
                    data.bookings.forEach(function(b) {
                        var statusBadge = '<span class="badge bg-warning text-dark" style="font-weight:600;">Pending</span>';
                        if (b.booking_status === 'confirmed') statusBadge = '<span class="badge bg-success" style="font-weight:600;">Confirmed</span>';
                        if (b.booking_status === 'checked_in') statusBadge = '<span class="badge bg-danger" style="font-weight:600;">Occupied</span>';
                        if (b.booking_status === 'checked_out') statusBadge = '<span class="badge bg-secondary" style="font-weight:600;">Completed</span>';
                        
                        var roomInfo = b.room_number ? 'Room ' + b.room_number + ' <span class="text-muted" style="font-size:11.5px;">(' + b.room_title + ')</span>' : '<span class="text-muted" style="font-size:12px;">Unallocated Category: </span>' + b.room_title;
                        var viewLink = 'booking-details.php?id=' + b.id;
                        
                        activeHtml += '<tr>';
                        activeHtml += '<td style="padding:10px 12px;"><strong>' + b.customer_name + '</strong></td>';
                        activeHtml += '<td style="padding:10px 12px;">' + roomInfo + '</td>';
                        activeHtml += '<td style="padding:10px 12px;">' + b.check_in + ' to ' + b.check_out + '</td>';
                        activeHtml += '<td style="padding:10px 12px; text-align:center;">' + statusBadge + '</td>';
                        activeHtml += '<td style="padding:10px 12px; text-align:center;"><a href="' + viewLink + '" class="btn btn-xs btn-outline-dark" style="padding:3px 10px; font-size:11px; border-radius:5px; font-weight:600;">View Details</a></td>';
                        activeHtml += '</tr>';
                    });
                    activeHtml += '</tbody></table>';
                } else {
                    activeHtml = '<p class="text-muted text-center py-20 mb-0">No active stays scheduled on this date.</p>';
                }
                document.getElementById('activeBookingsList').innerHTML = activeHtml;
                
                // Build Cancelled Stays
                var cancelledHtml = '';
                if (data.cancelled_bookings && data.cancelled_bookings.length > 0) {
                    cancelledHtml += '<table class="table table-hover align-middle mb-0" style="font-size:13.2px;">';
                    cancelledHtml += '<thead><tr style="background:#f8fafc;"><th style="padding:10px 12px;">Guest Name</th><th style="padding:10px 12px;">Room Type</th><th style="padding:10px 12px;">Stay Interval</th><th style="padding:10px 12px; text-align:center;">Status</th><th style="padding:10px 12px; text-align:center;">Action</th></tr></thead>';
                    cancelledHtml += '<tbody>';
                    data.cancelled_bookings.forEach(function(b) {
                        var viewLink = 'booking-details.php?id=' + b.id;
                        cancelledHtml += '<tr>';
                        cancelledHtml += '<td style="padding:10px 12px;"><strong>' + b.customer_name + '</strong></td>';
                        cancelledHtml += '<td style="padding:10px 12px;">' + b.room_title + '</td>';
                        cancelledHtml += '<td style="padding:10px 12px;">' + b.check_in + ' to ' + b.check_out + '</td>';
                        cancelledHtml += '<td style="padding:10px 12px; text-align:center;"><span class="badge bg-danger" style="font-weight:600;">Cancelled</span></td>';
                        cancelledHtml += '<td style="padding:10px 12px; text-align:center;"><a href="' + viewLink + '" class="btn btn-xs btn-outline-dark" style="padding:3px 10px; font-size:11px; border-radius:5px; font-weight:600;">View Details</a></td>';
                        cancelledHtml += '</tr>';
                    });
                    cancelledHtml += '</tbody></table>';
                } else {
                    cancelledHtml = '<p class="text-muted text-center py-20 mb-0">No cancelled reservations mapped to this date.</p>';
                }
                document.getElementById('cancelledBookingsList').innerHTML = cancelledHtml;
                
                // Launch modal
                var detailsModal = new bootstrap.Modal(document.getElementById('dayDetailsModal'));
                detailsModal.show();
            });
        });
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
