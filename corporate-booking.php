<?php
require_once __DIR__ . '/db.php';

$message_sent = false;
$errors = [];

if (isset($_SESSION['corporate_success']) && $_SESSION['corporate_success'] === true) {
    $message_sent = true;
    unset($_SESSION['corporate_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $company = isset($_POST['company']) ? htmlspecialchars(trim($_POST['company'])) : '';
    $date = isset($_POST['date']) ? htmlspecialchars(trim($_POST['date'])) : '';
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 10;
    $package = 'Corporate Event';
    $remarks = isset($_POST['remarks']) ? htmlspecialchars(trim($_POST['remarks'])) : '';

    if (empty($name)) {
        $errors['name'] = 'Full name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid company email is required';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Contact number is required';
    }
    if (empty($date)) {
        $errors['date'] = 'Event date is required';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, guests, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $req_summary = "Company: " . sanitize($company) . " | Package: " . sanitize($package) . " | Remarks: " . sanitize($remarks);
            $stmt->execute(['corporate', $name, $email, $phone, $date, $guests, $req_summary]);

            // Send email alert to admin
            require_once __DIR__ . '/mail-helper.php';
            send_enquiry_alert('corporate', $name, $email, $phone, $date, $guests, [
                'Company Name' => $company,
                'Package Chosen' => $package,
                'Remarks/Special Requests' => $remarks
            ]);

            $_SESSION['corporate_success'] = true;
            header("Location: corporate-booking.php");
            exit;
        } catch (Exception $e) {
            error_log("Corporate booking DB error: " . $e->getMessage());
            $_SESSION['corporate_success'] = true;
            header("Location: corporate-booking.php");
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
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Corporate Events & Conferences - Hotel Destin Gwalior</title>

    <style>
        .corp-split-hero {
            background: linear-gradient(0deg, rgba(14, 14, 14, 0.75) 0%, rgba(14, 14, 14, 0.5) 100%), url('uploads/corporate_booking_hero.jpg') no-repeat center center;
            background-size: cover;
            min-height: 520px;
            padding: 80px 0;
            display: flex;
            align-items: center;
        }

        .corp-hero-title {
            font-size: 38px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 15px;
            font-family: var(--bs-font-serif, "Playfair Display", serif);
            line-height: 1.25;
        }

        .corp-hero-subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
            max-width: 520px;
            line-height: 1.6;
        }

        .form-wrapper-split {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        /* Feature items on left column */
        .features-list {
            margin-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 20px;
        }

        .feature-icon-gold {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            margin-right: 12px;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
            background: #9c6047;
            flex-shrink: 0;
        }

        .form-label-custom {
            font-size: 10.5px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            font-weight: 700 !important;
            color: #475569 !important;
            margin-bottom: 4px !important;
            display: block;
        }

        .form-control-custom {
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            background-color: #f8fafc !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #0f172a !important;
            transition: all 0.2s ease;
        }

        .form-control-custom:focus {
            border-color: #9c6047 !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(156, 96, 71, 0.15) !important;
            outline: none !important;
        }

        .btn-submit-custom {
            height: 44px !important;
            background-color: #9c6047 !important;
            border: none !important;
            border-radius: 8px !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            transition: all 0.25s ease !important;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(156, 96, 71, 0.2) !important;
            width: 100%;
        }

        .btn-submit-custom:hover {
            background-color: #834f39 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(156, 96, 71, 0.3) !important;
        }

        @media (max-width: 991.98px) {
            .corp-split-hero {
                padding: 40px 0;
                min-height: auto;
            }
            .corp-hero-title {
                font-size: 28px;
            }
            .corp-hero-subtitle {
                font-size: 14px;
                margin-bottom: 20px;
            }
            .form-wrapper-split {
                padding: 20px;
                border-radius: 16px;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>

<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <!-- Hero & Form Split Section -->
        <section class="corp-split-hero">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Left Column: Heading & Features -->
                    <div class="col-lg-6 col-12 text-lg-start text-center mb-40 mb-lg-0 text-white">
                        <span class="badge mb-15" style="background-color: rgba(255, 255, 255, 0.15); color: #ffffff; font-weight: 700; padding: 6px 12px; font-size:11px; border-radius: 4px; letter-spacing: 0.8px; text-transform: uppercase;">Premium Conference Venues</span>
                        <h1 class="corp-hero-title">Corporate Meetings &amp; Events</h1>
                        <p class="corp-hero-subtitle mb-30">Gwalior's premier conference venue with top-tier executive arrangements and seamless technical services.</p>
                        
                        <div class="features-list d-none d-lg-block">
                            <div class="feature-item d-flex align-items-center mb-15">
                                <span class="feature-icon-gold">✓</span>
                                <span style="font-size: 14.5px; font-weight: 500;">High-Speed Fiber Wi-Fi &amp; AV Projector Screens</span>
                            </div>
                            <div class="feature-item d-flex align-items-center mb-15">
                                <span class="feature-icon-gold">✓</span>
                                <span style="font-size: 14.5px; font-weight: 500;">Dedicated Technical Coordinator On-Site</span>
                            </div>
                            <div class="feature-item d-flex align-items-center mb-15">
                                <span class="feature-icon-gold">✓</span>
                                <span style="font-size: 14.5px; font-weight: 500;">Custom Boardroom &amp; Banquet Seating Setups</span>
                            </div>
                            <div class="feature-item d-flex align-items-center">
                                <span class="feature-icon-gold">✓</span>
                                <span style="font-size: 14.5px; font-weight: 500;">Fine Catering &amp; Custom Lunch/High-Tea Menus</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Glassmorphic Callback Form -->
                    <div class="col-lg-6 col-12">
                        <div class="form-wrapper-split">
                            <h3 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px; text-align: left;">Request Callback</h3>
                            <p style="font-size: 13px; color: #64748b; margin-bottom: 20px; text-align: left;">Our events manager will reach out within 2 business hours.</p>
                            
                            <?php if ($message_sent): ?>
                                <div class="alert alert-success" style="border-radius: 8px; font-size: 13.5px; margin-bottom: 20px; background: rgba(156, 96, 71, 0.08); border: 1px solid rgba(156, 96, 71, 0.2); color: #9c6047; padding: 10px 16px;">
                                    Thank you! Your callback request was sent successfully.
                                </div>
                            <?php endif; ?>

                            <form action="corporate-booking.php" method="POST">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label-custom">Full Name *</label>
                                            <input class="form-control-custom w-100" type="text" name="name" placeholder="Contact Name" required style="height: 40px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label-custom">Company Name</label>
                                            <input class="form-control-custom w-100" type="text" name="company" placeholder="Acme Corp" style="height: 40px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label-custom">Company Email *</label>
                                            <input class="form-control-custom w-100" type="email" name="email" placeholder="work@company.com" required style="height: 40px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label-custom">Contact Number *</label>
                                            <input class="form-control-custom w-100" type="text" name="phone" placeholder="Phone Number" required style="height: 40px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label-custom">Event Date *</label>
                                            <input class="form-control-custom w-100" type="date" name="date" required style="height: 40px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-2">
                                            <label class="form-label-custom">Attendees</label>
                                            <input class="form-control-custom w-100" type="number" name="guests" value="10" min="5" max="300" style="height: 40px; font-size: 13px;">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label-custom">Specify Seating &amp; AV Requirements</label>
                                            <textarea class="form-control-custom w-100" name="remarks" rows="2" placeholder="Specify boardroom setup, projector options..." style="font-size: 13px;"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <button class="btn-submit-custom" type="submit">
                                            Send Conference Request
                                        </button>
                                    </div>
                                </div>
                            </form>
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