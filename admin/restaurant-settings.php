<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';
$active_tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'hero';
if (!in_array($active_tab, ['hero', 'facilities', 'ambience'])) {
    $active_tab = 'hero';
}

if (isset($_GET['success'])) {
    $success_message = 'Restaurant content settings updated successfully!';
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
        $error_message = 'An unexpected error occurred while updating settings.';
    }
}

// Default Configuration values fallback
$default_configs = [
    'restaurant_hero_bg' => 'assets/imgs/page/restaurant/hero.png',
    'restaurant_hero_title' => 'The Heights Rooftop & Club Bar',
    'restaurant_hero_tagline' => 'Elevated Gastronomy & Celestial Libations',
    'restaurant_hero_hours' => '07:00 AM to 11:30 PM',
    'restaurant_food_types' => 'We have both veg and non-veg food available with club bar facility at rooftop',
    'restaurant_room_service_text' => 'Room Service & Restaurant Facilities Available',

    'restaurant_facilities_title' => 'Restaurant Facilities',
    'restaurant_facilities_desc' => 'Indulge in our luxurious hospitality features that combine great taste with a premium lounge experience.',

    'restaurant_facility_1_badge' => 'Open Daily',
    'restaurant_facility_1_image' => 'assets/imgs/page/restaurant/rooftop_bar.png',
    'restaurant_facility_1_title' => 'Rooftop Club & Bar',
    'restaurant_facility_1_desc' => 'Unwind under the stars with our signature cocktails, handpicked spirits, and deep house beats at Gwalior\'s premier rooftop club facility.',
    'restaurant_facility_1_icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 22H6M12 15v7M12 15l7-10H5l7 10zM12 9l-2-2h4l-2 2z"/></svg>',

    'restaurant_facility_2_badge' => 'Family Friendly',
    'restaurant_facility_2_image' => 'assets/imgs/page/restaurant/fine_dining.png',
    'restaurant_facility_2_title' => 'Fine Dining Restaurant',
    'restaurant_facility_2_desc' => 'A sophisticated family dining atmosphere offering an exquisite spread of pure vegetarian and gourmet non-vegetarian options prepared by master chefs.',
    'restaurant_facility_2_icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20M12 4v3M12 7a8 8 0 0 0-8 8h16a8 8 0 0 0-8-8zM5 15h14"/></svg>',

    'restaurant_facility_3_badge' => 'For In-House Guests',
    'restaurant_facility_3_image' => 'assets/imgs/page/restaurant/room_service.png',
    'restaurant_facility_3_title' => 'In-Room Dining',
    'restaurant_facility_3_desc' => 'Experience restaurant-quality hot meals delivered directly to the comfort of your executive room or suite at any hour during operating times.',
    'restaurant_facility_3_icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M12 2v2M7 8h10M12 12h.01M3 20h18"/></svg>',

    'restaurant_ambience_title' => 'Ambience & Moments',
    'restaurant_ambience_desc' => 'Take a visual tour through our celestial rooftop club and warm indoor dining halls.',

    'restaurant_ambience_1_image' => 'assets/imgs/page/restaurant/hero.png',
    'restaurant_ambience_1_title' => 'Rooftop Skyline Dining',
    'restaurant_ambience_1_desc' => 'Unparalleled city views at dusk',

    'restaurant_ambience_2_image' => 'assets/imgs/page/restaurant/rooftop_bar.png',
    'restaurant_ambience_2_title' => 'Signature Bar Lounge',

    'restaurant_ambience_3_image' => 'assets/imgs/page/restaurant/fine_dining.png',
    'restaurant_ambience_3_title' => 'Gourmet Masterpieces',

    'restaurant_ambience_4_image' => 'assets/imgs/page/restaurant/room_service.png',
    'restaurant_ambience_4_title' => 'Luxury Suite Room Service',
];

// Handle Form Update Post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_restaurant_settings') {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: restaurant-settings.php?error=csrf&tab=" . $active_tab);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO settings (key_name, val_content) VALUES (?, ?) ON DUPLICATE KEY UPDATE val_content = VALUES(val_content)");
        
        // 1. Process all text inputs first
        foreach ($_POST as $key => $val) {
            if ($key !== 'csrf_token' && $key !== 'action' && strpos($key, 'restaurant_') === 0) {
                $stmt->execute([$key, trim($val)]);
            }
        }

        // 2. Process all uploaded file inputs
        $image_fields = [
            'restaurant_hero_bg',
            'restaurant_facility_1_image',
            'restaurant_facility_2_image',
            'restaurant_facility_3_image',
            'restaurant_ambience_1_image',
            'restaurant_ambience_2_image',
            'restaurant_ambience_3_image',
            'restaurant_ambience_4_image'
        ];

        foreach ($image_fields as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES[$field]['tmp_name'];
                $file_name = $_FILES[$field]['name'];
                $file_size = $_FILES[$field]['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($file_ext, $allowed_extensions)) {
                    header("Location: restaurant-settings.php?error=format&tab=" . $active_tab);
                    exit;
                } else if ($file_size > $max_size) {
                    header("Location: restaurant-settings.php?error=size&tab=" . $active_tab);
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
                        // Delete old file if custom upload was used previously
                        $old_val = get_setting($field);
                        if (!empty($old_val) && strpos($old_val, 'uploads/') === 0) {
                            $old_file_path = __DIR__ . '/../' . $old_val;
                            if (file_exists($old_file_path)) {
                                @unlink($old_file_path);
                            }
                        }
                        $stmt->execute([$field, $db_path]);
                    } else {
                        header("Location: restaurant-settings.php?error=upload&tab=" . $active_tab);
                        exit;
                    }
                }
            }
        }

        header("Location: restaurant-settings.php?success=1&tab=" . $active_tab);
        exit;
    } catch (Exception $e) {
        error_log("Restaurant settings save error: " . $e->getMessage());
        header("Location: restaurant-settings.php?error=db&tab=" . $active_tab);
        exit;
    }
}

// Fetch active config values from DB, fallback to default if not saved
$current_configs = [];
foreach ($default_configs as $key => $default_val) {
    $db_val = get_setting($key);
    $current_configs[$key] = ($db_val !== '') ? $db_val : $default_val;
}

?>

<style>
    .editor-tab-header {
        font-weight: 600;
        padding: 12px 20px;
        font-size: 14.5px;
        text-decoration: none;
        display: inline-block;
        border-bottom: 3px solid transparent;
        color: #475569;
    }
    .editor-tab-header.active {
        border-bottom-color: #9c6047;
        color: #9c6047;
        font-weight: 700;
    }
    .preview-thumbnail-box {
        width: 100%;
        max-width: 320px;
        height: 180px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .preview-thumbnail-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .facility-editor-card {
        background: #fafbfe;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    .ambience-editor-card {
        background: #fcfdfe;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Restaurant Page Editor</h1>
        <p class="text-sm text-neutral-500 mt-5">Customize the Hero Section, Facilities Showcase, and Ambience & Moments gallery of restaurant.php.</p>
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

<!-- Tabs navigation -->
<ul class="nav nav-tabs mb-30" id="restaurantTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a href="restaurant-settings.php?tab=hero" class="editor-tab-header <?= $active_tab === 'hero' ? 'active' : '' ?>">
            Hero Section
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="restaurant-settings.php?tab=facilities" class="editor-tab-header <?= $active_tab === 'facilities' ? 'active' : '' ?>">
            Restaurant Facilities
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="restaurant-settings.php?tab=ambience" class="editor-tab-header <?= $active_tab === 'ambience' ? 'active' : '' ?>">
            Ambience & Moments
        </a>
    </li>
</ul>

<form action="restaurant-settings.php?tab=<?= $active_tab ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <input type="hidden" name="action" value="update_restaurant_settings">

    <div class="tab-content">
        <!-- TAB 1: Hero Section Settings -->
        <?php if ($active_tab === 'hero'): ?>
            <div class="panel-card">
                <h3 class="font-heading" style="font-size:18px; margin-bottom: 20px;">Hero Background & Slogans</h3>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label-custom">Current Background Image</label>
                        <div class="preview-thumbnail-box">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_hero_bg']) ?>" alt="Hero BG Preview">
                        </div>
                        <label class="form-label-custom">Upload New Background Image <span style="font-weight: normal; font-size: 11px; color:#64748b;">(Recommended: 1920x800px, max 5MB)</span></label>
                        <input type="file" name="restaurant_hero_bg" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Restaurant Name / Hero Title *</label>
                            <input type="text" name="restaurant_hero_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_hero_title']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Tagline slogan *</label>
                            <input type="text" name="restaurant_hero_tagline" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_hero_tagline']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Operating Hours *</label>
                            <input type="text" name="restaurant_hero_hours" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_hero_hours']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-15">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Food Availability Description *</label>
                            <textarea name="restaurant_food_types" class="form-control-custom" rows="3" required><?= htmlspecialchars($current_configs['restaurant_food_types']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Room Service / Amenities Line *</label>
                            <textarea name="restaurant_room_service_text" class="form-control-custom" rows="3" required><?= htmlspecialchars($current_configs['restaurant_room_service_text']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- TAB 2: Restaurant Facilities -->
        <?php if ($active_tab === 'facilities'): ?>
            <div class="panel-card mb-25">
                <h3 class="font-heading" style="font-size:18px; margin-bottom: 20px;">Section Header Content</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Facilities Section Title *</label>
                            <input type="text" name="restaurant_facilities_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facilities_title']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Facilities Section Description *</label>
                            <input type="text" name="restaurant_facilities_desc" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facilities_desc']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 1 editor -->
            <div class="facility-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; margin-bottom: 15px;">Card 1: Rooftop Club & Bar</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Facility Image Preview</label>
                        <div class="preview-thumbnail-box">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_facility_1_image']) ?>" alt="Facility 1 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_facility_1_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Card Title *</label>
                                <input type="text" name="restaurant_facility_1_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facility_1_title']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Ribbon Badge Text *</label>
                                <input type="text" name="restaurant_facility_1_badge" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facility_1_badge']) ?>" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">Card Description *</label>
                                <textarea name="restaurant_facility_1_desc" class="form-control-custom" rows="2" required><?= htmlspecialchars($current_configs['restaurant_facility_1_desc']) ?></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">SVG Icon Code * <span style="font-weight: normal; font-size: 11px; color:#64748b;">(Must start with &lt;svg&gt; and end with &lt;/svg&gt;)</span></label>
                                <textarea name="restaurant_facility_1_icon" class="form-control-custom" rows="2" style="font-family: Courier, monospace; font-size:12px;" required><?= htmlspecialchars($current_configs['restaurant_facility_1_icon']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2 editor -->
            <div class="facility-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; margin-bottom: 15px;">Card 2: Fine Dining Restaurant</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Facility Image Preview</label>
                        <div class="preview-thumbnail-box">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_facility_2_image']) ?>" alt="Facility 2 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_facility_2_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Card Title *</label>
                                <input type="text" name="restaurant_facility_2_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facility_2_title']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Ribbon Badge Text *</label>
                                <input type="text" name="restaurant_facility_2_badge" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facility_2_badge']) ?>" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">Card Description *</label>
                                <textarea name="restaurant_facility_2_desc" class="form-control-custom" rows="2" required><?= htmlspecialchars($current_configs['restaurant_facility_2_desc']) ?></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">SVG Icon Code *</label>
                                <textarea name="restaurant_facility_2_icon" class="form-control-custom" rows="2" style="font-family: Courier, monospace; font-size:12px;" required><?= htmlspecialchars($current_configs['restaurant_facility_2_icon']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3 editor -->
            <div class="facility-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px dashed #cbd5e1; padding-bottom: 8px; margin-bottom: 15px;">Card 3: In-Room Dining</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Facility Image Preview</label>
                        <div class="preview-thumbnail-box">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_facility_3_image']) ?>" alt="Facility 3 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_facility_3_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Card Title *</label>
                                <input type="text" name="restaurant_facility_3_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facility_3_title']) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label-custom">Ribbon Badge Text *</label>
                                <input type="text" name="restaurant_facility_3_badge" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_facility_3_badge']) ?>" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">Card Description *</label>
                                <textarea name="restaurant_facility_3_desc" class="form-control-custom" rows="2" required><?= htmlspecialchars($current_configs['restaurant_facility_3_desc']) ?></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label-custom">SVG Icon Code *</label>
                                <textarea name="restaurant_facility_3_icon" class="form-control-custom" rows="2" style="font-family: Courier, monospace; font-size:12px;" required><?= htmlspecialchars($current_configs['restaurant_facility_3_icon']) ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- TAB 3: Ambience & Moments Settings -->
        <?php if ($active_tab === 'ambience'): ?>
            <div class="panel-card mb-25">
                <h3 class="font-heading" style="font-size:18px; margin-bottom: 20px;">Section Header Content</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Ambience Section Title *</label>
                            <input type="text" name="restaurant_ambience_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_title']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Ambience Section Description *</label>
                            <input type="text" name="restaurant_ambience_desc" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_desc']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image 1 (Large left photo) -->
            <div class="ambience-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Moment 1 (Large Image - Left Side)</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Image Preview</label>
                        <div class="preview-thumbnail-box" style="height:150px;">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_ambience_1_image']) ?>" alt="Ambience 1 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_ambience_1_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Caption Title *</label>
                            <input type="text" name="restaurant_ambience_1_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_1_title']) ?>" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Caption Subtitle / Hover details *</label>
                            <input type="text" name="restaurant_ambience_1_desc" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_1_desc']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image 2 -->
            <div class="ambience-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Moment 2 (Top Left Small Image)</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Image Preview</label>
                        <div class="preview-thumbnail-box" style="height:120px;">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_ambience_2_image']) ?>" alt="Ambience 2 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_ambience_2_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Caption Title *</label>
                            <input type="text" name="restaurant_ambience_2_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_2_title']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image 3 -->
            <div class="ambience-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Moment 3 (Top Right Small Image)</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Image Preview</label>
                        <div class="preview-thumbnail-box" style="height:120px;">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_ambience_3_image']) ?>" alt="Ambience 3 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_ambience_3_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Caption Title *</label>
                            <input type="text" name="restaurant_ambience_3_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_3_title']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image 4 -->
            <div class="ambience-editor-card">
                <h4 style="font-size: 16px; font-weight: 700; color: #0f172a; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 15px;">Moment 4 (Bottom Wide Image)</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label-custom">Image Preview</label>
                        <div class="preview-thumbnail-box" style="height:120px;">
                            <img src="../<?= htmlspecialchars($current_configs['restaurant_ambience_4_image']) ?>" alt="Ambience 4 Preview">
                        </div>
                        <label class="form-label-custom">Upload Image</label>
                        <input type="file" name="restaurant_ambience_4_image" class="form-control-custom" style="padding: 6px 14px;">
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Caption Title *</label>
                            <input type="text" name="restaurant_ambience_4_title" class="form-control-custom" value="<?= htmlspecialchars($current_configs['restaurant_ambience_4_title']) ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Submit block -->
    <div class="mt-25">
        <button class="btn btn-black text-white px-35 py-12" type="submit" style="border-radius: 8px; font-weight:700; padding: 10px 24px;">
            Save Changes
        </button>
    </div>
</form>

<?php 
require_once __DIR__ . '/includes/footer.php'; 
ob_end_flush();
?>
