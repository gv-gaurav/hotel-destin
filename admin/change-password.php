<?php
ob_start();
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../mail-helper.php';

$success_message = '';
$error_message = '';

if (isset($_SESSION['pwd_success'])) {
    $success_message = $_SESSION['pwd_success'];
    unset($_SESSION['pwd_success']);
}
if (isset($_SESSION['pwd_error'])) {
    $error_message = $_SESSION['pwd_error'];
    unset($_SESSION['pwd_error']);
}

// Step 1: Handle Initial Password Reset request and dispatch OTP email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_otp') {
    $current_pass = isset($_POST['current_password']) ? trim($_POST['current_password']) : '';
    $new_pass = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        $_SESSION['pwd_error'] = 'Security check failed. Please refresh and try again.';
        header("Location: change-password.php");
        exit;
    } else if (empty($current_pass) || empty($new_pass)) {
        $_SESSION['pwd_error'] = 'Both current and new passwords are required.';
        header("Location: change-password.php");
        exit;
    } else if (strlen($new_pass) < 6) {
        $_SESSION['pwd_error'] = 'New password must be at least 6 characters long.';
        header("Location: change-password.php");
        exit;
    } else {
        try {
            // Retrieve current admin details
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($current_pass, $admin['password'])) {
                // Generate secure 6-digit OTP code and expiry (10 minutes)
                $otp = strval(rand(100000, 999999));
                $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                // Save OTP in Database
                $upd = $pdo->prepare("UPDATE admins SET otp_code = ?, otp_expires_at = ? WHERE id = ?");
                $upd->execute([$otp, $expiry, $_SESSION['admin_id']]);

                // Store passwords temporarily in session
                $_SESSION['pwd_reset_temp'] = [
                    'new_hash' => password_hash($new_pass, PASSWORD_BCRYPT)
                ];

                // Send email
                $subject = "Security Verification OTP - Hotel Destin";
                $body = "
                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #e9ecf2; border-radius: 12px;'>
                    <h2 style='color: #9c6047; text-align: center; border-bottom: 2px solid #9c6047; padding-bottom: 10px;'>Security OTP Verification</h2>
                    <p>Dear Administrator,</p>
                    <p>You requested to change your password for Hotel Destin Admin Panel. Please use the following 6-digit verification code to authenticate this action:</p>
                    
                    <div style='background: #f7f9fc; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 4px; color: #111; border: 1px solid #e9ecf2; border-radius: 8px; margin: 20px 0;'>
                        " . $otp . "
                    </div>
                    
                    <p style='font-size:12.5px; color:#777;'>This OTP is valid for 10 minutes. If you did not request this security update, please log in and secure your account immediately.</p>
                </div>";

                if (send_mail($_SESSION['admin_email'], $subject, $body, true)) {
                    $_SESSION['otp_requested'] = true;
                    $_SESSION['pwd_success'] = 'A 6-digit security OTP code has been dispatched to ' . htmlspecialchars($_SESSION['admin_email']) . '.';
                } else {
                    $_SESSION['pwd_error'] = 'Failed to send verification email. Please check your config.php SMTP parameters.';
                }
                header("Location: change-password.php");
                exit;
            } else {
                $_SESSION['pwd_error'] = 'Incorrect current password.';
                header("Location: change-password.php");
                exit;
            }
        } catch (Exception $e) {
            error_log("Password reset DB query failure: " . $e->getMessage());
            $_SESSION['pwd_error'] = 'System database query failure.';
            header("Location: change-password.php");
            exit;
        }
    }
}

// Step 2: Handle OTP code submission and password updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'verify_otp') {
    $submitted_otp = isset($_POST['otp_code']) ? trim($_POST['otp_code']) : '';
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        $_SESSION['pwd_error'] = 'Security check failed. Please refresh and try again.';
        header("Location: change-password.php");
        exit;
    } else if (empty($submitted_otp)) {
        $_SESSION['pwd_error'] = 'Verification code is required.';
        header("Location: change-password.php");
        exit;
    } else if (empty($_SESSION['pwd_reset_temp'])) {
        $_SESSION['pwd_error'] = 'No pending password change sessions exist.';
        header("Location: change-password.php");
        exit;
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch();

            if ($admin && $admin['otp_code'] === $submitted_otp && strtotime($admin['otp_expires_at']) >= time()) {
                // Update password in DB
                $new_hash = $_SESSION['pwd_reset_temp']['new_hash'];
                
                $upd = $pdo->prepare("UPDATE admins SET password = ?, otp_code = NULL, otp_expires_at = NULL WHERE id = ?");
                $upd->execute([$new_hash, $_SESSION['admin_id']]);

                // Clear temporary sessions
                unset($_SESSION['pwd_reset_temp']);
                unset($_SESSION['otp_requested']);

                $_SESSION['pwd_success'] = 'Password has been changed successfully!';
                header("Location: change-password.php");
                exit;
            } else {
                $_SESSION['pwd_error'] = 'Invalid or expired OTP verification code.';
                header("Location: change-password.php");
                exit;
            }
        } catch (Exception $e) {
            error_log("OTP validation DB failure: " . $e->getMessage());
            $_SESSION['pwd_error'] = 'Database validation processing failure.';
            header("Location: change-password.php");
            exit;
        }
    }
}

// Cancel pending reset
if (isset($_GET['cancel_reset'])) {
    unset($_SESSION['pwd_reset_temp']);
    unset($_SESSION['otp_requested']);
    header("Location: change-password.php");
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-35">
    <div>
        <h1 class="panel-title mb-0" style="font-size:26px;">Security Settings</h1>
        <p class="text-sm text-neutral-500 mt-5">Modify your administrator credentials securely. Changes require Gmail SMTP verification.</p>
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

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-12">
        <div class="panel-card">
            
            <?php if (empty($_SESSION['otp_requested'])): ?>
                <!-- Password Reset Initial Form -->
                <h3 class="font-heading mb-25" style="font-size:18px;">Change Password</h3>
                <form action="change-password.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="request_otp">

                    <div class="form-group mb-20">
                        <label class="form-label-custom">Current Password *</label>
                        <input class="form-control-custom" type="password" name="current_password" required>
                    </div>

                    <div class="form-group mb-25">
                        <label class="form-label-custom">New Password *</label>
                        <input class="form-control-custom" type="password" name="new_password" required>
                    </div>

                    <button class="btn btn-black text-white w-100 py-12" type="submit" style="border-radius: 8px;">
                        Request Security OTP
                    </button>
                </form>
            <?php else: ?>
                <!-- OTP Verification Form -->
                <h3 class="font-heading mb-10" style="font-size:18px; color: #9c6047;">Verify Security Code</h3>
                <p class="neutral-500 mb-25" style="font-size: 13.5px;">Enter the 6-digit OTP code sent to your administrator email to apply modifications.</p>
                
                <form action="change-password.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="verify_otp">

                    <div class="form-group mb-25">
                        <label class="form-label-custom">6-Digit Verification OTP *</label>
                        <input class="form-control-custom text-center" type="text" name="otp_code" placeholder="000000" maxlength="6" style="font-size: 20px; letter-spacing: 6px; font-weight:700; height: 52px;" required>
                    </div>

                    <button class="btn btn-black text-white w-100 py-12 mb-15" type="submit" style="border-radius: 8px; background-color:#3c7a4b; border-color:#3c7a4b;">
                        Verify & Update Password
                    </button>
                    
                    <div class="text-center">
                        <a href="?cancel_reset=1" class="text-neutral-500" style="font-size:13px; text-decoration: underline;">Cancel Modification</a>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
