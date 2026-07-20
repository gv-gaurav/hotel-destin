<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_msg = '';
$error_msg = '';

// Handle POST actions (Create, Edit, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = trim($_POST['action']);
        
        if ($action === 'add' || $action === 'edit') {
            $room_category_id = intval($_POST['room_category_id']);
            $start_date = trim($_POST['start_date']);
            $end_date = trim($_POST['end_date']);
            $ep_price = floatval($_POST['ep_price']);
            $cp_price = floatval($_POST['cp_price']);
            $map_price = floatval($_POST['map_price']);
            $reason = htmlspecialchars(trim($_POST['reason']));
            
            // Basic validations
            if ($room_category_id <= 0) {
                $error_msg = 'Please select a valid Room Category.';
            } elseif (empty($start_date) || empty($end_date)) {
                $error_msg = 'Please select both Start Date and End Date.';
            } elseif ($end_date < $start_date) {
                $error_msg = 'End Date cannot be earlier than the Start Date.';
            } elseif ($ep_price <= 0 || $cp_price <= 0 || $map_price <= 0) {
                $error_msg = 'All plan prices (EP, CP, MAP) must be greater than zero.';
            } else {
                try {
                    if ($action === 'add') {
                        $stmt = $pdo->prepare("
                            INSERT INTO room_rate_calendars (room_category_id, start_date, end_date, ep_price, cp_price, map_price, reason)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([$room_category_id, $start_date, $end_date, $ep_price, $cp_price, $map_price, $reason]);
                        $success_msg = 'Seasonal pricing rule added successfully!';
                    } else {
                        $id = intval($_POST['rule_id']);
                        $stmt = $pdo->prepare("
                            UPDATE room_rate_calendars 
                            SET room_category_id = ?, start_date = ?, end_date = ?, ep_price = ?, cp_price = ?, map_price = ?, reason = ?
                            WHERE id = ?
                        ");
                        $stmt->execute([$room_category_id, $start_date, $end_date, $ep_price, $cp_price, $map_price, $reason, $id]);
                        $success_msg = 'Seasonal pricing rule updated successfully!';
                    }
                } catch (Exception $e) {
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['rule_id']);
            if ($id > 0) {
                try {
                    $stmt = $pdo->prepare("DELETE FROM room_rate_calendars WHERE id = ?");
                    $stmt->execute([$id]);
                    $success_msg = 'Seasonal pricing rule deleted successfully!';
                } catch (Exception $e) {
                    $error_msg = 'Database error: ' . $e->getMessage();
                }
            }
        }
    }
}

// Fetch all room categories
$categories = [];
try {
    $categories = $pdo->query("SELECT id, title FROM rooms ORDER BY id ASC")->fetchAll();
} catch (Exception $e) {
    error_log("Failed to load room categories: " . $e->getMessage());
}

// Setup month/year navigation for the calendar view
$month = isset($_GET['month']) ? intval($_GET['month']) : intval(date('m'));
$year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
$selected_category_id = isset($_GET['cal_category_id']) ? intval($_GET['cal_category_id']) : (count($categories) > 0 ? $categories[0]['id'] : 0);

if ($month < 1 || $month > 12) $month = intval(date('m'));
if ($year < 2000 || $year > 2100) $year = intval(date('Y'));

// Calendar math
$days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$first_day_timestamp = mktime(0, 0, 0, $month, 1, $year);
$month_name = date('F', $first_day_timestamp);
$start_month_date = sprintf("%04d-%02d-01", $year, $month);
$end_month_date = sprintf("%04d-%02d-%02d", $year, $month, $days_in_month);

// Build active rules list for calendar matching
$calendar_rules = [];
if ($selected_category_id > 0) {
    try {
        // Fetch rules that overlap with the selected month
        $stmt = $pdo->prepare("
            SELECT * FROM room_rate_calendars
            WHERE room_category_id = ?
              AND start_date <= ?
              AND end_date >= ?
            ORDER BY id ASC
        ");
        $stmt->execute([$selected_category_id, $end_month_date, $start_month_date]);
        $calendar_rules = $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Failed to fetch calendar rules: " . $e->getMessage());
    }
}

// Map seasonal rules day-by-day (later rules overwrite earlier ones because of ORDER BY id ASC)
$day_overrides = [];
for ($day = 1; $day <= $days_in_month; $day++) {
    $curr_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
    foreach ($calendar_rules as $rule) {
        if ($rule['start_date'] <= $curr_date && $rule['end_date'] >= $curr_date) {
            $day_overrides[$day] = $rule;
        }
    }
}

// Filters for the rules list table
$filter_category = isset($_GET['filter_category']) ? intval($_GET['filter_category']) : 0;
$filter_date = isset($_GET['filter_date']) ? trim($_GET['filter_date']) : '';

$list_query = "
    SELECT rrc.*, r.title as category_title 
    FROM room_rate_calendars rrc 
    JOIN rooms r ON rrc.room_category_id = r.id
    WHERE 1=1
";
$list_params = [];

if ($filter_category > 0) {
    $list_query .= " AND rrc.room_category_id = ? ";
    $list_params[] = $filter_category;
}

if (!empty($filter_date)) {
    $list_query .= " AND rrc.start_date <= ? AND rrc.end_date >= ? ";
    $list_params[] = $filter_date;
    $list_params[] = $filter_date;
}

$list_query .= " ORDER BY rrc.start_date DESC, rrc.id DESC ";

$rules_list = [];
try {
    $stmt = $pdo->prepare($list_query);
    $stmt->execute($list_params);
    $rules_list = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Failed to fetch rules list: " . $e->getMessage());
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
    .panel-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
    }
    
    .nav-tabs-custom {
        display: flex;
        border-bottom: 2px solid #e2e8f0;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .nav-tab-link {
        padding: 10px 5px;
        font-weight: 700;
        font-size: 15px;
        color: #64748b;
        text-decoration: none;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .nav-tab-link:hover, .nav-tab-link.active {
        color: #9c6047;
        border-bottom-color: #9c6047;
    }
    
    /* Calendar styles */
    .calendar-days-header {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        background: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        text-align: center;
        font-weight: 700;
        font-size: 13px;
        color: #475569;
        padding: 10px 0;
    }
    
    .calendar-days-body {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        border-left: 1px solid #e2e8f0;
        border-top: 1px solid #e2e8f0;
    }
    
    .calendar-day-cell {
        border-right: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        min-height: 100px;
        padding: 8px;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    
    .calendar-day-cell:hover {
        background: #fafaf9;
    }
    
    .calendar-day-cell.blank {
        background: #f8fafc;
        cursor: default;
    }
    
    .calendar-day-cell.has-override {
        background-color: #fffbeb;
        border-left: 3px solid #d97706;
    }
    
    .calendar-day-cell.has-override:hover {
        background-color: #fef3c7;
    }
    
    .day-number {
        font-size: 14px;
        font-weight: 700;
        color: #475569;
        text-align: right;
    }
    
    .calendar-day-cell.has-override .day-number {
        color: #b45309;
    }
    
    .override-prices {
        font-size: 10px;
        font-weight: 600;
        line-height: 1.3;
        display: flex;
        flex-direction: column;
        gap: 2px;
        margin-top: 5px;
    }
    
    .price-badge {
        display: inline-block;
        padding: 1px 4px;
        border-radius: 3px;
    }
    
    .badge-ep { background: #f1f5f9; color: #475569; }
    .badge-cp { background: #dbeafe; color: #1e40af; }
    .badge-map { background: #dcfce7; color: #166534; }
    
    .reason-text {
        font-size: 9.5px;
        font-weight: 700;
        color: #d97706;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
        margin-top: 4px;
        background: rgba(217, 119, 6, 0.08);
        padding: 2px 4px;
        border-radius: 3px;
        text-align: center;
    }
    
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
        background: #f8fafc;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Seasonal Pricing Rate Calendar</h1>
        <p class="text-sm text-neutral-500 mt-5">Define date-wise price rules for room plans. Overlapping rules default to the newest rule.</p>
    </div>
    <button class="btn btn-default" style="background:#9c6047; color:#fff;" onclick="openAddModal()">
        <span>+ Add Pricing Rule</span>
    </button>
</div>

<?php if (!empty($success_msg)): ?>
    <div class="alert alert-success mb-20" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        <?= $success_msg ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger mb-20" style="border-radius: 8px; font-size:14px; padding: 12px 20px;">
        <?= $error_msg ?>
    </div>
<?php endif; ?>

<div class="panel-card">
    <div class="nav-tabs-custom">
        <div id="tab-calendar" class="nav-tab-link active" onclick="switchTab('calendar')">📅 Rate Calendar View</div>
        <div id="tab-rules" class="nav-tab-link" onclick="switchTab('rules')">📋 Manage Pricing Rules</div>
    </div>
    
    <!-- TAB 1: CALENDAR VIEW -->
    <div id="content-calendar" class="tab-content-pane">
        <form method="GET" action="rate-calendar.php" id="calFilterForm">
            <input type="hidden" name="month" value="<?= $month ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            
            <div class="filter-bar">
                <div style="flex-grow: 1; min-width: 250px;">
                    <label class="form-label-custom">Select Category (Calendar View)</label>
                    <select class="form-control-custom" name="cal_category_id" onchange="document.getElementById('calFilterForm').submit()">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $selected_category_id === $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="d-flex align-items-center gap-10">
                    <a class="btn btn-outline-dark" style="height:42px; line-height:28px;" href="rate-calendar.php?cal_category_id=<?= $selected_category_id ?>&month=<?= $prev_month ?>&year=<?= $prev_year ?>">← Prev</a>
                    <span style="font-size: 16px; font-weight:700; color:#0f172a; min-width:140px; text-align:center;"><?= $month_name ?> <?= $year ?></span>
                    <a class="btn btn-outline-dark" style="height:42px; line-height:28px;" href="rate-calendar.php?cal_category_id=<?= $selected_category_id ?>&month=<?= $next_month ?>&year=<?= $next_year ?>">Next →</a>
                </div>
            </div>
        </form>
        
        <?php $first_day_of_week = intval(date('w', $first_day_timestamp)); ?>
        <div class="border rounded-3 overflow-hidden">
            <div class="calendar-days-header">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>
            <div class="calendar-days-body">
                <!-- Padding days from previous month -->
                <?php for ($i = 0; $i < $first_day_of_week; $i++): ?>
                    <div class="calendar-day-cell blank"></div>
                <?php endfor; ?>
                
                <!-- Days of current month -->
                <?php for ($day = 1; $day <= $days_in_month; $day++): 
                    $curr_date = sprintf("%04d-%02d-%02d", $year, $month, $day);
                    $override = isset($day_overrides[$day]) ? $day_overrides[$day] : null;
                    $class = $override ? 'has-override' : '';
                ?>
                    <div class="calendar-day-cell <?= $class ?>" 
                         data-date="<?= $curr_date ?>" 
                         onclick="handleCellClick(this, <?= $override ? htmlspecialchars(json_encode($override)) : 'null' ?>)">
                        <div class="day-number"><?= $day ?></div>
                        
                        <?php if ($override): ?>
                            <div class="override-prices">
                                <span class="price-badge badge-ep">EP: ₹<?= number_format($override['ep_price']) ?></span>
                                <span class="price-badge badge-cp">CP: ₹<?= number_format($override['cp_price']) ?></span>
                                <span class="price-badge badge-map">MAP: ₹<?= number_format($override['map_price']) ?></span>
                            </div>
                            <?php if (!empty($override['reason'])): ?>
                                <div class="reason-text" title="<?= htmlspecialchars($override['reason']) ?>"><?= htmlspecialchars($override['reason']) ?></div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="font-size:10px; color:#aaa; font-style:italic; margin-top:20px; text-align:center;">Standard Rate</div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <!-- TAB 2: RULES LIST -->
    <div id="content-rules" class="tab-content-pane" style="display:none;">
        <form method="GET" action="rate-calendar.php" id="listFilterForm">
            <input type="hidden" name="tab" value="rules">
            <div class="filter-bar">
                <div style="flex: 1; min-width: 200px;">
                    <label class="form-label-custom">Filter by Category</label>
                    <select class="form-control-custom" name="filter_category">
                        <option value="0">All Room Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $filter_category === $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <label class="form-label-custom">Filter by Date</label>
                    <input type="date" class="form-control-custom" name="filter_date" value="<?= htmlspecialchars($filter_date) ?>">
                </div>
                <div class="d-flex gap-10">
                    <button class="btn btn-dark" type="submit" style="height:42px;">Filter</button>
                    <a class="btn btn-outline-secondary" href="rate-calendar.php?tab=rules" style="height:42px; line-height:28px;">Reset</a>
                </div>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Room Category</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>EP Price</th>
                        <th>CP Price</th>
                        <th>MAP Price</th>
                        <th>Reason / Notes</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rules_list) > 0): ?>
                        <?php foreach ($rules_list as $rule): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($rule['category_title']) ?></strong></td>
                                <td><?= date('d-M-Y', strtotime($rule['start_date'])) ?></td>
                                <td><?= date('d-M-Y', strtotime($rule['end_date'])) ?></td>
                                <td><span class="badge bg-light text-dark">₹<?= number_format($rule['ep_price'], 2) ?></span></td>
                                <td><span class="badge bg-light text-dark">₹<?= number_format($rule['cp_price'], 2) ?></span></td>
                                <td><span class="badge bg-light text-dark">₹<?= number_format($rule['map_price'], 2) ?></span></td>
                                <td>
                                    <?php if (!empty($rule['reason'])): ?>
                                        <span class="badge bg-warning text-dark" style="font-size:11px;"><?= htmlspecialchars($rule['reason']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:12px;">None</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align:right;">
                                    <div class="d-inline-flex gap-2">
                                        <button class="btn-edit" onclick='openEditModal(<?= json_encode($rule) ?>)'>Edit</button>
                                        <button class="btn-delete" onclick="confirmDelete(<?= $rule['id'] ?>)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-30 text-muted">No custom pricing rules found. Click "+ Add Pricing Rule" to create one.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Rule Add/Edit Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1" aria-labelledby="ruleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; overflow:hidden;">
            <form method="POST" action="rate-calendar.php" id="ruleForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="rule_id" id="formRuleId" value="">
                
                <div class="modal-header bg-dark text-white py-15 px-20">
                    <h5 class="modal-title font-heading" id="ruleModalLabel" style="font-weight:700; font-size:17px;">Add Seasonal Pricing Rule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1) grayscale(1) brightness(2);"></button>
                </div>
                <div class="modal-body p-24">
                    <div class="form-group mb-15">
                        <label class="form-label-custom">Room Category *</label>
                        <select class="form-control-custom" name="room_category_id" id="modalCategory" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row g-3 mb-15">
                        <div class="col-6">
                            <label class="form-label-custom">Start Date *</label>
                            <input type="date" class="form-control-custom" name="start_date" id="modalStartDate" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-custom">End Date *</label>
                            <input type="date" class="form-control-custom" name="end_date" id="modalEndDate" required>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-15">
                        <div class="col-4">
                            <label class="form-label-custom">EP Price (₹) *</label>
                            <input type="number" class="form-control-custom" name="ep_price" id="modalEP" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label-custom">CP Price (₹) *</label>
                            <input type="number" class="form-control-custom" name="cp_price" id="modalCP" min="0.01" step="0.01" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label-custom">MAP Price (₹) *</label>
                            <input type="number" class="form-control-custom" name="map_price" id="modalMAP" min="0.01" step="0.01" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="form-label-custom">Reason / Notes</label>
                        <input type="text" class="form-control-custom" name="reason" id="modalReason" placeholder="e.g. Diwali Festival, New Year Peak, Weekend Surge">
                    </div>
                </div>
                <div class="modal-footer bg-light py-12 px-24 border-top-0 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark" style="background:#9c6047; border-color:#9c6047;">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Rule Delete Confirmation Modal -->
<form method="POST" action="rate-calendar.php" id="deleteForm" style="display:none;">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="rule_id" id="deleteRuleId" value="">
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto-switch tabs if parameter passed in URL
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'rules') {
            switchTab('rules');
        }
    });

    function switchTab(tabName) {
        document.querySelectorAll('.nav-tab-link').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-content-pane').forEach(el => el.style.display = 'none');
        
        document.getElementById('tab-' + tabName).classList.add('active');
        document.getElementById('content-' + tabName).style.display = 'block';
    }

    function openAddModal() {
        document.getElementById('formAction').value = 'add';
        document.getElementById('formRuleId').value = '';
        document.getElementById('ruleModalLabel').innerText = 'Add Seasonal Pricing Rule';
        
        document.getElementById('modalCategory').value = "<?= count($categories) > 0 ? $categories[0]['id'] : '' ?>";
        document.getElementById('modalStartDate').value = '';
        document.getElementById('modalEndDate').value = '';
        document.getElementById('modalEP').value = '';
        document.getElementById('modalCP').value = '';
        document.getElementById('modalMAP').value = '';
        document.getElementById('modalReason').value = '';
        
        var modal = new bootstrap.Modal(document.getElementById('ruleModal'));
        modal.show();
    }

    function openEditModal(rule) {
        document.getElementById('formAction').value = 'edit';
        document.getElementById('formRuleId').value = rule.id;
        document.getElementById('ruleModalLabel').innerText = 'Edit Seasonal Pricing Rule';
        
        document.getElementById('modalCategory').value = rule.room_category_id;
        document.getElementById('modalStartDate').value = rule.start_date;
        document.getElementById('modalEndDate').value = rule.end_date;
        document.getElementById('modalEP').value = rule.ep_price;
        document.getElementById('modalCP').value = rule.cp_price;
        document.getElementById('modalMAP').value = rule.map_price;
        document.getElementById('modalReason').value = rule.reason;
        
        var modal = new bootstrap.Modal(document.getElementById('ruleModal'));
        modal.show();
    }

    function handleCellClick(cell, override) {
        if (override) {
            // Clicked active override: open Edit modal
            openEditModal(override);
        } else {
            // Clicked standard date: open Add modal and auto-fill dates
            const dateVal = cell.getAttribute('data-date');
            openAddModal();
            
            document.getElementById('modalCategory').value = "<?= $selected_category_id ?>";
            document.getElementById('modalStartDate').value = dateVal;
            document.getElementById('modalEndDate').value = dateVal;
        }
    }

    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this custom pricing rule? This will restore standard pricing for its dates.")) {
            document.getElementById('deleteRuleId').value = id;
            document.getElementById('deleteForm').submit();
        }
    }

    // Dynamic Validation Check
    document.getElementById('ruleForm').addEventListener('submit', function(e) {
        const start = document.getElementById('modalStartDate').value;
        const end = document.getElementById('modalEndDate').value;
        const ep = parseFloat(document.getElementById('modalEP').value);
        const cp = parseFloat(document.getElementById('modalCP').value);
        const map = parseFloat(document.getElementById('modalMAP').value);
        
        if (end < start) {
            e.preventDefault();
            alert("Validation Error: End Date cannot be earlier than Start Date.");
            return;
        }
        
        if (ep <= 0 || cp <= 0 || map <= 0) {
            e.preventDefault();
            alert("Validation Error: Prices must be greater than zero.");
            return;
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
<?php ob_end_flush(); ?>
