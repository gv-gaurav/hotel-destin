<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    $success_message = 'Banquet hall settings saved successfully!';
}

if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') {
        $error_message = 'Security check failed. Please refresh and try again.';
    } else if ($_GET['error'] === 'format') {
        $error_message = 'Invalid image format! Only JPG, JPEG, PNG, WEBP, and GIF are allowed.';
    } else if ($_GET['error'] === 'size') {
        $error_message = 'Image file is too large! Maximum size allowed is 5MB.';
    } else if ($_GET['error'] === 'upload') {
        $error_message = 'Failed to save uploaded image file on the server.';
    } else {
        $error_message = 'Failed to update settings in the database.';
    }
}

// Default Configuration values fallback
$default_configs = [
    'banquet_hall_name' => 'Banquet Oh Saathi Re',
    'banquet_hall_capacity' => '300 Guests',
    'banquet_hall_size' => '3,800 Sq. Ft.',
    'banquet_rental_charges' => '₹15,000 for 6 hours',
    'banquet_decor_management_text' => 'Custom event packages, delicious catering, and dedicated service available',
    'banquet_hero_bg' => 'assets/imgs/page/hotel/banner-hotel.png',
    'banquet_showcase_bg' => 'assets/imgs/page/room/banner-room.png',
    'banquet_description' => "Banquet Oh Saathi Re is Gwalior's premier pillar-free venue, perfect for weddings, receptions, birthday celebrations, corporate meetings, conferences, and other social events. We offer flexible event packages, delicious catering, and dedicated service to make every event a success.",
    'banquet_exact_location' => 'Hotel Destin Gwalior'
];

// Handle Banquet settings updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_banquet_settings') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: banquet-settings.php?error=csrf");
        exit;
    } else {
        $banquet_hall_name = isset($_POST['banquet_hall_name']) ? trim($_POST['banquet_hall_name']) : '';
        $banquet_hall_capacity = isset($_POST['banquet_hall_capacity']) ? trim($_POST['banquet_hall_capacity']) : '';
        $banquet_hall_size = isset($_POST['banquet_hall_size']) ? trim($_POST['banquet_hall_size']) : '';
        $banquet_rental_charges = isset($_POST['banquet_rental_charges']) ? trim($_POST['banquet_rental_charges']) : '';
        $banquet_decor_management_text = isset($_POST['banquet_decor_management_text']) ? trim($_POST['banquet_decor_management_text']) : '';
        $banquet_description = isset($_POST['banquet_description']) ? trim($_POST['banquet_description']) : '';
        $banquet_exact_location = isset($_POST['banquet_exact_location']) ? trim($_POST['banquet_exact_location']) : '';

        try {
            $stmt = $pdo->prepare("INSERT INTO settings (key_name, val_content) VALUES (?, ?) ON DUPLICATE KEY UPDATE val_content = VALUES(val_content)");
            
            $stmt->execute(['banquet_hall_name', $banquet_hall_name]);
            $stmt->execute(['banquet_hall_capacity', $banquet_hall_capacity]);
            $stmt->execute(['banquet_hall_size', $banquet_hall_size]);
            $stmt->execute(['banquet_rental_charges', $banquet_rental_charges]);
            $stmt->execute(['banquet_decor_management_text', $banquet_decor_management_text]);
            $stmt->execute(['banquet_description', $banquet_description]);
            $stmt->execute(['banquet_exact_location', $banquet_exact_location]);

            // File uploads handling for backgrounds
            $image_fields = ['banquet_hero_bg', 'banquet_showcase_bg'];
            foreach ($image_fields as $field) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES[$field]['tmp_name'];
                    $file_name = $_FILES[$field]['name'];
                    $file_size = $_FILES[$field]['size'];
                    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    $max_size = 5 * 1024 * 1024; // 5MB

                    if (!in_array($file_ext, $allowed_extensions)) {
                        header("Location: banquet-settings.php?error=format");
                        exit;
                    } else if ($file_size > $max_size) {
                        header("Location: banquet-settings.php?error=size");
                        exit;
                    } else {
                        $upload_dir = __DIR__ . '/../uploads/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }

                        $new_filename = $field . '_' . uniqid('', true) . '.' . $file_ext;
                        $dest_path = $upload_dir . $new_filename;
                        $db_path = 'uploads/' . $new_filename;

                        if (move_uploaded_file($file_tmp, $dest_path)) {
                            // Delete old image if it was a customized upload path
                            $old_val = get_setting($field);
                            if (!empty($old_val) && strpos($old_val, 'uploads/') === 0) {
                                $old_file_path = __DIR__ . '/../' . $old_val;
                                if (file_exists($old_file_path)) {
                                    @unlink($old_file_path);
                                }
                            }
                            $stmt->execute([$field, $db_path]);
                        } else {
                            header("Location: banquet-settings.php?error=upload");
                            exit;
                        }
                    }
                }
            }

            header("Location: banquet-settings.php?success=1");
            exit;
        } catch (Exception $e) {
            error_log("Banquet settings save error: " . $e->getMessage());
            header("Location: banquet-settings.php?error=db");
            exit;
        }
    }
}

// Fetch active configs
$settings = [];
foreach ($default_configs as $key => $default_val) {
    $db_val = get_setting($key);
    $settings[$key] = ($db_val !== '') ? $db_val : $default_val;
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Banquet Hall Settings</h1>
        <p class="text-sm text-neutral-500 mt-5">Configure the presentation copy, capacities, pricing, and images shown on the public banquet portal.</p>
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
    <h3 class="font-heading mb-20" style="font-size:18px;">Edit Banquet Showcase Copy &amp; Specifications</h3>
    
    <form action="banquet-settings.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="update_banquet_settings">

        <div class="row g-4">
            <!-- Left Side Inputs -->
            <div class="col-lg-6 col-12">
                <div class="form-group mb-20">
                    <label class="form-label-custom">Banquet Hall Name *</label>
                    <input class="form-control-custom" type="text" name="banquet_hall_name" value="<?= htmlspecialchars($settings['banquet_hall_name']) ?>" required>
                </div>

                <div class="form-group mb-20">
                    <label class="form-label-custom">Guest Capacity Text *</label>
                    <input class="form-control-custom" type="text" name="banquet_hall_capacity" value="<?= htmlspecialchars($settings['banquet_hall_capacity']) ?>" placeholder="e.g. 300 Guests" required>
                </div>

                <div class="form-group mb-20">
                    <label class="form-label-custom">Hall Dimensions / Size *</label>
                    <input class="form-control-custom" type="text" name="banquet_hall_size" value="<?= htmlspecialchars($settings['banquet_hall_size']) ?>" placeholder="e.g. 3,800 Sq. Ft." required>
                </div>

                <input type="hidden" name="banquet_rental_charges" value="<?= htmlspecialchars($settings['banquet_rental_charges']) ?>">
                <input type="hidden" name="banquet_exact_location" value="<?= htmlspecialchars($settings['banquet_exact_location']) ?>">
            </div>

            <!-- Right Side: Files and Extras -->
            <div class="col-lg-6 col-12">
                <div class="form-group mb-20">
                    <label class="form-label-custom">Decor &amp; Catering Highlight Line *</label>
                    <input class="form-control-custom" type="text" name="banquet_decor_management_text" value="<?= htmlspecialchars($settings['banquet_decor_management_text']) ?>" required>
                </div>

                <!-- Hero Background Upload -->
                <div class="form-group mb-20">
                    <label class="form-label-custom">Hero Background Image</label>
                    <?php if (!empty($settings['banquet_hero_bg'])): ?>
                        <div class="mb-10 d-flex align-items-center gap-10">
                            <span class="text-xs text-neutral-500">Current:</span>
                            <a href="../<?= htmlspecialchars($settings['banquet_hero_bg']) ?>" target="_blank" class="text-xs text-primary" style="font-weight:600; text-decoration:underline;">View Image</a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="banquet_hero_bg" class="form-control-custom" style="padding-top:6px !important;">
                    <p class="text-xs text-neutral-400 mt-5">Only image files (JPG, PNG, WEBP, GIF) up to 5MB are accepted.</p>
                </div>

                <!-- Showcase Left Card Upload -->
                <div class="form-group mb-20">
                    <label class="form-label-custom">Showcase Image (Left Thumbnail Panel)</label>
                    <?php if (!empty($settings['banquet_showcase_bg'])): ?>
                        <div class="mb-10 d-flex align-items-center gap-10">
                            <span class="text-xs text-neutral-500">Current:</span>
                            <a href="../<?= htmlspecialchars($settings['banquet_showcase_bg']) ?>" target="_blank" class="text-xs text-primary" style="font-weight:600; text-decoration:underline;">View Image</a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="banquet_showcase_bg" class="form-control-custom" style="padding-top:6px !important;">
                    <p class="text-xs text-neutral-400 mt-5">Only image files (JPG, PNG, WEBP, GIF) up to 5MB are accepted.</p>
                </div>
            </div>

            <!-- Description Text -->
            <div class="col-md-12">
                <div class="form-group mb-25">
                    <label class="form-label-custom">Banquet Detailed Description *</label>
                    <textarea class="form-control-custom" name="banquet_description" rows="5" required style="height:auto !important;"><?= htmlspecialchars($settings['banquet_description']) ?></textarea>
                </div>
            </div>
        </div>

        <div class="mt-15">
            <button class="btn btn-black text-white px-35 py-12" type="submit" style="border-radius: 8px; font-weight:700;">
                Save Banquet Settings
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
