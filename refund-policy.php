<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Refund Policy | Hotel Destin Gwalior</title>
    <meta name="description" content="Read the Refund and Cancellation Policy for Hotel Destin Gwalior. Learn about booking cancellations, date modifications, and refund procedures.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hoteldestin.in/refund-policy">
    <meta property="og:title" content="Refund Policy | Hotel Destin Gwalior">
    <meta property="og:description" content="Read the Refund and Cancellation Policy for Hotel Destin Gwalior. Learn about booking cancellations, date modifications, and refund procedures.">
    <meta property="og:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Refund Policy | Hotel Destin Gwalior">
    <meta name="twitter:description" content="Read the Refund and Cancellation Policy for Hotel Destin Gwalior. Learn about booking cancellations, date modifications, and refund procedures.">
    <meta name="twitter:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">

    <style>
        .refund-hero {
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.72) 0%, rgba(15, 23, 42, 0.5) 100%), url('assets/imgs/page/pages/banner2.png') no-repeat center center;
            background-size: cover;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px 20px 40px;
        }

        .refund-hero-title {
            font-size: 38px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            font-family: 'Outfit', sans-serif;
        }

        .refund-date-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            padding: 6px 18px;
            border-radius: 30px;
            font-size: 13.5px;
            font-weight: 500;
        }

        .refund-document-wrap {
            background: #ffffff;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 48px 45px;
            box-shadow: 0 4px 20px rgba(156, 96, 71, 0.04);
        }

        @media (max-width: 768px) {
            .refund-document-wrap {
                padding: 28px 20px;
                border-radius: 14px;
            }

            .refund-hero-title {
                font-size: 28px;
            }

            .refund-date-badge {
                font-size: 12px;
                padding: 5px 12px;
            }
        }

        .policy-section {
            margin-bottom: 36px;
            padding-bottom: 32px;
            border-bottom: 1px solid #f1e6e0;
        }

        .policy-section:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .policy-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .policy-section-title .section-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background-color: #fdf5f0;
            color: #9c6047;
            font-size: 13.5px;
            font-weight: 700;
            flex-shrink: 0;
            border: 1px solid #f1e6e0;
        }

        .policy-text {
            font-size: 15px;
            line-height: 1.75;
            color: #475569;
            margin-bottom: 12px;
        }

        .policy-list {
            list-style: none;
            padding: 0;
            margin: 0 0 16px 0;
        }

        .policy-list li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 8px;
            font-size: 14.5px;
            color: #475569;
            line-height: 1.6;
        }

        .policy-list li::before {
            content: "•";
            color: #9c6047;
            font-weight: bold;
            font-size: 18px;
            position: absolute;
            left: 2px;
            top: -2px;
        }

        .policy-contact-card {
            background-color: #fdfaf8;
            border: 1px solid #ebdcd5;
            border-radius: 16px;
            padding: 24px 28px;
            margin-top: 16px;
        }

        .policy-contact-hotel {
            font-family: 'Outfit', sans-serif;
            font-size: 17px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .policy-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 14.5px;
            color: #334155;
            line-height: 1.55;
        }

        .policy-contact-item:last-child {
            margin-bottom: 0;
        }

        .policy-contact-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid #ebdcd5;
            color: #9c6047;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .policy-contact-link {
            color: #9c6047;
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s;
        }

        .policy-contact-link:hover {
            color: #834f37;
            text-decoration: underline;
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>

<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <!-- Hero Section -->
        <section class="refund-hero wow fadeIn">
            <div>
                <h1 class="refund-hero-title">Refund Policy</h1>
                <!-- <div class="refund-date-badge">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Effective Date: 2 November 2026 &nbsp;|&nbsp; Last Updated: 2 November 2026</span>
                </div> -->
            </div>
        </section>

        <!-- Content Section -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12">
                        <div class="refund-document-wrap wow fadeInUp">

                            <!-- 1. Introduction -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">1</span>
                                    <span>Introduction</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    At Hotel Destin, we aim to provide a comfortable and convenient booking experience for all our guests. This Refund Policy explains the general terms and conditions applicable to cancellations, booking modifications and refund requests for room reservations and other services offered by Hotel Destin.
                                </p>
                            </div>

                            <!-- 2. Room Booking Cancellation -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">2</span>
                                    <span>Room Booking Cancellation</span>
                                </h2>
                                <p class="policy-text">
                                    Cancellation conditions may vary depending on the selected room category, booking date and stay dates, applicable rate plan, booking platform or channel, and any promotional offer or discount applied.
                                </p>
                                <p class="policy-text mb-0">
                                    Guests are advised to review the cancellation conditions displayed or communicated at the time of booking.
                                </p>
                            </div>

                            <!-- 3. Refund Eligibility -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">3</span>
                                    <span>Refund Eligibility</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Refund eligibility will depend on the cancellation terms applicable to the confirmed reservation.</li>
                                    <li>Eligible cancellations may qualify for a refund according to the applicable booking conditions.</li>
                                    <li>Bookings made using special promotional codes or discounted offers may be non-refundable.</li>
                                    <li>Cancellation requests made outside the permitted cancellation period may not qualify for a refund.</li>
                                    <li>Any applicable cancellation charges or deductions will be determined according to the confirmed booking terms.</li>
                                </ul>
                            </div>

                            <!-- 4. Non-Refundable Bookings -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">4</span>
                                    <span>Non-Refundable Bookings</span>
                                </h2>
                                <p class="policy-text">
                                    Certain reservations may be marked as non-refundable. These may include special promotional bookings, discounted room offers with non-refundable conditions, and reservations explicitly confirmed under a non-refundable rate plan.
                                </p>
                                <p class="policy-text mb-0">
                                    Once such a booking is confirmed, cancellation may not qualify for a refund.
                                </p>
                            </div>

                            <!-- 5. Booking Modifications and Rescheduling -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">5</span>
                                    <span>Booking Modifications and Rescheduling</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Guests wishing to modify booking dates, room category or stay duration should contact Hotel Destin directly.</li>
                                    <li>All modification requests are subject to room availability.</li>
                                    <li>Changes may result in revised booking charges.</li>
                                    <li>Additional payment may be required where applicable.</li>
                                    <li>Any refund resulting from an approved modification will be handled according to the applicable booking conditions.</li>
                                </ul>
                            </div>

                            <!-- 6. Hourly Stay Bookings -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">6</span>
                                    <span>Hourly Stay Bookings</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Hourly stay reservations are subject to the selected room category, booking duration and applicable rate conditions. Cancellation or modification requests for hourly stays will be assessed according to the terms communicated at the time of confirmation.
                                </p>
                            </div>

                            <!-- 7. Banquet and Event Bookings -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">7</span>
                                    <span>Banquet and Event Bookings</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Refund and cancellation conditions for banquet halls, weddings, parties and other event bookings may differ from regular room reservations. Any advance payment, cancellation deduction, rescheduling arrangement or refund eligibility will be governed by the separate written quotation or booking agreement accepted by the customer.
                                </p>
                            </div>

                            <!-- 8. How to Request a Cancellation or Refund -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">8</span>
                                    <span>How to Request a Cancellation or Refund</span>
                                </h2>
                                <p class="policy-text">
                                    To request a cancellation or enquire about refund eligibility, please contact our reservations team and provide:
                                </p>
                                <ul class="policy-list">
                                    <li>Guest name.</li>
                                    <li>Booking confirmation or reference number.</li>
                                    <li>Registered mobile number.</li>
                                    <li>Booking date and stay dates.</li>
                                    <li>Reason for cancellation or refund request.</li>
                                </ul>
                                <p class="policy-text mb-0">
                                    Our team will review the request against the applicable booking conditions and communicate the outcome.
                                </p>
                            </div>

                            <!-- 9. Refund Processing -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">9</span>
                                    <span>Refund Processing</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Where a refund is approved, it will be processed through the applicable payment or booking channel.</li>
                                    <li>The original payment method may be used wherever supported.</li>
                                    <li>The actual time taken for the refund to appear in the customer's account may depend on the payment provider, bank or booking platform.</li>
                                    <li>Any applicable cancellation charges or deductions will be communicated before processing.</li>
                                    <li>For bookings made through third-party booking platforms, customers may need to submit refund requests directly through the respective platform.</li>
                                </ul>
                            </div>

                            <!-- 10. Duplicate or Incorrect Payments -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">10</span>
                                    <span>Duplicate or Incorrect Payments</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    If you believe that you have made a duplicate payment or an incorrect transaction, please contact our reservations team with the relevant transaction details and payment proof. We will review the transaction and communicate the appropriate resolution.
                                </p>
                            </div>

                            <!-- 11. Contact for Refund and Cancellation Requests -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">11</span>
                                    <span>Contact for Refund and Cancellation Requests</span>
                                </h2>
                                <p class="policy-text">
                                    For cancellation, modification and refund-related assistance, contact:
                                </p>
                                <div class="policy-contact-card">
                                    <div class="policy-contact-hotel">Hotel Destin</div>
                                    <div class="policy-contact-item">
                                        <div class="policy-contact-icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                                <circle cx="12" cy="10" r="3"></circle>
                                            </svg>
                                        </div>
                                        <div>
                                            Sachin Tendulkar Rd, Kailash Nagar, Ramanuj Nagar, Gwalior, Madhya Pradesh 474011
                                        </div>
                                    </div>
                                    <div class="policy-contact-item">
                                        <div class="policy-contact-icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <strong>Phone:</strong> <a class="policy-contact-link" href="tel:+919203509944">+91 9203509944</a>
                                        </div>
                                    </div>
                                    <div class="policy-contact-item">
                                        <div class="policy-contact-icon">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                <polyline points="22,6 12,13 2,6"></polyline>
                                            </svg>
                                        </div>
                                        <div>
                                            <strong>Email:</strong> <a class="policy-contact-link" href="mailto:info@hoteldestin.in">info@hoteldestin.in</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 12. Policy Updates -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">12</span>
                                    <span>Policy Updates</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Hotel Destin reserves the right to update this Refund Policy from time to time. Any changes will be published on this page with the revised effective date. We encourage guests to review this policy before making a reservation.
                                </p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("include/footer.php"); ?>

    <!-- Vendors Scripts -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/maine209.js?v=1.0.0"></script>
</body>

</html>