<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'add') $success_message = 'Room category added successfully!';
    if ($_GET['success'] === 'edit') $success_message = 'Room category updated successfully!';
    if ($_GET['success'] === 'delete') $success_message = 'Room category deleted successfully.';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'add') $error_message = 'Failed to create room category (slug might be duplicate).';
    else if ($_GET['error'] === 'edit') $error_message = 'Failed to update room details.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete room. Stays/bookings references exist.';
    else if ($_GET['error'] === 'req') $error_message = 'Title, room type, and pricing are required.';
}

// Handle Room Form CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: rooms.php?error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $title = isset($_POST['title']) ? htmlspecialchars(trim($_POST['title'])) : '';
            $type = isset($_POST['type']) ? htmlspecialchars(trim($_POST['type'])) : '';
            $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
            $struck_price = isset($_POST['struck_price']) ? floatval($_POST['struck_price']) : 0;
            $discount = isset($_POST['discount']) ? htmlspecialchars(trim($_POST['discount'])) : '';
            $code = isset($_POST['code']) ? htmlspecialchars(trim($_POST['code'])) : 'DESTIN';
            $inventory = isset($_POST['inventory']) ? intval($_POST['inventory']) : 1;
            $adults = isset($_POST['capacity_adults']) ? intval($_POST['capacity_adults']) : 2;
            $children = isset($_POST['capacity_children']) ? intval($_POST['capacity_children']) : 1;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $status = isset($_POST['status']) && $_POST['status'] === 'inactive' ? 'inactive' : 'active';

            // New dynamic fields
            $price_single_ep = isset($_POST['price_single_ep']) ? floatval($_POST['price_single_ep']) : 0;
            $price_single_cp = isset($_POST['price_single_cp']) ? floatval($_POST['price_single_cp']) : 0;
            $price_single_map = isset($_POST['price_single_map']) ? floatval($_POST['price_single_map']) : 0;
            $price_double_ep = isset($_POST['price_double_ep']) ? floatval($_POST['price_double_ep']) : 0;
            $price_double_cp = isset($_POST['price_double_cp']) ? floatval($_POST['price_double_cp']) : 0;
            $price_double_map = isset($_POST['price_double_map']) ? floatval($_POST['price_double_map']) : 0;
            $status_badge = isset($_POST['status_badge']) ? htmlspecialchars(trim($_POST['status_badge'])) : 'POPULAR';
            $rating = isset($_POST['rating']) ? htmlspecialchars(trim($_POST['rating'])) : 'G 4.8 ★';
            $banner_text = isset($_POST['banner_text']) ? htmlspecialchars(trim($_POST['banner_text'])) : '';

            // Build slug from title
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));

            if (empty($title) || empty($type) || $price <= 0) {
                header("Location: rooms.php?error=req");
                exit;
            } else {
                if ($action === 'add') {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO rooms (slug, title, type, price, struck_price, discount, code, inventory, capacity_adults, capacity_children, description, status, price_single_ep, price_single_cp, price_single_map, price_double_ep, price_double_cp, price_double_map, status_badge, rating, banner_text) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$slug, $title, $type, $price, $struck_price, $discount, $code, $inventory, $adults, $children, $description, $status, $price_single_ep, $price_single_cp, $price_single_map, $price_double_ep, $price_double_cp, $price_double_map, $status_badge, $rating, $banner_text]);

                        // Get last inserted room ID to add facilities
                        $new_room_id = $pdo->lastInsertId();

                        // Save amenities
                        if (isset($_POST['facilities'])) {
                            $facilities = explode(',', $_POST['facilities']);
                            $facility_stmt = $pdo->prepare("INSERT INTO room_facilities (room_id, facility_name) VALUES (?, ?)");
                            foreach ($facilities as $fac) {
                                $fac = trim($fac);
                                if (!empty($fac)) {
                                    $facility_stmt->execute([$new_room_id, $fac]);
                                }
                            }
                        } else {
                            $facility_stmt = $pdo->prepare("INSERT INTO room_facilities (room_id, facility_name) VALUES (?, 'AC'), (?, 'Free Wi-Fi'), (?, 'Laundry'), (?, 'King Bed')");
                            $facility_stmt->execute([$new_room_id, $new_room_id, $new_room_id, $new_room_id]);
                        }

                        // Process main image upload for ADD
                        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                            $fileTmpPath = $_FILES['main_image']['tmp_name'];
                            $fileName = $_FILES['main_image']['name'];
                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                            $uploadFileDir = __DIR__ . '/../uploads/rooms/';
                            if (!is_dir($uploadFileDir)) {
                                mkdir($uploadFileDir, 0777, true);
                            }
                            $dest_path = $uploadFileDir . $newFileName;
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $image_path = 'uploads/rooms/' . $newFileName;
                                $pdo->prepare("UPDATE rooms SET image_path = ? WHERE id = ?")->execute([$image_path, $new_room_id]);
                            }
                        }

                        // Process gallery images upload for ADD
                        if (isset($_FILES['gallery_images'])) {
                            $files = $_FILES['gallery_images'];
                            $file_count = count($files['name']);
                            for ($i = 0; $i < $file_count; $i++) {
                                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                                    $fileTmpPath = $files['tmp_name'][$i];
                                    $fileName = $files['name'][$i];
                                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                    $newFileName = md5(time() . $fileName . $i) . '.' . $fileExtension;
                                    $uploadFileDir = __DIR__ . '/../uploads/rooms/';
                                    if (!is_dir($uploadFileDir)) {
                                        mkdir($uploadFileDir, 0777, true);
                                    }
                                    $dest_path = $uploadFileDir . $newFileName;
                                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                        $gallery_path = 'uploads/rooms/' . $newFileName;
                                        $pdo->prepare("INSERT INTO room_images (room_id, image_path) VALUES (?, ?)")->execute([$new_room_id, $gallery_path]);
                                    }
                                }
                            }
                        }

                        header("Location: rooms.php?success=add");
                        exit;
                    } catch (Exception $e) {
                        error_log("Room insertion failure: " . $e->getMessage());
                        header("Location: rooms.php?error=add");
                        exit;
                    }
                } else if ($action === 'edit') {
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    try {
                        $stmt = $pdo->prepare("UPDATE rooms SET title = ?, type = ?, price = ?, struck_price = ?, discount = ?, code = ?, inventory = ?, capacity_adults = ?, capacity_children = ?, description = ?, status = ?, price_single_ep = ?, price_single_cp = ?, price_single_map = ?, price_double_ep = ?, price_double_cp = ?, price_double_map = ?, status_badge = ?, rating = ?, banner_text = ? WHERE id = ?");
                        $stmt->execute([$title, $type, $price, $struck_price, $discount, $code, $inventory, $adults, $children, $description, $status, $price_single_ep, $price_single_cp, $price_single_map, $price_double_ep, $price_double_cp, $price_double_map, $status_badge, $rating, $banner_text, $id]);

                        // Process amenities update
                        if (isset($_POST['facilities'])) {
                            $facilities = explode(',', $_POST['facilities']);
                            $pdo->prepare("DELETE FROM room_facilities WHERE room_id = ?")->execute([$id]);
                            $facility_stmt = $pdo->prepare("INSERT INTO room_facilities (room_id, facility_name) VALUES (?, ?)");
                            foreach ($facilities as $fac) {
                                $fac = trim($fac);
                                if (!empty($fac)) {
                                    $facility_stmt->execute([$id, $fac]);
                                }
                            }
                        }

                        // Process main image upload for EDIT
                        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                            $fileTmpPath = $_FILES['main_image']['tmp_name'];
                            $fileName = $_FILES['main_image']['name'];
                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                            $uploadFileDir = __DIR__ . '/../uploads/rooms/';
                            if (!is_dir($uploadFileDir)) {
                                mkdir($uploadFileDir, 0777, true);
                            }
                            $dest_path = $uploadFileDir . $newFileName;
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                $image_path = 'uploads/rooms/' . $newFileName;
                                $pdo->prepare("UPDATE rooms SET image_path = ? WHERE id = ?")->execute([$image_path, $id]);
                            }
                        }

                        // Process gallery images upload for EDIT
                        if (isset($_FILES['gallery_images'])) {
                            $files = $_FILES['gallery_images'];
                            $file_count = count($files['name']);
                            for ($i = 0; $i < $file_count; $i++) {
                                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                                    $fileTmpPath = $files['tmp_name'][$i];
                                    $fileName = $files['name'][$i];
                                    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                    $newFileName = md5(time() . $fileName . $i) . '.' . $fileExtension;
                                    $uploadFileDir = __DIR__ . '/../uploads/rooms/';
                                    if (!is_dir($uploadFileDir)) {
                                        mkdir($uploadFileDir, 0777, true);
                                    }
                                    $dest_path = $uploadFileDir . $newFileName;
                                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                        $gallery_path = 'uploads/rooms/' . $newFileName;
                                        $pdo->prepare("INSERT INTO room_images (room_id, image_path) VALUES (?, ?)")->execute([$id, $gallery_path]);
                                    }
                                }
                            }
                        }

                        // Process gallery image deletion
                        if (isset($_POST['delete_gallery']) && is_array($_POST['delete_gallery'])) {
                            foreach ($_POST['delete_gallery'] as $img_id) {
                                $img_stmt = $pdo->prepare("SELECT image_path FROM room_images WHERE id = ?");
                                $img_stmt->execute([$img_id]);
                                $img_path = $img_stmt->fetchColumn();
                                if ($img_path) {
                                    $full_path = __DIR__ . '/../' . $img_path;
                                    if (file_exists($full_path)) {
                                        @unlink($full_path);
                                    }
                                }
                                $pdo->prepare("DELETE FROM room_images WHERE id = ?")->execute([$img_id]);
                            }
                        }

                        header("Location: rooms.php?success=edit");
                        exit;
                    } catch (Exception $e) {
                        error_log("Room edit failure: " . $e->getMessage());
                        header("Location: rooms.php?error=edit");
                        exit;
                    }
                }
            }
        } else if ($action === 'delete') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            try {
                $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: rooms.php?success=delete");
                exit;
            } catch (Exception $e) {
                error_log("Room deletion error: " . $e->getMessage());
                header("Location: rooms.php?error=delete");
                exit;
            }
        }
    }
}

// Fetch current rooms from DB
$rooms = [];
try {
    $rooms = $pdo->query("SELECT * FROM rooms ORDER BY id ASC")->fetchAll();
    foreach ($rooms as &$r) {
        // Fetch facilities
        $f_stmt = $pdo->prepare("SELECT facility_name FROM room_facilities WHERE room_id = ?");
        $f_stmt->execute([$r['id']]);
        $r['facilities'] = $f_stmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch gallery
        $g_stmt = $pdo->prepare("SELECT id, image_path FROM room_images WHERE room_id = ?");
        $g_stmt->execute([$r['id']]);
        $r['gallery'] = $g_stmt->fetchAll();
    }
    unset($r);
} catch (Exception $e) {
    error_log("Rooms loading error in admin page: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Room Categories</h1>
        <p class="text-sm text-neutral-500 mt-5">Create new room configurations, edit rates, set coupon percentages, and update status.</p>
    </div>
    <button class="btn btn-black text-white" onclick="showAddModal()" style="padding: 10px 24px; border-radius: 8px; font-size:14px;">
        Add Room Category
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

<!-- Room Form Card Panel (Initially Hidden) -->
<div id="roomFormCard" class="panel-card mb-35" style="display:none; border-radius: 16px; border:1px solid var(--bs-border-color); box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px;">
    <div class="d-flex justify-content-between align-items-center border-bottom-0" style="border-bottom: 1px solid var(--bs-border-color);">
        <h3 id="roomModalLabel" class="font-heading mb-0" style="font-size: 18px; color: #0f172a;">Add New Room</h3>
        <button type="button" class="btn-close" onclick="hideRoomForm()" aria-label="Close"></button>
    </div>

    <form id="roomEditor" action="rooms.php" method="POST" enctype="multipart/form-data" onsubmit="tinymce.triggerSave();">
        <div class="modal-body" style="padding: 10px 30px 30px 30px;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" id="formAction" name="action" value="add">
            <input type="hidden" id="roomIdInput" name="id" value="">

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Room Title *</label>
                        <input id="roomTitle" class="form-control-custom" type="text" name="title" placeholder="e.g. Standard Room - Hotel Destin" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Room Type * (Standard, Executive, Premium etc.)</label>
                        <input id="roomType" class="form-control-custom" type="text" name="type" placeholder="e.g. Standard" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label-custom">Default Selling Price (₹) *</label>
                        <input id="roomPrice" class="form-control-custom" type="number" name="price" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label-custom">Struck Price (₹)</label>
                        <input id="roomStruckPrice" class="form-control-custom" type="number" name="struck_price" step="0.01">
                    </div>
                </div>
                <!-- Hidden fields for compatibility -->
                <input id="roomDiscount" type="hidden" name="discount" value="">
                <input id="roomCode" type="hidden" name="code" value="DESTIN">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Promo Banner Text (Offer Text)</label>
                        <input id="roomBannerText" class="form-control-custom" type="text" name="banner_text" placeholder="e.g., Get Destin, and get 25% off (up to ₹1,000) on your booking">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label-custom">Inventory (Units) *</label>
                        <input id="roomInventory" class="form-control-custom" type="number" name="inventory" value="1" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label-custom">Adult Capacity</label>
                        <input id="roomAdults" class="form-control-custom" type="number" name="capacity_adults" value="2">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label-custom">Children Capacity</label>
                        <input id="roomChildren" class="form-control-custom" type="number" name="capacity_children" value="1">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label-custom">Active Status</label>
                        <select id="roomStatus" class="form-control-custom" name="status" style="height:50px; background-position: right 15px center;">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Amenities (dynamic facilities) -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label-custom">Amenities / Facilities (comma-separated)</label>
                        <input id="roomFacilities" class="form-control-custom" type="text" name="facilities" placeholder="e.g. AC, Free Wi-Fi, Laundry, King Bed, Safe Box">
                    </div>
                </div>

                <!-- Badges and ratings -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Card Status Badge Text</label>
                        <input id="roomStatusBadge" class="form-control-custom" type="text" name="status_badge" placeholder="e.g. BEST SELLER, POPULAR">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Card Rating Text</label>
                        <input id="roomRating" class="form-control-custom" type="text" name="rating" placeholder="e.g. G 4.8 ★">
                    </div>
                </div>

                <!-- Single occupancy meal pricing -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label-custom">Single Occupancy EP Price (₹) *</label>
                        <input id="roomPriceSingleEP" class="form-control-custom" type="number" name="price_single_ep" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label-custom">Single Occupancy CP Price (₹) *</label>
                        <input id="roomPriceSingleCP" class="form-control-custom" type="number" name="price_single_cp" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label-custom">Single Occupancy MAP Price (₹) *</label>
                        <input id="roomPriceSingleMAP" class="form-control-custom" type="number" name="price_single_map" step="0.01" required>
                    </div>
                </div>

                <!-- Double occupancy meal pricing -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label-custom">Double Occupancy EP Price (₹) *</label>
                        <input id="roomPriceDoubleEP" class="form-control-custom" type="number" name="price_double_ep" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label-custom">Double Occupancy CP Price (₹) *</label>
                        <input id="roomPriceDoubleCP" class="form-control-custom" type="number" name="price_double_cp" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label-custom">Double Occupancy MAP Price (₹) *</label>
                        <input id="roomPriceDoubleMAP" class="form-control-custom" type="number" name="price_double_map" step="0.01" required>
                    </div>
                </div>

                <!-- Main room image file upload -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Main Room Image (Upload to replace)</label>
                        <input id="roomMainImage" class="form-control-custom" type="file" name="main_image" accept="image/*">
                        <span style="font-size:11px; color:#9c6047; font-weight:600; display:block; margin-top:4px;">* Recommended: 1200x675 px (16:9 ratio) for perfect fit</span>
                        <div id="roomMainImagePrev" style="margin-top: 10px; font-size: 12px; color: #777;"></div>
                    </div>
                </div>

                <!-- Gallery images file upload -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label-custom">Gallery Images (Upload multiple)</label>
                        <input id="roomGalleryImages" class="form-control-custom" type="file" name="gallery_images[]" accept="image/*" multiple>
                        <span style="font-size:11px; color:#9c6047; font-weight:600; display:block; margin-top:4px;">* Recommended: 1200x675 px (16:9 ratio) for perfect fit</span>
                        <div id="roomGalleryPrev" style="margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap;"></div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label-custom">Description Summary</label>
                        <textarea id="roomDescription" class="form-control-custom" name="description" rows="3" placeholder="Enter short detail specifications..."></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer border-top-0 d-flex gap-10 mt-20" style="padding: 20px 0 0 0; border-top: 1px solid var(--bs-border-color);">
            <button class="btn btn-black text-white" type="submit" style="padding: 10px 24px; border-radius: 8px;">Save Details</button>
            <button class="btn btn-outline-dark" type="button" onclick="hideRoomForm()" style="padding: 10px 24px; border-radius: 8px; border-color:#ccc; margin: 0;">Cancel</button>
        </div>
    </form>
</div>

<!-- Rooms Table List -->
<div id="roomListCard" class="panel-card">
    <h3 class="font-heading mb-20" style="font-size:18px;">Active Room Categories</h3>

    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Room Details</th>
                    <th>Type</th>
                    <th>Price Status</th>
                    <th>Capacity</th>
                    <th>Inventory</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($rooms) > 0): ?>
                    <?php foreach ($rooms as $r): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($r['title']) ?></strong><br>
                                <span style="font-size:11.5px; color:#777;">Slug: <?= htmlspecialchars($r['slug']) ?></span>
                            </td>
                            <td><?= htmlspecialchars($r['type']) ?></td>
                            <td>
                                <strong>₹<?= number_format($r['price'], 2) ?></strong>
                                <?php if ($r['struck_price'] > 0): ?><br>
                                    <span style="font-size:11.5px; text-decoration:line-through; color:#aaa;">₹<?= number_format($r['struck_price'], 2) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($r['capacity_adults']) ?> Adults, <?= htmlspecialchars($r['capacity_children']) ?> Children
                            </td>
                            <td><?= htmlspecialchars($r['inventory']) ?> Units</td>
                            <td>
                                <span class="badge" style="background: <?= $r['status'] === 'active' ? '#eef7f0; color:#3c7a4b;' : '#fff0f0; color:#d13232;' ?>; font-size:10.5px; padding:4px 8px;">
                                    <?= htmlspecialchars($r['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <button class="btn-edit" onclick="editRoom(<?= htmlspecialchars(json_encode($r)) ?>)">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>Edit
                                    </button>

                                    <form action="rooms.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this room configuration?')" style="display:inline; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button class="btn-delete" type="submit">
                                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-30 text-neutral-500">No rooms configured. Add one using the button above.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function showAddModal() {
        document.getElementById('roomModalLabel').innerText = 'Add New Room';
        document.getElementById('formAction').value = 'add';
        document.getElementById('roomIdInput').value = '';

        document.getElementById('roomTitle').value = '';
        document.getElementById('roomType').value = '';
        document.getElementById('roomPrice').value = '';
        document.getElementById('roomStruckPrice').value = '';
        document.getElementById('roomDiscount').value = '';
        document.getElementById('roomCode').value = 'DESTIN';
        document.getElementById('roomBannerText').value = '';
        document.getElementById('roomInventory').value = '1';
        document.getElementById('roomAdults').value = '2';
        document.getElementById('roomChildren').value = '1';
        document.getElementById('roomDescription').value = '';
        if (typeof tinymce !== 'undefined' && tinymce.get('roomDescription')) {
            tinymce.get('roomDescription').setContent('');
        }
        document.getElementById('roomStatus').value = 'active';

        // Clear dynamic fields
        document.getElementById('roomFacilities').value = '';
        document.getElementById('roomStatusBadge').value = 'POPULAR';
        document.getElementById('roomRating').value = 'G 4.8 ★';
        document.getElementById('roomPriceSingleEP').value = '';
        document.getElementById('roomPriceSingleCP').value = '';
        document.getElementById('roomPriceSingleMAP').value = '';
        document.getElementById('roomPriceDoubleEP').value = '';
        document.getElementById('roomPriceDoubleCP').value = '';
        document.getElementById('roomPriceDoubleMAP').value = '';
        document.getElementById('roomMainImage').value = '';
        document.getElementById('roomMainImagePrev').innerHTML = '';
        document.getElementById('roomGalleryImages').value = '';
        document.getElementById('roomGalleryPrev').innerHTML = '';

        var newPrev = document.getElementById('newGalleryPrev');
        if (newPrev) newPrev.innerHTML = '';

        document.getElementById('roomListCard').style.display = 'none';
        document.getElementById('roomFormCard').style.display = 'block';
        window.scrollTo(0, 0);
    }

    function editRoom(room) {
        document.getElementById('roomModalLabel').innerText = 'Edit Room details';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('roomIdInput').value = room.id;

        document.getElementById('roomTitle').value = room.title;
        document.getElementById('roomType').value = room.type;
        document.getElementById('roomPrice').value = room.price;
        document.getElementById('roomStruckPrice').value = room.struck_price;
        document.getElementById('roomDiscount').value = room.discount;
        document.getElementById('roomCode').value = room.code;
        document.getElementById('roomBannerText').value = room.banner_text || '';
        document.getElementById('roomInventory').value = room.inventory;
        document.getElementById('roomAdults').value = room.capacity_adults;
        document.getElementById('roomChildren').value = room.capacity_children;
        document.getElementById('roomDescription').value = room.description || '';
        if (typeof tinymce !== 'undefined' && tinymce.get('roomDescription')) {
            tinymce.get('roomDescription').setContent(room.description || '');
        }
        document.getElementById('roomStatus').value = room.status;

        // Set dynamic fields
        document.getElementById('roomFacilities').value = (room.facilities || []).join(', ');
        document.getElementById('roomStatusBadge').value = room.status_badge || 'POPULAR';
        document.getElementById('roomRating').value = room.rating || 'G 4.8 ★';
        document.getElementById('roomPriceSingleEP').value = room.price_single_ep || '';
        document.getElementById('roomPriceSingleCP').value = room.price_single_cp || '';
        document.getElementById('roomPriceSingleMAP').value = room.price_single_map || '';
        document.getElementById('roomPriceDoubleEP').value = room.price_double_ep || '';
        document.getElementById('roomPriceDoubleCP').value = room.price_double_cp || '';
        document.getElementById('roomPriceDoubleMAP').value = room.price_double_map || '';
        document.getElementById('roomMainImage').value = '';

        // Display main image preview
        var mainPrev = document.getElementById('roomMainImagePrev');
        if (room.image_path) {
            mainPrev.innerHTML = '<img src="../' + room.image_path + '" style="height:50px; border-radius:4px; margin-top:5px; border:1px solid #ddd; object-fit:cover;">';
        } else {
            mainPrev.innerHTML = '<span style="font-size:12px; color:#999;">No image uploaded</span>';
        }

        // Display gallery image thumbnails with delete checkboxes
        var galleryPrev = document.getElementById('roomGalleryPrev');
        galleryPrev.innerHTML = '';
        if (room.gallery && room.gallery.length > 0) {
            room.gallery.forEach(function(img) {
                galleryPrev.innerHTML += `
                    <div style="position:relative; width:65px; height:65px; border:1px solid #ddd; border-radius:6px; overflow:hidden;">
                        <img src="../${img.image_path}" style="width:100%; height:100%; object-fit:cover;">
                        <input type="checkbox" name="delete_gallery[]" value="${img.id}" style="position:absolute; top:4px; right:4px; width:16px !important; height:16px !important; z-index:10; cursor:pointer; -webkit-appearance:checkbox !important; appearance:checkbox !important; margin:0 !important; outline:none !important; box-shadow:none !important; border:1px solid #ddd !important;" title="Check to delete on save">
                        <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(220,53,69,0.85); color:#fff; font-size:9px; text-align:center; padding:1px 0; font-weight:bold;">delete</div>
                    </div>
                `;
            });
        } else {
            galleryPrev.innerHTML = '<span style="font-size:12px; color:#999;">No gallery images uploaded</span>';
        }

        var newPrev = document.getElementById('newGalleryPrev');
        if (newPrev) newPrev.innerHTML = '';

        document.getElementById('roomListCard').style.display = 'none';
        document.getElementById('roomFormCard').style.display = 'block';
        window.scrollTo(0, 0);
    }

    function hideRoomForm() {
        document.getElementById('roomFormCard').style.display = 'none';
        document.getElementById('roomListCard').style.display = 'block';
        window.scrollTo(0, 0);
    }

    // Dynamic image selection previews (main and gallery)
    document.getElementById('roomMainImage').addEventListener('change', function(e) {
        var prev = document.getElementById('roomMainImagePrev');
        prev.innerHTML = '';
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                prev.innerHTML = '<img src="' + e.target.result + '" style="height:50px; border-radius:4px; margin-top:5px; border:1px solid #ddd; object-fit:cover;">';
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });

    document.getElementById('roomGalleryImages').addEventListener('change', function(e) {
        var prev = document.getElementById('roomGalleryPrev');
        var newPrev = document.getElementById('newGalleryPrev');
        if (!newPrev) {
            newPrev = document.createElement('div');
            newPrev.id = 'newGalleryPrev';
            newPrev.style.display = 'flex';
            newPrev.style.gap = '8px';
            newPrev.style.flexWrap = 'wrap';
            newPrev.style.width = '100%';
            newPrev.style.marginTop = '10px';
            prev.parentNode.appendChild(newPrev);
        }
        newPrev.innerHTML = '';

        if (e.target.files) {
            Array.from(e.target.files).forEach(file => {
                var reader = new FileReader();
                reader.onload = function(e) {
                    newPrev.innerHTML += `
                        <div style="width:65px; height:65px; border:1px solid #28a745; border-radius:6px; overflow:hidden; position:relative;" title="Selected for upload">
                            <img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">
                            <div style="position:absolute; bottom:0; left:0; right:0; background:rgba(40,167,69,0.85); color:#fff; font-size:9px; text-align:center; padding:1px 0; font-weight:bold;">new</div>
                        </div>
                    `;
                }
                reader.readAsDataURL(file);
            });
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Include TinyMCE from CDN (Immune to theme font overrides, uses vector SVGs) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    $(document).ready(function() {
        tinymce.init({
            selector: '#roomDescription',
            height: 250,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
            toolbar: 'undo redo | blocks | fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table | code fullscreen',
            branding: false,
            promotion: false,
            setup: function(editor) {
                editor.on('change', function() {
                    editor.save();
                });
            }
        });
    });
</script>