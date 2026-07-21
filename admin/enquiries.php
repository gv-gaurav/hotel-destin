<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = 'Inquiry status updated successfully!';
}
if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
    $success_message = 'Enquiry record permanently deleted successfully!';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'status') $error_message = 'Invalid status selection.';
    else if ($_GET['error'] === 'db') $error_message = 'Failed to update inquiry status.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete enquiry record.';
}

// Define lead categories mapping
$lead_types = [
    'all' => ['title' => 'All Leads', 'icon' => '📋'],
    'contact' => ['title' => 'Contact Us', 'icon' => '📧'],
    'restaurant' => ['title' => 'Restaurant', 'icon' => '🍽️'],
    'banquet' => ['title' => 'Banquet & Events', 'icon' => '🎉'],
    'corporate' => ['title' => 'Corporate', 'icon' => '💼'],
    'airport_transfer' => ['title' => 'Airport Transfer', 'icon' => '🚗'],
    'long_stay' => ['title' => 'Long Stay', 'icon' => '🏨']
];

// Handle Delete Enquiry action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_enquiry') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';
    if (!verify_csrf_token($csrf_token)) {
        header("Location: enquiries.php?error=csrf");
        exit;
    }

    $enquiry_db_id = isset($_POST['enquiry_id']) ? intval($_POST['enquiry_id']) : 0;
    $active_type = isset($_POST['active_type']) ? strtolower(trim($_POST['active_type'])) : 'all';
    if (!isset($lead_types[$active_type])) $active_type = 'all';

    if ($enquiry_db_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM enquiries WHERE id = ?");
            $stmt->execute([$enquiry_db_id]);
            header("Location: enquiries.php?type=" . urlencode($active_type) . "&delete=success");
            exit;
        } catch (Exception $e) {
            error_log("Failed to delete enquiry: " . $e->getMessage());
            header("Location: enquiries.php?type=" . urlencode($active_type) . "&error=delete");
            exit;
        }
    }
}

// Handle inquiry status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: enquiries.php?error=csrf");
        exit;
    } else {
        $enquiry_id = isset($_POST['enquiry_id']) ? intval($_POST['enquiry_id']) : 0;
        $status = isset($_POST['status']) ? trim($_POST['status']) : '';
        $active_type = isset($_POST['active_type']) ? strtolower(trim($_POST['active_type'])) : 'all';
        if (!isset($lead_types[$active_type])) $active_type = 'all';

        $allowed_statuses = ['pending', 'contacted', 'converted', 'rejected'];
        if (!in_array($status, $allowed_statuses)) {
            header("Location: enquiries.php?type=" . urlencode($active_type) . "&error=status");
            exit;
        } else {
            try {
                if ($status === 'contacted') {
                    $followup_note = isset($_POST['followup_note']) ? trim($_POST['followup_note']) : '';
                    $stmt = $pdo->prepare("UPDATE enquiries SET status = ?, followup_note = ? WHERE id = ?");
                    $stmt->execute([$status, $followup_note, $enquiry_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
                    $stmt->execute([$status, $enquiry_id]);
                }
                header("Location: enquiries.php?type=" . urlencode($active_type) . "&success=1");
                exit;
            } catch (Exception $e) {
                error_log("Enquiry status update failure: " . $e->getMessage());
                header("Location: enquiries.php?type=" . urlencode($active_type) . "&error=db");
                exit;
            }
        }
    }
}

// Fetch enquiries from DB with date range filters
$start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

$enquiries = [];
try {
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

    if (count($conditions) > 0) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $query .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $enquiries = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Enquiries loading failure: " . $e->getMessage());
}

// Count dynamic lead numbers for each tab based on the filtered set
$counts = [];
foreach ($lead_types as $key => $val) {
    $counts[$key] = 0;
}
$counts['all'] = count($enquiries);
foreach ($enquiries as $e) {
    $cat = strtolower($e['category']);
    // Map wedding directly to banquet
    if ($cat === 'wedding') {
        $cat = 'banquet';
    }
    if (isset($counts[$cat])) {
        $counts[$cat]++;
    }
}

// Filtering leads for display
$active_type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : 'all';
if (!isset($lead_types[$active_type])) {
    $active_type = 'all';
}

$filtered_enquiries = [];
if ($active_type === 'all') {
    $filtered_enquiries = $enquiries;
} else {
    foreach ($enquiries as $e) {
        $cat = strtolower($e['category']);
        // Map wedding directly to banquet for filtering
        if ($cat === 'wedding') {
            $cat = 'banquet';
        }
        if ($cat === $active_type) {
            $filtered_enquiries[] = $e;
        }
    }
}
?>

<style>
.lead-nav-tabs {
    border-bottom: 2px solid #f1f5f9;
    gap: 4px;
    margin-bottom: 30px;
    display: flex;
    flex-wrap: wrap;
    padding-left: 0;
    list-style: none;
    max-width: fit-content; /* Reduced size, don't take full width */
}
.lead-nav-tabs .nav-item {
    margin-bottom: -2px;
}
.lead-nav-tabs .nav-link {
    border: none !important;
    background: transparent !important;
    color: #64748b !important;
    font-weight: 600;
    font-size: 13px; /* Reduced font size */
    padding: 10px 14px !important; /* Reduced padding size */
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
.lead-nav-tabs .badge {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 50px;
    margin-left: 8px;
    background-color: #f1f5f9;
    color: #475569;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}
.lead-nav-tabs .nav-link.active .badge {
    background-color: #9c6047;
    color: #ffffff;
    border-color: #9c6047;
}
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Enquiries</h1>
        <p class="text-sm text-neutral-500 mt-5">Track customer messages, contact requests, corporate reservation callbacks, and support leads.</p>
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

<!-- Lead Tabs Filters -->
<ul class="lead-nav-tabs">
    <?php foreach ($lead_types as $key => $type): ?>
        <li class="nav-item">
            <a class="nav-link <?= ($active_type === $key) ? 'active' : '' ?>" href="enquiries.php?type=<?= $key ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>">
                <span class="me-1"><?= $type['icon'] ?></span> <?= htmlspecialchars($type['title']) ?>
                <span class="badge"><?= $counts[$key] ?></span>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<div class="panel-card">
    <div class="d-flex justify-content-between align-items-center mb-25">
        <h3 class="font-heading mb-0" style="font-size:18px;">
            <?= htmlspecialchars($lead_types[$active_type]['title']) ?> Log
        </h3>
        <span class="text-sm text-neutral-400">Showing <?= count($filtered_enquiries) ?> records</span>
    </div>

    <!-- Search & Filter Bar -->
    <form method="GET" class="row g-3 align-items-end mb-30" style="background-color: #fafaf9; padding: 18px; border-radius: 12px; border: 1px solid #f1f1f0; margin: 0 0 25px 0;">
        <input type="hidden" name="type" value="<?= htmlspecialchars($active_type) ?>">
        <div class="col-6 col-md-3">
            <label class="form-label mb-5" style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">From Date</label>
            <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date) ?>" style="font-size: 13px; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #ffffff; color: #334155; font-weight: 550;">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label mb-5" style="font-size:12px; font-weight:700; color:#475569; text-transform:uppercase; letter-spacing:0.5px;">To Date</label>
            <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date) ?>" style="font-size: 13px; height: 38px; border-radius: 8px; border: 1px solid #cbd5e1; background-color: #ffffff; color: #334155; font-weight: 550;">
        </div>
        <div class="col-12 col-md-6 d-flex gap-2">
            <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0 20px; border-radius: 8px; font-weight:700; font-size:13px; background-color:#9c6047; border:none; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#824c36';" onmouseout="this.style.backgroundColor='#9c6047';">
                🔍 Filter
            </button>
            <?php if ($start_date !== '' || $end_date !== ''): ?>
                <a href="enquiries.php?type=<?= htmlspecialchars($active_type) ?>" class="btn btn-light border d-inline-flex align-items-center justify-content-center" style="height: 38px; padding: 0 15px; border-radius: 8px; font-weight:700; font-size:13px; border-color:#cbd5e1; color:#475569; background-color:#ffffff; text-decoration:none;">
                    Reset
                </a>
            <?php endif; ?>
            
            <a href="export-enquiries.php?type=<?= urlencode($active_type) ?>&start_date=<?= urlencode($start_date) ?>&end_date=<?= urlencode($end_date) ?>" class="btn btn-success ms-auto d-inline-flex align-items-center gap-2" style="height: 38px; padding: 0 16px; border-radius: 8px; font-weight:700; font-size:13px; background-color:#16a34a; border:none; color:#ffffff; text-decoration:none; transition: all 0.2s ease;" onmouseover="this.style.backgroundColor='#15803d';" onmouseout="this.style.backgroundColor='#16a34a';">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export to Excel
            </a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Date Received</th>
                    <th>Lead Type</th>
                    <th>Customer Contact Info</th>
                    <th>Message Details</th>
                    <th>Status</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($filtered_enquiries) > 0): ?>
                    <?php foreach ($filtered_enquiries as $e): ?>
                        <?php
                        $cat_slug = strtolower($e['category']);
                        // Map wedding directly to banquet for category badge in table list
                        if ($cat_slug === 'wedding') {
                            $cat_slug = 'banquet';
                        }
                        $display_cat = isset($lead_types[$cat_slug]) ? $lead_types[$cat_slug]['title'] : ucfirst($e['category']);
                        ?>
                        <tr>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <span style="font-size:13.5px; font-weight:600; color:#334155;"><?= date('d M Y', strtotime($e['created_at'])) ?></span><br>
                                <span style="font-size:11.5px; color:#64748b; font-weight:550;"><?= date('H:i A', strtotime($e['created_at'])) ?></span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <span class="badge bg-light text-dark border" style="font-size:11px; padding:5px 8px; text-transform: uppercase; font-weight:700;">
                                    <?= htmlspecialchars($display_cat) ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <strong style="color:#334155; font-size:14px;"><?= htmlspecialchars($e['name']) ?></strong><br>
                                <span style="font-size:12.5px; color:#64748b;"><?= htmlspecialchars($e['email']) ?></span><br>
                                <span style="font-size:12px; color:#64748b; font-weight:550;"><?= htmlspecialchars($e['phone']) ?></span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <div style="font-size:13px; max-width: 320px; word-break: break-word; color:#475569; line-height:1.5; font-weight:550;">
                                    <?php if (!empty($e['date']) && $e['date'] !== '0000-00-00'): ?>
                                        <div style="margin-bottom: 4px;">
                                            <span style="color:#9c6047; font-weight:700;">📅 Booking Date:</span> 
                                            <strong style="color:#0f172a;"><?= date('d M Y', strtotime($e['date'])) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($e['guests'])): ?>
                                        <div style="margin-bottom: 6px;">
                                            <span style="color:#9c6047; font-weight:700;">👥 Guests:</span> 
                                            <strong style="color:#0f172a;"><?= htmlspecialchars($e['guests']) ?> Person(s)</strong>
                                        </div>
                                    <?php endif; ?>
                                    <div style="color:#64748b; font-size:12.5px; border-top: 1px solid #f1f5f9; padding-top:6px; margin-top:4px;">
                                        <?= nl2br(htmlspecialchars($e['requirements'])) ?>
                                    </div>
                                    <?php if (!empty($e['followup_note'])): ?>
                                        <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e1; color:#9c6047; font-size:12.5px;">
                                            <span style="font-weight:700;">📝 Follow Back Note:</span><br>
                                            <span style="font-style: italic; color: #475569;"><?= nl2br(htmlspecialchars($e['followup_note'])) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <?php
                                $status_bg = '#f59e0b';
                                $display_status = $e['status'];
                                if (strtolower($e['status']) === 'converted') {
                                    $status_bg = '#10b981';
                                } else if (strtolower($e['status']) === 'rejected') {
                                    $status_bg = '#ef4444';
                                } else if (strtolower($e['status']) === 'contacted') {
                                    $status_bg = '#3b82f6';
                                    $display_status = 'Follow Back';
                                }
                                ?>
                                <span class="badge" style="background-color: <?= $status_bg ?>; color: #ffffff; font-size: 11px; padding: 5px 10px; border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($display_status) ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <div class="d-flex flex-column gap-2" style="width: 130px;">
                                    <!-- Status Update Form -->
                                    <form action="enquiries.php" method="POST" style="margin:0;" id="status-form-<?= $e['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
                                        <input type="hidden" name="active_type" value="<?= htmlspecialchars($active_type) ?>">
                                        <input type="hidden" name="followup_note" id="note-input-<?= $e['id'] ?>" value="">
                                        
                                        <select class="form-select" name="status" onchange="handleStatusChange(this, <?= $e['id'] ?>, '<?= htmlspecialchars($e['status']) ?>')" style="font-size: 12px; font-weight: 700; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; height: 34px; background-color: #ffffff; width: 100%; color: #334155; cursor: pointer;">
                                            <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="contacted" <?= $e['status'] === 'contacted' ? 'selected' : '' ?>>Follow Back</option>
                                            <option value="converted" <?= $e['status'] === 'converted' ? 'selected' : '' ?>>Converted</option>
                                            <option value="rejected" <?= $e['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                        </select>
                                    </form>

                                    <!-- Delete Enquiry Form -->
                                    <form action="enquiries.php" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this enquiry?');" style="margin: 0; display: inline;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="delete_enquiry">
                                        <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
                                        <input type="hidden" name="active_type" value="<?= htmlspecialchars($active_type) ?>">
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
                        <td colspan="6" class="text-center py-40 text-neutral-500">No leads found in this category.</td>
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
                    <textarea id="followUpNoteInput" class="form-control-custom" rows="3" placeholder="e.g., Talked to customer. Customized catering package details..."></textarea>
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
        modalEl.addEventListener('hidden.bs.modal', function () {
            // If the form wasn't submitted, revert the dropdown
            if (activeSelectElement && activeSelectElement.value === 'contacted') {
                activeSelectElement.value = previousStatusVal;
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
