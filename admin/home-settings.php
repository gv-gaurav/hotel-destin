<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';
$active_tab = 'settings';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'settings') {
        $success_message = 'Homepage hero and announcement settings updated successfully!';
        $active_tab = 'settings';
    }
    if ($_GET['success'] === 'add') {
        $success_message = 'Testimonial review added successfully!';
        $active_tab = 'testimonials';
    }
    if ($_GET['success'] === 'edit') {
        $success_message = 'Testimonial details updated successfully!';
        $active_tab = 'testimonials';
    }
    if ($_GET['success'] === 'delete') {
        $success_message = 'Testimonial deleted successfully.';
        $active_tab = 'testimonials';
    }
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') {
        $error_message = 'Security check failed. Please refresh and try again.';
    } else if ($_GET['error'] === 'settings') {
        $error_message = 'Failed to update homepage settings.';
        $active_tab = 'settings';
    } else if ($_GET['error'] === 'format') {
        $error_message = 'Invalid background format! Only JPG, JPEG, PNG, WEBP, and GIF are allowed.';
        $active_tab = 'settings';
    } else if ($_GET['error'] === 'size') {
        $error_message = 'Background image is too large! Maximum size allowed is 5MB.';
        $active_tab = 'settings';
    } else if ($_GET['error'] === 'upload') {
        $error_message = 'Failed to save uploaded background image file on the server.';
        $active_tab = 'settings';
    } else if ($_GET['error'] === 'add') {
        $error_message = 'Failed to add testimonial.';
        $active_tab = 'testimonials';
    } else if ($_GET['error'] === 'edit') {
        $error_message = 'Failed to update testimonial details.';
        $active_tab = 'testimonials';
    } else if ($_GET['error'] === 'delete') {
        $error_message = 'Failed to delete testimonial.';
        $active_tab = 'testimonials';
    } else if ($_GET['error'] === 'req') {
        $error_message = 'All required fields must be completed.';
        $active_tab = 'testimonials';
    }
}

// Handle Form CRUD actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: home-settings.php?error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'update_settings') {
            $announcement_text = isset($_POST['announcement_text']) ? trim($_POST['announcement_text']) : '';
            $announcement_status = isset($_POST['announcement_status']) ? trim($_POST['announcement_status']) : 'active';
            $hero_title = isset($_POST['hero_title']) ? trim($_POST['hero_title']) : '';
            $hero_subtitle = isset($_POST['hero_subtitle']) ? trim($_POST['hero_subtitle']) : '';

            if (empty($announcement_text) || empty($hero_title) || empty($hero_subtitle)) {
                header("Location: home-settings.php?error=req&tab=settings");
                exit;
            } else {
                try {
                    $hero_bg_image = get_setting('hero_bg_image', 'assets/imgs/page/homepage7/banner.png');
                    if (isset($_FILES['hero_bg_file']) && $_FILES['hero_bg_file']['error'] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['hero_bg_file']['tmp_name'];
                        $file_name = $_FILES['hero_bg_file']['name'];
                        $file_size = $_FILES['hero_bg_file']['size'];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                        $max_size = 5 * 1024 * 1024; // 5MB

                        if (!in_array($file_ext, $allowed_extensions)) {
                            header("Location: home-settings.php?error=format&tab=settings");
                            exit;
                        } else if ($file_size > $max_size) {
                            header("Location: home-settings.php?error=size&tab=settings");
                            exit;
                        } else {
                            $upload_dir = __DIR__ . '/../uploads/';
                            if (!is_dir($upload_dir)) {
                                mkdir($upload_dir, 0755, true);
                            }

                            $new_filename = 'hero_bg_' . uniqid('', true) . '.' . $file_ext;
                            $dest_path = $upload_dir . $new_filename;
                            $db_bg_path = 'uploads/' . $new_filename;

                            if (move_uploaded_file($file_tmp, $dest_path)) {
                                if (strpos($hero_bg_image, 'uploads/') === 0) {
                                    $old_file_path = __DIR__ . '/../' . $hero_bg_image;
                                    if (file_exists($old_file_path)) {
                                        @unlink($old_file_path);
                                    }
                                }
                                $hero_bg_image = $db_bg_path;
                            } else {
                                header("Location: home-settings.php?error=upload&tab=settings");
                                exit;
                            }
                        }
                    }

                    $stmt = $pdo->prepare("INSERT INTO settings (key_name, val_content) VALUES (?, ?) ON DUPLICATE KEY UPDATE val_content = VALUES(val_content)");
                    $stmt->execute(['announcement_text', $announcement_text]);
                    $stmt->execute(['announcement_status', $announcement_status]);
                    $stmt->execute(['hero_title', $hero_title]);
                    $stmt->execute(['hero_subtitle', $hero_subtitle]);
                    $stmt->execute(['hero_bg_image', $hero_bg_image]);

                    header("Location: home-settings.php?success=settings");
                    exit;
                } catch (Exception $e) {
                    error_log("Home settings update error: " . $e->getMessage());
                    header("Location: home-settings.php?error=settings");
                    exit;
                }
            }
        } else if ($action === 'add' || $action === 'edit') {
            $client_name = isset($_POST['client_name']) ? trim(htmlspecialchars($_POST['client_name'])) : '';
            $location = isset($_POST['location']) ? trim(htmlspecialchars($_POST['location'])) : '';
            $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
            $review_text = isset($_POST['review_text']) ? trim(htmlspecialchars($_POST['review_text'])) : '';
            $image_path = isset($_POST['image_path']) ? trim($_POST['image_path']) : 'assets/imgs/page/homepage1/avatar-placeholder.svg';
            $status = isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active';

            if (empty($client_name) || empty($location) || empty($review_text)) {
                header("Location: home-settings.php?error=req");
                exit;
            } else {
                if ($action === 'add') {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO testimonials (client_name, location, rating, review_text, image_path, status) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$client_name, $location, $rating, $review_text, $image_path, $status]);
                        header("Location: home-settings.php?success=add");
                        exit;
                    } catch (Exception $e) {
                        error_log("Testimonial addition error: " . $e->getMessage());
                        header("Location: home-settings.php?error=add");
                        exit;
                    }
                } else if ($action === 'edit') {
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    try {
                        $stmt = $pdo->prepare("UPDATE testimonials SET client_name = ?, location = ?, rating = ?, review_text = ?, image_path = ?, status = ? WHERE id = ?");
                        $stmt->execute([$client_name, $location, $rating, $review_text, $image_path, $status, $id]);
                        header("Location: home-settings.php?success=edit");
                        exit;
                    } catch (Exception $e) {
                        error_log("Testimonial update error: " . $e->getMessage());
                        header("Location: home-settings.php?error=edit");
                        exit;
                    }
                }
            }
        } else if ($action === 'delete') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            try {
                $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: home-settings.php?success=delete");
                exit;
            } catch (Exception $e) {
                error_log("Testimonial deletion error: " . $e->getMessage());
                header("Location: home-settings.php?error=delete");
                exit;
            }
        }
    }
}

// Load configurations
$home_configs = [
    'announcement_text' => '🌟 Experience Comfort & Luxury at Hotel Destin • Book Your Stay Today',
    'announcement_status' => 'active',
    'hero_title' => 'Experience Luxury & Comfort',
    'hero_subtitle' => 'in the heart of Gwalior',
    'hero_bg_image' => 'assets/imgs/page/homepage7/banner.png'
];

try {
    foreach ($home_configs as $key => $val) {
        $loaded_val = get_setting($key);
        if ($loaded_val !== '') {
            $home_configs[$key] = $loaded_val;
        }
    }
} catch (Exception $e) {
    error_log("Loading home settings error: " . $e->getMessage());
}

// Fetch testimonials
$testimonials = [];
try {
    $testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {
    error_log("Testimonials loading error: " . $e->getMessage());
}

// Read URL override parameter for active tab
if (isset($_GET['tab']) && ($_GET['tab'] === 'settings' || $_GET['tab'] === 'testimonials')) {
    $active_tab = $_GET['tab'];
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Home Page Content Editor</h1>
        <p class="text-sm text-neutral-500 mt-5">Customize hero slogans, toggle the top announcement bar, and manage guest review testimonials.</p>
    </div>
    
    <?php if ($active_tab === 'testimonials'): ?>
        <button class="btn btn-black text-white" onclick="showAddModal()" style="padding: 10px 24px; border-radius: 8px; font-size:14px;">
            Add Testimonial
        </button>
    <?php endif; ?>
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

<!-- Tab Selection Links -->
<ul class="nav nav-tabs mb-30" id="homeSettingsTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a href="home-settings.php?tab=settings" class="nav-link <?= $active_tab === 'settings' ? 'active' : '' ?>" style="font-weight:600; padding:12px 20px; font-size:14.5px; border:none; border-bottom:3px solid transparent; text-decoration:none; display:inline-block;">
            Hero & Announcement Bar
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a href="home-settings.php?tab=testimonials" class="nav-link <?= $active_tab === 'testimonials' ? 'active' : '' ?>" style="font-weight:600; padding:12px 20px; font-size:14.5px; border:none; border-bottom:3px solid transparent; text-decoration:none; display:inline-block;">
            Guest Testimonials (<?= count($testimonials) ?>)
        </a>
    </li>
</ul>

<div class="tab-content" id="homeSettingsTabContent">
    
    <!-- Tab 1: Hero & Top Bar Settings -->
    <?php if ($active_tab === 'settings'): ?>
        <div class="tab-pane fade show active" id="settings" role="tabpanel">
            <form action="home-settings.php?tab=settings" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="panel-card">
                    <h3 class="font-heading" style="font-size:18px;">Announcement Bar Options</h3>
                    
                    <div class="row">
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label-custom">Announcement Bar Text *</label>
                                <input class="form-control-custom" type="text" name="announcement_text" value="<?= htmlspecialchars($home_configs['announcement_text']) ?>" required placeholder="e.g. 🌟 Experience Comfort & Luxury at Hotel Destin...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label-custom">Visibility Status *</label>
                                <select class="form-control-custom" name="announcement_status" style="height:42px !important; padding:8px 12px;">
                                    <option value="active" <?= $home_configs['announcement_status'] === 'active' ? 'selected' : '' ?>>Active (Visible)</option>
                                    <option value="inactive" <?= $home_configs['announcement_status'] === 'inactive' ? 'selected' : '' ?>>Inactive (Hidden)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="panel-card mt-25">
                    <h3 class="font-heading" style="font-size:18px;">Homepage Hero Slogans</h3>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Main Hero Title Text *</label>
                                <input class="form-control-custom" type="text" name="hero_title" value="<?= htmlspecialchars($home_configs['hero_title']) ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label-custom">Hero Subtitle Text *</label>
                                <input class="form-control-custom" type="text" name="hero_subtitle" value="<?= htmlspecialchars($home_configs['hero_subtitle']) ?>" required>
                            </div>
                        </div>
                    </div>
                <div class="panel-card mt-25">
                    <h3 class="font-heading" style="font-size:18px;">Hero Background Image</h3>
                    
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label class="form-label-custom d-block">Current Background</label>
                                <img src="../<?= htmlspecialchars($home_configs['hero_bg_image']) ?>" alt="Hero BG Preview" style="max-width: 100%; height: auto; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label-custom">Upload New Background Image <span style="font-weight:normal; font-size:11.5px; color:#64748b;">(Allowed: JPG, JPEG, PNG, WEBP, GIF | Max 5MB)</span></label>
                                <input class="form-control-custom" type="file" name="hero_bg_file" style="height:auto !important; padding:8px 12px;">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-30 text-start">
                    <button type="submit" class="btn btn-black text-white" style="padding: 12px 30px; border-radius:8px; font-weight:700; font-size:14.5px;">
                        Save Homepage Changes
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Tab 2: Guest Testimonials Manager -->
    <?php if ($active_tab === 'testimonials'): ?>
        <div class="tab-pane fade show active" id="testimonials" role="tabpanel">
            <div class="panel-card">
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Client Name</th>
                                <th style="width: 15%;">Location</th>
                                <th style="width: 12%;">Rating Stars</th>
                                <th style="width: 38%;">Review Text</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 10%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($testimonials) > 0): ?>
                                <?php foreach ($testimonials as $t): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="../<?= htmlspecialchars($t['image_path']) ?>" alt="Author" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                                <span style="font-weight: 700;"><?= htmlspecialchars($t['client_name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($t['location']) ?></td>
                                        <td>
                                            <span style="color:#eab308; font-weight: 700;"><?= str_repeat('★', $t['rating']) ?><?= str_repeat('☆', 5 - $t['rating']) ?></span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 400px;" title="<?= htmlspecialchars($t['review_text']) ?>">
                                                <?= htmlspecialchars($t['review_text']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: <?= $t['status'] === 'active' ? '#eef7f0; color:#3c7a4b;' : '#fff0f0; color:#d13232;' ?> font-size:11px; padding:5px 10px; font-weight:700;">
                                                <?= htmlspecialchars($t['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <button class="btn-edit" onclick="editTestimonial(<?= htmlspecialchars(json_encode($t)) ?>)">
                                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit
                                                </button>
                                                
                                                <form action="home-settings.php?tab=testimonials" method="POST" onsubmit="return confirm('Are you sure you want to delete this testimonial?')" style="display:inline; margin:0;">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                    <button class="btn-delete" type="submit">
                                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-30 text-neutral-500">No guest testimonials found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Form Dialog for Add/Edit Testimonial -->
<div class="modal fade" id="testimonialModal" tabindex="-1" aria-labelledby="testimonialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:12px; border:none; box-shadow:0 15px 35px rgba(0,0,0,0.15);">
            <form action="home-settings.php?tab=testimonials" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="testimonialIdInput" value="">

                <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 18px 24px;">
                    <h5 class="modal-title font-heading" id="testimonialModalLabel" style="font-size:18px; font-weight:700; color:#0f172a;">Add Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 24px;">
                    <div class="form-group">
                        <label class="form-label-custom">Client Name *</label>
                        <input type="text" class="form-control-custom" id="clientName" name="client_name" required placeholder="e.g. Rahul Sharma">
                    </div>
                    <div class="form-group mt-15">
                        <label class="form-label-custom">Location *</label>
                        <input type="text" class="form-control-custom" id="clientLocation" name="location" required placeholder="e.g. New Delhi, India">
                    </div>
                    <div class="form-group mt-15">
                        <label class="form-label-custom">Rating Stars *</label>
                        <select class="form-control-custom" id="clientRating" name="rating" style="height:42px !important; padding:8px 12px;">
                            <option value="5">5 Stars (★★★★★)</option>
                            <option value="4">4 Stars (★★★★☆)</option>
                            <option value="3">3 Stars (★★★☆☆)</option>
                            <option value="2">2 Stars (★★☆☆☆)</option>
                            <option value="1">1 Star (★☆☆☆☆)</option>
                        </select>
                    </div>
                    <div class="form-group mt-15">
                        <label class="form-label-custom">Client Avatar Icon *</label>
                        <select class="form-control-custom" id="clientImage" name="image_path" style="height:42px !important; padding:8px 12px;">
                            <option value="assets/imgs/page/homepage1/avatar-placeholder.svg">Standard Grey Silhouette (avatar-placeholder.svg)</option>
                            <option value="assets/imgs/page/homepage1/author.png">Avatar Male 1 (author.png)</option>
                            <option value="assets/imgs/page/homepage1/author2.png">Avatar Female (author2.png)</option>
                            <option value="assets/imgs/page/homepage1/author3.png">Avatar Male 2 (author3.png)</option>
                        </select>
                    </div>
                    <div class="form-group mt-15">
                        <label class="form-label-custom">Visibility Status *</label>
                        <select class="form-control-custom" id="clientStatus" name="status" style="height:42px !important; padding:8px 12px;">
                            <option value="active">Active (Visible)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                    <div class="form-group mt-15">
                        <label class="form-label-custom">Review Text *</label>
                        <textarea class="form-control-custom" id="clientReviewText" name="review_text" rows="5" required placeholder="Write the guest review comments here..." style="min-height:100px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 18px 24px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background:#f1f5f9; color:#475569; border:none; padding:10px 20px; font-weight:600; border-radius:8px;">Cancel</button>
                    <button type="submit" class="btn btn-black text-white" style="padding:10px 24px; font-weight:600; border-radius:8px;">Save Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let testimonialModalObj = null;

    function initTestimonialModal() {
        if (!testimonialModalObj) {
            testimonialModalObj = new bootstrap.Modal(document.getElementById('testimonialModal'));
        }
        return testimonialModalObj;
    }

    function showAddModal() {
        document.getElementById('testimonialModalLabel').innerText = 'Add Testimonial';
        document.getElementById('formAction').value = 'add';
        document.getElementById('testimonialIdInput').value = '';
        
        document.getElementById('clientName').value = '';
        document.getElementById('clientLocation').value = '';
        document.getElementById('clientRating').value = '5';
        document.getElementById('clientImage').value = 'assets/imgs/page/homepage1/avatar-placeholder.svg';
        document.getElementById('clientStatus').value = 'active';
        document.getElementById('clientReviewText').value = '';

        initTestimonialModal().show();
    }

    function editTestimonial(t) {
        document.getElementById('testimonialModalLabel').innerText = 'Edit Testimonial Details';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('testimonialIdInput').value = t.id;

        document.getElementById('clientName').value = t.client_name;
        document.getElementById('clientLocation').value = t.location;
        document.getElementById('clientRating').value = t.rating;
        document.getElementById('clientImage').value = t.image_path;
        document.getElementById('clientStatus').value = t.status;
        document.getElementById('clientReviewText').value = t.review_text;

        initTestimonialModal().show();
    }
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
