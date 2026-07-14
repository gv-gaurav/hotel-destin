<?php
// Prevent direct file access if loaded independently
if (count(get_included_files()) === 1) {
    http_response_code(403);
    exit("Direct access not permitted.");
}

require_once __DIR__ . '/../db.php';

// Fetch analytics tracking script strings from db settings
$gtm = get_setting('gtm_code');
$pixel = get_setting('meta_pixel');
$ga4 = get_setting('google_analytics');

if (!empty($gtm)) {
    echo "\n<!-- Google Tag Manager -->\n" . $gtm . "\n<!-- End Google Tag Manager -->\n";
}

if (!empty($pixel)) {
    echo "\n<!-- Meta Pixel Code -->\n" . $pixel . "\n<!-- End Meta Pixel Code -->\n";
}

if (!empty($ga4)) {
    echo "\n<!-- Google Analytics (GA4) -->\n" . $ga4 . "\n<!-- End Google Analytics (GA4) -->\n";
}
?>
