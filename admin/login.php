<?php
ob_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../mail-helper.php';

$success = '';
$error = '';

if (!empty($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['success'])) {
    if ($_GET['success'] === 'reset') {
        $success = 'Password reset successfully! Please sign in with your new password.';
    }
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'csrf') {
        $error = 'Security check failed. Please refresh and try again.';
    }
}

// Handle Form Submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $csrf_token = isset($_POST['csrf_token']) ? trim($_POST['csrf_token']) : '';

    if (!verify_csrf_token($csrf_token)) {
        header("Location: login.php?error=csrf");
        exit;
    }

    if ($action === 'login') {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $admin = $stmt->fetch();

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['admin_id'] = $admin['id'];
                    
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } catch (Exception $e) {
                error_log("Login DB error: " . $e->getMessage());
                $error = 'Database authentication error.';
            }
        }
    } else if ($action === 'request_reset_otp') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';

        if (empty($email)) {
            $error = 'Email address is required.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin) {
                    $otp = strval(rand(100000, 999999));
                    $expiry = date('Y-m-d H:i:s', strtotime('+10 minutes'));

                    $upd = $pdo->prepare("UPDATE admins SET otp_code = ?, otp_expires_at = ? WHERE email = ?");
                    $upd->execute([$otp, $expiry, $email]);

                    $_SESSION['reset_email'] = $email;

                    // Send email
                    $subject = "Admin Password Reset OTP - Hotel Destin";
                    $body = "
                    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 25px; border: 1px solid #ebdcd5; border-radius: 16px; background-color: #fdfcfb;'>
                        <div style='text-align: center; margin-bottom: 20px;'>
                            <h2 style='color: #9c6047; margin: 0; font-size: 24px; font-weight: 600;'>Hotel Destin</h2>
                            <p style='color: #64748b; font-size: 14px; margin: 5px 0 0 0;'>Password Reset Assistance</p>
                        </div>
                        <p style='color: #334155; font-size: 15px;'>Hello,</p>
                        <p style='color: #334155; font-size: 15px;'>We received a request to reset the password for your Hotel Destin Admin account. Please use the verification code below to authorize this password reset request:</p>
                        
                        <div style='background: #fcfaf8; padding: 15px 25px; text-align: center; font-size: 28px; font-weight: bold; letter-spacing: 5px; color: #9c6047; border-radius: 8px; margin: 25px 0; border: 1px solid #ebdcd5;'>
                            " . $otp . "
                        </div>
                        
                        <p style='font-size: 13px; color: #64748b;'>This code will expire in 10 minutes. If you did not request this reset, you can safely ignore this email.</p>
                        <hr style='border: 0; border-top: 1px solid #ebdcd5; margin: 25px 0;'>
                        <p style='font-size: 12px; color: #9c6047; text-align: center; font-weight: 600;'>Hotel Destin Admin Portal</p>
                    </div>";

                    if (send_mail($email, $subject, $body, true)) {
                        header("Location: login.php?mode=forgot_otp");
                        exit;
                    } else {
                        $error = 'Failed to send OTP email. Please check your system email settings.';
                    }
                } else {
                    $error = 'No administrator is registered with that email address.';
                }
            } catch (Exception $e) {
                error_log("Forgot password OTP query failure: " . $e->getMessage());
                $error = 'System database query failure.';
            }
        }
    } else if ($action === 'verify_reset_otp') {
        $otp = isset($_POST['otp_code']) ? trim($_POST['otp_code']) : '';
        $email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';

        if (empty($otp)) {
            $error = 'Verification OTP code is required.';
        } else if (empty($email)) {
            $error = 'Session expired. Please request the code again.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $admin = $stmt->fetch();

                if ($admin && $admin['otp_code'] === $otp && strtotime($admin['otp_expires_at']) >= time()) {
                    $_SESSION['reset_otp_verified'] = true;
                    header("Location: login.php?mode=reset_password");
                    exit;
                } else {
                    $error = 'Invalid or expired OTP verification code.';
                }
            } catch (Exception $e) {
                error_log("OTP verification query failure: " . $e->getMessage());
                $error = 'System verification processing failure.';
            }
        }
    } else if ($action === 'perform_password_reset') {
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
        $email = isset($_SESSION['reset_email']) ? $_SESSION['reset_email'] : '';
        $verified = isset($_SESSION['reset_otp_verified']) ? $_SESSION['reset_otp_verified'] : false;

        if (empty($password) || empty($confirm_password)) {
            $error = 'Please fill out both password fields.';
        } else if ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else if (strlen($password) < 6) {
            $error = 'New password must be at least 6 characters.';
        } else if (empty($email) || !$verified) {
            $error = 'Session validation expired. Please start over.';
        } else {
            try {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE admins SET password = ?, otp_code = NULL, otp_expires_at = NULL WHERE email = ?");
                $stmt->execute([$hash, $email]);

                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_otp_verified']);

                header("Location: login.php?success=reset");
                exit;
            } catch (Exception $e) {
                error_log("Perform password reset DB failure: " . $e->getMessage());
                $error = 'Failed to reset password in the database.';
            }
        }
    }
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/imgs/template/favicon.png">
    
    <!-- Importing Premium Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    
    <title>Admin Portal - Hotel Destin</title>
    
    <style>
        :root {
            --primary: #9c6047;
            --primary-hover: #834f37;
            --bg-gradient: linear-gradient(135deg, #fdfbf7 0%, #f5ece9 100%);
            --neutral-dark: #0f172a;
            --neutral-grey: #64748b;
            --neutral-light: #fdfcfb;
            --border-color: #ebdcd5;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
            color: var(--neutral-dark);
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            perspective: 1000px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 12px 36px rgba(156, 96, 71, 0.08);
            transition: all 0.3s ease;
        }

        .logo-box {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-box img {
            width: 48px;
            height: auto;
            filter: drop-shadow(0 2px 5px rgba(156, 96, 71, 0.1));
        }

        .login-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 700;
            text-align: center;
            color: var(--neutral-dark);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-subtitle {
            font-size: 14px;
            color: var(--neutral-grey);
            text-align: center;
            margin-bottom: 32px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--neutral-dark);
            display: block;
            margin-bottom: 8px;
        }

        .form-control-custom {
            width: 100%;
            height: 46px;
            background-color: var(--neutral-light);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 16px;
            font-family: inherit;
            font-size: 14px;
            color: var(--neutral-dark);
            outline: none;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .form-control-custom:focus {
            border-color: var(--primary);
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(156, 96, 71, 0.08);
        }

        .forgot-link-box {
            display: flex;
            justify-content: flex-end;
            margin-top: -12px;
            margin-bottom: 24px;
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 500;
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            height: 48px;
            background-color: var(--primary);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(156, 96, 71, 0.15);
        }

        .btn-login:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(156, 96, 71, 0.25);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .back-link-box {
            text-align: center;
            margin-top: 24px;
        }

        .back-link {
            font-size: 13px;
            font-weight: 500;
            color: var(--neutral-grey);
            text-decoration: none;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link:hover {
            color: var(--neutral-dark);
        }

        /* Beautiful Responsive Custom Alerts */
        .alert-dismiss {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 12px;
            font-size: 13.5px;
            line-height: 1.5;
            margin-bottom: 24px;
            animation: slideIn 0.3s ease;
        }

        .alert-error {
            background-color: #fef2f2;
            border: 1px solid #fca5a5;
            color: #b91c1c;
        }

        .alert-success {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            color: #15803d;
        }

        .alert-dismiss svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive Breakpoints */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
                border-radius: 20px;
            }
            .login-title {
                font-size: 22px;
            }
            .login-subtitle {
                font-size: 13px;
                margin-bottom: 24px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            
            <div class="logo-box">
                <img src="../assets/imgs/template/logo-destin.png" alt="Hotel Destin">
            </div>

            <!-- Dynamic Alerts Processing -->
            <?php if (!empty($error)): ?>
                <div class="alert-dismiss alert-error">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-dismiss alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <!-- STATE 1: Default Login Form -->
            <?php if ($mode === 'login'): ?>
                <h2 class="login-title">Hotel Destin</h2>
                <p class="login-subtitle">Sign in to control rooms, pricing & lead manager</p>

                <form action="login.php?mode=login" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input class="form-control-custom" type="text" name="username" placeholder="Enter username" required autocomplete="username">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input class="form-control-custom" type="password" name="password" placeholder="Enter password" required autocomplete="current-password">
                    </div>

                    <div class="forgot-link-box">
                        <a href="login.php?mode=forgot_email" class="forgot-link">Forgot Password?</a>
                    </div>

                    <button class="btn-login" type="submit">Sign In</button>
                </form>

            <!-- STATE 2: Forgot Password - Enter Email -->
            <?php elseif ($mode === 'forgot_email'): ?>
                <h2 class="login-title">Reset Password</h2>
                <p class="login-subtitle">Enter your registered email address to receive a secure 6-digit verification code.</p>

                <form action="login.php?mode=forgot_email" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="request_reset_otp">
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input class="form-control-custom" type="email" name="email" placeholder="e.g. admin@hoteldestin.com" required autocomplete="email">
                    </div>

                    <button class="btn-login" type="submit">Send Verification Code</button>
                </form>

                <div class="back-link-box">
                    <a href="login.php?mode=login" class="back-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back to Sign In
                    </a>
                </div>

            <!-- STATE 3: Forgot Password - Enter OTP -->
            <?php elseif ($mode === 'forgot_otp'): ?>
                <h2 class="login-title">Verify OTP</h2>
                <p class="login-subtitle">We sent a 6-digit security code to your email. Please enter it below to verify.</p>

                <form action="login.php?mode=forgot_otp" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="verify_reset_otp">
                    
                    <div class="form-group">
                        <label class="form-label">6-Digit Verification Code</label>
                        <input class="form-control-custom" type="text" name="otp_code" placeholder="Enter 6-digit OTP" required max-length="6" style="text-align: center; letter-spacing: 2px; font-weight: bold; font-size: 16px;">
                    </div>

                    <button class="btn-login" type="submit">Verify & Continue</button>
                </form>

                <div class="back-link-box">
                    <a href="login.php?mode=forgot_email" class="back-link">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Resend Code
                    </a>
                </div>

            <!-- STATE 4: Forgot Password - Reset Password -->
            <?php elseif ($mode === 'reset_password'): ?>
                <h2 class="login-title">New Password</h2>
                <p class="login-subtitle">Your identity has been verified. Set a new password for your account.</p>

                <form action="login.php?mode=reset_password" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="perform_password_reset">
                    
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input class="form-control-custom" type="password" name="password" placeholder="Enter new password" required autocomplete="new-password">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password</label>
                        <input class="form-control-custom" type="password" name="confirm_password" placeholder="Confirm new password" required autocomplete="new-password">
                    </div>

                    <button class="btn-login" type="submit">Reset Password</button>
                </form>

            <?php endif; ?>

        </div>
    </div>

    <!-- Script to Auto-Dismiss Alerts after 5 seconds -->
    <script>
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert-dismiss');
            alerts.forEach(el => {
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-10px)';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
