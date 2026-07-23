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
function send_enquiry_alert($category, $name, $email, $phone, $date = null, $guests = null, $additional_details = []) {
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
    
    // Build and send WhatsApp notification to the owner
    $wa_message = "🔔 *NEW ENQUIRY RECEIVED* 🔔\n\n";
    $wa_message .= "*Category:* " . strtoupper(str_replace('_', ' ', $category)) . "\n";
    $wa_message .= "*Name:* {$name}\n";
    $wa_message .= "*Email:* {$email}\n";
    $wa_message .= "*Phone:* {$phone}\n";

    if (!empty($date)) {
        $wa_message .= "*Date:* {$date}\n";
    }
    if (!empty($guests)) {
        $wa_message .= "*Guests:* {$guests}\n";
    }

    if (!empty($additional_details)) {
        $wa_message .= "\n*Additional Info:*\n";
        foreach ($additional_details as $label => $value) {
            if (!empty($value) || $value === 0 || $value === '0') {
                $wa_message .= "- *{$label}:* " . strip_tags($value) . "\n";
            }
        }
    }

    send_whatsapp_message($wa_message);
    
    return send_mail(OWNER_EMAIL, $subject, $body, true);
}

/**
 * Sends a WhatsApp notification using Green API.
 *
 * @param string $message Text body to send
 * @return bool True on success, false on failure
 */
function send_whatsapp_message($message) {
    if (!defined('WHATSAPP_NOTIFICATION_ENABLED') || !WHATSAPP_NOTIFICATION_ENABLED) {
        return false;
    }
    if (empty(GREEN_API_URL) || empty(GREEN_API_INSTANCE_ID) || empty(GREEN_API_TOKEN) || empty(WHATSAPP_RECEIVER_NUMBER)) {
        return false;
    }

    // Format chat ID for Green API personal numbers (e.g., "919873272462@c.us")
    $chatId = WHATSAPP_RECEIVER_NUMBER . '@c.us';

    $payload = json_encode([
        'chatId' => $chatId,
        'message' => $message
    ]);

    $url = GREEN_API_URL . "/waInstance" . GREEN_API_INSTANCE_ID . "/sendMessage/" . GREEN_API_TOKEN;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        error_log("WhatsApp Alert Fail (Green API): " . $err);
        return false;
    }

    $res_data = json_decode($response, true);
    if ($http_code === 200 && isset($res_data['idMessage'])) {
        return true;
    } else {
        error_log("WhatsApp Alert Fail (Green API): Code {$http_code}, Response: {$response}");
        return false;
    }
}
?>
