<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = 'Operational settings saved successfully!';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') {
        $error_message = 'Security check failed. Please refresh and try again.';
    } else {
        $error_message = 'Failed to update settings in the database.';
    }
}

// Handle Settings updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: settings.php?error=csrf");
        exit;
    } else {
        $hotel_name = isset($_POST['hotel_name']) ? trim($_POST['hotel_name']) : '';
        $hotel_phone = isset($_POST['hotel_phone']) ? trim($_POST['hotel_phone']) : '';
        $hotel_email = isset($_POST['hotel_email']) ? trim($_POST['hotel_email']) : '';
        $hotel_address = isset($_POST['hotel_address']) ? trim($_POST['hotel_address']) : '';
        $hotel_whatsapp = isset($_POST['hotel_whatsapp']) ? trim($_POST['hotel_whatsapp']) : '';
        $gtm_code = isset($_POST['gtm_code']) ? $_POST['gtm_code'] : '';
        $meta_pixel = isset($_POST['meta_pixel']) ? $_POST['meta_pixel'] : '';
        $google_analytics = isset($_POST['google_analytics']) ? $_POST['google_analytics'] : '';
        $razorpay_key_id = isset($_POST['razorpay_key_id']) ? trim($_POST['razorpay_key_id']) : '';
        $razorpay_key_secret = isset($_POST['razorpay_key_secret']) ? trim($_POST['razorpay_key_secret']) : '';

        // Banquet settings
        $banquet_hall_name = isset($_POST['banquet_hall_name']) ? trim($_POST['banquet_hall_name']) : '';
        $banquet_hall_capacity = isset($_POST['banquet_hall_capacity']) ? trim($_POST['banquet_hall_capacity']) : '';
        $banquet_hall_size = isset($_POST['banquet_hall_size']) ? trim($_POST['banquet_hall_size']) : '';
        $banquet_rental_charges = isset($_POST['banquet_rental_charges']) ? trim($_POST['banquet_rental_charges']) : '';
        $banquet_decor_management_text = isset($_POST['banquet_decor_management_text']) ? trim($_POST['banquet_decor_management_text']) : '';
        $banquet_hero_bg = isset($_POST['banquet_hero_bg']) ? trim($_POST['banquet_hero_bg']) : '';
        $banquet_showcase_bg = isset($_POST['banquet_showcase_bg']) ? trim($_POST['banquet_showcase_bg']) : '';
        $banquet_description = isset($_POST['banquet_description']) ? trim($_POST['banquet_description']) : '';
        $banquet_exact_location = isset($_POST['banquet_exact_location']) ? trim($_POST['banquet_exact_location']) : '';

        try {
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, val_content) VALUES (?, ?) ON DUPLICATE KEY UPDATE val_content = VALUES(val_content)");
            
            $stmt->execute(['hotel_name', $hotel_name]);
            $stmt->execute(['hotel_phone', $hotel_phone]);
            $stmt->execute(['hotel_email', $hotel_email]);
            $stmt->execute(['hotel_address', $hotel_address]);
            $stmt->execute(['hotel_whatsapp', $hotel_whatsapp]);
            $stmt->execute(['gtm_code', $gtm_code]);
            $stmt->execute(['meta_pixel', $meta_pixel]);
            $stmt->execute(['google_analytics', $google_analytics]);
            $stmt->execute(['razorpay_key_id', $razorpay_key_id]);
            $stmt->execute(['razorpay_key_secret', $razorpay_key_secret]);

            // Save banquet settings
            $stmt->execute(['banquet_hall_name', $banquet_hall_name]);
            $stmt->execute(['banquet_hall_capacity', $banquet_hall_capacity]);
            $stmt->execute(['banquet_hall_size', $banquet_hall_size]);
            $stmt->execute(['banquet_rental_charges', $banquet_rental_charges]);
            $stmt->execute(['banquet_decor_management_text', $banquet_decor_management_text]);
            $stmt->execute(['banquet_hero_bg', $banquet_hero_bg]);
            $stmt->execute(['banquet_showcase_bg', $banquet_showcase_bg]);
            $stmt->execute(['banquet_description', $banquet_description]);
            $stmt->execute(['banquet_exact_location', $banquet_exact_location]);

            header("Location: settings.php?success=1");
            exit;
        } catch (Exception $e) {
            error_log("Settings update failure: " . $e->getMessage());
            header("Location: settings.php?error=1");
            exit;
        }
    }
}

// Load current settings values
$settings = [
    'hotel_name' => '',
    'hotel_phone' => '',
    'hotel_email' => '',
    'hotel_address' => '',
    'hotel_whatsapp' => '',
    'gtm_code' => '',
    'meta_pixel' => '',
    'google_analytics' => '',
    'razorpay_key_id' => '',
    'razorpay_key_secret' => '',
    'banquet_hall_name' => '',
    'banquet_hall_capacity' => '',
    'banquet_hall_size' => '',
    'banquet_rental_charges' => '',
    'banquet_decor_management_text' => '',
    'banquet_hero_bg' => '',
    'banquet_showcase_bg' => '',
    'banquet_description' => '',
    'banquet_exact_location' => ''
];

try {
    foreach ($settings as $key => $val) {
        $settings[$key] = get_setting($key);
    }
} catch (Exception $e) {
    error_log("Settings loading error: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Operational & Integration Settings</h1>
        <p class="text-sm text-neutral-500 mt-5">Configure hotel general metadata, third-party analytics script integrations, and online payment credentials.</p>
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

<!-- Bootstrap tab selectors for configurations groups -->
<ul class="nav nav-tabs mb-30" id="settingsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true" style="font-weight: 600; color: #475569; padding: 12px 20px; font-size: 14.5px;">
            General Info & Scripts
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment" type="button" role="tab" aria-controls="payment" aria-selected="false" style="font-weight: 600; color: #475569; padding: 12px 20px; font-size: 14.5px;">
            Razorpay Integration
        </button>
    </li>
</ul>

<form action="settings.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="update_settings" value="1">

    <div class="tab-content" id="settingsTabContent">
        
        <!-- Tab 1: General Info & Scripts -->
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="panel-card">
                <h3 class="font-heading" style="font-size:18px;">Hotel Properties & Meta Settings</h3>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Hotel Brand Name *</label>
                            <input class="form-control-custom" type="text" name="hotel_name" value="<?= htmlspecialchars($settings['hotel_name']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Contact Email Address * <span style="font-weight:normal; font-size:11.5px; color:#64748b;">(Separate multiples with commas)</span></label>
                            <input class="form-control-custom" type="text" name="hotel_email" value="<?= htmlspecialchars($settings['hotel_email']) ?>" placeholder="e.g. info@hoteldestin.in, bookings@hoteldestin.in" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Contact Phone Number * <span style="font-weight:normal; font-size:11.5px; color:#64748b;">(Separate multiples with commas)</span></label>
                            <input class="form-control-custom" type="text" name="hotel_phone" value="<?= htmlspecialchars($settings['hotel_phone']) ?>" placeholder="e.g. +919873646156, +918305597600" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">WhatsApp Chat Number * <span style="font-weight:normal; font-size:11.5px; color:#64748b;">(Single number, e.g., 917000000000)</span></label>
                            <input class="form-control-custom" type="text" name="hotel_whatsapp" value="<?= htmlspecialchars($settings['hotel_whatsapp']) ?>" placeholder="e.g. 919873646156" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Hotel Physical Address *</label>
                            <input class="form-control-custom" type="text" name="hotel_address" value="<?= htmlspecialchars($settings['hotel_address']) ?>" required>
                        </div>
                    </div>

                    <hr class="" style="color: #e2e8f0;">

                    <h3 class="font-heading mb-10" style="font-size:18px;">Analytics & Tracking Script Injections</h3>
                    <p class="text-sm text-neutral-500 mb-20">Script container code inputs are dynamically loaded inside frontend headers.</p>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label-custom">Google Tag Manager (GTM) Container Script</label>
                            <textarea class="form-control-custom" name="gtm_code" rows="4" placeholder="Paste standard GTM script tag block here..." style="font-family:Courier, monospace; font-size:13.5px;"><?= htmlspecialchars($settings['gtm_code']) ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label-custom">Meta (Facebook) Pixel Script Code</label>
                            <textarea class="form-control-custom" name="meta_pixel" rows="4" placeholder="Paste standard Meta Pixel snippet here..." style="font-family:Courier, monospace; font-size:13.5px;"><?= htmlspecialchars($settings['meta_pixel']) ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label-custom">Google Analytics 4 (GA4) Tag Script</label>
                            <textarea class="form-control-custom" name="google_analytics" rows="4" placeholder="Paste standard gtag.js code here..." style="font-family:Courier, monospace; font-size:13.5px;"><?= htmlspecialchars($settings['google_analytics']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab 2: Razorpay Settings -->
        <div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
            <div class="panel-card">
                <h3 class="font-heading mb-10" style="font-size:18px;">Razorpay Gateway API Integration</h3>
                <p class="text-sm text-neutral-500 mb-25">Configure API credentials to authorize test and live transaction calls securely.</p>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Razorpay Key ID (API Key)</label>
                            <input class="form-control-custom" type="text" name="razorpay_key_id" value="<?= htmlspecialchars($settings['razorpay_key_id']) ?>" placeholder="rzp_test_..." style="font-family:Courier, monospace;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label-custom">Razorpay Secret Key</label>
                            <input class="form-control-custom" type="password" name="razorpay_key_secret" value="<?= htmlspecialchars($settings['razorpay_key_secret']) ?>" placeholder="••••••••••••••••" style="font-family:Courier, monospace;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Submit block -->
    <div class="mt-20">
        <button class="btn btn-black text-white px-35 py-12" type="submit" style="border-radius: 8px; font-weight:700;">
            Save Settings Configuration
        </button>
    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
