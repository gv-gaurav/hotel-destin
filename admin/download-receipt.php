<?php
require_once __DIR__ . '/../db.php';

// Verify session authentication
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$booking_id = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$booking = null;

try {
    $stmt = $pdo->prepare("SELECT b.*, r.title as room_title FROM bookings b LEFT JOIN rooms r ON b.room_id = r.id WHERE b.id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
} catch (Exception $e) {
    error_log("Receipt data query failure: " . $e->getMessage());
}

if (!$booking) {
    echo "<h3>Error: Reservation invoice record not found.</h3>";
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
    <title>Invoice - #<?= htmlspecialchars($booking['booking_id']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            border-radius: 12px;
        }
        .header-table, .details-table, .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .header-table td {
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .logo-img {
            max-height: 70px;
            margin-bottom: 10px;
        }
        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #9c6047;
            text-transform: uppercase;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #9c6047;
            border-bottom: 2px solid #e9ecf2;
            padding-bottom: 5px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .items-table th {
            background: #f7f9fc;
            padding: 10px;
            font-weight: bold;
            border-bottom: 2px solid #e9ecf2;
            text-align: left;
        }
        .items-table td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }
        .total-box {
            float: right;
            width: 300px;
            margin-top: 20px;
        }
        .total-table {
            width: 100%;
            border-collapse: collapse;
        }
        .total-table td {
            padding: 6px 10px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #9c6047;
            border-top: 2px solid #e9ecf2;
        }
        .footer-note {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        @media print {
            body {
                margin: 0;
            }
            .invoice-box {
                border: none;
                box-shadow: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 20px auto; text-align: right;">
        <button onclick="window.print()" style="background:#9c6047; color:#fff; border:none; padding:10px 20px; border-radius:6px; font-weight:bold; cursor:pointer;">
            Print Invoice
        </button>
    </div>

    <div class="invoice-box">
        <table class="header-table">
            <tr>
                <td>
                    <img src="../assets/imgs/template/logo-destin.png" alt="Logo" class="logo-img"><br>
                    <strong><?= htmlspecialchars($hotel_name) ?></strong><br>
                    <?= htmlspecialchars($hotel_address) ?><br>
                    Phone: <?= htmlspecialchars($hotel_phone) ?><br>
                    Email: <?= htmlspecialchars($hotel_email) ?>
                </td>
                <td class="text-right">
                    <span class="invoice-title">Invoice</span><br>
                    <strong>Reference ID:</strong> #<?= htmlspecialchars($booking['booking_id']) ?><br>
                    <strong>Invoice Date:</strong> <?= date('d M Y', strtotime($booking['created_at'])) ?><br>
                    <strong>Status:</strong> <span style="text-transform: uppercase; font-weight: bold; color: <?= $booking['payment_status'] === 'paid' ? '#047857' : '#b91c1c' ?>;"><?= htmlspecialchars($booking['payment_status']) ?></span>
                </td>
            </tr>
        </table>

        <div class="section-title">Guest Details</div>
        <table class="details-table">
            <tr>
                <td style="width: 50%;">
                    <strong>Billing To:</strong><br>
                    <?= htmlspecialchars($booking['customer_name']) ?><br>
                    Phone: <?= htmlspecialchars($booking['customer_phone']) ?><br>
                    Email: <?= htmlspecialchars($booking['customer_email']) ?>
                </td>
                <td>
                    <strong>Stay Information:</strong><br>
                    <strong>Room:</strong> <?= htmlspecialchars($booking['room_title'] ?: 'Deluxe Room') ?><br>
                    <strong>Check In:</strong> <?= date('d M Y', strtotime($booking['check_in'])) ?><br>
                    <strong>Check Out:</strong> <?= date('d M Y', strtotime($booking['check_out'])) ?>
                </td>
            </tr>
        </table>

        <div class="section-title">Invoice Summary</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Nights</th>
                    <th class="text-right">Rate</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Room Booking Charge (<?= htmlspecialchars($booking['room_title'] ?: 'Deluxe Room') ?>)<br>
                        <span style="font-size:12px; color:#666;">Capacity: <?= htmlspecialchars($booking['capacity_adults'] ?? 2) ?> Adults, <?= htmlspecialchars($booking['capacity_children'] ?? 0) ?> Children</span>
                    </td>
                    <td><?= htmlspecialchars($booking['total_nights']) ?></td>
                    <td class="text-right">₹<?= number_format($booking['base_amount'] / $booking['total_nights'], 2) ?></td>
                    <td class="text-right">₹<?= number_format($booking['base_amount'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <table class="total-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">₹<?= number_format($booking['base_amount'], 2) ?></td>
                </tr>
                <?php if ($booking['discount_amount'] > 0): ?>
                    <tr>
                        <td>Discount (<?= htmlspecialchars($booking['coupon_code']) ?>):</td>
                        <td class="text-right" style="color: #047857;">-₹<?= number_format($booking['discount_amount'], 2) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>GST (18%):</td>
                    <td class="text-right">₹<?= number_format($booking['tax_amount'], 2) ?></td>
                </tr>
                <tr class="grand-total">
                    <td>Grand Total Paid:</td>
                    <td class="text-right">₹<?= number_format($booking['total_amount'], 2) ?></td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>

        <?php if (!empty($booking['razorpay_payment_id'])): ?>
            <div style="margin-top: 30px; font-size:12.5px; color:#555; background:#f7f9fc; padding: 12px; border-radius:6px; border:1px solid #e9ecf2;">
                <strong>Payment Method:</strong> Razorpay Online Payment Gateway<br>
                <strong>Transaction ID:</strong> <?= htmlspecialchars($booking['razorpay_payment_id']) ?>
            </div>
        <?php endif; ?>

        <div class="footer-note">
            Thank you for choosing Hotel Destin! We hope you enjoy your stay.<br>
            For any queries or modifications, please contact customer support.
        </div>
    </div>

    <script>
        // Trigger print automatically on page load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
