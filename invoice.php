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
$hotel_email = get_setting('hotel_email') ?: 'info@hoteldestin.in';
$hotel_address = get_setting('hotel_address') ?: 'Hotel destin Gwalior Sachin Tendulkar road Near Ram Vatika marriage garden Govindpuri Gwalior';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= htmlspecialchars($booking['invoice_no'] ?: $booking['booking_id']) ?></title>
    <style>
        /* Force printing of background colors and borders */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #334155;
            background-color: #f6f4ee;
            margin: 25px auto;
            padding: 0;
            font-size: 13px;
            line-height: 1.4;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 35px 35px 30px 35px;
            border: 1px solid #e0b070;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(140, 98, 57, 0.05);
            page-break-inside: avoid;
        }

        .logo-img {
            max-height: 135px;
            margin-bottom: 2px;
        }

        .invoice-title {
            font-family: 'Georgia', Times, serif;
            font-size: 28px;
            font-weight: 500;
            color: #5d3f23;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.1;
        }

        .ornament {
            display: flex;
            align-items: center;
            justify-content: right;
            margin: 4px 0 8px 0;
        }

        .ornament::before {
            content: "";
            height: 1px;
            background: #e0b070;
            width: 110px;
        }

        .ornament-symbol {
            color: #e0b070;
            font-size: 8px;
            margin-left: 6px;
        }

        .divider-ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 18px 0;
        }

        .divider-line {
            height: 1px;
            background-color: #e0b070;
            flex-grow: 1;
        }

        .divider-symbol {
            color: #e0b070;
            font-size: 9px;
            margin: 0 6px;
        }

        .btn-print {
            background: #8c6239;
            color: #ffffff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 3px 5px rgba(140, 98, 57, 0.15);
        }

        .btn-print:hover {
            background: #704e2d;
        }

        @media print {
            @page {
                size: A4;
                margin: 10mm 8mm;
            }

            body {
                background-color: #ffffff;
                margin: 0;
                padding: 0;
                color: #000;
            }

            .invoice-box {
                border: none;
                padding: 0;
                box-shadow: none;
                max-width: 100%;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 8px auto; display: flex; justify-content: space-between; align-items: center; padding: 0 5px;">
        <a href="index.php" style="color: #5d3f23; text-decoration: none; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 5px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="19" y1="12" x2="5" y2="12" />
                <polyline points="12 19 5 12 12 5" />
            </svg>
            Back to Home
        </a>
        <button onclick="window.print()" class="btn-print">Print Invoice</button>
    </div>

    <div class="invoice-box">
        <!-- Top Header Layout Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px;">
            <tr>
                <!-- Left side: logo -->
                <td style="width: 32%; vertical-align: top;">
                    <img src="assets/imgs/template/logo-destin.png" alt="Logo" class="logo-img">
                </td>
                <!-- Middle side: invoice details -->
                <td style="width: 38%; vertical-align: top; padding-left: 12px; font-size: 13px; line-height: 1.7;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 90px; color: #64748b; font-weight: 600;">Invoice No.</td>
                            <td style="width: 8px; color: #64748b;">:</td>
                            <td style="font-weight: 600; color: #1e293b;"><?= htmlspecialchars($booking['invoice_no'] ?: 'N/A (Pending)') ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Booking Ref.</td>
                            <td>:</td>
                            <td style="font-weight: 500; color: #1e293b;"><?= htmlspecialchars($booking['booking_id']) ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">GST No.</td>
                            <td>:</td>
                            <td style="font-weight: 700; color: #8c6239;">23ABEFB7662A1ZV</td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Date</td>
                            <td>:</td>
                            <td style="color: #1e293b;"><?= date('d M Y', strtotime($booking['created_at'])) ?></td>
                        </tr>
                        <tr>
                            <td style="color: #64748b; font-weight: 600;">Payment Status</td>
                            <td>:</td>
                            <td style="font-weight: 700; color: #8c6239; text-transform: uppercase;"><?= htmlspecialchars($booking['payment_status']) ?></td>
                        </tr>
                    </table>
                </td>
                <!-- Right side: TAX INVOICE heading and Payment Information -->
                <td style="width: 30%; vertical-align: top; text-align: right;">
                    <div style="text-align: right; margin-bottom: 12px;">
                        <h1 class="invoice-title">Tax Invoice</h1>
                        <div class="ornament">
                            <span class="ornament-symbol">❖</span>
                        </div>
                    </div>

                    <div style="border: 1px solid #e0b070; border-radius: 8px; overflow: hidden; background-color: #faf7f2; text-align: left;">
                        <div style="background-color: #8c6239; color: #ffffff; padding: 4px 10px; font-weight: 600; font-size: 10px; text-transform: uppercase; display: flex; align-items: center; gap: 5px; letter-spacing: 0.5px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="2" y="5" width="20" height="14" rx="2" ry="2" />
                                <line x1="2" y1="10" x2="22" y2="10" />
                            </svg>
                            Payment Information
                        </div>
                        <div style="padding: 8px 10px; font-size: 11.5px; line-height: 1.5; color: #334155;">
                            <div><span style="color: #64748b; font-weight: 500;">Payment Method :</span> <strong style="color: #1e293b;"><?= htmlspecialchars($booking['payment_method'] ?: 'Razorpay') ?></strong></div>
                            <div style="border-top: 1px dotted #e0b070; margin-top: 4px; padding-top: 4px;"><span style="color: #64748b; font-weight: 500;">Transaction ID :</span> <span style="font-family: monospace; font-size: 10.5px; color: #1e293b; word-break: break-all;"><?= htmlspecialchars($booking['razorpay_payment_id'] ?: 'N/A') ?></span></div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Divider Line with Ornament -->
        <div class="divider-ornament">
            <div class="divider-line"></div>
            <span class="divider-symbol">❖</span>
            <div class="divider-line"></div>
        </div>

        <!-- Guest Details & Stay Particulars Layout Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 22px;">
            <tr>
                <td style="width: 48%; vertical-align: top;">
                    <div style="border: 1px solid #e0b070; border-radius: 10px; padding: 15px 15px 18px 15px; background-color: #ffffff; min-height: 140px;">
                        <div style="position: relative; margin-bottom: 10px; height: 26px; display: flex; align-items: center;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background-color: #5d3f23; color: #ffffff; display: flex; align-items: center; justify-content: center; position: absolute; left: 0; top: 0; z-index: 2; border: 2px solid #ffffff;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                            <div style="background-color: #5d3f23; color: #ffffff; font-weight: 600; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.5px; padding: 3px 15px 3px 32px; border-radius: 10px 0 10px 0; margin-left: 0; z-index: 1;">
                                Guest Details
                            </div>
                        </div>
                        <div style="font-size: 13px; line-height: 1.6; color: #334155; padding-left: 4px;">
                            <strong style="color: #a17a42; font-size: 13px; display: block; margin-bottom: 4px;">Billing To:</strong>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 60px; color: #64748b; font-weight: 500;">Name</td>
                                    <td style="width: 10px; color: #64748b;">:</td>
                                    <td style="color: #1e293b; font-weight: 600;"><?= htmlspecialchars($booking['customer_name']) ?></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Phone</td>
                                    <td>:</td>
                                    <td style="color: #1e293b;"><?= htmlspecialchars($booking['customer_phone']) ?></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Email</td>
                                    <td>:</td>
                                    <td style="color: #1e293b; word-break: break-all;"><?= htmlspecialchars($booking['customer_email']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <td style="width: 48%; vertical-align: top;">
                    <div style="border: 1px solid #e0b070; border-radius: 10px; padding: 15px 15px 18px 15px; background-color: #ffffff; min-height: 140px;">
                        <div style="position: relative; margin-bottom: 10px; height: 26px; display: flex; align-items: center;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background-color: #5d3f23; color: #ffffff; display: flex; align-items: center; justify-content: center; position: absolute; left: 0; top: 0; z-index: 2; border: 2px solid #ffffff;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" y1="2" x2="16" y2="6" />
                                    <line x1="8" y1="2" x2="8" y2="6" />
                                    <line x1="3" y1="10" x2="21" y2="10" />
                                </svg>
                            </div>
                            <div style="background-color: #5d3f23; color: #ffffff; font-weight: 600; font-size: 9.5px; text-transform: uppercase; letter-spacing: 0.5px; padding: 3px 15px 3px 32px; border-radius: 10px 0 10px 0; margin-left: 0; z-index: 1;">
                                Stay Particulars
                            </div>
                        </div>
                        <div style="font-size: 13px; line-height: 1.6; color: #334155; padding-left: 4px; margin-top: 4px;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 70px; color: #64748b; font-weight: 500;">Category</td>
                                    <td style="width: 10px; color: #64748b;">:</td>
                                    <td style="color: #1e293b; font-weight: 600;"><?= htmlspecialchars($booking['room_title'] ?: 'Standard Room - Hotel Destin') ?></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Check In</td>
                                    <td>:</td>
                                    <td style="color: #1e293b;"><?= date('d M Y', strtotime($booking['check_in'])) ?></td>
                                </tr>
                                <tr>
                                    <td style="color: #64748b; font-weight: 500;">Check Out</td>
                                    <td>:</td>
                                    <td style="color: #1e293b;"><?= date('d M Y', strtotime($booking['check_out'])) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Invoice Summary Section with styled header bar -->
        <div style="border: 1px solid #e0b070; border-radius: 10px; overflow: hidden; margin-bottom: 22px;">
            <div style="position: relative; height: 28px; display: flex; align-items: center; background-color: #5d3f23;">
                <div style="width: 28px; height: 28px; border-radius: 50%; background-color: #5d3f23; color: #ffffff; display: flex; align-items: center; justify-content: center; position: absolute; left: 0; top: 0; z-index: 2; border: 2px solid #ffffff;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="8" y1="6" x2="21" y2="6" />
                        <line x1="8" y1="12" x2="21" y2="12" />
                        <line x1="8" y1="18" x2="21" y2="18" />
                        <line x1="3" y1="6" x2="3.01" y2="6" />
                        <line x1="3" y1="12" x2="3.01" y2="12" />
                        <line x1="3" y1="18" x2="3.01" y2="18" />
                    </svg>
                </div>
                <div style="color: #ffffff; font-weight: 600; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding-left: 35px; z-index: 1;">
                    Invoice Summary
                </div>
            </div>

            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #faf7f2; border-bottom: 1px solid #e0b070;">
                        <th style="padding: 10px 12px; font-weight: 600; text-align: left; font-size: 12px; color: #475569;">Description</th>
                        <th style="padding: 10px; font-weight: 600; text-align: center; font-size: 12px; color: #475569; width: 65px;">Nights</th>
                        <th style="padding: 10px; font-weight: 600; text-align: center; font-size: 12px; color: #475569; width: 90px;">Guests</th>
                        <th style="padding: 10px 12px; font-weight: 600; text-align: right; font-size: 12px; color: #475569; width: 100px;">Unit Rate</th>
                        <th style="padding: 10px 12px; font-weight: 600; text-align: right; font-size: 12px; color: #475569; width: 100px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 12px 12px; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; line-height: 1.5;">
                            <strong style="color: #1e293b;">Room Stay Charge</strong> (<?= htmlspecialchars($booking['room_title'] ?: 'Standard Room - Hotel Destin') ?>)<br>
                            <span style="font-size:11px; color:#64748b; font-weight: 500;">Rate Option: <?= htmlspecialchars($booking['meal_plan'] ?: 'CP') ?> Meal Plan</span>
                        </td>
                        <td style="padding: 12px; text-align: center; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; color: #334155;"><?= htmlspecialchars($booking['total_nights']) ?></td>
                        <td style="padding: 12px; text-align: center; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; color: #334155;"><?= htmlspecialchars($booking['guests']) ?> (<?= htmlspecialchars($booking['adults']) ?>A, <?= htmlspecialchars($booking['children']) ?>C)</td>
                        <td style="padding: 12px 12px; text-align: right; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; color: #334155;">₹<?= number_format($booking['base_amount'] / $booking['total_nights'], 2) ?></td>
                        <td style="padding: 12px 12px; text-align: right; border-bottom: 1px solid #f1f5f9; font-size: 12.5px; font-weight: 600; color: #1e293b;">₹<?= number_format($booking['base_amount'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Preference Box and Totals side-by-side Layout Table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 22px;">
            <tr>
                <!-- Preferences column -->
                <td style="width: 48%; vertical-align: top;">
                    <div style="border: 1px solid #e0b070; border-radius: 10px; padding: 15px; background-color: #ffffff; min-height: 170px;">
                        <div style="display: flex; align-items: center; margin-bottom: 10px; gap: 6px;">
                            <div style="width: 20px; height: 20px; border-radius: 50%; background-color: #8c6239; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 10px;">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0" />
                                </svg>
                            </div>
                            <span style="font-weight: 600; font-size: 10px; text-transform: uppercase; color: #5d3f23; letter-spacing: 0.5px;">Guest Preference Specifications</span>
                        </div>

                        <div style="font-size: 12.5px; line-height: 1.5; color: #475569; min-height: 50px; padding: 2px;">
                            <?php if (!empty($booking['special_request'])): ?>
                                <?= nl2br(htmlspecialchars($booking['special_request'])) ?>
                            <?php else: ?>
                                <span style="color: #94a3b8; font-style: italic;">No special preferences specified by guest.</span>
                            <?php endif; ?>
                        </div>

                        <!-- Dotted lines decorative effect like in mock-up -->
                        <div style="margin-top: 10px; border-top: 1px dotted #cbd5e1;"></div>
                        <div style="margin-top: 10px; border-top: 1px dotted #cbd5e1;"></div>
                        <div style="margin-top: 10px; border-top: 1px dotted #cbd5e1;"></div>
                    </div>
                </td>
                <td style="width: 4%;"></td>
                <!-- Totals column -->
                <td style="width: 48%; vertical-align: top;">
                    <div style="border: 1px solid #e0b070; border-radius: 10px; overflow: hidden; background-color: #ffffff;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 12.5px; color: #334155;">
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 6px 12px; color: #64748b;">Subtotal Room Rate</td>
                                <td style="padding: 6px 12px; text-align: right; font-weight: 500; color: #1e293b;">₹<?= number_format($booking['base_amount'], 2) ?></td>
                            </tr>

                            <?php if ($booking['discount_amount'] > 0): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 6px 12px; color: #dc2626;">Promo Discount (<?= htmlspecialchars($booking['coupon_code']) ?>)</td>
                                    <td style="padding: 6px 12px; text-align: right; color: #dc2626; font-weight: 500;">-₹<?= number_format($booking['discount_amount'], 2) ?></td>
                                </tr>
                            <?php endif; ?>

                            <tr style="background-color: #fafcfd; border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 6px 12px; font-weight: 600; color: #334155;">Net Invoice Subtotal</td>
                                <td style="padding: 6px 12px; text-align: right; font-weight: 600; color: #1e293b;">₹<?= number_format($booking['subtotal'], 2) ?></td>
                            </tr>

                            <!-- SGST & CGST Breakup (2.5% each) -->
                            <?php
                            $half_tax = $booking['tax'] / 2;
                            ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 6px 12px; color: #64748b;">CGST (2.5%)</td>
                                <td style="padding: 6px 12px; text-align: right; color: #1e293b;">₹<?= number_format($half_tax, 2) ?></td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 6px 12px; color: #64748b;">SGST (2.5%)</td>
                                <td style="padding: 6px 12px; text-align: right; color: #1e293b;">₹<?= number_format($half_tax, 2) ?></td>
                            </tr>

                            <tr style="background-color: #8c6239; color: #ffffff;">
                                <td style="padding: 8px 12px; font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Grand Total Paid</td>
                                <td style="padding: 8px 12px; text-align: right; font-weight: 700; font-size: 15px;">₹<?= number_format($booking['total_amount'], 2) ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Signature Block -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 32px; margin-bottom: 20px;">
            <tr>
                <td style="width: 40%; text-align: left; vertical-align: bottom;">
                    <div style="border-top: 1px dashed #94a3b8; width: 140px; margin-bottom: 6px;"></div>
                    <span style="font-weight: 600; font-size: 10.5px; color: #475569;">Guest Signature</span>
                </td>
                <td style="width: 20%;"></td>
                <td style="width: 40%; text-align: right; vertical-align: bottom;">
                    <div style="border-top: 1px dashed #94a3b8; width: 160px; margin-left: auto; margin-bottom: 6px;"></div>
                    <span style="font-weight: 600; font-size: 10.5px; color: #475569;">Authorised Signature</span>
                </td>
            </tr>
        </table>

        <!-- Elegant Footer with generated sketch and leaves -->
        <table style="width: 100%; border-collapse: collapse; border-top: 1px solid #e0b070; padding-top: 12px; margin-top: 25px;">
            <tr>
                <!-- Left: Hotel sketch drawing -->
                <td style="width: 25%; vertical-align: bottom; padding-top: 8px; text-align: left;">
                    <img src="assets/imgs/template/hotel_sketch.png" alt="Hotel Destin Building" style="max-height: 100px; max-width: 100%; opacity: 0.85; display: inline-block;">
                </td>

                <!-- Center: Thank you notes and decorative lines -->
                <td style="width: 50%; text-align: center; vertical-align: middle; padding: 12px 5px 2px 5px;">
                    <h3 style="font-family: 'Georgia', Times, serif; font-style: italic; color: #5d3f23; font-size: 16px; font-weight: 500; margin: 0 0 4px 0;">Thank you for choosing Hotel Destin!</h3>

                    <div style="display: flex; align-items: center; justify-content: center; gap: 4px; margin: 4px auto 6px auto; max-width: 130px;">
                        <div style="height: 1px; background-color: #d1a877; flex-grow: 1;"></div>
                        <span style="color: #d1a877; font-size: 6px;">❖</span>
                        <div style="height: 1px; background-color: #d1a877; flex-grow: 1;"></div>
                    </div>

                    <p style="font-size: 12.5px; font-weight: 500; color: #5d3f23; margin: 0 0 6px 0;">We hope you enjoy your stay.</p>
                    <p style="font-size: 10.5px; color: #64748b; border-top: 1px dashed #e2e8f0; padding-top: 6px; margin: 0; line-height: 1.35;">
                        For reservation modifications or safety inquiries,<br>please contact our helpline desk.
                    </p>
                </td>

                <!-- Right: Golden leaves decoration -->
                <td style="width: 25%; vertical-align: bottom; padding-top: 8px; text-align: right;">
                    <img src="assets/imgs/template/gold_leaves.png" alt="Decoration" style="max-height: 100px; max-width: 100%; opacity: 0.85; display: inline-block;">
                </td>
            </tr>
        </table>

        <!-- Very bottom Address Strip -->
        <div style="background-color: #5d3f23; color: #faf7f2; border-radius: 6px; margin-top: 28px; padding: 10px 15px; font-size: 10.5px; text-align: center; display: flex; align-items: center; justify-content: center; gap: 6px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 3px;">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: #e0b070;">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                    <circle cx="12" cy="10" r="3" />
                </svg>
                <span>Hotel Destin, Sachin Tendulkar road, Near Ram Vatika marriage garden, Govindpuri, Gwalior</span>
            </div>
            <span style="color: #e0b070;">|</span>
            <div style="display: flex; align-items: center; gap: 3px;">
                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: #e0b070;">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                </svg>
                <a href="https://www.hoteldestin.in" target="_blank" style="color: #faf7f2; text-decoration: none; font-weight: 500;">www.hoteldestin.in</a>
            </div>
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