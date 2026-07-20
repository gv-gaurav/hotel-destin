<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "<h2>SMTP Verbose Debug Test</h2>";
echo "Attempting to connect to: <strong>" . SMTP_HOST . "</strong> (Port: " . SMTP_PORT . ")...<br><br>";
echo "<pre style='background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 6px; overflow-x: auto;'>";

$mail = new PHPMailer(true);

try {
    // Enable verbose debug output
    $mail->SMTPDebug = 3; 
    // Output debug info as HTML/echo
    $mail->Debugoutput = 'echo';

    // SMTP Server Configuration Settings
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = (defined('SMTP_PORT') && (int)SMTP_PORT === 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;

    // Recipient configurations
    $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
    $mail->addAddress(OWNER_EMAIL);

    // Content
    $mail->isHTML(true);
    $mail->Subject = "SMTP Verbose Debug Test";
    $mail->Body    = "This is a verbose debug email test.";

    $mail->send();
    echo "</pre>";
    echo "<h3 style='color: green;'>✔ SUCCESS: Email sent successfully!</h3>";
} catch (Exception $e) {
    echo "</pre>";
    echo "<h3 style='color: red;'>✖ FAILED: Email sending failed.</h3>";
    echo "<p><strong>ErrorInfo:</strong> " . $mail->ErrorInfo . "</p>";
    echo "<p><strong>Exception Message:</strong> " . $e->getMessage() . "</p>";
}
?>
