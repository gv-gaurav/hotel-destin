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
    <title>Cookies Policy | Hotel Destin Gwalior Website</title>
    <meta name="description" content="Learn about Hotel Destin's Cookie Policy. Understand how we use cookies to improve your room booking experience, website security, and site performance.">
    <meta name="keywords" content="Hotel Destin cookie policy, cookies usage hoteldestin.in, privacy and cookies">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hoteldestin.in/cookies-policy">
    <meta property="og:title" content="Cookies Policy | Hotel Destin Gwalior Website">
    <meta property="og:description" content="Learn about Hotel Destin's Cookie Policy. Understand how we use cookies to improve your room booking experience, website security, and site performance.">
    <meta property="og:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Cookies Policy | Hotel Destin Gwalior Website">
    <meta name="twitter:description" content="Learn about Hotel Destin's Cookie Policy. Understand how we use cookies to improve your room booking experience, website security, and site performance.">
    <meta name="twitter:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    
    <style>
        .cookie-hero {
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.72) 0%, rgba(15, 23, 42, 0.5) 100%), url('assets/imgs/page/pages/banner2.png') no-repeat center center;
            background-size: cover;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .cookie-hero-title {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }
        .cookie-hero-subtitle {
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
        .cookie-support-box {
            background-color: #fdfbf7;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 50px;
        }
        .cookie-support-title {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .cookie-support-text {
            font-size: 14.5px;
            color: #64748b;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .cookie-support-btn {
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
        .cookie-support-btn:hover {
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
        <section class="cookie-hero wow fadeIn">
            <div>
                <h1 class="cookie-hero-title">Cookies Policy</h1>
                <p class="cookie-hero-subtitle">How Hotel Destin uses cookies to provide a smooth, secure booking experience.</p>
            </div>
        </section>

        <!-- Content Grid -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row g-4">
                    
                    <!-- Card 1: What Are Cookies -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"></path><path d="M2 12h20"></path></svg>
                            </div>
                            <h3 class="policy-title">What Are Cookies?</h3>
                            <ul class="policy-list">
                                <li>Cookies are small encrypted text files saved on your browser or device when you visit websites.</li>
                                <li>They allow our website to remember your device, preferences, and booking selections across visits.</li>
                                <li>Cookies do not harm your device and do not contain personal programs or viruses.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: Essential Cookies -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <h3 class="policy-title">Essential Booking Cookies</h3>
                            <ul class="policy-list">
                                <li><strong>Required for function:</strong> These cookies enable core functionality like check-in dates selection, room filtering, and checkout steps.</li>
                                <li><strong>Session management:</strong> They maintain your session state while navigating between rooms and enquiry forms.</li>
                                <li>The website cannot function properly without these necessary technical cookies.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 3: Performance & Analytics -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                            </div>
                            <h3 class="policy-title">Analytics & Performance</h3>
                            <ul class="policy-list">
                                <li>We utilize analytics cookies (such as Google Analytics) to understand visitor traffic patterns and popular room categories.</li>
                                <li>All aggregated analytical data is strictly anonymized and cannot identify you personally.</li>
                                <li>This helps us identify technical issues, optimize page speeds, and improve mobile responsiveness.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 4: Preference Cookies -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            </div>
                            <h3 class="policy-title">User Preferences</h3>
                            <ul class="policy-list">
                                <li>These cookies remember your personalized options such as currency choices, language, or recent search parameters.</li>
                                <li>They provide a more consistent experience when you return to browse rooms or check rates.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 5: Managing Cookies -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                            </div>
                            <h3 class="policy-title">How to Control Cookies</h3>
                            <ul class="policy-list">
                                <li>You have full control over cookies and can disable or clear them through your browser settings at any time.</li>
                                <li>Most browsers (Chrome, Edge, Safari, Firefox) provide straightforward options under "Privacy & Security".</li>
                                <li>Please note that disabling essential cookies may impact booking checkout functionality.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 6: Policy Updates -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            </div>
                            <h3 class="policy-title">Policy Revisions</h3>
                            <ul class="policy-list">
                                <li>We may update our Cookies Policy to align with technical improvements or regulatory compliance.</li>
                                <li>The latest effective policy will always be published on this dedicated page.</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Support Box -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="cookie-support-box wow fadeInUp">
                            <h3 class="cookie-support-title">Questions Regarding Our Cookies?</h3>
                            <p class="cookie-support-text">Our technical and guest service team is happy to assist with any questions regarding cookies and data usage on Hotel Destin.</p>
                            <a class="cookie-support-btn" href="mailto:info@hoteldestin.in">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                Email: info@hoteldestin.in
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
