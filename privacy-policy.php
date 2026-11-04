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
    <title>Privacy Policy | Hotel Destin Gwalior</title>
    <meta name="description" content="Read Hotel Destin Gwalior's Privacy Policy. Learn how we collect, safeguard, and manage your personal information and online booking details.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hoteldestin.in/privacy-policy">
    <meta property="og:title" content="Privacy Policy | Hotel Destin Gwalior">
    <meta property="og:description" content="Read Hotel Destin Gwalior's Privacy Policy. Learn how we collect, safeguard, and manage your personal information and online booking details.">
    <meta property="og:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Privacy Policy | Hotel Destin Gwalior">
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
            padding: 50px 20px 40px;
        }

        .policy-hero-title {
            font-size: 38px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            font-family: 'Outfit', sans-serif;
        }

        .policy-date-badge {
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

        .policy-document-wrap {
            background: #ffffff;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 48px 45px;
            box-shadow: 0 4px 20px rgba(156, 96, 71, 0.04);
        }

        @media (max-width: 768px) {
            .policy-document-wrap {
                padding: 28px 20px;
                border-radius: 14px;
            }

            .policy-hero-title {
                font-size: 28px;
            }

            .policy-date-badge {
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

        .policy-sub-heading {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin-top: 18px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .policy-sub-heading::before {
            content: "";
            display: inline-block;
            width: 4px;
            height: 14px;
            background-color: #9c6047;
            border-radius: 2px;
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
        <section class="policy-hero wow fadeIn">
            <div>
                <h1 class="policy-hero-title">Privacy Policy</h1>
                <!-- <div class="policy-date-badge">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Effective Date: 2 November 2026 &nbsp;|&nbsp; Last Updated: 2 November 2026</span>
                </div> -->
            </div>
        </section>

        <!-- Privacy Content Section -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12">
                        <div class="policy-document-wrap wow fadeInUp">

                            <!-- 1. Introduction -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">1</span>
                                    <span>Introduction</span>
                                </h2>
                                <p class="policy-text">
                                    Welcome to Hotel Destin. We respect your privacy and are committed to protecting the personal information you share with us through our website, booking enquiries, telephone calls, WhatsApp communications and other interactions.
                                </p>
                                <p class="policy-text mb-0">
                                    This Privacy Policy explains how Hotel Destin collects, uses, stores and protects your personal information when you visit our website or use our services.
                                </p>
                            </div>

                            <!-- 2. Information We Collect -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">2</span>
                                    <span>Information We Collect</span>
                                </h2>
                                <p class="policy-text">
                                    We may collect the following information when you interact with our website or contact us:
                                </p>

                                <h3 class="policy-sub-heading">Personal Information</h3>
                                <ul class="policy-list">
                                    <li>Full name</li>
                                    <li>Mobile number</li>
                                    <li>Email address</li>
                                    <li>Contact details</li>
                                </ul>

                                <h3 class="policy-sub-heading">Booking Information</h3>
                                <ul class="policy-list">
                                    <li>Check-in and check-out dates</li>
                                    <li>Preferred room category</li>
                                    <li>Check-in time and stay duration for hourly bookings</li>
                                    <li>Number of guests</li>
                                    <li>Special booking requirements</li>
                                    <li>Event and banquet enquiry details</li>
                                </ul>

                                <h3 class="policy-sub-heading">Technical Information</h3>
                                <ul class="policy-list mb-0">
                                    <li>IP address</li>
                                    <li>Browser type and device information</li>
                                    <li>Website usage and interaction data</li>
                                    <li>Cookies and similar tracking technologies, where enabled</li>
                                </ul>
                            </div>

                            <!-- 3. How We Use Your Information -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">3</span>
                                    <span>How We Use Your Information</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Respond to room booking and accommodation enquiries.</li>
                                    <li>Process and manage reservation requests.</li>
                                    <li>Provide information about room availability and pricing.</li>
                                    <li>Handle hourly stay and entire hotel booking enquiries.</li>
                                    <li>Respond to banquet, wedding and event enquiries.</li>
                                    <li>Communicate booking confirmations and updates.</li>
                                    <li>Provide customer support.</li>
                                    <li>Improve our website, services and guest experience.</li>
                                    <li>Maintain business, security and legal records.</li>
                                    <li>Send promotional communication where permitted and applicable.</li>
                                </ul>
                            </div>

                            <!-- 4. Sharing of Information -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">4</span>
                                    <span>Sharing of Information</span>
                                </h2>
                                <p class="policy-text">
                                    Hotel Destin does not sell your personal information. We may share relevant information with authorised service providers or third parties where necessary for processing payments and refunds, managing booking and reservation services, website hosting and technical maintenance, customer communication and support, or meeting applicable legal or regulatory requirements.
                                </p>
                                <p class="policy-text mb-0">
                                    Such sharing will be limited to what is reasonably necessary for the relevant purpose.
                                </p>
                            </div>

                            <!-- 5. Cookies and Website Tracking -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">5</span>
                                    <span>Cookies and Website Tracking</span>
                                </h2>
                                <p class="policy-text">
                                    Our website may use cookies and similar technologies to improve website functionality, understand visitor behaviour and enhance user experience.
                                </p>
                                <p class="policy-text mb-0">
                                    You can manage or disable cookies through your browser settings. Certain website features may not function properly if cookies are disabled. Where required, consent will be obtained before using non-essential cookies or tracking technologies.
                                </p>
                            </div>

                            <!-- 6. Data Security -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">6</span>
                                    <span>Data Security</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    We take reasonable technical and organisational measures to protect personal information against unauthorised access, disclosure, alteration, misuse or loss. However, no internet transmission or electronic storage system can be guaranteed to be completely secure.
                                </p>
                            </div>

                            <!-- 7. Data Retention -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">7</span>
                                    <span>Data Retention</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    We retain personal information only for as long as reasonably necessary for booking management, customer support, business operations, legal compliance and dispute resolution. Information that is no longer required will be deleted or anonymised, subject to applicable legal requirements.
                                </p>
                            </div>

                            <!-- 8. Your Privacy Rights -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">8</span>
                                    <span>Your Privacy Rights</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Subject to applicable laws, you may request access to your personal information.</li>
                                    <li>You may request correction of inaccurate or incomplete information.</li>
                                    <li>You may request deletion of personal information where legally permissible.</li>
                                    <li>You may withdraw consent for consent-based processing.</li>
                                    <li>You may raise concerns regarding the handling of your personal information.</li>
                                </ul>
                            </div>

                            <!-- 9. Third-Party Websites -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">9</span>
                                    <span>Third-Party Websites</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Our website may contain links to external websites or third-party services. Hotel Destin is not responsible for the privacy practices, policies or content of external websites. We recommend reviewing their respective privacy policies before sharing personal information.
                                </p>
                            </div>

                            <!-- 10. Children's Privacy -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">10</span>
                                    <span>Children's Privacy</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Our website and booking services are intended to be used by adults making accommodation or event enquiries. We do not knowingly collect personal information directly from children through our website.
                                </p>
                            </div>

                            <!-- 11. Changes to This Privacy Policy -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">11</span>
                                    <span>Changes to This Privacy Policy</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Hotel Destin may update this Privacy Policy from time to time to reflect changes in our services, business practices or applicable legal requirements. Any updated version will be published on this page with a revised effective date.
                                </p>
                            </div>

                            <!-- 12. Contact Us -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">12</span>
                                    <span>Contact Us</span>
                                </h2>
                                <p class="policy-text">
                                    For questions, concerns or requests regarding this Privacy Policy, please contact:
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