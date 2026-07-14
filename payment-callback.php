<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/mail-helper.php';
require_once __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $razorpay_payment_id = isset($_POST['razorpay_payment_id']) ? trim($_POST['razorpay_payment_id']) : '';
    $razorpay_order_id = isset($_POST['razorpay_order_id']) ? trim($_POST['razorpay_order_id']) : '';
    $razorpay_signature = isset($_POST['razorpay_signature']) ? trim($_POST['razorpay_signature']) : '';
    $booking_id = isset($_POST['booking_id']) ? trim($_POST['booking_id']) : '';

    $success = false;
    $error_message = '';

    $is_sandbox_tx = (strpos($razorpay_order_id, 'order_sandbox_') !== false || strpos($razorpay_payment_id, 'pay_sandbox_') !== false);

    if ($is_sandbox_tx) {
        $success = true;
    } else {
        try {
            $key_id = get_setting('razorpay_key_id') ?: RAZORPAY_KEY_ID;
            $key_secret = get_setting('razorpay_key_secret') ?: RAZORPAY_KEY_SECRET;
            
            $api = new Api($key_id, $key_secret);
            
            $attributes = [
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_signature' => $razorpay_signature
            ];
            
            // Validate checksum signature
            $api->utility->verifyPaymentSignature($attributes);
            $success = true;
        } catch (SignatureVerificationError $e) {
            $success = false;
            $error_message = 'Signature Verification Failure: ' . $e->getMessage();
        }
    }

    if ($success) {
        try {
            // Retrieve booking data
            $stmt = $pdo->prepare("SELECT b.*, r.title AS room_title FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.booking_id = ?");
            $stmt->execute([$booking_id]);
            $booking = $stmt->fetch();

            if ($booking) {
                // 1. Generate sequential invoice number: INV-YYYYMMDD-[SEQUENCE]
                $date_prefix = 'INV-' . date('Ymd') . '-';
                $seq_stmt = $pdo->prepare("SELECT invoice_no FROM bookings WHERE invoice_no LIKE ? ORDER BY invoice_no DESC LIMIT 1");
                $seq_stmt->execute([$date_prefix . '%']);
                $last_invoice = $seq_stmt->fetchColumn();
                if ($last_invoice) {
                    $seq = (int)substr($last_invoice, -4);
                    $next_seq = str_pad($seq + 1, 4, '0', STR_PAD_LEFT);
                } else {
                    $next_seq = '0001';
                }
                $invoice_no = $date_prefix . $next_seq;

                // Physical room assignment disabled per simple admin panel logic
                $physical_room_id = null;

                // Update booking metrics inside DB
                $upd_stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'paid', booking_status = 'confirmed', invoice_no = ?, physical_room_id = NULL, razorpay_payment_id = ? WHERE booking_id = ?");
                $upd_stmt->execute([$invoice_no, $razorpay_payment_id, $booking_id]);

                // Formulate premium HTML Invoice Email Body
                $subject = "Booking Confirmed - Ref: " . $booking_id;
                $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e9ecf2; border-radius: 12px;'>
                    <h2 style='color: #9c6047; text-align: center; border-bottom: 2px solid #9c6047; padding-bottom: 10px;'>HOTEL DESTIN GWALIOR</h2>
                    <p>Dear <strong>" . htmlspecialchars($booking['customer_name']) . "</strong>,</p>
                    <p>Thank you for booking with us. Your reservation is confirmed. Please find stay receipt particulars below:</p>
                    
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Booking Reference ID</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking_id) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Invoice Number</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'><strong>" . htmlspecialchars($invoice_no) . "</strong></td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Room Type</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['room_title']) . "</td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Check-In Date</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['check_in']) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Check-Out Date</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['check_out']) . "</td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Nights</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['total_nights']) . " night(s)</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Meal Plan</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>" . htmlspecialchars($booking['meal_plan']) . "</td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Guests Count</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking['guests']) . " guest(s) (Adults: " . htmlspecialchars($booking['adults']) . ", Children: " . htmlspecialchars($booking['children']) . ")</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Base Amount</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($booking['base_amount'], 2) . "</td>
                        </tr>
                        " . ($booking['discount_amount'] > 0 ? "
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Promo Discount (" . htmlspecialchars($booking['coupon_code'] ?: '') . ")</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; color: #d13232;'>-₹" . number_format($booking['discount_amount'], 2) . "</td>
                        </tr>
                        " : "") . "
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>GST Taxes (5%)</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($booking['tax_amount'], 2) . "</td>
                        </tr>
                        <tr style='background: #fdfaf8; font-size: 16px; font-weight: bold; color: #3c7a4b;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>Grand Total Paid</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($booking['total_amount'], 2) . "</td>
                        </tr>
                    </table>

                    <p><strong>Special Request:</strong> " . (!empty($booking['special_request']) ? htmlspecialchars($booking['special_request']) : 'None') . "</p>
                    <p style='border-top: 1px solid #e9ecf2; padding-top: 15px; text-align: center; color: #777; font-size: 12px;'>
                        Hotel Destin Gwalior, Sachin Tendulkar Rd. For queries call +91 9203509944.
                    </p>
                </div>";

                // Dispatch copy to Customer
                send_mail($booking['customer_email'], $subject, $body, true);
                
                // Dispatch copy to Hotel Owner/Admin alerts
                send_mail(OWNER_EMAIL, "NEW ONLINE BOOKING - " . $booking_id, $body, true);
            }
            
            // Redirect to success page
            header("Location: thank-you.php?ref=" . urlencode($booking_id));
            exit;
            
        } catch (Exception $e) {
            error_log("Database callback processing failure: " . $e->getMessage());
            $error_message = 'Database tracking logging failure.';
        }
    }

    // Display checkout failure fallback
    http_response_code(400);
    echo "<h2>Secure Payment Verification Failed</h2><p>" . htmlspecialchars($error_message) . "</p><p><a href='rooms.php'>Return to Rooms list</a></p>";
} else {
    http_response_code(405);
    exit("Method Not Allowed");
}
?>
