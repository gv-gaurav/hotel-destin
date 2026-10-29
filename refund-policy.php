<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Cancellation & Refund Policy | Hotel Destin Gwalior</title>
    <meta name="description" content="Review the booking cancellation, date change, and refund policies for Hotel Destin Gwalior. Clear, fair, and transparent reservation terms for our guests.">
    <meta name="keywords" content="Hotel Destin cancellation policy, refund terms Hotel Destin, hotel booking refund Gwalior">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hoteldestin.in/refund-policy">
    <meta property="og:title" content="Cancellation & Refund Policy | Hotel Destin Gwalior">
    <meta property="og:description" content="Review the booking cancellation, date change, and refund policies for Hotel Destin Gwalior. Clear, fair, and transparent reservation terms for our guests.">
    <meta property="og:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Cancellation & Refund Policy | Hotel Destin Gwalior">
    <meta name="twitter:description" content="Review the booking cancellation, date change, and refund policies for Hotel Destin Gwalior. Clear, fair, and transparent reservation terms for our guests.">
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
            padding: 40px 20px;
        }
        .refund-hero-title {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }
        .refund-hero-subtitle {
            font-size: 16px;
            color: rgba(255,255,255,0.9);
            max-width: 600px;
            margin: 0 auto;
        }
        .policy-card {
            border: 1px solid #ebdcd5;
            border-radius: 16px;
            padding: 30px;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
            box-shadow: 0 4px 12px rgba(156, 96, 71, 0.02);
        }
        .policy-card:hover {
            box-shadow: 0 10px 30px rgba(156, 96, 71, 0.06);
            border-color: #9c6047;
            transform: translateY(-2px);
        }
        .policy-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background-color: #fdfaf8;
            color: #9c6047;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            border: 1px solid #ebdcd5;
        }
        .policy-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 14px;
        }
        .policy-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .policy-list li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 10px;
            font-size: 14px;
            color: #475569;
            line-height: 1.5;
        }
        .policy-list li::before {
            content: "•";
            color: #9c6047;
            font-weight: bold;
            font-size: 18px;
            position: absolute;
            left: 0;
            top: -2px;
        }
        .refund-support-box {
            background-color: #fdfbf7;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 50px;
        }
        .refund-support-title {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .refund-support-text {
            font-size: 14.5px;
            color: #64748b;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .refund-support-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: #9c6047;
            color: #ffffff !important;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(156, 96, 71, 0.15);
        }
        .refund-support-btn:hover {
            background-color: #834f37;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(156, 96, 71, 0.25);
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
                <h1 class="refund-hero-title">Cancellation & Refund Policy</h1>
                <p class="refund-hero-subtitle">Clear, transparent policies regarding reservation changes, cancellations, and refunds.</p>
            </div>
        </section>

        <!-- Content Grid -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row g-4">
                    
                    <!-- Card 1: Standard Cancellation Window -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <h3 class="policy-title">Cancellation Window</h3>
                            <ul class="policy-list">
                                <li>Room bookings can be cancelled free of charge up to <strong>24 hours before</strong> scheduled check-in time (11:30 AM on arrival date).</li>
                                <li>Cancellations requested inside 24 hours of arrival are subject to a retention charge equal to 1 night's room tariff.</li>
                                <li>Same-day check-in bookings are completely non-cancellable and non-refundable.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: Refund Terms -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>
                            </div>
                            <h3 class="policy-title">Refund Processing</h3>
                            <ul class="policy-list">
                                <li>Eligible refunds will be credited back exclusively to the original payment source (credit card, debit card, or UPI account).</li>
                                <li>Refund processing takes between <strong>5 to 7 business days</strong> depending on your banking provider.</li>
                                <li>Cash refunds are not issued for online transactions.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 3: No-Show Policy -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            </div>
                            <h3 class="policy-title">No-Show Terms</h3>
                            <ul class="policy-list">
                                <li>If a guest fails to arrive by 8:00 PM on the check-in date without advance notice, the booking is treated as a No-Show.</li>
                                <li>In case of a No-Show, 100% of the booking amount will be forfeited and no refunds will be permitted.</li>
                                <li>The reserved room will be released back to public inventory.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 4: Date Modifications -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                            </div>
                            <h3 class="policy-title">Date Rescheduling</h3>
                            <ul class="policy-list">
                                <li>Guests may request a date change up to 48 hours prior to arrival, subject to room availability and seasonal price differences.</li>
                                <li>A difference in tariff may apply if rescheduled dates fall in peak seasons, weekends, or festivals.</li>
                                <li>A maximum of 1 date modification is permitted per reservation.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 5: Banquet & Group Bookings -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <h3 class="policy-title">Banquet & Group Bookings</h3>
                            <ul class="policy-list">
                                <li>Group bookings (3+ rooms) and Banquet hall reservations carry dedicated contract agreements.</li>
                                <li>Advance deposits for Banquet Oh Saathi Re are non-refundable upon confirmation.</li>
                                <li>Any cancellations for group blocks must be submitted in writing at least 15 days in advance.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 6: Early Departures -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 22H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h5"></path><polyline points="17 16 21 12 17 8"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            </div>
                            <h3 class="policy-title">Early Departure</h3>
                            <ul class="policy-list">
                                <li>If a guest chooses to shorten their stay after check-in, the remaining room nights are non-refundable.</li>
                                <li>Any meal plan vouchers or bundled services included with unutilized nights will also expire without cash value.</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Support Box -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="refund-support-box wow fadeInUp">
                            <h3 class="refund-support-title">Need to Cancel or Modify a Booking?</h3>
                            <p class="refund-support-text">Please reach out with your Booking Reference ID and registered phone number. Our front office is available 24/7 to help.</p>
                            <a class="refund-support-btn" href="tel:+919203509944">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                Call Desk: +91 92035 09944
                            </a>
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
