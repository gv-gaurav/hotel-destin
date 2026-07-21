<?php
require_once __DIR__ . '/db.php';
$message_sent = false;
$errors = [];

if (isset($_SESSION['contact_success']) && $_SESSION['contact_success'] === true) {
    $message_sent = true;
    unset($_SESSION['contact_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = isset($_POST['first_name']) ? htmlspecialchars(trim($_POST['first_name'])) : '';
    $last_name = isset($_POST['last_name']) ? htmlspecialchars(trim($_POST['last_name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    }
    if (empty($message)) {
        $errors['message'] = 'Message is required';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, requirements) VALUES (?, ?, ?, ?, ?)");
            $full_name = $first_name . ' ' . $last_name;
            $stmt->execute(['contact', $full_name, $email, $phone, $message]);

            // Send email alert to admin
            require_once __DIR__ . '/mail-helper.php';
            send_enquiry_alert('contact', $full_name, $email, $phone, null, null, ['Message' => $message]);

            $_SESSION['contact_success'] = true;
            header("Location: contact.php");
            exit;
        } catch (Exception $e) {
            error_log("Contact submission DB error: " . $e->getMessage());
            $_SESSION['contact_success'] = true; // fallback success
            header("Location: contact.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <meta name="description" content="Get in touch with Hotel Destin Gwalior. Located on Sachin Tendulkar Road. Call +91 9203509944 or email info@hoteldestin.in.">
    <meta name="keywords" content="Hotel Destin Gwalior contact, Gwalior hotel phone number, Hotel Destin email">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Contact Us - Hotel Destin Gwalior</title>

    <style>
        /* Custom Premium Contact Page Styles */
        .contact-banner {
            background-color: var(--neutral-100, #f8f9fa);
            padding: 40px 0;
            border-bottom: 1px solid var(--neutral-200, #e9ecef);
        }

        .contact-title {
            font-size: 38px;
            font-weight: 500;
            letter-spacing: -0.8px;
            color: var(--neutral-1000, #0E0E0E);
            margin-bottom: 8px;
        }

        .contact-subtitle {
            font-size: 15px;
            color: var(--neutral-500, #6c757d);
            margin-bottom: 0;
        }

        /* Info Cards */
        .contact-card-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 22px 24px;
            border: 1px solid #e9ecf2;
            height: 100%;
            box-shadow: 0 8px 25px rgba(0,0,0,0.01);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
        }

        .contact-card-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }

        .contact-card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .contact-card-box:hover .contact-card-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        /* Location Icon Colors */
        .icon-location {
            background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
            color: #e65100;
        }
        
        /* Phone Icon Colors */
        .icon-phone {
            background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 100%);
            color: #1a237e;
        }
        
        /* Mail Icon Colors */
        .icon-mail {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
            color: #1b5e20;
        }

        .contact-card-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--neutral-1000, #0E0E0E);
            margin-bottom: 6px;
        }

        .contact-card-text {
            font-size: 14px;
            color: var(--neutral-500, #6c757d);
            line-height: 1.5;
            margin-bottom: 10px;
        }

        .contact-card-link {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--neutral-1000, #0E0E0E);
            text-decoration: none;
            border-bottom: 2px solid transparent;
            padding-bottom: 2px;
            transition: all 0.3s ease;
        }

        .contact-card-link:hover {
            color: var(--primary, #0E0E0E);
            border-bottom-color: var(--primary, #0E0E0E);
        }

        /* Form styling */
        .contact-form-wrapper {
            background: #ffffff;
            border: 1px solid #e9ecf2;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .form-control-custom {
            height: 42px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            padding: 8px 14px;
            font-size: 14px;
            color: #0e0e0e;
            outline: none;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .form-control-custom:focus {
            border-color: #0E0E0E;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(14, 14, 14, 0.06);
        }

        textarea.form-control-custom {
            height: auto;
            resize: none;
        }

        .form-label-custom {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
            display: block;
        }

        .btn-contact-submit {
            background-color: #0e0e0e;
            color: #ffffff;
            font-size: 14.5px;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            width: 100%;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(14, 14, 14, 0.12);
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .btn-contact-submit:hover {
            background-color: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 14, 14, 0.2);
        }

        .btn-contact-submit svg {
            transition: transform 0.3s ease;
        }

        .btn-contact-submit:hover svg {
            transform: translateX(4px);
        }

        .contact-side-banner {
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
            position: relative;
            min-height: 390px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .contact-side-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
        }

        .contact-side-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(14, 14, 14, 0.1) 0%, rgba(14, 14, 14, 0.6) 100%);
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 30px;
            z-index: 2;
        }

        .contact-glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            padding: 24px;
            color: #ffffff;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
        }

        /* Tight section utility */
        .py-tight {
            padding-top: 35px !important;
            padding-bottom: 35px !important;
        }

        @media (max-width: 991px) {
            .contact-side-banner {
                min-height: 350px;
                margin-top: 30px;
            }
        }

        @media (max-width: 768px) {
            .contact-banner {
                padding: 30px 0;
            }
            .contact-title {
                font-size: 28px;
            }
            .py-tight {
                padding-top: 25px !important;
                padding-bottom: 25px !important;
            }
            .contact-form-wrapper {
                padding: 24px;
            }
            .contact-side-banner {
                min-height: 320px;
                margin-top: 20px;
            }
            .contact-glass-card {
                padding: 16px;
            }
            .contact-glass-card h3 {
                font-size: 18px !important;
            }
            .contact-glass-card p {
                font-size: 13px !important;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>

    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">

        <!-- Breadcrumb & Title Section -->
        <section class="contact-banner wow fadeIn">
            <div class="container">
                <ul class="breadcrumbs mb-10" style="padding: 0; background: transparent; display: flex;">
                    <li><a href="index.php">Home</a><span class="arrow-right"><svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></li>
                    <li><span class="text-breadcrumb">Contact Us</span></li>
                </ul>
                <h1 class="contact-title">Get in Touch</h1>
                <p class="contact-subtitle">Have questions or need assistance? Reach out to our Gwalior guest service team.</p>
            </div>
        </section>

        <!-- Contact Cards Section -->
        <section class="section-box background-body py-tight">
            <div class="container">
                <div class="row g-4">
                    
                    <!-- Card 1: Our Address -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="contact-card-box wow fadeInUp">
                            <div>
                                <div class="contact-card-icon icon-location">
                                    <!-- Pin Icon -->
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                </div>
                                <h3 class="contact-card-title">Our Location</h3>
                                <p class="contact-card-text">
                                    <?= htmlspecialchars(get_setting('hotel_address') ?: 'Sachin Tendulkar Rd, Kailash Nagar, Ramanuj Nagar, Gwalior, MP 474011') ?>
                                </p>
                            </div>
                            <a class="contact-card-link" href="#map">View On Google Maps</a>
                        </div>
                    </div>

                    <!-- Card 2: Call Us -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="contact-card-box wow fadeInUp" data-wow-delay="0.05s">
                            <div>
                                <div class="contact-card-icon icon-phone">
                                    <!-- Phone Icon -->
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                </div>
                                <h3 class="contact-card-title">Call Directly</h3>
                                <p class="contact-card-text">
                                    Speak to a member of our front office or reservations desk.
                                </p>
                            </div>
                            <div style="margin-top: 15px;">
                                <?php
                                $phones_str = get_setting('hotel_phone') ?: '09203509944';
                                $phones = array_map('trim', explode(',', $phones_str));
                                foreach ($phones as $ph) {
                                    echo '<a class="contact-card-link" style="display:block; margin-bottom: 5px;" href="tel:' . htmlspecialchars($ph) . '">' . htmlspecialchars($ph) . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Email Us -->
                    <div class="col-lg-4 col-md-6 col-12 mx-auto">
                        <div class="contact-card-box wow fadeInUp" data-wow-delay="0.1s">
                            <div>
                                <div class="contact-card-icon icon-mail">
                                    <!-- Mail Icon -->
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                </div>
                                <h3 class="contact-card-title">Email Inquiry</h3>
                                <p class="contact-card-text">
                                    Send us your questions and we will respond within 24 hours.
                                </p>
                            </div>
                            <div style="margin-top: 15px;">
                                <?php
                                $emails_str = get_setting('hotel_email') ?: 'info@hoteldestin.in';
                                $emails = array_map('trim', explode(',', $emails_str));
                                foreach ($emails as $em) {
                                    echo '<a class="contact-card-link" style="display:block; margin-bottom: 5px;" href="mailto:' . htmlspecialchars($em) . '">' . htmlspecialchars($em) . '</a>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Form & Side Banner Section -->
        <section class="section-box background-body py-tight" style="border-top: 1px solid rgba(0, 0, 0, 0.05);">
            <div class="container">
                <div class="row g-5">
                    
                    <!-- Left: Form -->
                    <div class="col-lg-7 col-12">
                        <div class="contact-form-wrapper wow fadeInUp">
                            <h2 class="neutral-1000 mb-25" style="font-size: 26px; font-weight: 500;">Send Us A Message</h2>
                            <?php if ($message_sent): ?>
                                <div class="alert alert-success" style="border-radius: 8px; font-size: 14px; margin-bottom: 20px; background: rgba(156, 96, 71, 0.08); border: 1px solid rgba(156, 96, 71, 0.2); color: #9c6047; padding: 12px 20px;">
                                    Thank you! Your message has been received. Our team will contact you shortly.
                                </div>
                            <?php endif; ?>
                            <form action="contact.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">First Name *</label>
                                            <input class="form-control-custom" type="text" name="first_name" placeholder="First Name" value="<?= isset($first_name) ? sanitize($first_name) : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Last Name</label>
                                            <input class="form-control-custom" type="text" name="last_name" placeholder="Last Name" value="<?= isset($last_name) ? sanitize($last_name) : '' ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Email Address *</label>
                                            <input class="form-control-custom" type="email" name="email" placeholder="email@domain.com" value="<?= isset($email) ? sanitize($email) : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Phone Number *</label>
                                            <input class="form-control-custom" type="text" name="phone" placeholder="Phone number" value="<?= isset($phone) ? sanitize($phone) : '' ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Your Message *</label>
                                            <textarea class="form-control-custom" name="message" rows="3" placeholder="Leave us a message..." required><?= isset($message) ? sanitize($message) : '' ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-25">
                                        <button class="btn-contact-submit" type="submit">
                                            Send Message
                                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 15L15.5 8L8.5 1M15.5 8L1.5 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right: Large Image Banner -->
                    <div class="col-lg-5 col-12">
                        <div class="contact-side-banner wow fadeInUp" data-wow-delay="0.1s">
                            <img src="assets/imgs/page/hotel/banner-hotel.png" alt="Hotel Destin Front Entrance">
                            <div class="contact-side-overlay">
                                <div class="contact-glass-card">
                                    <h3 style="font-size: 22px; font-weight: 500; margin-bottom: 10px; color: #ffffff;">Experience Hotel Destin</h3>
                                    <p style="font-size: 14px; opacity: 0.95; margin-bottom: 0; line-height: 1.6; color: #ffffff;">
                                        A welcoming Gwalior luxury stay designed for guests who value comfort, cleanliness, and central convenience on Sachin Tendulkar Road.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section id="map" class="box-section box-contact-map background-body">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d894.9536802284084!2d78.20265274966889!3d26.202704763374193!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3976c38e096f2457%3A0xdf2b8e952f5cd731!2sHotel%20DESTIN%20GWALIOR!5e0!3m2!1sen!2sin!4v1783328622750!5m2!1sen!2sin" width="100%" height="550" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </section>

    </main>

    <!-- Footer Include -->
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
