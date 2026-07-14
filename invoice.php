<?php
require_once __DIR__ . '/db.php';

$ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$booking = null;

if (!empty($ref)) {
    try {
        $stmt = $pdo->prepare("
            SELECT b.*, r.title as room_title, r.type as room_type 
            FROM bookings b 
            LEFT JOIN rooms r ON b.room_id = r.id 
            WHERE b.booking_id = ? OR b.invoice_no = ?
        ");
        $stmt->execute([$ref, $ref]);
        $booking = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Invoice query failure: " . $e->getMessage());
    }
}

if (!$booking) {
    http_response_code(404);
    echo "<div style='font-family: Arial, sans-serif; text-align: center; padding: 50px;'>";
    echo "<h2 style='color:#dc2626;'>Error: Reservation invoice record not found.</h2>";
    echo "<p>Please verify your reference number or contact support.</p>";
    echo "<p><a href='index.php' style='color:#9c6047; text-decoration:none; font-weight:bold;'>Return to Home</a></p>";
    echo "</div>";
    exit;
}

// Load dynamic hotel contact details from database
$hotel_name = get_setting('hotel_name') ?: 'Hotel Destin';
$hotel_phone = get_setting('hotel_phone') ?: '+91 70000 00000';
$hotel_email = get_setting('hotel_email') ?: 'info@hoteldestin.com';
$hotel_address = get_setting('hotel_address') ?: 'Sachin Tendulkar Road, Kailash Nagar, Gwalior, MP, India';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= htmlspecialchars($booking['invoice_no'] ?: $booking['booking_id']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #1e293b;
            margin: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .logo-img {
            max-height: 55px;
            margin-bottom: 12px;
        }
        .header-table, .details-table, .items-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .header-table td, .details-table td {
            border: none;
            padding: 0;
        }
        .text-right {
            text-align: right;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: 700;
            color: #9c6047;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .section-title {
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 12px;
            margin-top: 15px;
        }
        .details-table td {
            font-size: 13.5px;
            line-height: 1.6;
        }
        .items-table {
            border-collapse: collapse;
        }
        .items-table th {
            background: #f8fafc;
            padding: 10px;
            font-weight: 600;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            font-size: 12.5px;
            color: #475569;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }
        .total-box {
            float: right;
            width: 320px;
            margin-top: 15px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 6px 10px;
            font-size: 13.5px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: 700;
            color: #3c7a4b;
            border-top: 2px dashed #e2e8f0;
            padding-top: 10px !important;
        }
        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #64748b;
            margin-top: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .btn-print {
            background: #9c6047;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-print:hover {
            background: #854f38;
        }
        @media print {
            body {
                margin: 0;
                color: #000;
            }
            .invoice-box {
                border: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px auto; display: flex; justify-content: space-between; align-items: center;">
        <a href="index.php" style="color: #475569; text-decoration: none; font-weight: 600; font-size: 14px;">← Back to Home</a>
        <button onclick="window.print()" class="btn-print">Print Invoice</button>
    </div>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <img src="assets/imgs/template/logo-destin.png" alt="Logo" class="logo-img"><br>
                    <strong><?= htmlspecialchars($hotel_name) ?></strong><br>
                    <?= htmlspecialchars($hotel_address) ?><br>
                    Phone: <?= htmlspecialchars($hotel_phone) ?><br>
                    Email: <?= htmlspecialchars($hotel_email) ?>
                </td>
                <td class="text-right">
                    <span class="invoice-title">Tax Invoice</span><br>
                    <strong>Invoice No:</strong> <?= htmlspecialchars($booking['invoice_no'] ?: 'N/A (Pending)') ?><br>
                    <strong>Booking Ref:</strong> <?= htmlspecialchars($booking['booking_id']) ?><br>
                    <strong>Date:</strong> <?= date('d M Y', strtotime($booking['created_at'])) ?><br>
                    <strong>Payment Method:</strong> <?= htmlspecialchars($booking['payment_method'] ?: 'Razorpay') ?><br>
                    <strong>Status:</strong> <span style="text-transform: uppercase; font-weight: 700; color: <?= $booking['payment_status'] === 'paid' ? '#3c7a4b' : ($booking['payment_status'] === 'refunded' ? '#3b82f6' : '#dc2626') ?>;"><?= htmlspecialchars($booking['payment_status']) ?></span>
                </td>
            </tr>
        </table>

        <div class="section-title">Guest Details</div>
        <table class="details-table">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <strong>Billing To:</strong><br>
                    Name: <?= htmlspecialchars($booking['customer_name']) ?><br>
                    Phone: <?= htmlspecialchars($booking['customer_phone']) ?><br>
                    Email: <?= htmlspecialchars($booking['customer_email']) ?>
                </td>
                <td style="vertical-align: top;">
                    <strong>Stay Particulars:</strong><br>
                    Category: <?= htmlspecialchars($booking['room_title'] ?: 'Deluxe Room') ?><br>
                    Check In: <?= date('d M Y', strtotime($booking['check_in'])) ?><br>
                    Check Out: <?= date('d M Y', strtotime($booking['check_out'])) ?>
                </td>
            </tr>
        </table>

        <div class="section-title">Invoice Summary</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Nights</th>
                    <th style="text-align: center;">Guests</th>
                    <th class="text-right">Unit Rate</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Room Stay Charge</strong> (<?= htmlspecialchars($booking['room_title']) ?>)<br>
                        <span style="font-size:11.5px; color:#64748b;">Rate Option: <?= htmlspecialchars($booking['meal_plan'] ?: 'EP') ?> Meal Plan</span>
                    </td>
                    <td style="text-align: center;"><?= htmlspecialchars($booking['total_nights']) ?></td>
                    <td style="text-align: center;"><?= htmlspecialchars($booking['guests']) ?> (<?= htmlspecialchars($booking['adults']) ?>A, <?= htmlspecialchars($booking['children']) ?>C)</td>
                    <td class="text-right">₹<?= number_format($booking['base_amount'] / $booking['total_nights'], 2) ?></td>
                    <td class="text-right">₹<?= number_format($booking['base_amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <table class="total-table">
                <tr>
                    <td>Subtotal Room Rate:</td>
                    <td class="text-right">₹<?= number_format($booking['base_amount'], 2) ?></td>
                </tr>
                <?php if ($booking['discount_amount'] > 0): ?>
                    <tr>
                        <td style="color: #dc2626;">Promo Discount (<?= htmlspecialchars($booking['coupon_code']) ?>):</td>
                        <td class="text-right" style="color: #dc2626;">-₹<?= number_format($booking['discount_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td style="font-weight: 600;">Net Invoice Subtotal:</td>
                    <td class="text-right" style="font-weight: 600;">₹<?= number_format($booking['subtotal'], 2) ?></td>
                </tr>
                <tr>
                    <td>GST Taxes (5%):</td>
                    <td class="text-right">₹<?= number_format($booking['tax'], 2) ?></td>
                </tr>
                <tr class="grand-total">
                    <td><strong>Grand Total Paid:</strong></td>
                    <td class="text-right"><strong>₹<?= number_format($booking['total_amount'], 2) ?></strong></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>

        <?php if (!empty($booking['razorpay_payment_id'])): ?>
            <div style="margin-top: 30px; font-size:12.5px; color:#475569; background:#f8fafc; padding: 12px; border-radius:6px; border:1px solid #e2e8f0;">
                <strong>Payment Method:</strong> Razorpay Gateway Checkout Secure Token<br>
                <strong>Transaction Receipt ID:</strong> <?= htmlspecialchars($booking['razorpay_payment_id']) ?>
                <?php if (!empty($booking['refund_tx_id'])): ?>
                    <br><strong>Refund Reference ID:</strong> <?= htmlspecialchars($booking['refund_tx_id']) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($booking['special_request'])): ?>
            <div style="margin-top: 15px; font-size:13px; color:#475569;">
                <strong>Guest Preference Specifications:</strong><br>
                <div style="padding: 8px 12px; background: #faf5f2; border-left: 3px solid #9c6047; margin-top: 5px; border-radius: 4px;">
                    <?= nl2br(htmlspecialchars($booking['special_request'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="footer-note">
            Thank you for choosing <?= htmlspecialchars($hotel_name) ?>! We hope you enjoy your stay.<br>
            For reservation modifications or safety inquiries, please contact our helpline desk.
        </div>
    </div>

    <script>
        // Auto print prompt when launched
        window.addEventListener('DOMContentLoaded', () => {
            if (window.location.search.indexOf('print=true') !== -1) {
                setTimeout(() => {
                    window.print();
                }, 500);
            }
        });
    </script>
</body>
</html>
