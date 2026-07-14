<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'add') $success_message = 'Media successfully uploaded and added to the gallery!';
    if ($_GET['success'] === 'delete') $success_message = 'Gallery media item successfully deleted.';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'format') $error_message = 'Invalid file format. Allowed: Images (JPG, JPEG, PNG, GIF, WEBP) or Videos (MP4, WEBM, MOV, M4V).';
    else if ($_GET['error'] === 'size') $error_message = 'File size exceeds limit. Max 5MB for photographs, 15MB for video clips.';
    else if ($_GET['error'] === 'db') $error_message = 'Failed to save gallery entry to database.';
    else if ($_GET['error'] === 'upload') $error_message = 'Failed to move uploaded file to target uploads directory.';
    else if ($_GET['error'] === 'invalid_file') $error_message = 'Please select a valid media file to upload.';
    else if ($_GET['error'] === 'not_found') $error_message = 'Media record not found.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete gallery item.';
}

// Handle Gallery Image/Video Addition / Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: gallery.php?error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'add') {
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
            $category = isset($_POST['category']) ? htmlspecialchars(trim($_POST['category'])) : 'rooms';
            $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
            $media_type = isset($_POST['media_type']) ? trim($_POST['media_type']) : 'image';

            // Handle Media Upload
            if (isset($_FILES['gallery_file']) && $_FILES['gallery_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['gallery_file']['tmp_name'];
                $file_name = $_FILES['gallery_file']['name'];
                $file_size = $_FILES['gallery_file']['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                if ($media_type === 'video') {
                    $allowed_extensions = ['mp4', 'webm', 'mov', 'm4v'];
                    $max_size = 15 * 1024 * 1024; // 15MB
                } else {
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                }

                if (!in_array($file_ext, $allowed_extensions)) {
                    header("Location: gallery.php?error=format");
                    exit;
                } else if ($file_size > $max_size) {
                    header("Location: gallery.php?error=size");
                    exit;
                } else {
                    // Create uploads directory if it does not exist
                    $upload_dir = __DIR__ . '/../uploads/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    // Generate safe unique filename
                    $prefix = ($media_type === 'video') ? 'vid_' : 'img_';
                    $new_filename = uniqid($prefix, true) . '.' . $file_ext;
                    $dest_path = $upload_dir . $new_filename;
                    $db_image_path = 'uploads/' . $new_filename;

                    if (move_uploaded_file($file_tmp, $dest_path)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_path, description) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$title, $category, $db_image_path, $description]);
                            header("Location: gallery.php?success=add");
                            exit;
                        } catch (Exception $e) {
                            error_log("Gallery insertion database failure: " . $e->getMessage());
                            header("Location: gallery.php?error=db");
                            exit;
                        }
                    } else {
                        header("Location: gallery.php?error=upload");
                        exit;
                    }
                }
            } else {
                header("Location: gallery.php?error=invalid_file");
                exit;
            }
        } else if ($action === 'delete') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            try {
                // Fetch image details to delete file from disk
                $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
                $stmt->execute([$id]);
                $item = $stmt->fetch();

                if ($item) {
                    $relative_path = $item['image_path'];
                    $full_filepath = __DIR__ . '/../' . $relative_path;

                    // Delete database record
                    $del = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
                    $del->execute([$id]);

                    // Remove file from disk if it exists and resides in the uploads directory
                    if (strpos($relative_path, 'uploads/') === 0 && file_exists($full_filepath)) {
                        unlink($full_filepath);
                    }

                    header("Location: gallery.php?success=delete");
                    exit;
                } else {
                    header("Location: gallery.php?error=not_found");
                    exit;
                }
            } catch (Exception $e) {
                error_log("Gallery deletion failure: " . $e->getMessage());
                header("Location: gallery.php?error=delete");
                exit;
            }
        }
    }
}

// Fetch all gallery items from DB
$gallery_items = [];
try {
    $gallery_items = $pdo->query("SELECT * FROM gallery ORDER BY id DESC")->fetchAll();
} catch (Exception $e) {
    error_log("Gallery items load failure: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Gallery</h1>
        <p class="text-sm text-neutral-500 mt-5">Upload new photographs or video clips to the Hotel Gallery showcase.</p>
    </div>
    <button class="btn btn-black text-white" onclick="showUploadForm()" style="padding: 10px 24px; border-radius: 8px; font-size:14px;">
        Upload New Media
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

<!-- Media Upload Form Card (Initially Hidden) -->
<div id="uploadFormCard" class="panel-card" style="display:none;">
    <h3 class="font-heading mb-25" style="font-size:18px;">Upload Gallery Media</h3>
    <form action="gallery.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <input type="hidden" name="action" value="add">

        <div class="row">
            <div class="col-md-12 mb-10">
                <label class="form-label-custom d-block">Media Type *</label>
                <div class="btn-group" role="group" aria-label="Media Type Selection">
                    <input type="radio" class="btn-check" name="media_type" id="mediaTypeImage" value="image" checked onclick="toggleMediaType('image')">
                    <label class="btn btn-outline-dark px-25 py-10" for="mediaTypeImage" style="border-radius: 6px 0 0 6px; font-size: 13.5px; font-weight:600; cursor:pointer;">📷 Photograph</label>

                    <input type="radio" class="btn-check" name="media_type" id="mediaTypeVideo" value="video" onclick="toggleMediaType('video')">
                    <label class="btn btn-outline-dark px-25 py-10" for="mediaTypeVideo" style="border-radius: 0 6px 6px 0; font-size: 13.5px; font-weight:600; border-left: none; cursor:pointer;">🎥 Short Video</label>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom" id="fileLabel">Select Image File *</label>
                    <input class="form-control-custom" type="file" name="gallery_file" id="galleryFile" accept="image/*" required style="padding-top:10px;">
                    <span id="fileHelper" style="font-size:11.5px; color:#777; display:block; margin-top:5px;">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB.</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">Showcase Category *</label>
                    <select class="form-control-custom" name="category" required style="padding: 10px 14px;">
                        <option value="rooms">Rooms</option>
                        <option value="banquet">Banquet & Events</option>
                        <option value="restaurant">Restaurant</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">Media Title *</label>
                    <input class="form-control-custom" type="text" name="title" id="mediaTitle" placeholder="e.g. Deluxe Room Bedding" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label-custom">Description (Optional)</label>
                    <input class="form-control-custom" type="text" name="description" placeholder="Brief details about this media">
                </div>
            </div>

            <div class="col-md-12 mt-20 d-flex gap-10">
                <button class="btn btn-black text-white" type="submit" style="padding: 10px 24px; border-radius: 8px;">Upload Media</button>
                <button class="btn btn-outline-dark" type="button" onclick="cancelUpload()" style="padding: 10px 24px; border-radius: 8px; border-color:#ccc;">Cancel</button>
            </div>
        </div>
    </form>
</div>

<!-- Gallery Images/Videos List Grid -->
<div class="panel-card">
    <h3 class="font-heading mb-25" style="font-size:18px;">Uploaded Gallery Media</h3>

    <div class="row g-4">
        <?php if (count($gallery_items) > 0): ?>
            <?php foreach ($gallery_items as $item): ?>
                <?php
                $file_path = $item['image_path'];
                $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                $is_video = in_array($ext, ['mp4', 'webm', 'mov', 'm4v']);
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff;" 
                         class="gallery-item-card" 
                         <?php if ($is_video): ?>
                         onmouseenter="var v=this.querySelector('video'); if(v)v.play();" 
                         onmouseleave="var v=this.querySelector('video'); if(v){v.pause(); v.currentTime=0;}"
                         <?php endif; ?>>
                        <div style="width: 100%; height: 180px; overflow: hidden; background: #eee; position: relative;">
                            <?php if ($is_video): ?>
                                <video src="../<?= htmlspecialchars($file_path) ?>" muted loop playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                                <span class="badge bg-warning text-dark" style="position: absolute; top: 12px; right: 12px; font-size: 11px; font-weight:700; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; z-index:2;">
                                    VIDEO
                                </span>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.6); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; pointer-events: none; z-index:2;">
                                    <svg width="20" height="20" fill="#ffffff" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            <?php else: ?>
                                <img src="../<?= htmlspecialchars($file_path) ?>" alt="Gallery item" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php endif; ?>
                            <span class="badge bg-dark text-white" style="position: absolute; top: 12px; left: 12px; font-size: 11px; text-transform: uppercase; padding: 4px 8px; border-radius: 4px; z-index:2;">
                                <?= htmlspecialchars($item['category']) ?>
                            </span>
                        </div>
                        <div style="padding: 15px;">
                            <h5 style="font-size: 14.5px; font-weight:600; margin-bottom: 5px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; color:#0f172a;"><?= htmlspecialchars($item['title']) ?></h5>
                            <p style="font-size: 12px; color: #64748b; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; margin-bottom: 15px;"><?= htmlspecialchars($item['description'] ?: 'No description provided') ?></p>
                            
                            <form action="gallery.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this gallery item?')" style="margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn-delete w-100 justify-content-center" style="padding: 8px 0 !important;">
                                    Delete Item
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 py-30 text-center text-neutral-500">No gallery items uploaded yet. Upload one using the button above.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    function showUploadForm() {
        document.getElementById('uploadFormCard').style.display = 'block';
        window.scrollTo({ top: document.getElementById('uploadFormCard').offsetTop - 30, behavior: 'smooth' });
    }
    function cancelUpload() {
        document.getElementById('uploadFormCard').style.display = 'none';
    }
    function toggleMediaType(type) {
        const fileInput = document.getElementById('galleryFile');
        const fileLabel = document.getElementById('fileLabel');
        const fileHelper = document.getElementById('fileHelper');
        const mediaTitle = document.getElementById('mediaTitle');

        if (type === 'video') {
            fileInput.accept = 'video/mp4,video/webm,video/quicktime,video/x-m4v';
            fileLabel.textContent = 'Select Video File *';
            fileHelper.textContent = 'Allowed formats: MP4, WEBM, MOV, M4V. Max size: 15MB.';
            mediaTitle.placeholder = 'e.g. Deluxe Suite Video Walkthrough';
        } else {
            fileInput.accept = 'image/*';
            fileLabel.textContent = 'Select Image File *';
            fileHelper.textContent = 'Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 5MB.';
            mediaTitle.placeholder = 'e.g. Deluxe Room Bedding';
        }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
