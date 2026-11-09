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
    <title>Terms & Conditions | Hotel Destin Gwalior</title>
    <meta name="description" content="Read the Terms and Conditions for Hotel Destin Gwalior. Review our reservation policies, guest rules, timings, and accommodation terms.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hoteldestin.in/terms">
    <meta property="og:title" content="Terms & Conditions | Hotel Destin Gwalior">
    <meta property="og:description" content="Read the Terms and Conditions for Hotel Destin Gwalior. Review our reservation policies, guest rules, timings, and accommodation terms.">
    <meta property="og:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Terms & Conditions | Hotel Destin Gwalior">
    <meta name="twitter:description" content="Read the Terms and Conditions for Hotel Destin Gwalior. Review our reservation policies, guest rules, timings, and accommodation terms.">
    <meta name="twitter:image" content="https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png">

    <style>
        .terms-hero {
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.72) 0%, rgba(15, 23, 42, 0.5) 100%), url('assets/imgs/page/pages/banner2.png') no-repeat center center;
            background-size: cover;
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 50px 20px 40px;
        }

        .terms-hero-title {
            font-size: 38px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            font-family: 'Outfit', sans-serif;
        }

        .terms-date-badge {
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

        .terms-document-wrap {
            background: #ffffff;
            border: 1px solid #ebdcd5;
            border-radius: 20px;
            padding: 48px 45px;
            box-shadow: 0 4px 20px rgba(156, 96, 71, 0.04);
        }

        @media (max-width: 768px) {
            .terms-document-wrap {
                padding: 28px 20px;
                border-radius: 14px;
            }

            .terms-hero-title {
                font-size: 28px;
            }

            .terms-date-badge {
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
        <section class="terms-hero wow fadeIn">
            <div>
                <h1 class="terms-hero-title">Terms & Conditions</h1>
                <!-- <div class="terms-date-badge">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    <span>Effective Date: 2 November 2026 &nbsp;|&nbsp; Last Updated: 2 November 2026</span>
                </div> -->
            </div>
        </section>

        <!-- Terms Content Section -->
        <section class="section-box py-60 background-body">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12">
                        <div class="terms-document-wrap wow fadeInUp">

                            <!-- 1. Introduction -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">1</span>
                                    <span>Introduction</span>
                                </h2>
                                <p class="policy-text">
                                    Welcome to Hotel Destin. These Terms & Conditions govern your access to and use of the Hotel Destin website and the accommodation, hourly stay, banquet, event and related services offered by us.
                                </p>
                                <p class="policy-text mb-0">
                                    By accessing our website, submitting an enquiry or making a booking, you agree to these Terms & Conditions. Please read them carefully before using our services.
                                </p>
                            </div>

                            <!-- 2. Hotel Services -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">2</span>
                                    <span>Hotel Services</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Executive, Premium and Deluxe room accommodation.</li>
                                    <li>Hourly stay booking options.</li>
                                    <li>Regular overnight accommodation.</li>
                                    <li>Entire hotel booking enquiries.</li>
                                    <li>Banquet hall and event-related services.</li>
                                    <li>Dining and related guest services.</li>
                                </ul>
                            </div>

                            <!-- 3. Booking and Reservations -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">3</span>
                                    <span>Booking and Reservations</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Website booking forms are used to submit reservation requests and enquiries.</li>
                                    <li>Submitting an enquiry does not automatically guarantee a confirmed reservation.</li>
                                    <li>A booking will be considered confirmed only after confirmation by Hotel Destin through an authorised communication channel.</li>
                                    <li>Room availability, applicable prices and booking conditions will be communicated during the reservation process.</li>
                                    <li>Guests must provide accurate and complete information when making a booking.</li>
                                    <li>Hotel Destin reserves the right to decline a booking request where availability or operational requirements do not permit acceptance.</li>
                                </ul>
                            </div>

                            <!-- 4. Room Rates and Payments -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">4</span>
                                    <span>Room Rates and Payments</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Room prices displayed on the website are indicative and may change.</li>
                                    <li>The applicable price will be confirmed at the time of booking.</li>
                                    <li>Taxes, additional services and other applicable charges may be charged separately where applicable.</li>
                                    <li>Payment requirements and accepted payment methods will be communicated during the booking process.</li>
                                    <li>Promotional offers and discounts are subject to their respective terms and validity.</li>
                                </ul>
                            </div>

                            <!-- 5. Check-in and Check-out -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">5</span>
                                    <span>Check-in and Check-out</span>
                                </h2>
                                <p class="policy-text">
                                    The standard hotel timings are:
                                </p>
                                <ul class="policy-list">
                                    <li><strong>Check-in:</strong> Starts at 12:00 PM.</li>
                                    <li><strong>Check-out:</strong> By 11:00 AM.</li>
                                </ul>
                                <p class="policy-text mb-0">
                                    Early check-in and late check-out are subject to availability and additional charges. Our front desk is open 24 hours. Guests are required to present valid government-issued photo identification upon check-in.
                                </p>
                            </div>

                            <!-- 6. Guest Responsibilities -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">6</span>
                                    <span>Guest Responsibilities</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Provide accurate personal and booking information.</li>
                                    <li>Follow hotel rules and staff instructions.</li>
                                    <li>Maintain cleanliness and respect hotel property.</li>
                                    <li>Avoid causing disturbance or inconvenience to other guests.</li>
                                    <li>Use hotel facilities responsibly.</li>
                                    <li>Comply with applicable laws and regulations.</li>
                                </ul>
                            </div>

                            <!-- 7. Child Policy and Additional Guests -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">7</span>
                                    <span>Child Policy and Additional Guests</span>
                                </h2>
                                <p class="policy-text">
                                    Children under 5 years of age may stay free of charge when sharing the room with their parents without an additional bed.
                                </p>
                                <p class="policy-text mb-0">
                                    Additional guests, older children or requests for extra beds may attract additional charges. The applicable charges and accommodation arrangements will be communicated during booking.
                                </p>
                            </div>

                            <!-- 8. Cancellation and Modifications -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">8</span>
                                    <span>Cancellation and Modifications</span>
                                </h2>
                                <p class="policy-text">
                                    Free cancellation is offered up to 48 hours prior to check-in (12:00 PM). Cancellations made within 48 hours of check-in will incur a charge equal to the first night's room tariff. No-shows are charged the full first night. Reservations confirmed under special promotional discount codes are non-refundable. Approved refunds are processed within 5 to 7 working days to the original payment method.
                                </p>
                                <p class="policy-text mb-0">
                                    Please refer to our <a href="refund-policy" class="policy-contact-link">Refund Policy</a> for further information.
                                </p>
                            </div>

                            <!-- 9. Hourly Stay Bookings -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">9</span>
                                    <span>Hourly Stay Bookings</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Hourly stay bookings are subject to the selected room category, requested date and check-in time, selected stay duration, room availability and applicable charges. Guests must follow the confirmed booking duration. Any extension is subject to availability and additional charges.
                                </p>
                            </div>

                            <!-- 10. Banquet and Event Bookings -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">10</span>
                                    <span>Banquet and Event Bookings</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Banquet, wedding, party and other event bookings are subject to separate availability, quotations and booking arrangements. Specific terms relating to advance payments, cancellation, rescheduling, guest capacity and event services will be communicated and agreed upon separately before confirmation.
                                </p>
                            </div>

                            <!-- 11. Website Information and Accuracy -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">11</span>
                                    <span>Website Information and Accuracy</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    We make reasonable efforts to ensure that the information displayed on our website is accurate and up to date. However, room photographs, descriptions, facilities, prices and service information are provided for general guidance and may change. Actual room allocation and service availability are subject to confirmation.
                                </p>
                            </div>

                            <!-- 12. Limitation of Liability -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">12</span>
                                    <span>Limitation of Liability</span>
                                </h2>
                                <p class="policy-text">
                                    Hotel Destin will take reasonable care in providing its services. To the extent permitted by applicable law, Hotel Destin shall not be responsible for losses arising from circumstances beyond its reasonable control, including natural disasters, government restrictions, interruptions to public utilities or third-party service failures.
                                </p>
                                <p class="policy-text mb-0">
                                    Nothing in these Terms & Conditions excludes any liability or consumer rights that cannot legally be excluded.
                                </p>
                            </div>

                            <!-- 13. Website Use -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">13</span>
                                    <span>Website Use</span>
                                </h2>
                                <ul class="policy-list mb-0">
                                    <li>Use the website only for lawful purposes.</li>
                                    <li>Do not submit false, misleading or fraudulent information.</li>
                                    <li>Do not attempt unauthorised access to website systems.</li>
                                    <li>Do not interfere with website functionality or security.</li>
                                    <li>Do not use website content in a manner that infringes applicable rights.</li>
                                </ul>
                            </div>

                            <!-- 14. Privacy -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">14</span>
                                    <span>Privacy</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Personal information collected through our website and booking services is handled in accordance with our <a href="privacy-policy" class="policy-contact-link">Privacy Policy</a>. Please review our Privacy Policy to understand how your information is collected and used.
                                </p>
                            </div>

                            <!-- 15. Changes to These Terms -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">15</span>
                                    <span>Changes to These Terms</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    Hotel Destin reserves the right to update these Terms & Conditions when necessary. Changes will be published on this page with the revised effective date. Continued use of the website after such updates will be subject to the updated terms, to the extent permitted by law.
                                </p>
                            </div>

                            <!-- 16. Governing Law and Jurisdiction -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">16</span>
                                    <span>Governing Law and Jurisdiction</span>
                                </h2>
                                <p class="policy-text mb-0">
                                    These Terms & Conditions shall be governed by the applicable laws of India. Subject to applicable consumer protection laws and jurisdictional requirements, disputes shall be subject to the competent courts in Gwalior, Madhya Pradesh.
                                </p>
                            </div>

                            <!-- 17. Contact Information -->
                            <div class="policy-section">
                                <h2 class="policy-section-title">
                                    <span class="section-num">17</span>
                                    <span>Contact Information</span>
                                </h2>
                                <p class="policy-text">
                                    For booking-related questions or clarification regarding these Terms & Conditions, contact:
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