<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = 'Reservation payment status updated successfully!';
}
if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
    $success_message = 'Reservation record permanently deleted successfully!';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'status') $error_message = 'Invalid reservation status selection.';
    else if ($_GET['error'] === 'db') $error_message = 'Failed to update reservation status in database.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete reservation record.';
}

// Handle booking deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_booking') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: bookings.php?error=csrf");
        exit;
    }

    $booking_db_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    if ($booking_db_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
            $stmt->execute([$booking_db_id]);
            header("Location: bookings.php?delete=success");
            exit;
        } catch (Exception $e) {
            error_log("Failed to delete booking: " . $e->getMessage());
            header("Location: bookings.php?error=delete");
            exit;
        }
    }
}

// Handle booking status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: bookings.php?error=csrf");
        exit;
    } else {
        $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';

        $allowed_statuses = ['pending', 'paid', 'cancelled'];
        if (!in_array($status, $allowed_statuses)) {
            header("Location: bookings.php?error=status");
            exit;
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE bookings SET payment_status = ? WHERE id = ?");
                $stmt->execute([$status, $booking_id]);
                header("Location: bookings.php?success=1");
                exit;
            } catch (Exception $e) {
                error_log("Booking status update failure: " . $e->getMessage());
                header("Location: bookings.php?error=db");
                exit;
            }
        }
    }
}

// Fetch bookings from database
$bookings = [];
try {
    $bookings = $pdo->query("
        SELECT b.*, r.title as room_title 
        FROM bookings b 
        LEFT JOIN rooms r ON b.room_id = r.id 
        ORDER BY b.id DESC
    ")->fetchAll();
} catch (Exception $e) {
    error_log("Bookings listing DB load failure: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Reservations</h1>
        <p class="text-sm text-neutral-500 mt-5">Track online room bookings, payment statuses, and print guest invoice receipts.</p>
    </div>
</div>

<?php if (!empty($success_message)): ?>
    <div class="alert alert-success alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        <?= htmlspecialchars($success_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-25" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        <?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="panel-card">
    <h3 class="font-heading mb-25" style="font-size:18px;">Master Booking Register</h3>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Ref ID / Guest Details</th>
                    <th>Room Reserved</th>
                    <th>Stay Dates</th>
                    <th>Amount Details</th>
                    <th>Razorpay ID</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($bookings) > 0): ?>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <strong style="color: #9c6047;">#<?= htmlspecialchars($b['booking_id']) ?></strong><br>
                                <span style="font-size:11.5px; font-weight:700; color:#555;"><?= htmlspecialchars($b['invoice_no'] ?: 'Pending Invoicing') ?></span><br>
                                <strong style="color:#334155;"><?= htmlspecialchars($b['customer_name']) ?></strong><br>
                                <span style="font-size:12.5px; color:#64748b;"><?= htmlspecialchars($b['customer_email']) ?></span><br>
                                <span style="font-size:12px; color:#64748b;"><?= htmlspecialchars($b['customer_phone']) ?></span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <strong style="color:#334155; font-size:13.5px;"><?= htmlspecialchars($b['room_title'] ?: 'Deluxe Room') ?></strong>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <span style="font-size:13.5px; font-weight:600; color:#334155;"><?= date('d M Y', strtotime($b['check_in'])) ?></span><br>
                                <span style="font-size:12px; color:#64748b; font-weight:550;">to</span><br>
                                <span style="font-size:13.5px; font-weight:600; color:#334155;"><?= date('d M Y', strtotime($b['check_out'])) ?></span><br>
                                <span style="font-size:11.5px; color:#888880; font-weight:600;">(<?= htmlspecialchars($b['total_nights']) ?> night(s))</span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <strong style="font-size:14.5px; color:#0f172a;">₹<?= number_format($b['total_amount'], 2) ?></strong><br>
                                <span style="font-size:11.5px; color:#64748b;">Base: ₹<?= number_format($b['base_amount'], 2) ?></span><br>
                                <?php if (!empty($b['coupon_code'])): ?>
                                    <span class="badge bg-light text-success border mt-5" style="font-size:10px; padding:3px 6px; font-weight:700;">Used: <?= htmlspecialchars($b['coupon_code']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <span style="font-family:Courier, monospace; font-size:12.5px; color:#475569; font-weight:600;">
                                    <?= htmlspecialchars($b['razorpay_payment_id'] ?: 'N/A') ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <?php
                                $status_bg = '#ef4444';
                                if (strtolower($b['payment_status']) === 'paid') $status_bg = '#10b981';
                                else if (strtolower($b['payment_status']) === 'pending') $status_bg = '#f59e0b';
                                ?>
                                <span class="badge" style="background-color: <?= $status_bg ?>; color: #ffffff; font-size: 11px; padding: 5px 10px; border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($b['payment_status']) ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <div class="d-flex flex-column gap-2" style="width: 130px;">
                                    <!-- Edit Status Form -->
                                    <form action="bookings.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        
                                        <select class="form-select" name="status" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; height: 34px; background-color: #ffffff; width: 100%; color: #334155;">
                                            <option value="pending" <?= $b['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="paid" <?= $b['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                            <option value="cancelled" <?= $b['payment_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </form>

                                    <!-- Horizontal Actions Strip -->
                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                        <!-- View Details -->
                                        <a href="booking-details.php?id=<?= $b['id'] ?>" class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center" style="padding: 6px; border-radius: 6px; width: 38px; height: 32px; color: #64748b;" title="View Details">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        
                                        <!-- Print Receipt link -->
                                        <a href="../invoice.php?ref=<?= urlencode($b['booking_id']) ?>&print=true" target="_blank" class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center" style="padding: 6px; border-radius: 6px; width: 38px; height: 32px; border-color: #cbd5e1; color: #16a34a;" title="Print Receipt">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                            </svg>
                                        </a>

                                        <!-- Delete Reservation Form -->
                                        <form action="bookings.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this booking?');" style="margin: 0; display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="delete_booking">
                                            <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-light border d-inline-flex align-items-center justify-content-center text-danger" style="padding: 6px; border-radius: 6px; width: 38px; height: 32px; border-color: #cbd5e1;" title="Delete Record">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-40 text-neutral-500">No room booking logs exist in the system database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
