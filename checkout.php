<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;

// AJAX Coupon Validator Endpoint
if (isset($_POST['action']) && $_POST['action'] === 'apply_coupon') {
    header('Content-Type: application/json');
    $coupon_code = isset($_POST['code']) ? strtoupper(trim($_POST['code'])) : '';

    try {
        $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
        $stmt->execute([$coupon_code]);
        $coupon = $stmt->fetch();

        if ($coupon) {
            echo json_encode([
                'success' => true,
                'discount_percent' => (int)$coupon['discount_percent'],
                'message' => 'Coupon applied successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'System error validating coupon.']);
    }
    exit;
}

// AJAX Dynamic Pricing Endpoint
if (isset($_POST['action']) && $_POST['action'] === 'get_pricing') {
    header('Content-Type: application/json');
    $check_in = isset($_POST['check_in']) ? trim($_POST['check_in']) : '';
    $check_out = isset($_POST['check_out']) ? trim($_POST['check_out']) : '';
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : 2;
    $room_id = isset($_POST['room_id']) ? intval($_POST['room_id']) : 0;

    // Fetch room from DB
    $room_data = null;
    try {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$room_id]);
        $room_data = $stmt->fetch();
    } catch (Exception $e) {
    }

    if (!$room_data) {
        echo json_encode(['success' => false, 'message' => 'Room not found']);
        exit;
    }

    // Calculate nights
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $nights = $date2->diff($date1)->format("%a");
    $nights = max(1, (int)$nights);

    // Sum prices day-by-day for each plan
    $ep_base_price = 0.00;
    $cp_base_price = 0.00;
    $map_base_price = 0.00;

    $curr_date_ptr = clone $date1;
    while ($curr_date_ptr < $date2) {
        $date_str = $curr_date_ptr->format('Y-m-d');
        $ep_base_price += get_resolved_room_price($pdo, $room_id, $date_str, 'EP', $adults, $room_data);
        $cp_base_price += get_resolved_room_price($pdo, $room_id, $date_str, 'CP', $adults, $room_data);
        $map_base_price += get_resolved_room_price($pdo, $room_id, $date_str, 'MAP', $adults, $room_data);
        $curr_date_ptr->modify('+1 day');
    }

    echo json_encode([
        'success' => true,
        'nights' => $nights,
        'ep_base_price' => $ep_base_price,
        'ep_average_rate' => round($ep_base_price / $nights, 2),
        'cp_base_price' => $cp_base_price,
        'cp_average_rate' => round($cp_base_price / $nights, 2),
        'map_base_price' => $map_base_price,
        'map_average_rate' => round($map_base_price / $nights, 2)
    ]);
    exit;
}

function get_matrix_room_price($room, $adults, $meal_plan)
{
    if (!$room) {
        return 0.00;
    }
    $occupancy = ($adults >= 2) ? 'double' : 'single';
    $plan = strtolower(trim($meal_plan)); // 'ep', 'cp', or 'map'
    $column = "price_" . $occupancy . "_" . $plan;

    return isset($room[$column]) ? (float)$room[$column] : (float)$room['price'];
}

// Get selected room and dates from query parameters
$room_slug = isset($_GET['room']) ? trim($_GET['room']) : '';
$checkin_param = isset($_GET['checkin']) ? trim($_GET['checkin']) : '';
$checkout_param = isset($_GET['checkout']) ? trim($_GET['checkout']) : '';
$adults_param = isset($_GET['adults']) ? intval($_GET['adults']) : 2;
$children_param = isset($_GET['children']) ? intval($_GET['children']) : 0;
$meal_plan_param = isset($_GET['meal_plan']) ? htmlspecialchars(trim($_GET['meal_plan'])) : 'EP';
$room = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE slug = ? AND status = 'active'");
    $stmt->execute([$room_slug]);
    $room = $stmt->fetch();
} catch (Exception $e) {
    error_log("Database error in checkout room load: " . $e->getMessage());
}

// Fallback to default Standard Room if none found
if (!$room) {
    $room = [
        'id' => 1,
        'slug' => 'standard-room',
        'title' => 'Standard Room - Hotel Destin',
        'price' => 1690.00,
        'struck_price' => 4000.00,
        'discount' => '58% off',
        'inventory' => 20
    ];
}

// Process booking generation on Form Submit
$booking_error = '';
$razorpay_order_id = '';
$booking_id = '';
$total_amount_paise = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_booking'])) {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $check_in = isset($_POST['check_in']) ? htmlspecialchars(trim($_POST['check_in'])) : '';
    $check_out = isset($_POST['check_out']) ? htmlspecialchars(trim($_POST['check_out'])) : '';
    $adults = isset($_POST['adults']) ? intval($_POST['adults']) : 2;
    $children = isset($_POST['children']) ? intval($_POST['children']) : 0;
    $meal_plan = isset($_POST['meal_plan']) ? htmlspecialchars(trim($_POST['meal_plan'])) : 'EP';
    $coupon_code = isset($_POST['coupon_code']) ? strtoupper(trim($_POST['coupon_code'])) : '';
    $special_request = isset($_POST['special_request']) ? htmlspecialchars(trim($_POST['special_request'])) : '';

    $guests = $adults + $children;

    // Enforce guest occupancy validation rules
    if ($adults > 3) {
        $booking_error = 'Maximum 3 adults are allowed per room.';
    } else if ($adults === 3 && $children > 0) {
        $booking_error = 'Children are not allowed when reserving for 3 adults in a single room.';
    }

    // Calculate nights
    $date1 = new DateTime($check_in);
    $date2 = new DateTime($check_out);
    $nights = $date2->diff($date1)->format("%a");
    $nights = max(1, (int)$nights);

    // Dynamic date overlap availability check before proceeding to payment
    try {
        $total_inventory = (int)$room['inventory'];

        $booked_stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ? AND check_in < ? AND check_out > ? AND booking_status != 'cancelled'");
        $booked_stmt->execute([$room['id'], $check_out, $check_in]);
        $booked_count = (int)$booked_stmt->fetchColumn();

        $available_count = $total_inventory - $booked_count;
        if ($available_count <= 0) {
            $booking_error = 'We are sold out of this room category for your selected dates. Please search another category or different stay dates.';
        }
    } catch (Exception $e) {
        $booking_error = 'Error validating room availability: ' . $e->getMessage();
    }

    if (empty($booking_error)) {
        // Compute base price by resolving seasonal rates day-by-day
        $base_price = 0.00;
        $curr_date_ptr = new DateTime($check_in);
        $end_date_ptr = new DateTime($check_out);
        while ($curr_date_ptr < $end_date_ptr) {
            $date_str = $curr_date_ptr->format('Y-m-d');
            $base_price += get_resolved_room_price($pdo, $room['id'], $date_str, $meal_plan, $adults, $room);
            $curr_date_ptr->modify('+1 day');
        }
        $discount_amount = 0.00;

        // Apply Coupon discount if valid
        if (!empty($coupon_code)) {
            try {
                $c_stmt = $pdo->prepare("SELECT discount_percent FROM coupons WHERE code = ? AND status = 'active' AND expiry_date >= CURDATE()");
                $c_stmt->execute([$coupon_code]);
                $discount_percent = $c_stmt->fetchColumn();
                if ($discount_percent) {
                    $discount_amount = round(($base_price * $discount_percent) / 100, 2);
                }
            } catch (Exception $e) {
                error_log("Coupon verification error during booking: " . $e->getMessage());
            }
        }

        $subtotal = $base_price - $discount_amount;
        $tax_amount = round($subtotal * 0.05, 2); // 5% GST
        $total_amount = $subtotal + $tax_amount;

        // Generate unique booking tracking reference
        // Format: GV-YYYYMMDD-[HEX_STRING]
        $date_str = date('Ymd');
        $hex_str = strtoupper(bin2hex(random_bytes(3))); // 3 bytes = 6 hex characters
        $booking_id = "GV-" . $date_str . "-" . $hex_str;

        $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'online';

        if ($payment_method === 'offline') {
            try {
                $ins_stmt = $pdo->prepare("INSERT INTO bookings (
                    booking_id, customer_name, customer_email, customer_phone, 
                    check_in, check_out, guests, meal_plan, adults, children, 
                    room_id, coupon_code, total_nights, subtotal, tax, 
                    base_amount, tax_amount, discount_amount, total_amount, 
                    payment_status, booking_status, special_request, payment_method
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'confirmed', ?, 'Pay at Hotel')");

                $ins_stmt->execute([
                    $booking_id,
                    $name,
                    $email,
                    $phone,
                    $check_in,
                    $check_out,
                    $guests,
                    $meal_plan,
                    $adults,
                    $children,
                    $room['id'],
                    !empty($coupon_code) ? $coupon_code : null,
                    $nights,
                    $subtotal,
                    $tax_amount,
                    $base_price,
                    $tax_amount,
                    $discount_amount,
                    $total_amount,
                    $special_request
                ]);

                // Send HTML confirmation email
                require_once __DIR__ . '/mail-helper.php';
                $subject = "Reservation Confirmed (Pay Offline on Arrival) - Ref: " . $booking_id;
                $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e9ecf2; border-radius: 12px;'>
                    <h2 style='color: #9c6047; text-align: center; border-bottom: 2px solid #9c6047; padding-bottom: 10px;'>HOTEL DESTIN GWALIOR</h2>
                    <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                    <p>Thank you for booking with us. Your reservation is confirmed. Please find stay particulars below:</p>
                    <p>Please note that you have chosen to pay offline. <strong>Payment of ₹" . number_format($total_amount, 2) . " is due at the hotel receptionist counter during check-in.</strong></p>
                    
                    <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Booking Reference ID</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($booking_id) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Room Type</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($room['title']) . "</td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Check-In Date</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($check_in) . "</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Check-Out Date</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($check_out) . "</td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Nights</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($nights) . " night(s)</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Meal Plan</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>" . htmlspecialchars($meal_plan) . "</td>
                        </tr>
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Guests Count</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>" . htmlspecialchars($guests) . " guest(s) (Adults: " . htmlspecialchars($adults) . ", Children: " . htmlspecialchars($children) . ")</td>
                        </tr>
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Base Amount</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($base_price, 2) . "</td>
                        </tr>
                        " . ($discount_amount > 0 ? "
                        <tr style='background: #f7f9fc;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>Promo Discount (" . htmlspecialchars($coupon_code) . ")</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; color: #d13232;'>-₹" . number_format($discount_amount, 2) . "</td>
                        </tr>
                        " : "") . "
                        <tr>
                            <td style='padding: 10px; border: 1px solid #e9ecf2; font-weight: bold;'>GST Taxes (5%)</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($tax_amount, 2) . "</td>
                        </tr>
                        <tr style='background: #fdfaf8; font-size: 16px; font-weight: bold; color: #9c6047;'>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>Total Cost (Payable at Hotel)</td>
                            <td style='padding: 10px; border: 1px solid #e9ecf2;'>₹" . number_format($total_amount, 2) . "</td>
                        </tr>
                    </table>

                    <p><strong>Special Request:</strong> " . (!empty($special_request) ? htmlspecialchars($special_request) : 'None') . "</p>
                    <p style='border-top: 1px solid #e9ecf2; padding-top: 15px; text-align: center; color: #777; font-size: 12px;'>
                        Hotel Destin Gwalior, Sachin Tendulkar Rd. For queries call +91 9203509944.
                    </p>
                </div>";

                // Dispatch copy to Customer
                send_mail($email, $subject, $body, true);

                // Dispatch copy to Hotel Owner/Admin alerts
                send_mail(OWNER_EMAIL, "NEW OFFLINE BOOKING - " . $booking_id, $body, true);

                // Send WhatsApp notification to the owner
                $booking_msg = "🏨 *NEW OFFLINE BOOKING (PAY AT HOTEL)* 🏨\n\n";
                $booking_msg .= "*Booking ID:* " . $booking_id . "\n";
                $booking_msg .= "*Name:* " . $name . "\n";
                $booking_msg .= "*Phone:* " . $phone . "\n";
                $booking_msg .= "*Email:* " . $email . "\n";
                $booking_msg .= "*Room Type:* " . $room['title'] . "\n";
                $booking_msg .= "*Check-in:* " . $check_in . "\n";
                $booking_msg .= "*Check-out:* " . $check_out . "\n";
                $booking_msg .= "*Nights:* " . $nights . " night(s)\n";
                $booking_msg .= "*Meal Plan:* " . $meal_plan . "\n";
                $booking_msg .= "*Guests:* " . $guests . " (Adults: " . $adults . ", Children: " . $children . ")\n";
                $booking_msg .= "*Total Cost:* ₹" . number_format($total_amount, 2) . "\n";
                if (!empty($special_request)) {
                    $booking_msg .= "*Special Request:* " . $special_request . "\n";
                }
                send_whatsapp_message($booking_msg);

                header("Location: thank-you.php?ref=" . urlencode($booking_id));
                exit;
            } catch (Exception $e) {
                error_log("Offline booking DB insert error: " . $e->getMessage());
                $booking_error = 'Error saving stay transaction: ' . $e->getMessage();
            }
        }

        $is_sandbox_simulation = false;
        $key_id = get_setting('razorpay_key_id') ?: RAZORPAY_KEY_ID;
        $key_secret = get_setting('razorpay_key_secret') ?: RAZORPAY_KEY_SECRET;

        if (empty($key_id) || empty($key_secret) || strpos($key_id, 'placeholder') !== false || strpos($key_id, 'YourKeyHere') !== false) {
            $is_sandbox_simulation = true;
        }

        if ($is_sandbox_simulation) {
            $razorpay_order_id = 'order_sandbox_' . rand(100000, 999999);
            try {
                $ins_stmt = $pdo->prepare("INSERT INTO bookings (
                    booking_id, customer_name, customer_email, customer_phone, 
                    check_in, check_out, guests, meal_plan, adults, children, 
                    room_id, coupon_code, total_nights, subtotal, tax, 
                    base_amount, tax_amount, discount_amount, total_amount, 
                    payment_status, booking_status, special_request, razorpay_order_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, ?)");

                $ins_stmt->execute([
                    $booking_id,
                    $name,
                    $email,
                    $phone,
                    $check_in,
                    $check_out,
                    $guests,
                    $meal_plan,
                    $adults,
                    $children,
                    $room['id'],
                    !empty($coupon_code) ? $coupon_code : null,
                    $nights,
                    $subtotal,
                    $tax_amount,
                    $base_price,
                    $tax_amount,
                    $discount_amount,
                    $total_amount,
                    $special_request,
                    $razorpay_order_id
                ]);
            } catch (Exception $e) {
                error_log("Sandbox DB insert error: " . $e->getMessage());
                $booking_error = 'Error saving stay transaction: ' . $e->getMessage();
            }
        } else {
            try {
                $api = new Api($key_id, $key_secret);

                $orderData = [
                    'receipt'         => $booking_id,
                    'amount'          => round($total_amount * 100),
                    'currency'        => 'INR',
                    'payment_capture' => 1
                ];

                $razorpayOrder = $api->order->create($orderData);
                $razorpay_order_id = $razorpayOrder['id'];
                $total_amount_paise = $orderData['amount'];

                $ins_stmt = $pdo->prepare("INSERT INTO bookings (
                    booking_id, customer_name, customer_email, customer_phone, 
                    check_in, check_out, guests, meal_plan, adults, children, 
                    room_id, coupon_code, total_nights, subtotal, tax, 
                    base_amount, tax_amount, discount_amount, total_amount, 
                    payment_status, booking_status, special_request, razorpay_order_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, ?)");

                $ins_stmt->execute([
                    $booking_id,
                    $name,
                    $email,
                    $phone,
                    $check_in,
                    $check_out,
                    $guests,
                    $meal_plan,
                    $adults,
                    $children,
                    $room['id'],
                    !empty($coupon_code) ? $coupon_code : null,
                    $nights,
                    $subtotal,
                    $tax_amount,
                    $base_price,
                    $tax_amount,
                    $discount_amount,
                    $total_amount,
                    $special_request,
                    $razorpay_order_id
                ]);
            } catch (Exception $e) {
                error_log("Razorpay Order creation failed, falling back to Sandbox simulation: " . $e->getMessage());
                $is_sandbox_simulation = true;
                $razorpay_order_id = 'order_sandbox_' . rand(100000, 999999);

                $ins_stmt = $pdo->prepare("INSERT INTO bookings (
                    booking_id, customer_name, customer_email, customer_phone, 
                    check_in, check_out, guests, meal_plan, adults, children, 
                    room_id, coupon_code, total_nights, subtotal, tax, 
                    base_amount, tax_amount, discount_amount, total_amount, 
                    payment_status, booking_status, special_request, razorpay_order_id
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, ?)");

                $ins_stmt->execute([
                    $booking_id,
                    $name,
                    $email,
                    $phone,
                    $check_in,
                    $check_out,
                    $guests,
                    $meal_plan,
                    $adults,
                    $children,
                    $room['id'],
                    !empty($coupon_code) ? $coupon_code : null,
                    $nights,
                    $subtotal,
                    $tax_amount,
                    $base_price,
                    $tax_amount,
                    $discount_amount,
                    $total_amount,
                    $special_request,
                    $razorpay_order_id
                ]);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Room Checkout - Hotel Destin Gwalior</title>

    <style>
        .checkout-container {
            padding: 30px 0 60px 0;
            background-color: #F3EDE2;
            overflow-x: hidden;
        }

        .checkout-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #cbd5e1;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
        }

        .summary-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #cbd5e1;
            position: sticky;
            top: 100px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 13.5px;
            color: #555;
            font-weight: 500;
        }

        .price-total {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed #cbd5e1;
            font-size: 16px;
            font-weight: 800;
            color: #15803d;
            margin-bottom: 15px;
        }

        .coupon-box {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
        }

        .form-control-custom {
            height: 40px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            padding: 6px 12px !important;
            font-size: 13px !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
            color: #0f172a !important;
        }

        .form-control-custom:focus {
            border-color: #9c6047 !important;
            box-shadow: 0 0 0 3px rgba(156, 96, 71, 0.1) !important;
            outline: none !important;
        }

        .form-label-custom {
            font-size: 10.5px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #64748b !important;
            font-weight: 700 !important;
            margin-bottom: 4px !important;
            display: block !important;
        }

        .btn-payment {
            background: #0f172a;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14.5px;
            padding: 13px 20px;
            transition: all 0.2s ease;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.1);
        }

        .btn-payment:hover {
            background: #9c6047;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(156, 96, 71, 0.2);
        }

        .btn-payment:active {
            transform: translateY(0);
        }

        /* Premium Payment Selection Cards */
        .payment-option-card {
            border: 2px solid #cbd5e1;
            border-radius: 12px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            user-select: none;
        }

        .payment-option-card:hover {
            border-color: #9c6047;
            box-shadow: 0 4px 12px rgba(156, 96, 71, 0.05);
            transform: translateY(-2px);
        }

        .payment-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }

        .payment-icon {
            transition: transform 0.25s ease;
        }

        .payment-option-card:hover .payment-icon {
            transform: scale(1.1);
        }

        .payment-title {
            font-size: 14.5px;
            font-weight: 700;
            color: #0f172a;
            transition: color 0.25s ease;
        }

        .payment-desc {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
            margin: 0;
        }

        .payment-indicator {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            position: relative;
            transition: all 0.25s ease;
            flex-shrink: 0;
        }

        .payment-indicator::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #9c6047;
            transition: transform 0.2s ease;
        }

        /* Check State Rules */
        .btn-check:checked+.payment-option-card {
            border-color: #9c6047;
            background: rgba(156, 96, 71, 0.02);
            box-shadow: 0 4px 16px rgba(156, 96, 71, 0.08);
        }

        .btn-check:checked+.payment-option-card .payment-icon-wrapper {
            background: rgba(156, 96, 71, 0.1);
            color: #9c6047;
        }

        .btn-check:checked+.payment-option-card .payment-title {
            color: #9c6047;
        }

        .btn-check:checked+.payment-option-card .payment-indicator {
            border-color: #9c6047;
        }

        .btn-check:checked+.payment-option-card .payment-indicator::after {
            transform: translate(-50%, -50%) scale(1);
        }

        /* Payment Gateway Badges Under Submit Button */
        .pay-gateway-badge:hover {
            transform: translateY(-2px);
            border-color: #9c6047 !important;
            box-shadow: 0 4px 6px rgba(156, 96, 71, 0.1) !important;
        }

        /* Available Coupons styling */
        .available-coupon-tag {
            background: rgba(156, 96, 71, 0.05) !important;
            border: 1px dashed rgba(156, 96, 71, 0.3) !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
            font-size: 11.5px !important;
            font-weight: 600 !important;
            color: #9c6047 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .available-coupon-tag:hover {
            background: #9c6047 !important;
            color: #ffffff !important;
            border-style: solid !important;
            border-color: #9c6047 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(156, 96, 71, 0.15) !important;
        }

        .coupon-tag-code {
            font-weight: 750 !important;
            text-transform: uppercase !important;
        }

        .coupon-tag-percent {
            opacity: 0.85 !important;
            font-size: 10.5px !important;
            border-left: 1px solid rgba(156, 96, 71, 0.25);
            padding-left: 6px;
        }
        
        .available-coupon-tag:hover .coupon-tag-percent {
            border-left-color: rgba(255, 255, 255, 0.35);
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>

<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <section class="checkout-container">
            <div class="container">
                <div class="mb-20">
                    <h1 class="font-heading neutral-1000 mb-5" style="font-size: 26px; font-weight: 800;">Room Checkout</h1>
                    <p class="neutral-500" style="font-size: 13.5px;">Provide stay details and pay securely using Razorpay to confirm booking instantly.</p>
                </div>

                <?php if (!empty($booking_error)): ?>
                    <div class="alert alert-danger mb-30" style="border-radius: 8px; font-size: 14.5px; padding: 12px 20px;">
                        <?= $booking_error ?>
                    </div>
                <?php endif; ?>

                <form id="checkoutForm" action="checkout.php?room=<?= urlencode($room['slug']) ?>" method="POST">
                    <div class="row g-4">
                        <!-- Left: Details Form -->
                        <div class="col-lg-7">
                            <div class="checkout-card">
                                <h3 class="font-heading mb-25" style="font-size: 20px;">Guest Information</h3>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Full Name *</label>
                                            <input class="form-control-custom" type="text" name="name" placeholder="Guest Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Email Address *</label>
                                            <input class="form-control-custom" type="email" name="email" placeholder="email@domain.com" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Phone Number *</label>
                                            <input class="form-control-custom" type="text" name="phone" placeholder="Phone Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Check-In Date *</label>
                                            <input id="checkInDate" class="form-control-custom" type="date" name="check_in" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($checkin_param) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Check-Out Date *</label>
                                            <input id="checkOutDate" class="form-control-custom" type="date" name="check_out" value="<?= htmlspecialchars($checkout_param) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Adults *</label>
                                            <input id="adultsInput" class="form-control-custom" type="number" name="adults" value="<?= htmlspecialchars($adults_param > 3 ? 3 : $adults_param) ?>" min="1" max="3" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Children</label>
                                            <input id="childrenInput" class="form-control-custom" type="number" name="children" value="<?= htmlspecialchars($children_param) ?>" min="0" max="4" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Meal Plan *</label>
                                            <select id="mealPlanSelect" class="form-control-custom" name="meal_plan" style="height:42px; background-position: right 15px center;" required>
                                                <option value="EP" <?= $meal_plan_param === 'EP' ? 'selected' : '' ?>>EP (Room Only)</option>
                                                <option value="CP" <?= $meal_plan_param === 'CP' ? 'selected' : '' ?>>CP (Room + Breakfast)</option>
                                                <option value="MAP" <?= $meal_plan_param === 'MAP' ? 'selected' : '' ?>>MAP (Room + Breakfast + One Meal)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Special Requests / Requirements</label>
                                            <textarea class="form-control-custom" name="special_request" rows="3" placeholder="Double bed, tea kettle preferences, airport transfer scheduling..."></textarea>
                                        </div>
                                    </div>



                                    <!-- Hidden Inputs -->
                                    <input type="hidden" id="hiddenCouponCode" name="coupon_code" value="">
                                    <input type="hidden" name="submit_booking" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Right: Price Breakdown Sidebar -->
                        <div class="col-lg-5">
                            <div class="summary-card">
                                <h3 class="font-heading mb-20" style="font-size: 18px;">Booking Summary</h3>
                                <div class="d-flex mb-20 align-items-center">
                                    <div style="width: 80px; height: 60px; border-radius: 8px; overflow: hidden; background: #eee; margin-right: 15px; flex-shrink: 0;">
                                        <img src="<?= $room['image_path'] ?? 'assets/imgs/page/room/banner-room.png' ?>" alt="Room Image" style="width:100%; height:100%; object-fit:cover;">
                                    </div>
                                    <div>
                                        <h5 style="font-size: 14.5px; font-weight: 600; margin-bottom: 4px; line-height: 1.3; color: #0f172a;"><?= htmlspecialchars($room['title']) ?></h5>
                                        <span class="badge bg-dark text-white" style="font-size: 10px; padding: 4px 8px; border-radius: 4px;"><?= htmlspecialchars($room['type'] ?? 'Deluxe') ?> Room</span>
                                    </div>
                                </div>

                                <!-- Coupon Box -->
                                <label class="form-label-custom">Apply Promo Code</label>
                                <div class="coupon-box">
                                    <input id="couponInput" class="form-control-custom" type="text" placeholder="e.g. DESTIN" style="height: 42px;">
                                    <button id="btnApplyCoupon" class="btn btn-black text-white" type="button" style="padding:0 20px; border-radius:8px; font-size:13px;">Apply</button>
                                </div>
                                <div id="couponStatus" class="mb-15" style="font-size:13px; display:none;"></div>

                                <?php
                                $public_coupons = [];
                                try {
                                    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE status = 'active' AND show_in_checkout = 1 AND expiry_date >= CURDATE() ORDER BY discount_percent DESC");
                                    $stmt->execute();
                                    $public_coupons = $stmt->fetchAll();
                                } catch (Exception $e) {
                                    error_log("Database error loading public coupons: " . $e->getMessage());
                                }
                                if (count($public_coupons) > 0):
                                ?>
                                    <div class="available-coupons-wrapper mb-15">
                                        <span class="d-block mb-5" style="font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; text-align: left;">Available Promo Code(s)</span>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($public_coupons as $pub_cp): ?>
                                                <button type="button" class="available-coupon-tag" onclick="selectCouponCode('<?= htmlspecialchars($pub_cp['code']) ?>')" title="<?= htmlspecialchars($pub_cp['title']) ?>">
                                                    <span class="coupon-tag-code"><?= htmlspecialchars($pub_cp['code']) ?></span>
                                                    <span class="coupon-tag-percent"><?= htmlspecialchars($pub_cp['discount_percent']) ?>% OFF</span>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Pricing Breakdown -->
                                <div class="price-row">
                                    <span>Room Rate (1 Night)</span>
                                    <span>₹<span id="ratePerNight"><?= number_format($room['price'], 2) ?></span></span>
                                </div>
                                <div class="price-row">
                                    <span>Total Nights</span>
                                    <span><span id="labelNights">1</span> Night(s)</span>
                                </div>
                                <div class="price-row">
                                    <span>Base Price</span>
                                    <span>₹<span id="basePrice"><?= number_format($room['price'], 2) ?></span></span>
                                </div>
                                <div class="price-row" id="couponDiscountRow" style="display:none; color: #d13232;">
                                    <span>Promo Discount (<span id="couponPercent">0</span>%)</span>
                                    <span>-₹<span id="discountAmount">0.00</span></span>
                                </div>
                                <div class="price-row">
                                    <span>Subtotal</span>
                                    <span>₹<span id="subtotalPrice"><?= number_format($room['price'], 2) ?></span></span>
                                </div>
                                <div class="price-row">
                                    <span>GST Taxes (5%)</span>
                                    <span>₹<span id="taxAmount"><?= number_format($room['price'] * 0.05, 2) ?></span></span>
                                </div>
                                <!-- Select Payment Option -->
                                <div class="mt-20 mb-15 pt-15" style="border-top: 1px dashed #cbd5e1;">
                                    <label class="form-label-custom mb-10">Select Payment Option *</label>
                                    <div class="row g-2">
                                        <!-- Online Payment Card -->
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="payment_method" id="pay_online" value="online" checked autocomplete="off">
                                            <label class="payment-option-card d-flex align-items-center gap-2 px-2 py-2 w-100 h-100" for="pay_online" style="min-height: 52px;">
                                                <div class="payment-icon-wrapper d-flex align-items-center justify-content-center" style="width:28px; height:28px; border-radius:5px; flex-shrink:0;">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="payment-icon">
                                                        <rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect>
                                                        <line x1="2" y1="10" x2="22" y2="10"></line>
                                                    </svg>
                                                </div>
                                                <div class="payment-content flex-grow-1" style="min-width: 0;">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="payment-title" style="font-size: 12px; display: block; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pay Online</span>
                                                        <div class="payment-indicator" style="width:14px; height:14px; flex-shrink:0;"></div>
                                                    </div>
                                                    <span style="font-size: 9.5px; color:#64748b; display:block; line-height:1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: left;">Instant Confirm</span>
                                                </div>
                                            </label>
                                        </div>

                                        <!-- Offline Payment Card -->
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="payment_method" id="pay_offline" value="offline" autocomplete="off">
                                            <label class="payment-option-card d-flex align-items-center gap-2 px-2 py-2 w-100 h-100" for="pay_offline" style="min-height: 52px;">
                                                <div class="payment-icon-wrapper d-flex align-items-center justify-content-center" style="width:28px; height:28px; border-radius:5px; flex-shrink:0;">
                                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="payment-icon">
                                                        <path d="M3 21h18M3 7v14M21 7v14M16 3H8a2 2 0 00-2 2v2h12V5a2 2 0 00-2-2zM12 11h.01M12 15h.01M8 11h.01M8 15h.01M16 11h.01M16 15h.01"></path>
                                                    </svg>
                                                </div>
                                                <div class="payment-content flex-grow-1" style="min-width: 0;">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="payment-title" style="font-size: 12px; display: block; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Pay at Hotel</span>
                                                        <div class="payment-indicator" style="width:14px; height:14px; flex-shrink:0;"></div>
                                                    </div>
                                                    <span style="font-size: 9.5px; color:#64748b; display:block; line-height:1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; text-align: left;">Pay on Arrival</span>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="price-total">
                                    <span>Total Payable</span>
                                    <span>₹<span id="grandTotal"><?= number_format($room['price'] * 1.05, 2) ?></span></span>
                                </div>

                                <button class="btn-payment" type="submit">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Proceed to Online Payment
                                </button>

                                 <div class="online-payment-gateways" style="margin-top: 15px; text-align: center;">
                                     <span style="font-size: 10.5px; color: #64748b; font-weight: 600; display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Guaranteed Safe &amp; Secure Checkout</span>
                                     <div style="display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: nowrap; width: 100%;">
                                         <!-- UPI -->
                                         <div class="pay-gateway-badge" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.2s; cursor: default; overflow: hidden; flex: 1.5 1 auto; max-width: 72px; min-width: 44px; flex-shrink: 1;">
                                             <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo-vector.svg" alt="UPI" style="max-height: 50%; max-width: 85%; object-fit: contain;">
                                         </div>
                                         <!-- Google Pay -->
                                         <div class="pay-gateway-badge" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.2s; cursor: default; overflow: hidden; flex: 1 1 auto; max-width: 48px; min-width: 32px; flex-shrink: 1;">
                                             <img src="https://www.svgrepo.com/show/508690/google-pay.svg" alt="Google Pay" style="max-height: 85%; max-width: 85%; object-fit: contain; transform: scale(1.1); margin-top: 1px;">
                                         </div>
                                         <!-- Apple Pay -->
                                         <div class="pay-gateway-badge" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.2s; cursor: default; overflow: hidden; flex: 1 1 auto; max-width: 48px; min-width: 32px; flex-shrink: 1;">
                                             <img src="https://www.svgrepo.com/show/508402/apple-pay.svg" alt="Apple Pay" style="max-height: 80%; max-width: 85%; object-fit: contain; transform: scale(1.1); margin-top: 1px;">
                                         </div>
                                         <!-- Mastercard -->
                                         <div class="pay-gateway-badge" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.2s; cursor: default; overflow: hidden; flex: 1 1 auto; max-width: 48px; min-width: 32px; flex-shrink: 1;">
                                             <img src="https://www.svgrepo.com/show/508703/mastercard.svg" alt="Mastercard" style="max-height: 65%; max-width: 85%; object-fit: contain;">
                                         </div>
                                         <!-- Visa -->
                                         <div class="pay-gateway-badge" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.2s; cursor: default; overflow: hidden; flex: 1 1 auto; max-width: 48px; min-width: 32px; flex-shrink: 1;">
                                             <img src="https://www.svgrepo.com/show/508730/visa-classic.svg" alt="Visa" style="max-height: 60%; max-width: 85%; object-fit: contain;">
                                         </div>
                                         <!-- PayPal -->
                                         <div class="pay-gateway-badge" style="background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0; display: inline-flex; align-items: center; justify-content: center; height: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.03); transition: all 0.2s; cursor: default; overflow: hidden; flex: 1 1 auto; max-width: 48px; min-width: 32px; flex-shrink: 1;">
                                             <img src="https://cdn.jsdelivr.net/gh/aaronfagan/svg-credit-card-payment-icons@main/flat/paypal.svg" alt="PayPal" style="max-height: 60%; max-width: 85%; object-fit: contain;">
                                         </div>
                                     </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <?php include("include/footer.php"); ?>

    <!-- Razorpay payment client script integration -->
    <?php if (!empty($razorpay_order_id)): ?>
        <?php if ($is_sandbox_simulation): ?>
            <!-- Simulated Sandbox Payment overlay screen -->
            <div id="sandboxPaymentModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.65); z-index: 99999; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                <div style="background: #ffffff; border-radius: 16px; max-width: 440px; width: 100%; padding: 30px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15); text-align: center; border: 1px solid #e5e7eb; margin: 20px;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(156, 96, 71, 0.08); color: #9c6047; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 24px;">🔒</div>
                    <h3 style="font-size: 19px; font-weight: 850; color: #0f172a; margin-bottom: 6px;">Razorpay Sandbox Simulator</h3>
                    <p style="font-size: 13px; color: #64748b; margin-bottom: 22px; line-height: 1.5;">Test your stay checkouts securely. Click success to finalize mock payments.</p>

                    <div style="background: #fafaf9; border-radius: 12px; padding: 16px; margin-bottom: 25px; border: 1px solid #f0f0ed; text-align: left;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px;">
                            <span style="color:#64748b; font-weight:550;">Booking Ref:</span>
                            <span style="font-weight: 700; color:#0f172a; font-family: monospace;"><?= htmlspecialchars($booking_id) ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; border-top: 1px dashed #e2e8f0; padding-top: 10px; margin-top: 6px;">
                            <span style="color:#64748b;">Payable Amount:</span>
                            <span style="color: #16a34a;">₹<?= number_format($total_amount, 2) ?></span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <button onclick="triggerSandboxPayment(true)" style="background: #16a34a; color: #ffffff; border: none; border-radius: 8px; font-weight: 700; font-size: 14.5px; padding: 12px; width: 100%; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#15803d';" onmouseout="this.style.backgroundColor='#16a34a';">Simulate Success</button>
                        <button onclick="triggerSandboxPayment(false)" style="background: #ef4444; color: #ffffff; border: none; border-radius: 8px; font-weight: 700; font-size: 14.5px; padding: 12px; width: 100%; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#dc2626';" onmouseout="this.style.backgroundColor='#ef4444';">Simulate Failure</button>
                    </div>
                </div>
            </div>

            <script>
                function triggerSandboxPayment(success) {
                    if (success) {
                        var form = document.createElement('form');
                        form.setAttribute('method', 'POST');
                        form.setAttribute('action', 'payment-callback.php');

                        var fields = {
                            'razorpay_payment_id': 'pay_sandbox_' + Math.floor(Math.random() * 899999 + 100000),
                            'razorpay_order_id': "<?= $razorpay_order_id ?>",
                            'razorpay_signature': 'sig_sandbox_' + Math.floor(Math.random() * 899999 + 100000),
                            'booking_id': "<?= $booking_id ?>"
                        };

                        for (var key in fields) {
                            if (fields.hasOwnProperty(key)) {
                                var hiddenField = document.createElement('input');
                                hiddenField.setAttribute('type', 'hidden');
                                hiddenField.setAttribute('name', key);
                                hiddenField.setAttribute('value', fields[key]);
                                form.appendChild(hiddenField);
                            }
                        }

                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        alert("Simulated Payment Cancelled/Failed.");
                        window.location.href = "rooms.php";
                    }
                }
            </script>
        <?php else: ?>
            <!-- Real Razorpay script -->
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                var options = {
                    "key": "<?= htmlspecialchars($key_id) ?>",
                    "amount": "<?= $total_amount_paise ?>",
                    "currency": "INR",
                    "name": "<?= SITE_NAME ?>",
                    "description": "Secure Room Stay Booking",
                    "image": "assets/imgs/template/logo-destin.png",
                    "order_id": "<?= $razorpay_order_id ?>",
                    "handler": function(response) {
                        var form = document.createElement('form');
                        form.setAttribute('method', 'POST');
                        form.setAttribute('action', 'payment-callback.php');

                        var fields = {
                            'razorpay_payment_id': response.razorpay_payment_id,
                            'razorpay_order_id': response.razorpay_order_id,
                            'razorpay_signature': response.razorpay_signature,
                            'booking_id': "<?= $booking_id ?>"
                        };

                        for (var key in fields) {
                            if (fields.hasOwnProperty(key)) {
                                var hiddenField = document.createElement('input');
                                hiddenField.setAttribute('type', 'hidden');
                                hiddenField.setAttribute('name', key);
                                hiddenField.setAttribute('value', fields[key]);
                                form.appendChild(hiddenField);
                            }
                        }

                        document.body.appendChild(form);
                        form.submit();
                    },
                    "prefill": {
                        "name": "<?= htmlspecialchars($name) ?>",
                        "email": "<?= htmlspecialchars($email) ?>",
                        "contact": "<?= htmlspecialchars($phone) ?>"
                    },
                    "theme": {
                        "color": "#9c6047"
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function(response) {
                    alert("Payment failed: " + response.error.description);
                });
                rzp1.open();
            </script>
        <?php endif; ?>
    <?php endif; ?>

    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            var roomPrice = <?= (float)$room['price'] ?>;
            var discountPercent = 0;

            // Set Check-out date min attribute dynamically based on check-in date choice
            $('#checkInDate').change(function() {
                var checkInVal = $(this).val();
                if (checkInVal) {
                    $('#checkOutDate').attr('min', checkInVal);
                    recalculatePrices();
                }
            });

            $('#checkOutDate').change(function() {
                recalculatePrices();
            });

            // Handle coupon apply button clicks via AJAX
            $('#btnApplyCoupon').click(function() {
                var code = $('#couponInput').val().trim();
                if (!code) {
                    $('#couponStatus').removeClass().addClass('text-danger').text('Please enter a coupon code.').show();
                    return;
                }

                $.ajax({
                    url: 'checkout.php',
                    method: 'POST',
                    data: {
                        action: 'apply_coupon',
                        code: code
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            discountPercent = response.discount_percent;
                            $('#hiddenCouponCode').val(code);
                            $('#couponStatus').removeClass().addClass('text-success').text(response.message).show();
                            recalculatePrices();
                        } else {
                            discountPercent = 0;
                            $('#hiddenCouponCode').val('');
                            $('#couponStatus').removeClass().addClass('text-danger').text(response.message).show();
                            recalculatePrices();
                        }
                    },
                    error: function() {
                        $('#couponStatus').removeClass().addClass('text-danger').text('Error validating promo code.').show();
                    }
                });
            });

            // Recalculate when parameters change
            $('#adultsInput, #childrenInput, #mealPlanSelect').change(function() {
                recalculatePrices();
            });

            function recalculatePrices() {
                var checkIn = $('#checkInDate').val();
                var checkOut = $('#checkOutDate').val();
                var adults = parseInt($('#adultsInput').val()) || 2;
                var mealPlan = $('#mealPlanSelect').val() || 'EP';
                var roomId = <?= intval($room['id']) ?>;

                if (!checkIn || !checkOut) return;

                $.ajax({
                    url: 'checkout.php',
                    method: 'POST',
                    data: {
                        action: 'get_pricing',
                        check_in: checkIn,
                        check_out: checkOut,
                        adults: adults,
                        room_id: roomId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            var nights = response.nights;
                            var basePrice = 0;
                            var ratePerNight = 0;

                            if (mealPlan === 'CP') {
                                basePrice = response.cp_base_price;
                                ratePerNight = response.cp_average_rate;
                            } else if (mealPlan === 'MAP') {
                                basePrice = response.map_base_price;
                                ratePerNight = response.map_average_rate;
                            } else {
                                basePrice = response.ep_base_price;
                                ratePerNight = response.ep_average_rate;
                            }

                            $('#labelNights').text(nights);
                            $('#ratePerNight').text(ratePerNight.toFixed(2));
                            $('#basePrice').text(basePrice.toFixed(2));

                            updateTotals(basePrice);
                        }
                    }
                });
            }

            function updateTotals(basePrice) {
                var discountAmount = 0;
                if (discountPercent > 0) {
                    discountAmount = (basePrice * discountPercent) / 100;
                    $('#couponPercent').text(discountPercent);
                    $('#discountAmount').text(discountAmount.toFixed(2));
                    $('#couponDiscountRow').show();
                } else {
                    $('#couponDiscountRow').hide();
                }

                var subtotal = basePrice - discountAmount;
                $('#subtotalPrice').text(subtotal.toFixed(2));

                var taxAmount = subtotal * 0.05; // 5% GST
                var grandTotal = subtotal + taxAmount;

                $('#taxAmount').text(taxAmount.toFixed(2));
                $('#grandTotal').text(grandTotal.toFixed(2));
            }

            // Payment method dynamic text toggle helper
            function updateSubmitButton() {
                var payOffline = document.getElementById('pay_offline');
                var btnPayment = document.querySelector('.btn-payment');
                var gatewayLogos = document.querySelector('.online-payment-gateways');
                if (payOffline && payOffline.checked) {
                    btnPayment.innerHTML = `
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Confirm Booking (Pay at Hotel)
                                            `;
                    if (gatewayLogos) gatewayLogos.style.display = 'none';
                } else {
                    btnPayment.innerHTML = `
                                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="display:inline-block; vertical-align:middle; margin-right:4px;">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                Proceed to Online Payment
                                            `;
                    if (gatewayLogos) gatewayLogos.style.display = 'flex';
                }
            }

            $(document).on('change', 'input[name="payment_method"]', updateSubmitButton);

            // Enforce occupancy rules inside checkout form input fields
            $('#adultsInput, #childrenInput').on('change input', function() {
                var adults = parseInt($('#adultsInput').val()) || 1;
                var children = parseInt($('#childrenInput').val()) || 0;

                if (adults >= 3) {
                    $('#adultsInput').val(3);
                    $('#childrenInput').val(0).attr('max', 0);
                } else {
                    $('#childrenInput').attr('max', 4);
                }
                recalculatePrices();
            });

            // Initial execution on page load
            recalculatePrices();
        });

        function selectCouponCode(code) {
            var input = document.getElementById('couponInput');
            if (input) {
                input.value = code;
                var applyBtn = document.getElementById('btnApplyCoupon');
                if (applyBtn) {
                    applyBtn.click();
                }
            }
        }
    </script>
</body>

</html>