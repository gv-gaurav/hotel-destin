<?php
ob_start();
require_once __DIR__ . '/includes/header.php';

$success_message = '';
$error_message = '';

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'add') $success_message = 'Physical room added successfully!';
    if ($_GET['success'] === 'edit') $success_message = 'Physical room updated successfully!';
    if ($_GET['success'] === 'delete') $success_message = 'Physical room deleted successfully.';
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') $error_message = 'Security check failed. Please refresh and try again.';
    else if ($_GET['error'] === 'add') $error_message = 'Failed to create room (room number might be duplicate).';
    else if ($_GET['error'] === 'edit') $error_message = 'Failed to update physical room details.';
    else if ($_GET['error'] === 'delete') $error_message = 'Failed to delete room. Booking logs reference this physical room.';
    else if ($_GET['error'] === 'req') $error_message = 'Room number and Category are required.';
}

// Handle Room Form CRUD Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: physical-rooms.php?error=csrf");
        exit;
    } else {
        $action = $_POST['action'];

        if ($action === 'add' || $action === 'edit') {
            $room_number = isset($_POST['room_number']) ? htmlspecialchars(trim($_POST['room_number'])) : '';
            $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
            $status = isset($_POST['status']) ? trim($_POST['status']) : 'Available';

            $allowed_statuses = ['Available', 'Booked', 'Occupied', 'Cleaning', 'Maintenance', 'Out of Service'];
            if (!in_array($status, $allowed_statuses)) {
                $status = 'Available';
            }

            if (empty($room_number) || $category_id <= 0) {
                header("Location: physical-rooms.php?error=req");
                exit;
            } else {
                if ($action === 'add') {
                    try {
                        $stmt = $pdo->prepare("INSERT INTO physical_rooms (room_number, category_id, status) VALUES (?, ?, ?)");
                        $stmt->execute([$room_number, $category_id, $status]);
                        header("Location: physical-rooms.php?success=add");
                        exit;
                    } catch (Exception $e) {
                        error_log("Physical room insertion failure: " . $e->getMessage());
                        header("Location: physical-rooms.php?error=add");
                        exit;
                    }
                } else if ($action === 'edit') {
                    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
                    try {
                        $stmt = $pdo->prepare("UPDATE physical_rooms SET room_number = ?, category_id = ?, status = ? WHERE id = ?");
                        $stmt->execute([$room_number, $category_id, $status, $id]);
                        header("Location: physical-rooms.php?success=edit");
                        exit;
                    } catch (Exception $e) {
                        error_log("Physical room edit failure: " . $e->getMessage());
                        header("Location: physical-rooms.php?error=edit");
                        exit;
                    }
                }
            }
        } else if ($action === 'delete') {
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            try {
                $stmt = $pdo->prepare("DELETE FROM physical_rooms WHERE id = ?");
                $stmt->execute([$id]);
                header("Location: physical-rooms.php?success=delete");
                exit;
            } catch (Exception $e) {
                error_log("Physical room deletion error: " . $e->getMessage());
                header("Location: physical-rooms.php?error=delete");
                exit;
            }
        }
    }
}

// Fetch categories for select dropdown
$categories = [];
try {
    $categories = $pdo->query("SELECT id, title FROM rooms ORDER BY title ASC")->fetchAll();
} catch (Exception $e) {
    error_log("Categories loading error: " . $e->getMessage());
}

// Fetch physical rooms from DB
$physical_rooms = [];
try {
    $physical_rooms = $pdo->query("
        SELECT pr.*, r.title as category_title 
        FROM physical_rooms pr 
        JOIN rooms r ON pr.category_id = r.id 
        ORDER BY pr.room_number ASC
    ")->fetchAll();
} catch (Exception $e) {
    error_log("Physical rooms loading error: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Manage Physical Rooms Inventory</h1>
        <p class="text-sm text-neutral-500 mt-5">Create individual physical rooms, link them to parent category room tariffs, and set housecleaning/maintenance status.</p>
    </div>
    <button class="btn btn-black text-white" onclick="showAddModal()" style="padding: 10px 24px; border-radius: 8px; font-size:14px;">
        Add Physical Room
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

<!-- Bootstrap Modal Form for Add/Edit Physical Rooms -->
<div class="modal fade" id="physicalRoomModal" tabindex="-1" aria-labelledby="physicalRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border:none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0" style="padding: 25px 30px 10px 30px;">
                <h3 class="modal-title font-heading" id="physicalRoomModalLabel" style="font-size: 20px; color: #0f172a;">Add Physical Room</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="physicalRoomEditor" action="physical-rooms.php" method="POST">
                <div class="modal-body" style="padding: 10px 30px 30px 30px;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" id="formAction" name="action" value="add">
                    <input type="hidden" id="roomIdInput" name="id" value="">

                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Room Number * (e.g. 101, 204B)</label>
                                <input id="roomNumber" class="form-control-custom" type="text" name="room_number" placeholder="e.g. 101" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">Room Category *</label>
                                <select id="roomCategory" class="form-control-custom" name="category_id" required style="height:46px; background-position: right 15px center;">
                                    <option value="">Select Parent Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label-custom">PMS Housekeeping Status</label>
                                <select id="roomStatus" class="form-control-custom" name="status" style="height:46px; background-position: right 15px center;">
                                    <option value="Available">Available</option>
                                    <option value="Booked">Booked</option>
                                    <option value="Occupied">Occupied</option>
                                    <option value="Cleaning">Cleaning</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Out of Service">Out of Service</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 d-flex gap-10" style="padding: 0 30px 30px 30px;">
                    <button class="btn btn-black text-white" type="submit" style="padding: 10px 24px; border-radius: 8px; margin: 0;">Save Room</button>
                    <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal" style="padding: 10px 24px; border-radius: 8px; border-color:#ccc; margin: 0;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Physical Rooms Table List -->
<div class="panel-card">
    <h3 class="font-heading mb-20" style="font-size:18px;">Physical Room Registries</h3>
    
    <div class="table-responsive">
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Linked Category</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($physical_rooms) > 0): ?>
                    <?php foreach ($physical_rooms as $pr): ?>
                        <tr>
                            <td>
                                <strong style="font-size:16px; color:#9c6047;">Room <?= htmlspecialchars($pr['room_number']) ?></strong>
                            </td>
                            <td><?= htmlspecialchars($pr['category_title']) ?></td>
                            <td>
                                <?php
                                $status_colors = [
                                    'Available' => '#eef7f0; color:#3c7a4b;',
                                    'Booked' => '#eff6ff; color:#1d4ed8;',
                                    'Occupied' => '#fffbeb; color:#b45309;',
                                    'Cleaning' => '#f5f5f4; color:#575757;',
                                    'Maintenance' => '#fff0f0; color:#d13232;',
                                    'Out of Service' => '#fff0f0; color:#991b1b;'
                                ];
                                $color = isset($status_colors[$pr['status']]) ? $status_colors[$pr['status']] : '#fafafa; color:#555;';
                                ?>
                                <span class="badge" style="background: <?= $color ?> font-size:11px; padding:5px 10px; font-weight:700;">
                                    <?= htmlspecialchars($pr['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center" style="gap: 12px;">
                                    <button class="btn-edit" onclick="editPhysicalRoom(<?= htmlspecialchars(json_encode($pr)) ?>)">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px; margin-top:-2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>Edit
                                    </button>
                                    
                                    <form action="physical-rooms.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this physical room?')" style="display:inline; margin:0;">
                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $pr['id'] ?>">
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
                        <td colspan="4" class="text-center py-30 text-neutral-500">No physical rooms registered.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    let physicalRoomModalObj = null;

    function initPhysicalRoomModal() {
        if (!physicalRoomModalObj) {
            physicalRoomModalObj = new bootstrap.Modal(document.getElementById('physicalRoomModal'));
        }
        return physicalRoomModalObj;
    }

    function showAddModal() {
        document.getElementById('physicalRoomModalLabel').innerText = 'Add Physical Room';
        document.getElementById('formAction').value = 'add';
        document.getElementById('roomIdInput').value = '';
        
        document.getElementById('roomNumber').value = '';
        document.getElementById('roomCategory').value = '';
        document.getElementById('roomStatus').value = 'Available';

        initPhysicalRoomModal().show();
    }

    function editPhysicalRoom(room) {
        document.getElementById('physicalRoomModalLabel').innerText = 'Edit Physical Room Details';
        document.getElementById('formAction').value = 'edit';
        document.getElementById('roomIdInput').value = room.id;

        document.getElementById('roomNumber').value = room.room_number;
        document.getElementById('roomCategory').value = room.category_id;
        document.getElementById('roomStatus').value = room.status;

        initPhysicalRoomModal().show();
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
