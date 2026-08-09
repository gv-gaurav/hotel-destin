<?php
// Prevent direct GET requests/displays
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$data_json = isset($_POST['data']) ? $_POST['data'] : '';
$token = isset($_POST['token']) ? $_POST['token'] : '';

$secret = 'secure_async_key_hotel_destin';
$expected_token = hash_hmac('sha256', $action . $data_json, $secret);

if (!hash_equals($expected_token, $token)) {
    http_response_code(403);
    exit("Forbidden: Signature mismatch");
}

// Ignore user abort and allow execution to continue in background
ignore_user_abort(true);
set_time_limit(180);

// Set up clean connection termination if client/caller is still connected
if (function_exists('fastcgi_finish_request')) {
    fastcgi_finish_request();
}

require_once __DIR__ . '/mail-helper.php';
require_once __DIR__ . '/db.php';

$data = json_decode($data_json, true);

if ($action === 'enquiry') {
    $category = $data['category'] ?? '';
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $date = $data['date'] ?? null;
    $guests = $data['guests'] ?? null;
    $additional_details = $data['additional_details'] ?? [];

    // 1. Send SMTP Email Alert to Owner
    $subject = "New Enquiry Received [" . strtoupper($category) . "] - " . $name;
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e9ecf2; border-radius: 12px;'>
        <h2 style='color: #9c6047; text-align: center; border-bottom: 2px solid #9c6047; padding-bottom: 10px;'>NEW " . strtoupper(str_replace('_', ' ', $category)) . " ENQUIRY</h2>
        <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
            <tr style='background: #f7f9fc;'>
                <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold; width: 30%;'>Name</td>
                <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($name) . "</td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Email</td>
                <td style='padding: 10px; border: 1px solid #e9ecf2;'><a href='mailto:" . htmlspecialchars($email) . "'>" . htmlspecialchars($email) . "</a></td>
            </tr>
            <tr style='background: #f7f9fc;'>
                <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Phone</td>
                <td style='padding: 10px; border: 1px solid #e9ecf2;'><a href='tel:" . htmlspecialchars($phone) . "'>" . htmlspecialchars($phone) . "</a></td>
            </tr>";

    if (!empty($date)) {
        $body .= "
            <tr>
                <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Preferred Date</td>
                <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($date) . "</td>
            </tr>";
    }

    if (!empty($guests)) {
        $body .= "
            <tr style='background: #f7f9fc;'>
                <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Guests Count</td>
                <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($guests) . "</td>
            </tr>";
    }

    foreach ($additional_details as $label => $value) {
        if (!empty($value) || $value === 0 || $value === '0') {
            $body .= "
            <tr>
                <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>" . htmlspecialchars($label) . "</td>
                <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . nl2br(htmlspecialchars($value)) . "</td>
            </tr>";
        }
    }

    $body .= "
        </table>
        <p style='border-top: 1px solid #e9ecf2; padding-top: 15px; text-align: center; color: #777; font-size: 12px;'>
            Hotel Destin Gwalior System Alert
        </p>
    </div>";

    send_mail(OWNER_EMAIL, $subject, $body, true);

    // 2. Send Meta WhatsApp Alert to Owner
    if (defined('WHATSAPP_NOTIFICATION_ENABLED') && WHATSAPP_NOTIFICATION_ENABLED) {
        $wa_msg = "🔔 *NEW ENQUIRY RECEIVED*\n\n"
            . "• *Category*: " . strtoupper(str_replace('_', ' ', $category)) . "\n"
            . "• *Name*: " . $name . "\n"
            . "• *Phone*: " . $phone . "\n";

        if (!empty($date)) {
            $wa_msg .= "• *Date*: " . $date . "\n";
        }
        if (!empty($guests)) {
            $wa_msg .= "• *Guests*: " . $guests . "\n";
        }

        foreach ($additional_details as $lbl => $val) {
            if (!empty($val) || $val === 0 || $val === '0') {
                $wa_msg .= "• *" . $lbl . "*: " . str_replace('<br>', "\n", str_replace('<br/>', "\n", $val)) . "\n";
            }
        }

        send_whatsapp_notification(WHATSAPP_RECEIVER_NUMBER, $wa_msg);
    }
} elseif ($action === 'booking') {
    $booking_id = $data['booking_id'] ?? '';
    $type = $data['type'] ?? '';
    execute_booking_notification($booking_id, $type);
}
?>
