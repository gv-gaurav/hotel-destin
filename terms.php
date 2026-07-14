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
    <title>Terms & Conditions - Hotel Destin Gwalior</title>
    
    <style>
        .terms-hero {
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.7) 0%, rgba(15, 23, 42, 0.45) 100%), url('assets/imgs/page/pages/banner2.png') no-repeat center center;
            background-size: cover;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .terms-hero-title {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }
        .terms-hero-subtitle {
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
        .terms-support-box {
            background-color: #fdfbf7;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 50px;
        }
        .terms-support-title {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .terms-support-text {
            font-size: 14.5px;
            color: #64748b;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .terms-support-btn {
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
        .terms-support-btn:hover {
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
        <section class="terms-hero wow fadeIn">
            <div>
                <h1 class="terms-hero-title">Terms & Conditions</h1>
                <p class="terms-hero-subtitle">Please read our hotel policies, reservation guidelines, and general house rules.</p>
            </div>
        </section>

        <!-- Terms Content Grid -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row g-4">
                    
                    <!-- Card 1: Check-in & Check-out -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            </div>
                            <h3 class="policy-title">Check-In / Out Timings</h3>
                            <ul class="policy-list">
                                <li><strong>Check-in & Check-out time:</strong> 11:30 AM daily.</li>
                                <li>Early check-in or late check-out is subject to room availability and may incur supplemental charges.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: Verification Requirements -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            </div>
                            <h3 class="policy-title">Guest Identification</h3>
                            <ul class="policy-list">
                                <li>Valid government-issued photo ID proof is mandatory for the registration process.</li>
                                <li><strong>Accepted ID documents:</strong> Aadhaar Card, Driving Licence, or Passport.</li>
                                <li>PAN cards are not accepted as proof of identity.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 3: Cancellation Policy -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="9" x2="15" y2="15"></line><line x1="15" y1="9" x2="9" y2="15"></line></svg>
                            </div>
                            <h3 class="policy-title">Cancellation & Refunds</h3>
                            <ul class="policy-list">
                                <li>Bookings can be cancelled up to <strong>24 hours before</strong> the arrival date without any cancellation fees.</li>
                                <li>No cancellation options are available for same-day check-in bookings.</li>
                                <li><strong>Refund Policy:</strong> Under no circumstances will refunds be allowed or issued.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 4: Children & Extra Bed Policies -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            </div>
                            <h3 class="policy-title">Children & Extra Beds</h3>
                            <ul class="policy-list">
                                <li><strong>Below 08 Years:</strong> Children under 8 years stay free of charge (no charges apply).</li>
                                <li><strong>Above 08 Years:</strong> Children above 8 years will be charged at <strong>₹300 + applicable taxes</strong> per child.</li>
                                <li><strong>Extra Bed:</strong> Extra bed facility is available at <strong>₹500 + GST</strong>.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 5: Smoking Policy -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8h1a4 4 0 0 1 0 8h-1"></path><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path><line x1="6" y1="1" x2="6" y2="4"></line><line x1="10" y1="1" x2="10" y2="4"></line><line x1="14" y1="1" x2="14" y2="4"></line></svg>
                            </div>
                            <h3 class="policy-title">Room Selections</h3>
                            <ul class="policy-list">
                                <li>Both smoking and non-smoking rooms are available.</li>
                                <li>Please indicate your room type preference during reservation or at check-in (subject to availability).</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 6: General Rules -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <h3 class="policy-title">General Policies</h3>
                            <ul class="policy-list">
                                <li>Guests are responsible for any damages caused to the hotel property during their stay.</li>
                                <li>All disputes are subject to the jurisdiction of the Gwalior local courts.</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Support & Inquiries Box -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="terms-support-box wow fadeInUp">
                            <h3 class="terms-support-title">Need Help with Our Policies?</h3>
                            <p class="terms-support-text">If you have any questions regarding our policies, refunds, or check-in requirements, please contact our support desk.</p>
                            <a class="terms-support-btn" href="mailto:info.hoteldestingwalior@gmail.com">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                Email: info.hoteldestingwalior@gmail.com
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
    <!-- Plugins -->
    <script src="assets/js/plugins/magnific-popup.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="assets/js/plugins/swiper-bundle.min.js"></script>
    <script src="assets/js/plugins/slick.js"></script>
    <script src="assets/js/plugins/jquery.carouselTicker.js"></script>
    <script src="assets/js/plugins/scrollup.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/plugins/waypoints.js"></script>
    <script src="assets/js/plugins/dark.js"></script>
    <!-- Custom template script -->
    <script src="assets/js/maine209.js?v=1.0.0"></script>
</body>
</html>
