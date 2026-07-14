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
                $stmt = $pdo->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
                $stmt->execute([$status, $enquiry_id]);
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

// Fetch enquiries from DB
$enquiries = [];
try {
    $enquiries = $pdo->query("SELECT * FROM enquiries ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {
    error_log("Enquiries loading failure: " . $e->getMessage());
}

// Count dynamic lead numbers for each tab
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
            <a class="nav-link <?= ($active_type === $key) ? 'active' : '' ?>" href="enquiries.php?type=<?= $key ?>">
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
                                </div>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <?php
                                $status_bg = '#f59e0b';
                                if (strtolower($e['status']) === 'converted') $status_bg = '#10b981';
                                else if (strtolower($e['status']) === 'rejected') $status_bg = '#ef4444';
                                else if (strtolower($e['status']) === 'contacted') $status_bg = '#3b82f6';
                                ?>
                                <span class="badge" style="background-color: <?= $status_bg ?>; color: #ffffff; font-size: 11px; padding: 5px 10px; border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <?= htmlspecialchars($e['status']) ?>
                                </span>
                            </td>
                            <td style="vertical-align: middle; padding: 16px 12px;">
                                <div class="d-flex flex-column gap-2" style="width: 130px;">
                                    <!-- Status Update Form -->
                                    <form action="enquiries.php" method="POST" style="margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="enquiry_id" value="<?= $e['id'] ?>">
                                        <input type="hidden" name="active_type" value="<?= htmlspecialchars($active_type) ?>">
                                        
                                        <select class="form-select" name="status" onchange="this.form.submit()" style="font-size: 12px; font-weight: 700; padding: 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; height: 34px; background-color: #ffffff; width: 100%; color: #334155; cursor: pointer;">
                                            <option value="pending" <?= $e['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="contacted" <?= $e['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
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

<?php require_once __DIR__ . '/includes/footer.php'; ?>
