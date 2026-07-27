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

    // Fetch booking details from DB inside background process to ensure fresh, secure data
    $stmt = $pdo->prepare("SELECT b.*, r.title AS room_title FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if ($booking) {
        $invoice_no = $booking['invoice_no'];
        $child_ages_label = !empty($booking['child_ages']) ? " [Ages: " . htmlspecialchars($booking['child_ages']) . "]" : "";

        // Formulate premium HTML Invoice Email Body
        $subject = "Booking Confirmed - Ref: " . $booking_id;
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; border: 1px solid #e9ecf2; padding: 20px; border-radius: 8px;'>
            <h2 style='color: " . ($type === 'online' ? '#3c7a4b' : '#9c6047') . "; text-align: center;'>Hotel Destin - Booking Confirmed</h2>
            <p style='font-size: 15px;'>Dear " . htmlspecialchars($booking['customer_name']) . ",</p>
            <p style='font-size: 14px;'>Thank you for choosing Hotel Destin Gwalior. Your " . ($type === 'online' ? 'online' : 'offline') . " transaction was completed successfully, and your stay details are listed below:</p>
            
            <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Booking ID / Ref</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold; color: #9c6047;'>" . htmlspecialchars($booking_id) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Invoice Number</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>" . htmlspecialchars($invoice_no) . "</td>
                </tr>
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Room Category</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['room_title']) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Check-In Date</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['check_in']) . "</td>
                </tr>
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Check-Out Date</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['check_out']) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Duration</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['total_nights']) . " night(s)</td>
                </tr>
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Meal Plan</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['meal_plan']) . "</td>
                </tr>
                <tr>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Guests Count</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['guests']) . " guest(s) (Adults: " . htmlspecialchars($booking['adults']) . ", Children: " . htmlspecialchars($booking['children']) . $child_ages_label . ")</td>
                </tr>
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Base Amount</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($booking['base_amount'], 2) . "</td>
                </tr>";

        if ($booking['discount_amount'] > 0) {
            $body .= "
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Promo Discount (" . htmlspecialchars($booking['coupon_code'] ?: '') . ")</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; color: #d13232;'>-₹" . number_format($booking['discount_amount'], 2) . "</td>
                </tr>";
        }

        $body .= "
                <tr>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>GST Taxes (5%)</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($booking['tax_amount'], 2) . "</td>
                </tr>
                <tr style='background: #fdfaf8; font-size: 16px; font-weight: bold; color: " . ($type === 'online' ? '#3c7a4b' : '#9c6047') . ";'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . ($type === 'online' ? 'Grand Total Paid' : 'Total Cost (Payable at Hotel)') . "</td>
                    <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($booking['total_amount'], 2) . "</td>
                </tr>
            </table>

            <p><strong>Special Request:</strong> " . (!empty($booking['special_request']) ? htmlspecialchars($booking['special_request']) : 'None') . "</p>
            <p style='border-top: 1px solid #e9ecf2; padding-top: 15px; text-align: center; color: #777; font-size: 12px;'>
                Hotel Destin Gwalior, Sachin Tendulkar Rd. For queries call +91 9203509944.
            </p>
        </div>";

        // Dispatch email copy to Customer
        send_mail($booking['customer_email'], $subject, $body, true);

        // Dispatch email copy to Hotel Owner/Admin alerts
        send_mail(OWNER_EMAIL, "NEW " . strtoupper($type) . " BOOKING - " . $booking_id, $body, true);

        // Dispatch WhatsApp Notification to Owner
        if (defined('WHATSAPP_NOTIFICATION_ENABLED') && WHATSAPP_NOTIFICATION_ENABLED) {
            $payment_label = ($type === 'online') ? '(Paid via Razorpay)' : '(Pay at Hotel)';
            $wa_msg = "🏨 *NEW " . strtoupper($type) . " BOOKING*\n\n"
                . "• *Booking ID*: " . $booking_id . "\n"
                . "• *Customer*: " . $booking['customer_name'] . "\n"
                . "• *Phone*: " . $booking['customer_phone'] . "\n"
                . "• *Room Type*: " . $booking['room_title'] . "\n"
                . "• *Check-in*: " . $booking['check_in'] . "\n"
                . "• *Check-out*: " . $booking['check_out'] . "\n"
                . "• *Nights*: " . $booking['total_nights'] . "\n"
                . "• *Meal Plan*: " . $booking['meal_plan'] . "\n"
                . "• *Total Cost*: ₹" . number_format($booking['total_amount'], 2) . " " . $payment_label . "\n";
            send_whatsapp_notification(WHATSAPP_RECEIVER_NUMBER, $wa_msg);
        }
    }
}
?>
