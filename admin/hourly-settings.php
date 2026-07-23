<?php
// admin/hourly-settings.php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'pricing') $success_message = 'Hourly stay pricing updated successfully!';
    else if ($_GET['success'] === 'status') $success_message = 'Hourly enquiry status updated successfully!';
}
if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
    $success_message = 'Hourly enquiry record permanently deleted successfully!';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'status') $error_message = 'Invalid status selection.';
    else if ($_GET['error'] === 'db') $error_message = 'Failed to update settings in the database.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete hourly enquiry record.';
}

$active_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'enquiries';
if ($active_tab !== 'enquiries' && $active_tab !== 'pricing') {
    $active_tab = 'enquiries';
}

// 1. Handle Pricing Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_pricing') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: hourly-settings.php?tab=pricing&error=csrf");
        exit;
    }

    $rate_std = floatval($_POST['hourly_rate_standard']);
    $rate_exe = floatval($_POST['hourly_rate_executive']);
    $rate_prem = floatval($_POST['hourly_rate_premium']);

    try {
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, val_content) VALUES (?, ?) ON DUPLICATE KEY UPDATE val_content = VALUES(val_content)");
        $stmt->execute(['hourly_rate_standard', $rate_std]);
        $stmt->execute(['hourly_rate_executive', $rate_exe]);
        $stmt->execute(['hourly_rate_premium', $rate_prem]);

        header("Location: hourly-settings.php?tab=pricing&success=pricing");
        exit;
    } catch (Exception $e) {
        error_log("Failed to save hourly pricing settings: " . $e->getMessage());
        header("Location: hourly-settings.php?tab=pricing&error=db");
        exit;
    }
}

// 2. Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: hourly-settings.php?tab=enquiries&error=csrf");
        exit;
    }

    $enquiry_id = isset($_POST['enquiry_id']) ? intval($_POST['enquiry_id']) : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $allowed_statuses = ['pending', 'contacted', 'converted', 'rejected'];

    if (!in_array($status, $allowed_statuses)) {
        header("Location: hourly-settings.php?tab=enquiries&error=status");
        exit;
    }

    try {
        if ($status === 'contacted') {
            $followup_note = isset($_POST['followup_note']) ? trim($_POST['followup_note']) : '';
            $stmt = $pdo->prepare("UPDATE enquiries SET status = ?, followup_note = ? WHERE id = ?");
            $stmt->execute([$status, $followup_note, $enquiry_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
            $stmt->execute([$status, $enquiry_id]);
        }
        header("Location: hourly-settings.php?tab=enquiries&success=status");
        exit;
    } catch (Exception $e) {
        error_log("Failed to update hourly status: " . $e->getMessage());
        header("Location: hourly-settings.php?tab=enquiries&error=db");
        exit;
    }
}

// 3. Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_enquiry') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: hourly-settings.php?tab=enquiries&error=csrf");
        exit;
    }

    $enquiry_id = isset($_POST['enquiry_id']) ? intval($_POST['enquiry_id']) : 0;
    if ($enquiry_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM enquiries WHERE id = ?");
            $stmt->execute([$enquiry_id]);
            header("Location: hourly-settings.php?tab=enquiries&delete=success");
            exit;
        } catch (Exception $e) {
            error_log("Failed to delete hourly enquiry: " . $e->getMessage());
            header("Location: hourly-settings.php?tab=enquiries&error=delete");
            exit;
        }
    }
}

// Load hourly rates for pricing form
$hourly_rates = [
    'hourly_rate_standard' => 250.00,
    'hourly_rate_executive' => 350.00,
    'hourly_rate_premium' => 500.00
];
try {
    $stmt_h = $pdo->query("SELECT key_name, val_content FROM settings WHERE key_name IN ('hourly_rate_standard', 'hourly_rate_executive', 'hourly_rate_premium')");
    while ($row_h = $stmt_h->fetch()) {
        $hourly_rates[$row_h['key_name']] = floatval($row_h['val_content']);
    }
} catch (Exception $e) {
    // Fail silently
}

// Query enquiries log
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

$query = "SELECT * FROM enquiries WHERE category = 'hourly_booking'";
$params = [];

if (!empty($start_date)) {
    $query .= " AND date >= ?";
    $params[] = $start_date;
}
if (!empty($end_date)) {
    $query .= " AND date <= ?";
    $params[] = $end_date;
}
if (!empty($status_filter)) {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

$query .= " ORDER BY id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $enquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $enquiries = [];
    error_log("Failed to query hourly enquiries: " . $e->getMessage());
}
?>

<style>
    /* Styling tabs navigation */
    .lead-nav-tabs {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 25px 0;
        border-bottom: 2px solid #e2e8f0;
    }

    .lead-nav-tabs .nav-item {
        margin-bottom: -2px;
    }

    .lead-nav-tabs .nav-link {
        font-size: 15px;
        font-weight: 600;
        color: #64748b !important;
        padding: 12px 24px;
        position: relative;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        text-decoration: none;
        cursor: pointer;
    }

    .lead-nav-tabs .nav-link:hover {
        color: #0f172a !important;
    }

    .lead-nav-tabs .nav-link.active {
        color: #9c6047 !important;
    }

    .lead-nav-tabs .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #9c6047;
        border-radius: 3px 3px 0 0;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Hourly Stay Management</h1>
        <p class="text-sm text-neutral-500 mt-5">Configure short-term room stay prices and manage guest call-back callback requests.</p>
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

<ul class="lead-nav-tabs">
    <li class="nav-item">
        <a class="nav-link <?= ($active_tab === 'enquiries') ? 'active' : '' ?>" href="hourly-settings.php?tab=enquiries">
            📋 Hourly Enquiries Log
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($active_tab === 'pricing') ? 'active' : '' ?>" href="hourly-settings.php?tab=pricing">
            ⚙️ Hourly Stay Rates Settings
        </a>
    </li>
</ul>

<?php if ($active_tab === 'enquiries'): ?>
    <!-- 1. Enquiries Log Tab -->
    <div class="panel-card">
        <div class="d-flex justify-content-between align-items-center mb-25">
            <h3 class="font-heading mb-0" style="font-size:18px;">Hourly Request Logs</h3>
            <span class="text-sm text-neutral-400">Showing <?= count($enquiries) ?> records</span>
        </div>

        <!-- Filter Bar -->
        <form method="GET" class="row g-3 align-items-end mb-30" style="background-color: #fafaf9; padding: 18px; border-radius: 12px; border: 1px solid #f1f1f0; margin: 0 0 25px 0;">
            <input type="hidden" name="tab" value="enquiries">
            
            <div class="col-6 col-md-3">
                <label class="form-label mb-5" style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">From Date</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>" style="font-size: 13px; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #ffffff; color: #334155; font-weight: 550;">
            </div>
            
            <div class="col-6 col-md-3">
                <label class="form-label mb-5" style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">To Date</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>" style="font-size: 13px; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #ffffff; color: #334155; font-weight: 550;">
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label mb-5" style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">Lead Status</label>
                <select name="status" class="form-select" style="font-size: 13px; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #ffffff; color: #334155; font-weight: 550;">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($status_filter === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="contacted" <?= ($status_filter === 'contacted') ? 'selected' : '' ?>>Follow Back</option>
                    <option value="converted" <?= ($status_filter === 'converted') ? 'selected' : '' ?>>Converted</option>
                    <option value="rejected" <?= ($status_filter === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100" style="height: 38px; padding: 0 12px; border-radius: 8px; font-weight:700; font-size:13px; background-color:#9c6047; border:none; transition: all 0.2s ease; white-space:nowrap; color: white;" onmouseover="this.style.backgroundColor='#824c36';" onmouseout="this.style.backgroundColor='#9c6047';">
                    🔍 Filter
                </button>
                <?php if ($start_date !== '' || $end_date !== '' || $status_filter !== ''): ?>
                    <a href="hourly-settings.php?tab=enquiries" class="btn btn-light border d-inline-flex align-items-center justify-content-center w-100" style="height: 38px; padding: 0 10px; border-radius: 8px; font-weight:700; font-size:13px; border-color:#cbd5e1; color:#475569; background-color:#ffffff; text-decoration:none; white-space:nowrap;">
                        Reset
                    </a>
                <?php endif; ?>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Date Received</th>
                        <th>Customer Details</th>
                        <th>Stay Details</th>
                        <th>pricing Breakdown</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($enquiries) > 0): ?>
                        <?php foreach ($enquiries as $e): 
                            // Parse message details
                            $lines = explode("\n", $e['requirements']);
                            $room_type = 'Standard Room';
                            $checkin_time = 'N/A';
                            $total_price = 'N/A';
                            $rate_per_hour = 'N/A';
                            
                            foreach ($lines as $line) {
                                if (strpos($line, 'Room Category:') !== false) $room_type = trim(str_replace('Room Category:', '', $line));
                                if (strpos($line, 'Check-in Time:') !== false) $checkin_time = trim(str_replace('Check-in Time:', '', $line));
                                if (strpos($line, 'Hourly Rate:') !== false) $rate_per_hour = trim(str_replace('Hourly Rate:', '', $line));
                                if (strpos($line, 'Total Calculated Price:') !== false) $total_price = trim(str_replace('Total Calculated Price:', '', $line));
                            }
                        ?>
                            <tr>
                                <td>
                                    <strong><?= date('d-M-Y', strtotime($e['created_at'])) ?></strong><br>
                                    <span class="text-muted" style="font-size:11.5px;"><?= date('h:i A', strtotime($e['created_at'])) ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 14.5px; font-weight:600; color:#0f172a;"><?= htmlspecialchars($e['name']) ?></span><br>
                                    <span style="font-size:13px; color:#475569;"><a href="tel:<?= htmlspecialchars($e['phone']) ?>">📞 <?= htmlspecialchars($e['phone']) ?></a></span>
                                </td>
                                <td>
                                    <strong><?= date('d-M-Y', strtotime($e['date'])) ?></strong> @ <span class="badge bg-light text-dark" style="font-size:12px; font-weight:700; border: 1px solid #cbd5e1;"><?= htmlspecialchars($checkin_time) ?></span><br>
                                    <span class="text-muted" style="font-size:12.5px;">Duration: <strong><?= htmlspecialchars($e['guests']) ?> hours</strong></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-white" style="font-size: 11px;"><?= htmlspecialchars($room_type) ?></span><br>
                                    <span class="text-muted" style="font-size:12.5px;">Rate: <?= htmlspecialchars($rate_per_hour) ?></span><br>
                                    <span style="font-size:14px; font-weight:700; color:#a17a42;">Total: <?= htmlspecialchars($total_price) ?></span>
                                </td>
                                <td>
                                    <span class="status-badge <?= htmlspecialchars($e['status']) ?>"><?= htmlspecialchars($e['status'] === 'contacted' ? 'Follow Back' : $e['status']) ?></span>
                                    <?php if ($e['status'] === 'contacted' && !empty($e['followup_note'])): ?>
                                        <div style="font-size: 11px; max-width: 200px; word-wrap: break-word; color:#475569; margin-top:5px; border-left: 2px solid #b45309; padding-left: 6px; font-style: italic;">
                                            <?= htmlspecialchars($e['followup_note']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <div class="d-inline-flex flex-column gap-2" style="width: 140px;">
                                        <!-- Change Status Action Dropdown Form -->
                                        <form action="hourly-settings.php" method="POST" style="margin:0;">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
                                            <input type="hidden" name="followup_note" id="note-input-<?= $e['id'] ?>" value="">

                                            <select class="form-select" name="status" onchange="handleStatusChange(this, <?= $e['id'] ?>, '<?= htmlspecialchars($e['status']) ?>')" style="font-size: 12px; font-weight: 700; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; height: 34px; background-color: #ffffff; width: 100%; color: #334155; cursor: pointer;">
                                                <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="contacted" <?= $e['status'] === 'contacted' ? 'selected' : '' ?>>Follow Back</option>
                                                <option value="converted" <?= $e['status'] === 'converted' ? 'selected' : '' ?>>Converted</option>
                                                <option value="rejected" <?= $e['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                        </form>

                                        <!-- Delete Enquiry Form -->
                                        <form action="hourly-settings.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this enquiry?');" style="margin: 0; display: inline;">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="delete_enquiry">
                                            <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-light border text-danger text-center w-100" style="padding: 4px 0; font-size: 12px; font-weight:600; border-radius:6px; height:32px; border-color:#cbd5e1; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#ef4444'; this.style.color='#ffffff';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ef4444';">
                                                Delete Record
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-40 text-neutral-500">No hourly stay enquiries found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Follow Up Note Modal -->
    <div class="modal fade" id="followUpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                <div class="modal-header bg-dark text-white py-15 px-20">
                    <h5 class="modal-title font-heading" style="font-weight:700; font-size:17px;">Add Follow Back Note</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1) grayscale(1) brightness(2);"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="form-group mb-0">
                        <label class="form-label-custom">Enter a small follow-up note (Optional)</label>
                        <textarea id="followUpNoteInput" class="form-control-custom" rows="3" placeholder="e.g., Talked to customer. Agreed on check-in window..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-12 px-24 border-top-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600; padding: 8px 16px;">Cancel</button>
                    <button type="button" class="btn btn-primary text-white" onclick="submitFollowUpStatus()" style="border-radius: 8px; font-weight: 600; padding: 8px 16px; background-color:#9c6047; border:none;">Submit Status</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let activeEnquiryId = null;
        let activeSelectElement = null;
        let previousStatusVal = null;

        function handleStatusChange(selectElement, enquiryId, currentStatus) {
            if (selectElement.value === 'contacted') {
                activeEnquiryId = enquiryId;
                activeSelectElement = selectElement;
                previousStatusVal = currentStatus;

                // Clear previous note
                document.getElementById('followUpNoteInput').value = '';

                // Open the modal
                var modalEl = document.getElementById('followUpModal');
                var myModal = bootstrap.Modal.getOrCreateInstance(modalEl);
                myModal.show();
            } else {
                selectElement.form.submit();
            }
        }

        function submitFollowUpStatus() {
            if (activeEnquiryId && activeSelectElement) {
                const note = document.getElementById('followUpNoteInput').value;
                document.getElementById('note-input-' + activeEnquiryId).value = note;
                activeSelectElement.form.submit();
            }
        }

        // When the modal is dismissed (via cancel button or clicking outside/close)
        document.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('followUpModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function() {
                    // If the form wasn't submitted, revert the dropdown
                    if (activeSelectElement && activeSelectElement.value === 'contacted') {
                        activeSelectElement.value = previousStatusVal;
                    }
                });
            }
        });
    </script>

<?php else: ?>
    <!-- 2. Pricing Settings Tab -->
    <div class="panel-card">
        <h3 class="font-heading mb-20" style="font-size:18px;">Configure Hourly Pricing Rates</h3>
        
        <form method="POST" action="hourly-settings.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="update_pricing">

            <div class="row g-3 mb-25">
                <div class="col-md-4">
                    <label class="form-label-custom">Standard Room Hourly Rate (₹ / hour) *</label>
                    <input type="number" name="hourly_rate_standard" class="form-control-custom" value="<?= htmlspecialchars($hourly_rates['hourly_rate_standard']) ?>" min="0.01" step="0.01" required>
                    <span style="font-size: 11.5px; color: #64748b;">Current 4-hour standard stay cost: <strong>₹<?= number_format($hourly_rates['hourly_rate_standard'] * 4) ?></strong></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Executive Room Hourly Rate (₹ / hour) *</label>
                    <input type="number" name="hourly_rate_executive" class="form-control-custom" value="<?= htmlspecialchars($hourly_rates['hourly_rate_executive']) ?>" min="0.01" step="0.01" required>
                    <span style="font-size: 11.5px; color: #64748b;">Current 4-hour executive stay cost: <strong>₹<?= number_format($hourly_rates['hourly_rate_executive'] * 4) ?></strong></span>
                </div>
                <div class="col-md-4">
                    <label class="form-label-custom">Premium Room Hourly Rate (₹ / hour) *</label>
                    <input type="number" name="hourly_rate_premium" class="form-control-custom" value="<?= htmlspecialchars($hourly_rates['hourly_rate_premium']) ?>" min="0.01" step="0.01" required>
                    <span style="font-size: 11.5px; color: #64748b;">Current 4-hour premium stay cost: <strong>₹<?= number_format($hourly_rates['hourly_rate_premium'] * 4) ?></strong></span>
                </div>
            </div>

            <div class="border-top pt-20">
                <button type="submit" class="btn btn-primary" style="background-color:#9c6047; border-color:#9c6047; padding: 10px 24px; font-weight:700; font-size:14px; border-radius:8px; color: white;">
                    Save Hourly Rates
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
