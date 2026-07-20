<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$booking = null;

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'confirm') $success_message = 'Reservation confirmed successfully!';
    if ($_GET['success'] === 'checkin') $success_message = 'Guest checked in successfully!';
    if ($_GET['success'] === 'checkout') $success_message = 'Guest checked out successfully!';
    if ($_GET['success'] === 'cancel') $success_message = 'Reservation cancelled and transaction marked as refunded.';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'db') $error_message = 'System failure executing database status update.';
}

// Fetch reservation details
try {
    $stmt = $pdo->prepare("
        SELECT b.*, r.title as category_title
        FROM bookings b 
        JOIN rooms r ON b.room_id = r.id 
        WHERE b.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
} catch (Exception $e) {
    error_log("Failed to load booking details: " . $e->getMessage());
}

if (!$booking) {
    echo "<h3>Error: Reservation record not found.</h3>";
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Process Action Forms
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: booking-details.php?id=" . $booking_id . "&error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'confirm_booking') {
            try {
                $upd = $pdo->prepare("UPDATE bookings SET booking_status = 'confirmed' WHERE id = ?");
                $upd->execute([$booking_id]);
                header("Location: booking-details.php?id=" . $booking_id . "&success=confirm");
                exit;
            } catch (Exception $e) {
                header("Location: booking-details.php?id=" . $booking_id . "&error=db");
                exit;
            }
        } else if ($action === 'check_in') {
            try {
                $upd = $pdo->prepare("UPDATE bookings SET booking_status = 'checked_in' WHERE id = ?");
                $upd->execute([$booking_id]);
                header("Location: booking-details.php?id=" . $booking_id . "&success=checkin");
                exit;
            } catch (Exception $e) {
                header("Location: booking-details.php?id=" . $booking_id . "&error=db");
                exit;
            }
        } else if ($action === 'check_out') {
            try {
                $upd = $pdo->prepare("UPDATE bookings SET booking_status = 'checked_out' WHERE id = ?");
                $upd->execute([$booking_id]);
                header("Location: booking-details.php?id=" . $booking_id . "&success=checkout");
                exit;
            } catch (Exception $e) {
                header("Location: booking-details.php?id=" . $booking_id . "&error=db");
                exit;
            }
        } else if ($action === 'cancel_refund') {
            $refund_tx_id = isset($_POST['refund_tx_id']) ? htmlspecialchars(trim($_POST['refund_tx_id'])) : 'REFUND-MOCK-' . rand(1000, 9999);
            try {
                $upd = $pdo->prepare("UPDATE bookings SET booking_status = 'cancelled', payment_status = 'refunded', refund_tx_id = ? WHERE id = ?");
                $upd->execute([$refund_tx_id, $booking_id]);
                header("Location: booking-details.php?id=" . $booking_id . "&success=cancel");
                exit;
            } catch (Exception $e) {
                header("Location: booking-details.php?id=" . $booking_id . "&error=db");
                exit;
            }
        }
    }
}
?>

<style>
    .timeline-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px 30px;
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    }

    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        z-index: 2;
        width: 130px;
        text-align: center;
        position: relative;
    }

    .step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #cbd5e1;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        transition: all 0.3s ease;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 2px #cbd5e1;
    }

    .step-label {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
    }

    .timeline-line {
        height: 3px;
        background: #cbd5e1;
        flex-grow: 1;
        margin-bottom: 22px;
        z-index: 1;
    }

    .timeline-step.active .step-num {
        background: #018acd;
        color: #ffffff;
        box-shadow: 0 0 0 2px #018acd, 0 0 0 5px rgba(156, 96, 71, 0.15);
    }

    .timeline-step.active .step-label {
        color: #018acd;
        font-weight: 600;
    }

    .timeline-line.active {
        background: #018acd;
    }

    /* Layout styling */
    .grid-info-col {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    }

    .grid-info-col h3 {
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 6px;
        color: #0f172a;
        border-bottom: 1px solid #f1f5f9;
        /* padding-bottom: 12px; */
        letter-spacing: -0.3px;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
    }

    .meta-row:last-child {
        border-bottom: none;
    }

    .meta-label {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .meta-value {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Reservation Details</h1>
        <p class="text-sm text-neutral-500 mt-5">Confirm arrivals, review guest payments, and monitor stay lifecycle timelines.</p>
    </div>
    <a href="bookings.php" class="btn btn-outline-dark d-inline-flex align-items-center gap-2" style="padding: 10px 24px; border-radius: 8px; font-size:14px; border-color:#cbd5e1; font-weight:700; height:42px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Reservations
    </a>
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

<!-- Check if Cancelled -->
<?php if ($booking['booking_status'] === 'cancelled'): ?>
    <div class="alert alert-danger mb-35 d-flex align-items-center justify-content-between" style="border-radius: 12px; padding: 16px 24px;">
        <div>
            <h5 class="mb-5 font-heading" style="color: #991b1b; font-weight:800; font-size:16px;">This Booking has been Cancelled and Refunded</h5>
            <span style="font-size:13px; color:#7f1d1d; font-weight:600;">Refund Transaction ID: <strong style="font-family:monospace;"><?= htmlspecialchars($booking['refund_tx_id'] ?: 'N/A') ?></strong></span>
        </div>
        <span class="badge" style="font-size:12px; padding:6px 12px; background-color:#ef4444; color:#ffffff; font-weight:700; text-transform:uppercase;">Refunded</span>
    </div>
<?php else: ?>
    <!-- Active stay timeline -->
    <div class="timeline-wrapper mb-35">
        <div class="timeline-step <?= in_array($booking['booking_status'], ['pending', 'confirmed', 'checked_in', 'checked_out']) ? 'active' : '' ?>">
            <span class="step-num">1</span>
            <span class="step-label">Created (Pending)</span>
        </div>
        <div class="timeline-line <?= in_array($booking['booking_status'], ['confirmed', 'checked_in', 'checked_out']) ? 'active' : '' ?>"></div>
        <div class="timeline-step <?= in_array($booking['booking_status'], ['confirmed', 'checked_in', 'checked_out']) ? 'active' : '' ?>">
            <span class="step-num">2</span>
            <span class="step-label">Confirmed</span>
        </div>
        <div class="timeline-line <?= in_array($booking['booking_status'], ['checked_in', 'checked_out']) ? 'active' : '' ?>"></div>
        <div class="timeline-step <?= in_array($booking['booking_status'], ['checked_in', 'checked_out']) ? 'active' : '' ?>">
            <span class="step-num">3</span>
            <span class="step-label">Checked In</span>
        </div>
        <div class="timeline-line <?= $booking['booking_status'] === 'checked_out' ? 'active' : '' ?>"></div>
        <div class="timeline-step <?= $booking['booking_status'] === 'checked_out' ? 'active' : '' ?>">
            <span class="step-num">4</span>
            <span class="step-label">Checked Out</span>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Left Column: Reservation Info & Invoicing -->
    <div class="col-lg-8">
        <div class="grid-info-col">
            <h3>Stay Particulars & Billing</h3>

            <div class="row">
                <div class="col-md-6">
                    <div class="meta-row">
                        <span class="meta-label">Booking Reference ID:</span>
                        <span class="meta-value" style="color:#018acd; font-family:monospace; font-size:14px;">#<?= htmlspecialchars($booking['booking_id']) ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Sequence Invoice ID:</span>
                        <span class="meta-value" style="font-weight:700;"><?= htmlspecialchars($booking['invoice_no'] ?: 'Pending Invoicing') ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Payment Method:</span>
                        <span class="meta-value" style="font-weight:700; color: <?= $booking['payment_method'] === 'Pay at Hotel' ? '#c2410c' : '#1d4ed8' ?>;"><?= htmlspecialchars($booking['payment_method'] ?: 'Razorpay') ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Room Type Category:</span>
                        <span class="meta-value"><?= htmlspecialchars($booking['category_title']) ?></span>
                    </div>
                    <div class="meta-row" style="border-bottom:none;">
                        <span class="meta-label">Stay Dates (Nights):</span>
                        <span class="meta-value" style="font-size:12.5px;"><?= date('d M Y', strtotime($booking['check_in'])) ?> to <?= date('d M Y', strtotime($booking['check_out'])) ?> (<?= htmlspecialchars($booking['total_nights']) ?> Nights)</span>
                    </div>
                </div>
                <div class="col-md-6" style="border-left:1px solid #f1f5f9;">
                    <div class="meta-row">
                        <span class="meta-label">Meal Plan Selection:</span>
                        <span class="meta-value" style="background:#fafafa; border:1px solid #cbd5e1; padding:2px 8px; border-radius:4px; font-weight:700;"><?= htmlspecialchars($booking['meal_plan'] ?: 'EP') ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Total Guests:</span>
                        <span class="meta-value"><?= htmlspecialchars($booking['guests']) ?> (Adults: <?= htmlspecialchars($booking['adults']) ?>, Children: <?= htmlspecialchars($booking['children']) ?>)</span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Razorpay Order ID:</span>
                        <span class="meta-value" style="font-family:Courier, monospace; font-size:12px; color:#64748b;"><?= htmlspecialchars($booking['razorpay_order_id'] ?: 'N/A') ?></span>
                    </div>
                    <div class="meta-row">
                        <span class="meta-label">Razorpay Payment ID:</span>
                        <span class="meta-value" style="font-family:Courier, monospace; font-size:12px; color:#64748b;"><?= htmlspecialchars($booking['razorpay_payment_id'] ?: 'N/A') ?></span>
                    </div>
                    <div class="meta-row" style="border-bottom:none;">
                        <span class="meta-label">Coupon Code Applied:</span>
                        <span class="meta-value"><span style="background:#ecfdf5; padding:2px 8px; border-radius:4px; border:1px solid #a7f3d0; font-family:monospace; color:#047857;"><?= htmlspecialchars($booking['coupon_code'] ?: 'None') ?></span></span>
                    </div>
                </div>
            </div>

            <div class="row mt-25 pt-20" style="border-top:1px solid #cbd5e1;">
                <div class="col-md-6 offset-md-6">
                    <div style="background: #fafaf9; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px 22px;">
                        <div class="meta-row">
                            <span class="meta-label">Room Rate (Subtotal):</span>
                            <span class="meta-value">₹<?= number_format($booking['base_amount'], 2) ?></span>
                        </div>
                        <?php if ($booking['discount_amount'] > 0): ?>
                            <div class="meta-row">
                                <span class="meta-label" style="color: #dc2626;">Coupon Discount:</span>
                                <span class="meta-value" style="color: #dc2626;">-₹<?= number_format($booking['discount_amount'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="meta-row">
                            <span class="meta-label">Net Subtotal:</span>
                            <span class="meta-value">₹<?= number_format($booking['subtotal'], 2) ?></span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">GST Tax (5%):</span>
                            <span class="meta-value">₹<?= number_format($booking['tax'], 2) ?></span>
                        </div>
                        <div class="meta-row" style="font-size: 15px; font-weight:800; border-top:1px dashed #cbd5e1; padding-top:12px; margin-top:5px; color:#16a34a;">
                            <span><?= strtolower($booking['payment_status']) === 'paid' ? 'Total Paid Amount:' : 'Total Payable Amount:' ?></span>
                            <span>₹<?= number_format($booking['total_amount'], 2) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($booking['special_request'])): ?>
            <div class="grid-info-col">
                <h3>Guest Special Preference Requests</h3>
                <div style="padding: 14px 20px; background: #fffdfb; border-left: 4px solid #9c6047; border-radius: 8px; font-size:13.5px; line-height:1.6; color:#475569; font-weight:550; border: 1px solid #fed7aa; border-left-width: 4px;">
                    <?= nl2br(htmlspecialchars($booking['special_request'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Right Column: Operations Panel & Actions -->
    <div class="col-lg-4">
        <!-- Guest Details Panel -->
        <div class="grid-info-col">
            <h3>Guest Information</h3>
            <div class="meta-row" style="flex-direction:column; gap:4px; align-items:flex-start;">
                <span class="meta-label">Customer Name:</span>
                <span class="meta-value" style="font-size:14.5px; color:#0f172a;"><?= htmlspecialchars($booking['customer_name']) ?></span>
            </div>
            <div class="meta-row" style="flex-direction:column; gap:4px; align-items:flex-start;">
                <span class="meta-label">Email Address:</span>
                <span class="meta-value" style="font-size:13.5px; color:#0f172a;"><?= htmlspecialchars($booking['customer_email']) ?></span>
            </div>
            <div class="meta-row" style="flex-direction:column; gap:4px; align-items:flex-start; border-bottom:none;">
                <span class="meta-label">Contact Phone:</span>
                <span class="meta-value" style="font-size:13.5px; color:#0f172a;"><?= htmlspecialchars($booking['customer_phone']) ?></span>
            </div>
        </div>

        <!-- PMS Operations Panel -->
        <?php if ($booking['booking_status'] !== 'cancelled'): ?>
            <div class="grid-info-col">
                <h3>Stay Operations</h3>

                <div class="d-flex flex-column gap-12">
                    <!-- Confirm Reservation -->
                    <?php if ($booking['booking_status'] === 'pending'): ?>
                        <form action="booking-details.php?id=<?= $booking_id ?>" method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="confirm_booking">
                            <button type="submit" class="btn btn-black text-white w-100 py-12" style="border-radius:8px; font-weight:700; font-size:14px; background:#0f172a;">Confirm Booking</button>
                        </form>
                    <?php endif; ?>

                    <!-- Check In Guest -->
                    <?php if ($booking['booking_status'] === 'confirmed'): ?>
                        <form action="booking-details.php?id=<?= $booking_id ?>" method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="check_in">
                            <button type="submit" class="btn btn-success w-100 py-12 text-white d-inline-flex align-items-center justify-content-center gap-2" style="border-radius:8px; font-weight:700; font-size:14px; background:#16a34a; border-color:#16a34a;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Check In Guest
                            </button>
                        </form>
                    <?php endif; ?>

                    <!-- Check Out Guest -->
                    <?php if ($booking['booking_status'] === 'checked_in'): ?>
                        <form action="booking-details.php?id=<?= $booking_id ?>" method="POST" style="margin:0;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="check_out">
                            <button type="submit" class="btn btn-primary w-100 py-12 text-white d-inline-flex align-items-center justify-content-center gap-2" style="border-radius:8px; font-weight:700; font-size:14px; background:#9c6047; border-color:#9c6047;">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Check Out Guest
                            </button>
                        </form>
                    <?php endif; ?>

                    <!-- Printable Invoice -->
                    <div class="mt-5">
                        <a href="../invoice.php?ref=<?= urlencode($booking['booking_id']) ?>&print=true" target="_blank" class="btn btn-outline-dark w-100 py-12 text-center d-inline-flex align-items-center justify-content-center gap-2" style="border-radius:8px; font-weight:700; font-size:13.5px; border-color:#cbd5e1; text-decoration:none; display:flex; color:#16A34A; height:46px;">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print Invoice Receipt
                        </a>
                    </div>

                    <!-- Cancel & Refund action -->
                    <?php if ($booking['booking_status'] !== 'checked_out'): ?>
                        <div class="mt-15 pt-15" style="border-top:1px solid #f1f5f9;">
                            <button class="btn btn-outline-danger w-100 py-10" onclick="showCancelForm()" style="border-radius:8px; font-weight:700; font-size:13px; border-color:#fecaca;">Cancel & Issue Refund</button>

                            <div id="cancelFormContainer" style="display:none;" class="mt-10 p-15" style="background:#fef2f2; border:1px solid #fee2e2; border-radius:8px;">
                                <form action="booking-details.php?id=<?= $booking_id ?>" method="POST" style="margin:0;">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="cancel_refund">
                                    <div class="form-group mb-10">
                                        <label class="form-label-custom" style="font-size:11px; font-weight:700;">Refund Gateway ID / Memo *</label>
                                        <input class="form-control-custom" type="text" name="refund_tx_id" required placeholder="e.g. RFND_123456" style="height:34px; font-size:12px; padding:4px 10px; border-radius:6px; border:1px solid #cbd5e1;">
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100 py-8 text-white" style="font-size:12px; font-weight:700; border-radius:6px; background:#dc2626; border-color:#dc2626;">Confirm Cancellation</button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function showCancelForm() {
        var container = document.getElementById('cancelFormContainer');
        if (container.style.display === 'none') {
            container.style.display = 'block';
            container.style.background = '#fef2f2';
            container.style.border = '1px solid #fee2e2';
            container.style.borderRadius = '8px';
        } else {
            container.style.display = 'none';
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>