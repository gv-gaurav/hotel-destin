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
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
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
?>
