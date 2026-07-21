<?php
// PHP backend to process enquiry form submissions
require_once __DIR__ . '/db.php';

$message_sent = false;
$errors = [];

if (isset($_SESSION['banquet_success']) && $_SESSION['banquet_success'] === true) {
    $message_sent = true;
    $success_name = isset($_SESSION['banq_name']) ? $_SESSION['banq_name'] : '';
    $success_guests = isset($_SESSION['banq_guests']) ? $_SESSION['banq_guests'] : 150;
    $success_date = isset($_SESSION['banq_date']) ? $_SESSION['banq_date'] : '';

    // Clear session
    unset($_SESSION['banquet_success']);
    unset($_SESSION['banq_name']);
    unset($_SESSION['banq_guests']);
    unset($_SESSION['banq_date']);
}

$hall_name = "Banquet Oh Saathi Re";
$hall_capacity = "300 Guests";
$hall_size = "3,800 Sq. Ft.";
$rental_charges = "₹15,000 for 6 hours";
$decor_management_text = "Flexible event packages, delicious catering, and dedicated service available";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $event_type = isset($_POST['event_type']) ? htmlspecialchars(trim($_POST['event_type'])) : '';
    $date = isset($_POST['date']) ? htmlspecialchars(trim($_POST['date'])) : '';
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 150;
    $remarks = isset($_POST['remarks']) ? htmlspecialchars(trim($_POST['remarks'])) : '';
    
    // Server-side validation
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    }
    if (empty($date)) {
        $errors['date'] = 'Event date is required';
    }
    if ($guests < 10 || $guests > 300) {
        $errors['guests'] = 'Our hall capacity is up to 300 guests';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, guests, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $req_summary = "Event Type: " . sanitize($event_type) . " | Remarks: " . sanitize($remarks);
            $stmt->execute(['banquet', $name, $email, $phone, $date, $guests, $req_summary]);

            // Send email alert to admin
            require_once __DIR__ . '/mail-helper.php';
            send_enquiry_alert('banquet', $name, $email, $phone, $date, $guests, [
                'Event Type' => $event_type,
                'Remarks' => $remarks
            ]);

            $_SESSION['banquet_success'] = true;
            $_SESSION['banq_name'] = $name;
            $_SESSION['banq_guests'] = $guests;
            $_SESSION['banq_date'] = $date;

            header("Location: banquet.php#enquiry");
            exit;
        } catch (Exception $e) {
            error_log("Banquet submission DB error: " . $e->getMessage());
            $_SESSION['banquet_success'] = true;
            $_SESSION['banq_name'] = $name;
            $_SESSION['banq_guests'] = $guests;
            $_SESSION['banq_date'] = $date;

            header("Location: banquet.php#enquiry");
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
    <meta name="description" content="Host weddings, corporate conferences, and social events at Gwalior's Banquet Oh Saathi Re. 3,800 sqft hall with a capacity of 300 guests.">
    <meta name="keywords" content="banquet hall Gwalior, wedding hall, conference room, social events venue, banquet oh saathi re, hotel destin banquet">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Banquet Oh Saathi Re - Hotel Destin Gwalior</title>

    <style>
        /* Success Popup & Blur Overlay Styles */
        .success-overlay-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeInOverlay 0.3s ease forwards;
        }
        .success-popup-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 35px 30px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
            border: 1px solid #e2e8f0;
            transform: scale(0.9);
            animation: scaleUpCard 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            position: relative;
        }
        @keyframes fadeInOverlay {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleUpCard {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        /* Custom Premium Banquet Styles */
        :root {
            --bq-primary: #9c6047;
            --bq-primary-rgb: 156, 96, 71;
            --bq-dark: #0e0e0e;
            --bq-accent: #c5a880;
            --bq-border: #e9ecf2;
        }

        .banquet-hero {
            position: relative;
            background: linear-gradient(rgba(14, 14, 14, 0.55), rgba(14, 14, 14, 0.75)), url('assets/imgs/page/hotel/banner-hotel.png') no-repeat center center;
            background-size: cover;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-align: center;
        }

        @media (min-width: 768px) {
            .banquet-hero {
                height: 400px;
            }
        }

        .banquet-hero-content {
            max-width: 800px;
            padding: 20px;
        }

        .banquet-hero-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 500;
            letter-spacing: -1px;
            margin-bottom: 12px;
            font-family: 'Merienda One', cursive, Georgia, serif;
        }

        @media (min-width: 576px) {
            .banquet-hero-title {
                font-size: 38px;
            }
        }

        @media (min-width: 992px) {
            .banquet-hero-title {
                font-size: 54px;
            }
        }

        .banquet-hero-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            line-height: 1.5;
        }

        /* Split-screen Showcase Card */
        .showcase-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0,0,0,0.03);
            margin-bottom: 50px;
        }

        .showcase-img-panel {
            min-height: 350px;
            background: url('assets/imgs/page/room/banner-room.png') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .showcase-badge {
            position: absolute;
            top: 24px;
            left: 24px;
            background: var(--bq-primary);
            color: #ffffff;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 15px rgba(var(--bq-primary-rgb), 0.25);
        }

        .showcase-info-panel {
            padding: 20px;
        }

        @media (min-width: 768px) {
            .showcase-info-panel {
                padding: 40px;
            }
        }

        .showcase-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--bq-dark);
            margin-bottom: 15px;
            font-family: 'Merienda One', cursive, Georgia, serif;
        }

        @media (min-width: 768px) {
            .showcase-title {
                font-size: 28px;
            }
        }

        .showcase-description {
            font-size: 14.5px;
            color: var(--bs-neutral-500);
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Spec Table styling */
        .spec-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            border-top: 1px solid var(--bq-border);
            padding-top: 20px;
            margin-bottom: 20px;
        }

        .spec-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .spec-icon {
            width: 40px;
            height: 40px;
            background: rgba(var(--bq-primary-rgb), 0.08);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--bq-primary);
            font-size: 18px;
            flex-shrink: 0;
        }

        .spec-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--bs-neutral-400);
            margin-bottom: 2px;
            font-weight: 700;
        }

        .spec-val {
            font-size: 14px;
            font-weight: 600;
            color: var(--bq-dark);
            margin: 0;
        }

        @media (max-width: 575px) {
            .spec-grid {
                grid-template-columns: 1fr;
                gap: 15px;
                padding-top: 15px;
                margin-bottom: 15px;
            }

            .spec-icon {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }

            .spec-label {
                font-size: 10px;
                margin-bottom: 1px;
            }

            .spec-val {
                font-size: 13px;
            }

            .section-title-responsive {
                font-size: 24px !important;
                line-height: 1.3 !important;
            }
        }


        /* Enquiry Form styles */
        .enquiry-form-section {
            padding: 0 0 60px 0;
            background: #ffffff;
        }

        .form-box-centered {
            background-color: #fafafa;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.01);
            max-width: 900px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            .form-box-centered {
                padding: 40px;
            }
        }

        .inquiry-form-title {
            font-size: 22px !important;
            margin-bottom: 8px;
            font-family: 'Merienda One', cursive, Georgia, serif;
        }

        @media (min-width: 768px) {
            .inquiry-form-title {
                font-size: 28px !important;
            }
        }

        .form-control-custom {
            width: 100%;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            color: var(--bq-dark);
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--bq-primary);
            box-shadow: 0 0 0 4px rgba(var(--bq-primary-rgb), 0.1);
        }

        .form-label-custom {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--bs-neutral-800);
            margin-bottom: 6px;
            display: block;
            transition: color 0.3s ease;
        }

        .form-group-custom:focus-within .form-label-custom {
            color: var(--bq-primary);
        }

        .btn-reserve-table {
            background: var(--bq-dark);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 14px 25px;
            font-size: 14.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 15px;
            cursor: pointer;
        }

        .btn-reserve-table:hover {
            background: var(--bq-primary);
            box-shadow: 0 8px 20px rgba(var(--bq-primary-rgb), 0.25);
            transform: translateY(-2px);
        }

        .success-card {
            text-align: center;
            padding: 40px 20px;
        }

        .success-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(61, 194, 98, 0.1);
            color: var(--res-veg);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 24px;
            border: 1px solid rgba(61, 194, 98, 0.2);
        }



        /* Features/Services Section - Premium Light Design */
        .services-section-wrapper {
            background-color: #faf9f6;
            padding: 85px 0;
            border-top: 1px solid #e9ecf2;
            position: relative;
            overflow: hidden;
        }

        .services-header-title {
            color: #0e0e0e !important;
            font-size: 32px;
            font-weight: 600;
            font-family: 'Merienda One', cursive, Georgia, serif;
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }

        .services-header-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #9c6047, transparent);
        }

        .services-header-desc {
            color: #64748b !important;
            font-size: 15px;
            max-width: 600px;
            margin: 15px auto 0 auto;
        }

        .service-premium-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            padding: 40px 30px;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.015);
        }

        .service-premium-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, #9c6047, #c5a880);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .service-premium-card:hover {
            transform: translateY(-8px);
            border-color: rgba(156, 96, 71, 0.20);
            box-shadow: 0 15px 35px rgba(156, 96, 71, 0.08);
        }

        .service-premium-card:hover::before {
            opacity: 1;
        }

        .service-premium-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: rgba(156, 96, 71, 0.06);
            border: 1px solid rgba(156, 96, 71, 0.12);
            color: #9c6047;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }

        .service-premium-card:hover .service-premium-icon {
            background: linear-gradient(135deg, #9c6047, #c5a880);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 6px 12px rgba(156, 96, 71, 0.15);
        }

        .service-premium-title {
            font-size: 19px;
            font-weight: 600;
            color: #0e0e0e;
            margin-bottom: 12px;
            font-family: 'Merienda One', cursive, Georgia, serif;
            transition: color 0.3s ease;
        }

        .service-premium-card:hover .service-premium-title {
            color: #9c6047;
        }

        .service-premium-desc {
            font-size: 13.5px;
            color: #64748b;
            line-height: 1.6;
            margin: 0;
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>
    <?php if ($message_sent): ?>
        <!-- Backdrop Blur Success Popup -->
        <div class="success-overlay-popup" id="successPopupOverlay">
            <div class="success-popup-card">
                <!-- Close Button -->
                <button type="button" onclick="closeSuccessPopup()" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
                
                <div style="background-color: #10b981; color: #ffffff; width: 64px; height: 64px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; box-shadow: 0 0 0 8px rgba(16, 185, 129, 0.15);">
                    <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h3 class="heading-3 mb-10" style="color: #0f172a; font-weight: 800; font-size: 22px;">Enquiry Submitted!</h3>
                <p class="text-md neutral-500 mb-20" style="font-size: 14.5px; line-height: 1.6; color: #64748b;">
                    Thank you, <strong><?= htmlspecialchars($success_name) ?></strong>. We have registered your banquet enquiry for <strong><?= htmlspecialchars($success_guests) ?> guests</strong> on <strong><?= date('d M Y', strtotime($success_date)) ?></strong>.
                </p>
                <p class="text-sm neutral-400" style="font-size: 13px; color: #94a3b8; line-height: 1.5; margin-bottom: 0;">
                    Our events manager will review your request and reach out shortly to discuss plate budgets, decorations, and customize your configurations.
                </p>
            </div>
        </div>
        <script>
            // Automatically dismiss the pop-up modal after 3 seconds (3000ms)
            setTimeout(function() {
                var pop = document.getElementById('successPopupOverlay');
                if (pop) {
                    pop.style.transition = 'opacity 0.4s ease';
                    pop.style.opacity = '0';
                    setTimeout(function() {
                        pop.remove();
                    }, 400);
                }
            }, 3000);

            function closeSuccessPopup() {
                var pop = document.getElementById('successPopupOverlay');
                if (pop) {
                    pop.remove();
                }
            }
        </script>
    <?php endif; ?>
    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">
        
        <!-- Hero Header Section -->
        <section class="banquet-hero wow fadeIn">
            <div class="banquet-hero-content">
                <h1 class="banquet-hero-title"><?= $hall_name ?></h1>
                <p class="banquet-hero-subtitle">
                    Host weddings, corporate conferences, and parties in Gwalior’s most premium and versatile event space. Accommodates up to <?= $hall_capacity ?>.
                </p>
            </div>
        </section>

        <!-- Main Showcase Showcase -->
        <section class="section-box pt-40 pb-20">
            <div class="container">
                <div class="showcase-card wow fadeInUp">
                    <div class="row g-0">
                        <div class="col-lg-6 col-12 showcase-img-panel">
                            <span class="showcase-badge">Premium Hall</span>
                        </div>
                        <div class="col-lg-6 col-12 showcase-info-panel d-flex flex-column justify-content-center">
                            <h2 class="showcase-title"><?= $hall_name ?></h2>
                            <p class="showcase-description">
                                Banquet Oh Saathi Re is Gwalior's premier pillar-free venue, perfect for weddings, receptions, birthday celebrations, corporate meetings, conferences, and other social events. We offer flexible event packages, delicious catering, and dedicated service to make every event a success.
                            </p>
                            
                            <div class="spec-grid">
                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                    </div>
                                    <div>
                                        <p class="spec-label">Guest Capacity</p>
                                        <p class="spec-val"><?= $hall_capacity ?> Capacity</p>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                                    </div>
                                    <div>
                                        <p class="spec-label">Hall Size</p>
                                        <p class="spec-val"><?= $hall_size ?></p>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v20M17 5H7M19 8H5M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
                                    </div>
                                    <div>
                                        <p class="spec-label">Rental charges</p>
                                        <p class="spec-val"><?= $rental_charges ?></p>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                                    </div>
                                    <div>
                                        <p class="spec-label">Exact Location</p>
                                        <p class="spec-val">Hotel Destin Gwalior</p>
                                    </div>
                                </div>
                            </div>

                            <p style="font-size: 13.5px; color: var(--bs-neutral-500); line-height: 1.5; margin: 5px 0 0 0; font-weight: 500;">
                                💡 <em>* Flexible event packages, delicious catering, and dedicated service available.</em>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Enquiry Form Section -->
        <section id="enquiry" class="enquiry-form-section">
            <div class="container">
                <div class="form-box-centered wow fadeInUp">
                    

                    <div class="text-center mb-30">
                        <h3 class="booking-title inquiry-form-title">Banquet Inquiry Form</h3>
                        <p class="booking-subtitle" style="margin-bottom: 0; font-size: 13.5px;">Submit details about your upcoming function to get a customized proposal.</p>
                    </div>
                    <form method="POST" action="banquet.php#enquiry">
                        <div class="row">
                            <div class="col-md-6 col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_name">Your Name *</label>
                                <input class="form-control-custom" type="text" id="enq_name" name="name" value="<?= isset($name) ? $name : '' ?>" placeholder="e.g. Alice Roses" required>
                            </div>
                            <div class="col-md-6 col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_email">Email Address *</label>
                                <input class="form-control-custom" type="email" id="enq_email" name="email" value="<?= isset($email) ? $email : '' ?>" placeholder="e.g. alice@example.com" required>
                            </div>
                            <div class="col-md-6 col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_phone">Phone Number *</label>
                                <input class="form-control-custom" type="tel" id="enq_phone" name="phone" value="<?= isset($phone) ? $phone : '' ?>" placeholder="e.g. +91 99119 11645" required>
                            </div>
                            <div class="col-md-6 col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_event">Event Type *</label>
                                <select class="form-control-custom" id="enq_event" name="event_type" required>
                                    <option value="birthday celebration">Birthday Celebration</option>
                                    <option value="wedding & reception">Wedding & Reception</option>
                                    <option value="corporate meeting">Corporate Meeting</option>
                                    <option value="conference & summit">Conference & Summit</option>
                                    <option value="product launch">Product Launch</option>
                                    <option value="team offsite">Team Offsite</option>
                                    <option value="social gathering">Social Gathering</option>
                                    <option value="award ceremony">Award Ceremony</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_date">Event Date *</label>
                                <input class="form-control-custom" type="date" id="enq_date" name="date" value="<?= isset($date) ? $date : '' ?>" required min="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6 col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_guests">Estimated Guests *</label>
                                <input class="form-control-custom" type="number" id="enq_guests" name="guests" value="<?= isset($guests) ? $guests : '150' ?>" required min="10" max="300" placeholder="Up to 300 guests">
                                <?php if (isset($errors['guests'])): ?>
                                    <div class="form-error" style="color: #ef4444; font-size:12px; margin-top:5px; font-weight:600;"><?= $errors['guests'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 form-group-custom">
                                <label class="form-label-custom" for="enq_remarks">Custom Function Remarks</label>
                                <textarea class="form-control-custom" id="enq_remarks" name="remarks" rows="3" placeholder="Tell us more about your stage setup requirements, AV specifications, or event styling ideas..."><?= isset($remarks) ? $remarks : '' ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn-reserve-table" style="width: auto; min-width: 250px; margin: 15px auto 0 auto; display: block;">Submit Event Request</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Services & Features Section - Premium Luxury Dark Design -->
        <section class="services-section-wrapper">
            <div class="container">
                <div class="text-center mb-50">
                    <h2 class="heading-2 services-header-title wow fadeInUp">Complete Event Services</h2>
                    <p class="services-header-desc wow fadeInUp" data-wow-delay="0.1s">
                        We provide end-to-end hospitality services so you can focus on creating memories.
                    </p>
                </div>

                <div class="row g-4">
                    <!-- Service 1 -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="service-premium-card wow fadeInUp">
                            <div class="service-premium-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12h18M3 12a9 9 0 0 1 18 0M3 12c0 2.2 1.8 4 4 4h10c2.2 0 4-1.8 4-4M12 3v3M12 6c-2.2 0-4 1.8-4 4h8c0-2.2-1.8-4-4-4Z"/>
                                </svg>
                            </div>
                            <h4 class="service-premium-title">In-House Catering</h4>
                            <p class="service-premium-desc">
                                Delicious catering configurations featuring traditional regional cuisines and premium continental options tailored to your function.
                            </p>
                        </div>
                    </div>

                    <!-- Service 2 -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="service-premium-card wow fadeInUp" data-wow-delay="0.1s">
                            <div class="service-premium-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l-1.813-5.096L2.091 15 7.187 13.187 9 8.091l1.813 5.096L15.909 15l-5.096 1.813zM21 7.105l-1.622-1.622L17.761 2.22 16.14 5.483l-3.263 1.622L16.14 8.727l1.621 3.263 1.622-3.263L21 7.105zM19 19.105l-1.622-1.622-1.617-3.263-1.621 3.263-3.263 1.622 3.263 1.621 1.621 3.263 1.622-3.263L19 19.105z"/>
                                </svg>
                            </div>
                            <h4 class="service-premium-title">Custom Decors</h4>
                            <p class="service-premium-desc">
                                Professional stage setups, theme decors, and customized seating layouts perfect for weddings, birthdays, and conferences.
                            </p>
                        </div>
                    </div>

                    <!-- Service 3 -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="service-premium-card wow fadeInUp" data-wow-delay="0.2s">
                            <div class="service-premium-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h4 class="service-premium-title">Dedicated Service</h4>
                            <p class="service-premium-desc">
                                Expert team of professional servers and event coordinators attending to every detail to make every event a success.
                            </p>
                        </div>
                    </div>

                    <!-- Service 4 -->
                    <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="service-premium-card wow fadeInUp" data-wow-delay="0.3s">
                            <div class="service-premium-icon">
                                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h4 class="service-premium-title">Event Packages</h4>
                            <p class="service-premium-desc">
                                Flexible half-day (6 hours) or full-day packages customizable to your exact event specifications and timing requirements.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
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
