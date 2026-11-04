<?php
// =========================================================================
// HOTEL DESTIN - CONFIGURATION TEMPLATE (EXAMPLE)
// Copy this file to config.php and update your credentials.
// NEVER commit config.php with real passwords or API keys to git!
// =========================================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token for secure forms
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

define('ENABLE_SEARCH_INDEXING', true);

if (!ENABLE_SEARCH_INDEXING) {
    if (!headers_sent()) {
        header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet', true);
    }
}

// System Constants & Environment Configuration
define('SITE_NAME', 'Hotel Destin Gwalior');

// Automatically detects whether running locally (Laragon) or live on production (CyberPanel)
$is_local = (
    (isset($_SERVER['HTTP_HOST']) && (
        strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
        strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.test') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.local') !== false
    )) ||
    (php_sapi_name() === 'cli' && (DIRECTORY_SEPARATOR === '\\' || strpos(__DIR__, 'laragon') !== false))
);

if ($is_local) {
    define('SITE_URL', 'http://localhost/travila-demos.vercel.app');
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'hotel_destin');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    define('SITE_URL', 'https://hoteldestin.in');
    define('DB_HOST', '127.0.0.1');
    define('DB_NAME', 'YOUR_PROD_DB_NAME');
    define('DB_USER', 'YOUR_PROD_DB_USER');
    define('DB_PASS', 'YOUR_PROD_DB_PASS');
}

// SMTP Email Configurations (GoDaddy / Custom SMTP)
define('SMTP_HOST', 'smtpout.secureserver.net');
define('SMTP_PORT', 465);
define('SMTP_USER', 'info@hoteldestin.in');
define('SMTP_PASS', 'YOUR_SMTP_PASSWORD');
define('SMTP_FROM', 'info@hoteldestin.in');
define('SMTP_FROM_NAME', 'Hotel Destin System');
define('OWNER_EMAIL', 'info@hoteldestin.in');

// WhatsApp Notifications (Meta Cloud API Settings)
define('WHATSAPP_NOTIFICATION_ENABLED', false);
define('META_WA_ACCESS_TOKEN', 'YOUR_META_WA_ACCESS_TOKEN');
define('META_WA_PHONE_NUMBER_ID', 'YOUR_META_PHONE_ID');
define('META_WA_BUSINESS_ACCOUNT_ID', 'YOUR_META_WABA_ID');
define('WHATSAPP_RECEIVER_NUMBER', '919873272462');

// Razorpay Credentials & Payment Switch
define('ONLINE_PAYMENT_ENABLED', false);
define('RAZORPAY_KEY_ID', 'rzp_test_placeholder_key');
define('RAZORPAY_KEY_SECRET', 'rzp_test_placeholder_secret');

function verify_csrf_token($token)
{
    return !empty($token) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize($data)
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

