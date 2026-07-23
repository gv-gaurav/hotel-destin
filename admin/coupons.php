<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'add') $success_message = 'Coupon code created successfully!';
    if ($_GET['success'] === 'edit') $success_message = 'Coupon details updated successfully!';
    if ($_GET['success'] === 'delete') $success_message = 'Coupon deleted successfully.';
    if ($_GET['success'] === 'settings') $success_message = 'Coupon promotion banner settings updated successfully!';
}

// Load dynamic coupon banner settings
$banner_configs = [
    'coupon_banner_title' => 'Flat ₹500 Cashback on bookings above ₹5,000!',
    'coupon_banner_subtitle' => 'Celebrate your stay at Hotel Destin with exclusive savings. Use the coupon code below at checkout.',
    'coupon_banner_terms' => '*Terms & conditions apply. Valid for standard, executive, and premium room reservations.',
    'coupon_banner_status' => 'active',
];

try {
    foreach ($banner_configs as $key => $val) {
        $loaded_val = get_setting($key);
        if ($loaded_val !== '') {
            $banner_configs[$key] = $loaded_val;
        }
    }
} catch (Exception $e) {
    error_log("Loading banner settings error in coupons page: " . $e->getMessage());
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'add') $error_message = 'Failed to create coupon (code might be duplicate).';
    else if ($_GET['error'] === 'edit') $error_message = 'Failed to update coupon details.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete coupon.';
    else if ($_GET['error'] === 'req') $error_message = 'All fields are required.';
}

// Handle Form CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: coupons.php?error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
            $code = isset($_POST['code']) ? strtoupper(trim(htmlspecialchars($_POST['code']))) : '';
            $discount_percent = isset($_POST['discount_percent']) ? intval($_POST['discount_percent']) : 0;
            $expiry_date = isset($_POST['expiry_date']) ? trim($_POST['expiry_date']) : '';
            $status = isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active';
            $show_in_checkout = isset($_POST['show_in_checkout']) ? 1 : 0;

            if (empty($title) || empty($code) || $discount_percent <= 0 || empty($expiry_date)) {
                header("Location: coupons.php?error=req");
                exit;
            } else {
                if ($action === 'add') {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO coupons (title, code, discount_percent, expiry_date, status, show_in_checkout) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$title, $code, $discount_percent, $expiry_date, $status, $show_in_checkout]);
                        header("Location: coupons.php?success=add");
                        exit;
                    } catch (Exception $e) {
                        error_log("Coupon insertion error: " . $e->getMessage());
                        header("Location: coupons.php?error=add");
                        exit;
                    }
                } else if ($action === 'edit') {
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    try {
                        $stmt = $pdo->prepare("UPDATE coupons SET title = ?, code = ?, discount_percent = ?, expiry_date = ?, status = ?, show_in_checkout = ? WHERE id = ?");
                        $stmt->execute([$title, $code, $discount_percent, $expiry_date, $status, $show_in_checkout, $id]);
                        header("Location: coupons.php?success=edit");
                        exit;
                    } catch (Exception $e) {
                        error_log("Coupon update error: " . $e->getMessage());
                        header("Location: coupons.php?error=edit");
                        exit;
                    }
                }
            }
        } else if ($action === 'delete') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            try {
                $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: coupons.php?success=delete");
                exit;
            } catch (Exception $e) {
                error_log("Coupon deletion error: " . $e->getMessage());
                header("Location: coupons.php?error=delete");
                exit;
            }
        } else if ($action === 'update_banner_settings') {
            $coupon_banner_title = isset($_POST['coupon_banner_title']) ? trim($_POST['coupon_banner_title']) : '';
            $coupon_banner_subtitle = isset($_POST['coupon_banner_subtitle']) ? trim($_POST['coupon_banner_subtitle']) : '';
            $coupon_banner_terms = isset($_POST['coupon_banner_terms']) ? trim($_POST['coupon_banner_terms']) : '';
            $coupon_banner_status = isset($_POST['coupon_banner_status']) ? trim($_POST['coupon_banner_status']) : 'active';

            if (empty($coupon_banner_title) || empty($coupon_banner_subtitle)) {
                header("Location: coupons.php?error=req");
                exit;
            } else {
                try {
                    $stmt = $pdo->prepare("INSERT INTO settings (key_name, val_content) VALUES (?, ?) ON DUPLICATE KEY UPDATE val_content = VALUES(val_content)");
                    $stmt->execute(['coupon_banner_title', $coupon_banner_title]);
                    $stmt->execute(['coupon_banner_subtitle', $coupon_banner_subtitle]);
                    $stmt->execute(['coupon_banner_terms', $coupon_banner_terms]);
                    $stmt->execute(['coupon_banner_status', $coupon_banner_status]);

                    header("Location: coupons.php?success=settings");
                    exit;
                } catch (Exception $e) {
                    error_log("Coupon banner settings update error: " . $e->getMessage());
                    header("Location: coupons.php?error=edit");
                    exit;
                }
            }
        }
    }
}

// Fetch all coupons from DB
$coupons = [];
try {
    $coupons = $pdo->query("SELECT * FROM coupons ORDER BY expiry_date DESC")->fetchAll();
} catch (Exception $e) {
    error_log("Coupons loading error in admin page: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Coupon Codes</h1>
        <p class="text-sm text-neutral-500 mt-5">Create and issue guest coupon codes, specify stay rate discount percentages, set code expiration check limits, and toggle active/inactive states.</p>
    </div>
    <button class="btn btn-black text-white" onclick="showAddModal()" style="padding: 10px 24px; border-radius: 8px; font-size:14px;">
        Add Coupon Code
    </button>
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

<!-- Bootstrap Modal Form for Add/Edit Coupons -->
<div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border:none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0" style="padding: 25px 30px 10px 30px;">
                <h3 class="modal-title font-heading" id="couponModalLabel" style="font-size: 20px; color: #0f172a;">Add Coupon</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="couponEditor" action="coupons.php" method="POST">
                <div class="modal-body" style="padding: 10px 30px 30px 30px;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" id="formAction" name="action" value="add">
                    <input type="hidden" id="couponIdInput" name="id" value="">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Coupon Title * (e.g. Special Holiday Promo)</label>
                                <input id="couponTitle" class="form-control-custom" type="text" name="title" placeholder="e.g. Monsoon Special" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Promo Code * (e.g. FESTIVE20)</label>
                                <input id="couponCode" class="form-control-custom" type="text" name="code" placeholder="e.g. FESTIVE20" required style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Discount Percentage * (1-99%)</label>
                                <input id="couponDiscount" class="form-control-custom" type="number" name="discount_percent" min="1" max="99" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Expiry Date *</label>
                                <input id="couponExpiry" class="form-control-custom" type="date" name="expiry_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Coupon Status</label>
                                <select id="couponStatus" class="form-control-custom" name="status" style="height:42px; background-position: right 15px center;">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-10">
                            <div class="form-check form-switch d-flex align-items-center gap-10">
                                <input class="form-check-input" type="checkbox" id="couponShowInCheckout" name="show_in_checkout" value="1" checked style="width: 38px; height: 20px; cursor: pointer;">
                                <label class="form-check-label mb-0" for="couponShowInCheckout" style="font-size: 13.5px; font-weight: 600; color: #334155; cursor: pointer; padding-left: 10px;">
                                    Show this coupon code publicly in Checkout
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 d-flex gap-10" style="padding: 0 30px 30px 30px;">
                    <button class="btn btn-black text-white" type="submit" style="padding: 10px 24px; border-radius: 8px; margin: 0;">Save Coupon</button>
                    <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 8px; border-color:#ccc; margin: 0;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Coupons List Table -->
<div class="panel-card">
    <h3 class="font-heading" style="font-size:18px;">Active Promo Code registries</h3>
    
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Coupon Title</th>
                    <th>Promo Code</th>
                    <th>Discount Rate</th>
                    <th>Expiration Limit</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($coupons) > 0): ?>
                    <?php foreach ($coupons as $cp): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($cp['title']) ?></strong>
                            </td>
                            <td>
                                <span style="font-family:Courier, monospace; font-size:14.5px; font-weight:700; background:#f1f5f9; padding:4px 8px; border-radius:6px; border:1px solid #cbd5e1; color:#9c6047;">
                                    <?= htmlspecialchars($cp['code']) ?>
                                </span>
                            </td>
                            <td>
                                <strong style="font-size:15px; color:#3c7a4b;"><?= htmlspecialchars($cp['discount_percent']) ?>% OFF</strong>
                            </td>
                            <td>
                                <?php
                                $expired = (strtotime($cp['expiry_date']) < strtotime(date('Y-m-d')));
                                ?>
                                <span style="font-weight:600; color: <?= $expired ? '#dc2626' : '#475569' ?>;">
                                    <?= date('d M Y', strtotime($cp['expiry_date'])) ?>
                                    <?= $expired ? ' (Expired)' : '' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background: <?= ($cp['status'] === 'active' && !$expired) ? '#eef7f0; color:#3c7a4b;' : '#fff0f0; color:#d13232;' ?>; font-size:11px; padding:5px 10px; font-weight:700;">
                                    <?= $expired ? 'inactive' : htmlspecialchars($cp['status']) ?>
                                </span>
                                <?php if (isset($cp['show_in_checkout']) && (int)$cp['show_in_checkout'] === 1 && !$expired): ?>
                                    <br><span style="font-size: 10px; color:#475569; font-weight:600; display:inline-block; margin-top:4px;">👁️ Public Checkout</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <button class="btn-edit" onclick="editCoupon(<?= htmlspecialchars(json_encode($cp)) ?>)">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit
                                    </button>
                                    
                                    <form action="coupons.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this coupon code?')" style="display:inline; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $cp['id'] ?>">
                                        <button class="btn-delete" type="submit">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-30 text-neutral-500">No promo coupons issued yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Coupon Banner Settings Form -->
<div class="panel-card mt-25">
    <h3 class="font-heading mb-10" style="font-size:18px;">Homepage Promotion Banner Settings</h3>
    <p class="text-sm text-neutral-500 mb-20">Configure the special promotion section displayed on the home page. When active, it will automatically showcase the latest 3 active and unexpired coupon codes.</p>
    
    <form action="coupons.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="update_banner_settings">
        
        <div class="row">
            <div class="col-md-9">
                <div class="form-group">
                    <label class="form-label-custom">Promotion Section Title *</label>
                    <input class="form-control-custom" type="text" name="coupon_banner_title" value="<?= htmlspecialchars($banner_configs['coupon_banner_title']) ?>" required placeholder="e.g. Flat ₹500 Cashback on bookings above ₹5,000!">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label class="form-label-custom">Section Status *</label>
                    <select class="form-control-custom" name="coupon_banner_status" style="height:42px !important; padding:8px 12px; background-position: right 15px center;">
                        <option value="active" <?= $banner_configs['coupon_banner_status'] === 'active' ? 'selected' : '' ?>>Active (Visible)</option>
                        <option value="inactive" <?= $banner_configs['coupon_banner_status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label-custom">Promotion Section Description / Subtitle *</label>
                    <textarea class="form-control-custom" name="coupon_banner_subtitle" rows="3" required placeholder="Describe the promotion guidelines or how to apply the code..."><?= htmlspecialchars($banner_configs['coupon_banner_subtitle']) ?></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label-custom">Promotion Footer / Terms & Conditions Text</label>
                    <input class="form-control-custom" type="text" name="coupon_banner_terms" value="<?= htmlspecialchars($banner_configs['coupon_banner_terms']) ?>" placeholder="e.g. *Terms & conditions apply.">
                </div>
            </div>
        </div>
        
        <div class="mt-20 text-start">
            <button type="submit" class="btn btn-black text-white" style="padding: 10px 24px; border-radius:8px; font-weight:700; font-size:14px;">
                Save Banner Settings
            </button>
        </div>
    </form>
</div>

<script>
    let couponModalObj = null;

    function initCouponModal() {
        if (!couponModalObj) {
            couponModalObj = new bootstrap.Modal(document.getElementById('couponModal'));
        }
        return couponModalObj;
    }

    function showAddModal() {
        document.getElementById('couponModalLabel').innerText = 'Add Coupon Code';
        document.getElementById('formAction').value = 'add';
        document.getElementById('couponIdInput').value = '';
        
        document.getElementById('couponTitle').value = '';
        document.getElementById('couponCode').value = '';
        document.getElementById('couponDiscount').value = '';
        document.getElementById('couponExpiry').value = '';
        document.getElementById('couponStatus').value = 'active';
        document.getElementById('couponShowInCheckout').checked = true;

        initCouponModal().show();
    }

    function editCoupon(cp) {
        document.getElementById('couponModalLabel').innerText = 'Edit Coupon Details';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('couponIdInput').value = cp.id;

        document.getElementById('couponTitle').value = cp.title;
        document.getElementById('couponCode').value = cp.code;
        document.getElementById('couponDiscount').value = cp.discount_percent;
        document.getElementById('couponExpiry').value = cp.expiry_date;
        document.getElementById('couponStatus').value = cp.status;
        document.getElementById('couponShowInCheckout').checked = (parseInt(cp.show_in_checkout) === 1);

        initCouponModal().show();
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
