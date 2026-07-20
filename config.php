<?php
// Prevent direct file access if loaded independently
if (count(get_included_files()) === 1) {
    http_response_code(403);
    exit("Direct access not permitted.");
}

// Start secure sessions
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
    session_start();
}

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// System Constants
define('SITE_NAME', 'Hotel Destin Gwalior');
define('SITE_URL', 'https://hoteldestin.in');

// Database Credentials (Laragon default MySQL)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hotel_destin');
define('DB_USER', 'root');
define('DB_PASS', '');

// SMTP Email Configurations (Titan/GoDaddy Setup)
define('SMTP_HOST', 'smtp.titan.email');
define('SMTP_PORT', 465);
define('SMTP_USER', 'info@hoteldestin.in');
define('SMTP_PASS', 'gU3_2VYVdJM8&');
define('SMTP_FROM', 'info@hoteldestin.in');
define('SMTP_FROM_NAME', 'Hotel Destin System');
define('OWNER_EMAIL', 'info@hoteldestin.in'); // Admin email recipient

// Razorpay Credentials (Sandbox mode settings)
define('RAZORPAY_KEY_ID', 'rzp_test_placeholder_key');
define('RAZORPAY_KEY_SECRET', 'rzp_test_placeholder_secret');

// Core Helper: Check CSRF token validation
function verify_csrf_token($token)
{
    return !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}

// Core Helper: XSS Sanitization
function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
