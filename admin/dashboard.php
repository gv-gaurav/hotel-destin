<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

// Handle Delete Booking action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_booking') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: dashboard.php?error=csrf");
        exit;
    }

    $booking_db_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    if ($booking_db_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
            $stmt->execute([$booking_db_id]);
            header("Location: dashboard.php?delete=success");
            exit;
        } catch (Exception $e) {
            error_log("Failed to delete booking: " . $e->getMessage());
            header("Location: dashboard.php?delete=error");
            exit;
        }
    }
}

// Handle Reset Booking Data action
if (isset($_GET['action']) && $_GET['action'] === 'reset') {
    try {
        $pdo->query("SET FOREIGN_KEY_CHECKS = 0;");
        $pdo->query("TRUNCATE TABLE bookings;");
        $pdo->query("SET FOREIGN_KEY_CHECKS = 1;");
        header("Location: dashboard.php?reset=success");
        exit;
    } catch (Exception $e) {
        error_log("Failed to reset booking data: " . $e->getMessage());
    }
}

$selected_month = isset($_GET['month']) ? trim($_GET['month']) : 'all';

// Fetch dynamic unique months list for the dropdown
$months_list = [];
try {
    $b_months = $pdo->query("SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as ym FROM bookings WHERE created_at IS NOT NULL ORDER BY ym DESC")->fetchAll(PDO::FETCH_COLUMN);
    $e_months = $pdo->query("SELECT DISTINCT DATE_FORMAT(created_at, '%Y-%m') as ym FROM enquiries WHERE created_at IS NOT NULL ORDER BY ym DESC")->fetchAll(PDO::FETCH_COLUMN);
    $months_list = array_unique(array_filter(array_merge($b_months, $e_months)));
    rsort($months_list);
} catch (Exception $e) {
    error_log("Failed to fetch unique months: " . $e->getMessage());
}

// Initialize metrics
$gross_revenue = 0.00;
$online_revenue = 0.00;
$offline_revenue = 0.00;
$room_bookings = 0;
$banquet_leads = 0;
$pending_enquiries = 0;
$recent_bookings = [];

try {
    $month_filter_b = '';
    $month_filter_e = '';
    $params_b = [];
    $params_e = [];

    if ($selected_month !== 'all' && preg_match('/^\d{4}-\d{2}$/', $selected_month)) {
        $month_filter_b = " AND DATE_FORMAT(created_at, '%Y-%m') = :month ";
        $month_filter_e = " AND DATE_FORMAT(created_at, '%Y-%m') = :month ";
        $params_b['month'] = $selected_month;
        $params_e['month'] = $selected_month;
    }

    // 1. Calculate Gross Revenue by payment methods (payment_status = 'paid')
    $online_query = "SELECT SUM(total_amount) FROM bookings WHERE payment_status = 'paid' AND (payment_method != 'Pay at Hotel' OR payment_method IS NULL) " . $month_filter_b;
    $online_stmt = $pdo->prepare($online_query);
    $online_stmt->execute($params_b);
    $online_revenue = floatval($online_stmt->fetchColumn() ?: 0.00);

    $offline_query = "SELECT SUM(total_amount) FROM bookings WHERE payment_status = 'paid' AND payment_method = 'Pay at Hotel' " . $month_filter_b;
    $offline_stmt = $pdo->prepare($offline_query);
    $offline_stmt->execute($params_b);
    $offline_revenue = floatval($offline_stmt->fetchColumn() ?: 0.00);

    $gross_revenue = $online_revenue + $offline_revenue;

    // 2. Count Room Bookings (confirmed & checked_in reservation status)
    $bookings_query = "SELECT COUNT(*) FROM bookings WHERE booking_status IN ('confirmed', 'checked_in') " . $month_filter_b;
    $bookings_stmt = $pdo->prepare($bookings_query);
    $bookings_stmt->execute($params_b);
    $room_bookings = intval($bookings_stmt->fetchColumn() ?: 0);

    // 3. Count Banquet Leads (enquiries belonging to banquet or wedding categories)
    $leads_query = "SELECT COUNT(*) FROM enquiries WHERE category IN ('banquet', 'wedding') " . $month_filter_e;
    $leads_stmt = $pdo->prepare($leads_query);
    $leads_stmt->execute($params_e);
    $banquet_leads = intval($leads_stmt->fetchColumn() ?: 0);

    // 4. Count Pending Enquiries (unresolved enquiries not in banquet/wedding)
    $enq_query = "SELECT COUNT(*) FROM enquiries WHERE status = 'pending' AND category NOT IN ('banquet', 'wedding') " . $month_filter_e;
    $enq_stmt = $pdo->prepare($enq_query);
    $enq_stmt->execute($params_e);
    $pending_enquiries = intval($enq_stmt->fetchColumn() ?: 0);

    // 5. Fetch recent 10 reservation requests
    $recent_stmt = $pdo->query("
        SELECT b.*, r.title AS room_title 
        FROM bookings b 
        LEFT JOIN rooms r ON b.room_id = r.id 
        ORDER BY b.id DESC 
        LIMIT 10
    ");
    $recent_bookings = $recent_stmt->fetchAll();

} catch (Exception $e) {
    error_log("Dashboard analytics failed to calculate: " . $e->getMessage());
}
?>

<!-- Action Status Alert -->
<?php if (isset($_GET['reset']) && $_GET['reset'] === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        Database booking registry reset to empty state successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['delete']) && $_GET['delete'] === 'success'): ?>
    <div class="alert alert-success alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        Booking record deleted successfully!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['delete']) && $_GET['delete'] === 'error'): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        Failed to delete the booking record.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php elseif (isset($_GET['error']) && $_GET['error'] === 'csrf'): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        Security check mismatch. Please try again.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Control Console Header -->
<div class="d-flex justify-content-between align-items-center mb-35" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 20px; margin-bottom: 30px;">
    <div>
        <h1 class="panel-title mb-5" style="font-size: 28px; font-weight: 700; color: #0f172a; letter-spacing: -0.5px;">Hotel Destin Control Console</h1>
        <p class="text-sm text-neutral-500 mb-0" style="font-size: 14px; color: #64748b; font-weight: 500;">Real-time resort parameters monitoring.</p>
    </div>
    <div>
        <a href="dashboard.php?action=reset" onclick="return confirm('WARNING: This will permanently wipe all booking and reservation transaction logs. Proceed?');" class="btn d-flex align-items-center gap-2" style="background-color: #ef4444; color: #ffffff; border-radius: 8px; font-size: 13.5px; font-weight: 700; padding: 10px 20px; border: none; transition: background 0.2s ease;" onmouseover="this.style.backgroundColor='#dc2626';" onmouseout="this.style.backgroundColor='#ef4444';">
            <!-- Trash Icon -->
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Reset Booking Data
        </a>
    </div>
</div>

<!-- Analytics Filter Bar -->
<div class="d-flex justify-content-between align-items-center mb-25" style="background-color: #fafaf9; padding: 12px 18px; border-radius: 12px; border: 1px solid #f1f1f0;">
    <div class="d-flex align-items-center gap-3">
        <span style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">Filter Analytics:</span>
        <form method="GET" action="dashboard.php" style="margin: 0; display: inline-flex; align-items: center;">
            <select name="month" onchange="this.form.submit()" class="form-select" style="font-size: 13px; font-weight: 600; padding: 6px 12px 6px 36px; border-radius: 8px; border: 1px solid #cbd5e1; height: 36px; background-color: #ffffff; color: #334155; cursor: pointer; min-width: 160px; background-image: url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2216%22 height=%2216%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2364748b%22 stroke-width=%222%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><rect x=%223%22 y=%224%22 width=%2218%22 height=%2218%22 rx=%222%22 ry=%222%22></rect><line x1=%2216%22 y1=%222%22 x2=%2216%22 y2=%226%22></line><line x1=%228%22 y1=%222%22 x2=%228%22 y2=%226%22></line><line x1=%223%22 y1=%2210%22 x2=%2221%22 y2=%2210%22></line></svg>'); background-repeat: no-repeat; background-position: 10px center; background-size: 16px;">
                <option value="all">All Time</option>
                <?php foreach ($months_list as $ym): ?>
                    <?php
                    $date_obj = DateTime::createFromFormat('Y-m', $ym);
                    $formatted_ym = $date_obj ? $date_obj->format('F Y') : $ym;
                    ?>
                    <option value="<?= $ym ?>" <?= $selected_month === $ym ? 'selected' : '' ?>><?= htmlspecialchars($formatted_ym) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
    <?php if ($selected_month !== 'all'): ?>
        <?php
        $selected_date_obj = DateTime::createFromFormat('Y-m', $selected_month);
        $display_month_name = $selected_date_obj ? $selected_date_obj->format('F Y') : $selected_month;
        ?>
        <span style="font-size: 12.5px; font-weight: 600; color: #9c6047; background-color: rgba(156,96,71,0.06); padding: 4px 12px; border-radius: 6px; border: 1px solid rgba(156,96,71,0.15);">
            Showing results for: <strong><?= htmlspecialchars($display_month_name) ?></strong>
        </span>
    <?php endif; ?>
</div>

<!-- Refined Control Console Metrics Grid -->
<div class="row g-4 mb-35" style="margin-bottom: 35px;">
    <!-- Metric 1: Gross Revenue -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="metric-card" style="padding: 24px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); height: 100%;">
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Gross Revenue</span>
                <div class="metric-val" style="font-size: 26px; font-weight: 600; color: #0f172a; margin-top: 5px; margin-bottom: 5px;">₹<?= number_format($gross_revenue, 2) ?></div>
                <span style="font-size: 11.5px; font-weight: 500; color: #64748b; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                    <span>Online: <strong style="color: #334155;">₹<?= number_format($online_revenue, 2) ?></strong></span>
                    <span style="color: #cbd5e1;">•</span>
                    <span>Hotel: <strong style="color: #334155;">₹<?= number_format($offline_revenue, 2) ?></strong></span>
                </span>
            </div>
            <div style="background: #fef3c7; color: #d97706; padding: 12px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                <!-- Rupee coin icon -->
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 5h8M8 9h8M8 5a4 4 0 0 1 0 8H8M11 13l5 5"></path>
                    <circle cx="12" cy="12" r="10"></circle>
                </svg>
            </div>
        </div>
    </div>

    <!-- Metric 2: Room Bookings -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="metric-card" style="padding: 24px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Room Bookings</span>
                <div class="metric-val" style="font-size: 26px; font-weight: 600; color: #0f172a; margin-top: 5px; margin-bottom: 5px;"><?= $room_bookings ?></div>
                <span style="font-size: 11.5px; font-weight: 500; color: #64748b;">Confirmed & Checked-In</span>
            </div>
            <div style="background: #dbeafe; color: #2563eb; padding: 12px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                <!-- Calendar icon -->
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Metric 3: Banquet Leads -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="metric-card" style="padding: 24px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Banquet Leads</span>
                <div class="metric-val" style="font-size: 26px; font-weight: 600; color: #0f172a; margin-top: 5px; margin-bottom: 5px;"><?= $banquet_leads ?></div>
                <span style="font-size: 11.5px; font-weight: 500; color: #64748b; display: flex; align-items: center; gap: 4px;">
                    🟡 <span style="color: #64748b; font-weight: 500;">Awaiting quotation mail</span>
                </span>
            </div>
            <div style="background: #fef2f2; color: #dc2626; padding: 12px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                <!-- Banquet icon -->
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Metric 4: Pending Enquiries -->
    <div class="col-xl-3 col-md-6 col-12">
        <div class="metric-card" style="padding: 24px; border-radius: 12px; background: #ffffff; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div>
                <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.8px;">Pending Enquiries</span>
                <div class="metric-val" style="font-size: 26px; font-weight: 600; color: #0f172a; margin-top: 5px; margin-bottom: 5px;"><?= $pending_enquiries ?></div>
                <span style="font-size: 11.5px; font-weight: 500; color: #64748b;">General support & wedding</span>
            </div>
            <div style="background: #f3f4f6; color: #4b5563; padding: 12px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center;">
                <!-- Envelope / enquiry icon -->
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Master Table View: Recent Reservation Requests -->
<div class="panel-card" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);">
    <div class="d-flex justify-content-between align-items-center mb-25" style="margin-bottom: 20px;">
        <h3 class="font-heading mb-0" style="font-size: 18px; font-weight: 700; color: #0f172a;">Recent Reservation Requests</h3>
        <a href="bookings.php" class="text-sm text-neutral-500" style="font-weight: 600; text-decoration: underline; color: #64748b;">View All Registers</a>
    </div>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Guest Name</th>
                    <th>Accommodation</th>
                    <th>Duration (Nights)</th>
                    <th>Total Cost</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($recent_bookings) > 0): ?>
                    <?php foreach ($recent_bookings as $b): ?>
                        <?php 
                        $check_in_fmt = date('d M', strtotime($b['check_in']));
                        $check_out_fmt = date('d M', strtotime($b['check_out']));
                        $nights_fmt = $b['total_nights'] . ' N';
                        ?>
                        <tr>
                            <td><strong style="color: #9c6047; font-family: monospace; font-size: 13.5px;"><?= htmlspecialchars($b['booking_id']) ?></strong></td>
                            <td><strong style="color: #334155; font-size: 13.5px;"><?= htmlspecialchars($b['customer_name']) ?></strong></td>
                            <td><span style="font-weight: 600; color: #475569;"><?= htmlspecialchars($b['room_title'] ?: 'Standard Room') ?></span></td>
                            <td><span style="font-size: 13px; font-weight: 600; color: #475569;"><?= $check_in_fmt ?> - <?= $check_out_fmt ?> (<?= $nights_fmt ?>)</span></td>
                            <td><strong style="color: #0f172a; font-size: 14px;">₹<?= number_format($b['total_amount'], 2) ?></strong></td>
                            <td>
                                <?php if (strtolower($b['payment_status']) === 'paid'): ?>
                                    <span class="badge" style="background-color: #10b981; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 700;">Paid</span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #ef4444; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 700;">Unpaid</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (strtolower($b['booking_status']) === 'confirmed'): ?>
                                    <span class="badge" style="background-color: #3b82f6; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 700;">Confirmed</span>
                                <?php elseif (strtolower($b['booking_status']) === 'cancelled'): ?>
                                    <span class="badge" style="background-color: #ef4444; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 700;">Cancelled</span>
                                <?php else: ?>
                                    <span class="badge" style="background-color: #f59e0b; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 4px; font-weight: 700; text-transform: capitalize;"><?= htmlspecialchars($b['booking_status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="white-space: nowrap;">
                                <div class="d-flex align-items-center gap-2">
                                    <a href="booking-details.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center" style="padding: 6px; border-radius: 6px; color: #64748b;" title="Edit Reservation">
                                        <!-- Edit Pen Icon -->
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    
                                    <form action="dashboard.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this booking?');" style="margin: 0; display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="delete_booking">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center text-danger" style="padding: 6px; border-radius: 6px;" title="Delete Reservation">
                                            <!-- Trash/Delete Icon -->
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-40 text-neutral-500" style="font-weight: 500;">No reservation requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
