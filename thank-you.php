<?php
require_once __DIR__ . '/db.php';

$booking_id = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$booking = null;

if (!empty($booking_id)) {
    try {
        $stmt = $pdo->prepare("SELECT b.*, r.title AS room_title FROM bookings b JOIN rooms r ON b.room_id = r.id WHERE b.booking_id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
    } catch (Exception $e) {
        error_log("Database error in thank-you: " . $e->getMessage());
    }
}

// Fallback values if booking reference isn't matched in database
if (!$booking) {
    $booking = [
        'booking_id' => !empty($booking_id) ? $booking_id : 'GV-' . date('Ymd') . '-EA9171',
        'room_title' => 'Standard Room - Hotel Destin',
        'check_in' => date('Y-m-d'),
        'check_out' => date('Y-m-d', strtotime('+1 day')),
        'adults' => 2,
        'children' => 0,
        'total_amount' => 2100.00,
        'customer_email' => 'guest@example.com'
    ];
}

$check_in_formatted = date('D, d M Y', strtotime($booking['check_in']));
$check_out_formatted = date('D, d M Y', strtotime($booking['check_out']));
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Reservation Confirmed - Hotel Destin Gwalior</title>
    
    <style>
        body {
            background-color: #faf9f6;
            color: #334155;
            font-family: 'Inter', sans-serif;
        }

        .thank-you-container {
            padding: 30px 0 60px 0;
        }

        .confirmation-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #f0f0ed;
            max-width: 680px;
            margin: 0 auto;
            padding: 30px 35px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.015);
            text-align: center;
        }

        /* Success circular badge */
        .success-checkmark-badge {
            width: 56px;
            height: 56px;
            background: #16a34a;
            color: #ffffff;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            font-size: 22px;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.2);
        }

        .status-tag {
            font-size: 10.5px;
            font-weight: 700;
            color: #c5a880;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
            display: block;
        }

        .main-heading {
            font-size: 28px;
            font-weight: 850;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 10px;
        }

        .intro-text {
            font-size: 14px;
            line-height: 1.55;
            color: #64748b;
            margin-bottom: 22px;
            max-width: 580px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Booking Details Box */
        .booking-details-box {
            background: #fafaf9;
            border: 1px solid #f0f0ed;
            border-radius: 12px;
            padding: 18px 22px;
            text-align: left;
            margin-bottom: 20px;
        }

        .booking-details-box h3 {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
            border-bottom: 1px solid #e5e5e0;
            padding-bottom: 8px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
        }

        .detail-item-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .detail-item-col label {
            font-size: 11px;
            font-weight: 600;
            color: #888880;
            text-transform: capitalize;
        }

        .detail-item-col span {
            font-size: 13.5px;
            font-weight: 750;
            color: #0f172a;
        }

        .price-highlight {
            color: #9c6047 !important;
        }

        /* Guidelines Section */
        .guidelines-box {
            text-align: left;
            margin-bottom: 25px;
            border-top: 1px solid #f0f0ed;
            padding-top: 18px;
        }

        .guidelines-box h4 {
            font-size: 13.5px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .guideline-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .guideline-list li {
            font-size: 12.5px;
            color: #64748b;
            line-height: 1.45;
            padding-left: 12px;
            position: relative;
        }

        .guideline-list li::before {
            content: '*';
            position: absolute;
            left: 0;
            top: 1px;
            color: #9c6047;
            font-weight: bold;
        }

        /* Button controls */
        .buttons-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .btn-gold-grad {
            background: linear-gradient(135deg, #c5a880 0%, #9c6047 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 22px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-gold-grad:hover {
            box-shadow: 0 4px 15px rgba(156, 96, 71, 0.2);
            transform: translateY(-1px);
        }

        .btn-outlined-custom {
            background: #ffffff;
            color: #334155 !important;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 22px;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outlined-custom:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            transform: translateY(-1px);
        }

        @media (max-width: 575px) {
            .details-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .confirmation-card {
                padding: 30px 20px;
            }
            .buttons-row {
                flex-direction: column;
                width: 100%;
            }
            .buttons-row a {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <section class="thank-you-container">
            <div class="container">
                <div class="confirmation-card wow fadeInUp">
                    <!-- Checkmark Badge -->
                    <div class="success-checkmark-badge">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>

                    <span class="status-tag">RESERVATION CONFIRMED</span>
                    <h1 class="main-heading">Your Luxury Sanctuary Awaits</h1>
                    
                    <p class="intro-text">
                        Thank you for booking your stay with us. A confirmation email detailing check-in directions and room access credentials has been dispatched to <strong><?= htmlspecialchars($booking['customer_email']) ?></strong>.
                    </p>

                    <!-- Booking Details Card -->
                    <div class="booking-details-box">
                        <h3>Booking Details</h3>
                        <div class="details-grid">
                            <div class="detail-item-col">
                                <label>Booking Reference ID</label>
                                <span style="font-family: monospace; font-size:14.5px;"><?= htmlspecialchars($booking['booking_id']) ?></span>
                            </div>
                            <div class="detail-item-col">
                                <label>Selected Accommodation</label>
                                <span><?= htmlspecialchars($booking['room_title']) ?></span>
                            </div>
                            <div class="detail-item-col">
                                <label>Check-In Date</label>
                                <span><?= $check_in_formatted ?></span>
                            </div>
                            <div class="detail-item-col">
                                <label>Check-Out Date</label>
                                <span><?= $check_out_formatted ?></span>
                            </div>
                            <div class="detail-item-col">
                                <label>Guests Registered</label>
                                <span><?= htmlspecialchars($booking['adults']) ?> Adults, <?= htmlspecialchars($booking['children']) ?> Children</span>
                            </div>
                            <div class="detail-item-col">
                                <label>Total Cost Paid (GST Incl.)</label>
                                <span class="price-highlight">₹<?= number_format($booking['total_amount'], 2) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Guidelines -->
                    <div class="guidelines-box">
                        <h4>
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="color: #9c6047;">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                            </svg>
                            Check-In Guidelines
                        </h4>
                        <ul class="guideline-list">
                            <li>Standard check-in commences at 2:00 PM; check-out is before 11:00 AM.</li>
                            <li>Please bring a valid government photo identification card for verification at the reception desk.</li>
                            <li>To customize airport shuttle pickups, call our desk 24 hours prior to flight arrival.</li>
                        </ul>
                    </div>

                    <!-- Actions Buttons -->
                    <div class="buttons-row">
                        <a href="index.php" class="btn-gold-grad">Return Home</a>
                        
                        <a href="invoice.php?ref=<?= urlencode($booking['booking_id']) ?>&print=true" target="_blank" class="btn-outlined-custom">
                            <!-- Simple PDF icon -->
                            <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16" style="color:#ef4444;">
                                <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/>
                                <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a.768.768 0 0 1-.248-1.006c.079-.164.212-.3.394-.39a.784.784 0 0 1 .43-.1c.143.01.272.06.381.144a.798.798 0 0 1 .302.546c.371-.15.756-.3 1.113-.4a.578.578 0 0 1-.03-.26c-.007-.2-.027-.386-.062-.53A1.85 1.85 0 0 0 7 9.2c-.1-.17-.186-.334-.252-.488C6.54 8.243 6.45 7.747 6.48 7.3c.026-.395.203-.7.525-.79a.6.6 0 0 1 .436.06c.176.1.32.25.438.423a2.3 2.3 0 0 1 .288.756c.074.341.1.728.082 1.127.354.183.743.35 1.11.47a.71.71 0 0 1 .403-.047c.18.04.322.146.43.3.118.17.182.383.185.602.006.385-.23.665-.583.79a.75.75 0 0 1-.602-.036c-.4-.22-.733-.553-1.007-.94a15.7 15.7 0 0 1-1.636.5c-.328.618-.636 1.16-.92 1.63-.299.497-.6.915-.9 1.25a.8.8 0 0 1-.587.324zm.447-1.424c-.114.162-.164.33-.146.48.01.07.03.11.05.13a.13.13 0 0 0 .08.02.58.58 0 0 0 .302-.13c.183-.162.373-.42.56-.73a15.7 15.7 0 0 0-.846.26zM7.227 8.3c.05.14.113.29.176.438.084-.33.115-.65.11-.947a.99.99 0 0 0-.053-.25.13.13 0 0 0-.05-.045.08.08 0 0 0-.053-.01.3.3 0 0 0-.158.12.78.78 0 0 0-.12.3c.01.127.027.26.048.4zM8.32 10.66a9 9 0 0 0-.898.37 12.8 12.8 0 0 0 1.21.6c.144-.3.268-.61.372-.92-.224-.03-.45-.05-.684-.05zm.968-.53c.144.2.327.38.536.53.076-.05.118-.11.123-.18a.18.18 0 0 0-.036-.08.43.43 0 0 0-.168-.13c-.156-.05-.3-.09-.455-.14z"/>
                            </svg>
                            Download Receipt
                        </a>
                        
                        <a href="contact.php" class="btn-outlined-custom">Contact Guest Desk</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("include/footer.php"); ?>

    <!-- Scripts -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
</body>
</html>
