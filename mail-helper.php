<?php
// Prevent direct access
if (count(get_included_files()) === 1) {
    http_response_code(403);
    exit("Direct access not permitted.");
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Sends a secure SMTP email.
 *
 * @param string $to Recipient email address
 * @param string $subject Subject of the email
 * @param string $body Body content of the email
 * @param bool $isHtml Whether the body should be treated as HTML (default: true)
 * @return bool True on success, false on failure
 */
function send_mail($to, $subject, $body, $isHtml = true) {
    $mail = new PHPMailer(true);

    try {
        // SMTP Server Configuration Settings
        $mail->isSMTP();
        $mail->CharSet    = 'UTF-8';
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        // GoDaddy usually requires SSL (SMTPS) on port 465, TLS (STARTTLS) on port 587
        $mail->SMTPSecure = (defined('SMTP_PORT') && (int)SMTP_PORT === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        // Recipient configurations
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML($isHtml);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Fallback plain text for non-HTML email clients
        if ($isHtml) {
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>'], ["\n", "\n", "\n\n"], $body));
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("PHPMailer Exception: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Sends an email notification to the hotel owner about a new enquiry.
 *
 * @param string $category The category of enquiry (e.g. contact, wedding, restaurant, corporate, banquet, airport_transfer)
 * @param string $name Name of the person making the enquiry
 * @param string $email Email address of the person
 * @param string $phone Phone number of the person
 * @param string|null $date Event/Booking date if applicable
 * @param int|null $guests Number of guests if applicable
 * @param array $additional_details Key-value pairs of other details
 * @return bool True on success, false on failure
 */
function send_enquiry_alert($category, $name, $email, $phone, $date = null, $guests = null, $additional_details = [], $enquiry_id = null) {
    $payload = [
        'category' => $category,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'date' => $date,
        'guests' => $guests,
        'additional_details' => $additional_details,
        'enquiry_id' => $enquiry_id
    ];
    
    $async_success = trigger_async_notification('enquiry', $payload);
    
    // Fallback to synchronous execution if async trigger failed (e.g. sockets disabled)
    if (!$async_success) {
        $subject = "New Enquiry Received [" . strtoupper($category) . "] - " . $name;
        $body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e9ecf2; border-radius: 12px;'>
            <h2 style='color: #9c6047; text-align: center; border-bottom: 2px solid #9c6047; padding-bottom: 10px;'>NEW " . strtoupper(str_replace('_', ' ', $category)) . " ENQUIRY</h2>
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                <tr style='background: #f7f9fc;'>
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold; width: 35%;'>Name</td>
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
                    <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Date</td>
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
        
        $mail_success = send_mail(OWNER_EMAIL, $subject, $body, true);

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
        return $mail_success;
    }
    return true;
}

/**
 * Sends a WhatsApp text notification via Meta Cloud API.
 *
 * @param string $to Recipient phone number (e.g., 919873272462)
 * @param string $message Text message content
 * @return bool True on API success, false otherwise
 */
function send_whatsapp_notification($to, $message) {
    if (!defined('META_WA_ACCESS_TOKEN') || !defined('META_WA_PHONE_NUMBER_ID')) {
        error_log("Meta WhatsApp credentials not defined.");
        return false;
    }

    $token = META_WA_ACCESS_TOKEN;
    $phone_id = META_WA_PHONE_NUMBER_ID;

    if (empty($token) || empty($phone_id)) {
        error_log("Meta WhatsApp credentials are empty.");
        return false;
    }

    $url = "https://graph.facebook.com/v18.0/" . $phone_id . "/messages";

    $data = [
        'messaging_product' => 'whatsapp',
        'recipient_type'    => 'individual',
        'to'                => $to,
        'type'              => 'text',
        'text'              => [
            'preview_url' => false,
            'body'        => $message
        ]
    ];

    $headers = [
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        error_log("Meta WhatsApp API Curl Error: " . $curl_error);
        return false;
    }

    $res_json = json_decode($response, true);
    if ($http_code >= 200 && $http_code < 300 && isset($res_json['messages'])) {
        return true;
    } else {
        error_log("Meta WhatsApp API Error (HTTP " . $http_code . "): " . $response);
        return false;
    }
}

/**
 * Triggers a non-blocking asynchronous POST request to the local background worker.
 *
 * @param string $action Action name (e.g. 'enquiry', 'booking')
 * @param array $data Data payload to pass to the handler
 * @return bool True if socket connection succeeded, false otherwise
 */
function trigger_async_notification($action, $data) {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $port = ($_SERVER['SERVER_PORT'] ?? 80) == 443 ? 443 : 80;
    
    if (strpos($host, ':') !== false) {
        $parts = explode(':', $host);
        $host = $parts[0];
        $port = (int)$parts[1];
    }
    
    $ssl = ($port === 443) ? 'ssl://' : '';
    
    $fp = @fsockopen($ssl . $host, $port, $errno, $errstr, 2);
    if (!$fp) {
        error_log("fsockopen failed: $errstr ($errno). Falling back to synchronous processing.");
        return false;
    }
    
    $path = '/async-notification-handler.php';
    if (isset($_SERVER['SCRIPT_NAME'])) {
        $dir = dirname($_SERVER['SCRIPT_NAME']);
        $dir = str_replace('\\', '/', $dir);
        $path = ($dir === '/' || $dir === '') ? '/async-notification-handler.php' : rtrim($dir, '/') . '/async-notification-handler.php';
    }

    $secret = 'secure_async_key_hotel_destin';
    $data_json = json_encode($data);
    $token = hash_hmac('sha256', $action . $data_json, $secret);

    $post_data = http_build_query([
        'action' => $action,
        'data'   => $data_json,
        'token'  => $token
    ]);
    
    $out = "POST " . $path . " HTTP/1.1\r\n";
    $out .= "Host: " . $host . ($port !== 80 && $port !== 443 ? ':' . $port : '') . "\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Content-Length: " . strlen($post_data) . "\r\n";
    $out .= "Connection: Close\r\n\r\n";
    $out .= $post_data;
    
    fwrite($fp, $out);
    fclose($fp);
    return true;
}

/**
 * Triggers a background notification for a room booking.
 *
 * @param string $booking_id The unique booking ID string
 * @param string $type The booking type ('online' or 'offline')
 * @return bool True if async trigger succeeded
 */
function trigger_booking_notification($booking_id, $type) {
    $payload = [
        'booking_id' => $booking_id,
        'type' => $type
    ];
    return trigger_async_notification('booking', $payload);
}
?>
