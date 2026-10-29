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
    <title>Privacy Policy | Guest Data Protection - Hotel Destin Gwalior</title>
    <meta name="description" content="Read Hotel Destin Gwalior's Privacy Policy. Learn how we collect, safeguard, and manage your personal information and online booking details.">
    <meta name="keywords" content="Hotel Destin privacy policy, guest data protection, privacy terms Hotel Destin">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hoteldestin.in/privacy-policy">
    <meta property="og:title" content="Privacy Policy | Guest Data Protection - Hotel Destin Gwalior">
    <meta property="og:description" content="Read Hotel Destin Gwalior's Privacy Policy. Learn how we collect, safeguard, and manage your personal information and online booking details.">
    <meta property="og:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Privacy Policy | Guest Data Protection - Hotel Destin Gwalior">
    <meta name="twitter:description" content="Read Hotel Destin Gwalior's Privacy Policy. Learn how we collect, safeguard, and manage your personal information and online booking details.">
    <meta name="twitter:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    
    <style>
        .policy-hero {
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.72) 0%, rgba(15, 23, 42, 0.5) 100%), url('assets/imgs/page/pages/banner2.png') no-repeat center center;
            background-size: cover;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .policy-hero-title {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
        }
        .policy-hero-subtitle {
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
        .policy-support-box {
            background-color: #fdfbf7;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            margin-top: 50px;
        }
        .policy-support-title {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .policy-support-text {
            font-size: 14.5px;
            color: #64748b;
            max-width: 500px;
            margin: 0 auto 20px;
        }
        .policy-support-btn {
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
        .policy-support-btn:hover {
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
        <section class="policy-hero wow fadeIn">
            <div>
                <h1 class="policy-hero-title">Privacy Policy</h1>
                <p class="policy-hero-subtitle">Learn how Hotel Destin safeguards your personal data, privacy, and booking details.</p>
            </div>
        </section>

        <!-- Privacy Content Grid -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row g-4">
                    
                    <!-- Card 1: Information Collection -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </div>
                            <h3 class="policy-title">Information We Collect</h3>
                            <ul class="policy-list">
                                <li><strong>Personal details:</strong> Name, email address, mobile number, and residential address provided during booking or enquiry.</li>
                                <li><strong>Identity proof:</strong> Government photo ID details required by local hospitality regulations at check-in.</li>
                                <li><strong>Payment info:</strong> Transaction identifiers encrypted via secure PCI-DSS compliant gateways.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 2: How We Use Data -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            </div>
                            <h3 class="policy-title">How We Use Information</h3>
                            <ul class="policy-list">
                                <li>To confirm and manage room reservations, banquet bookings, and dining enquiries.</li>
                                <li>To provide automated booking invoices, SMS confirmations, and payment receipts.</li>
                                <li>To improve our hospitality services and assist guests with tailored customer support during their stay.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 3: Data Security -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </div>
                            <h3 class="policy-title">Data Protection & Security</h3>
                            <ul class="policy-list">
                                <li>All online transactions and web sessions are encrypted using 256-bit SSL protocols.</li>
                                <li>Guest records are stored in protected databases with role-based access controls.</li>
                                <li>We regularly audit our systems to prevent unauthorized access, data loss, or disclosure.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 4: Third-Party Sharing -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg>
                            </div>
                            <h3 class="policy-title">Third-Party Disclosure</h3>
                            <ul class="policy-list">
                                <li>Hotel Destin strictly <strong>never sells, rents, or trades</strong> guest personal data to any third-party marketing companies.</li>
                                <li>Data is only shared with trusted technical partners (e.g., payment gateways, SMS providers) strictly to fulfill your booking.</li>
                                <li>Information may be disclosed to government or law enforcement agencies only when required by Indian law.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 5: Guest Rights -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.05s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                            </div>
                            <h3 class="policy-title">Your Rights</h3>
                            <ul class="policy-list">
                                <li>You have the right to request access to any personal information we hold about you.</li>
                                <li>You can request corrections to outdated or incomplete details at any time.</li>
                                <li>You may opt-out of promotional communications by contacting our support desk.</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Card 6: Updates to Policy -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="policy-card">
                            <div class="policy-icon-box">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <h3 class="policy-title">Policy Modifications</h3>
                            <ul class="policy-list">
                                <li>Hotel Destin reserves the right to update this policy periodically to reflect operational or legal updates.</li>
                                <li>The latest version will always be accessible directly on this page with the effective date.</li>
                                <li>Continued use of our website signifies acceptance of our revised terms.</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Support Box -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="policy-support-box wow fadeInUp">
                            <h3 class="policy-support-title">Questions About Your Privacy?</h3>
                            <p class="policy-support-text">If you have any questions or concerns regarding our privacy practices or how your data is handled, feel free to reach out to our team.</p>
                            <a class="policy-support-btn" href="mailto:info@hoteldestin.in">
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
