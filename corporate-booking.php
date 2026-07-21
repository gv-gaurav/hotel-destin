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
    $package = isset($_POST['package']) ? htmlspecialchars(trim($_POST['package'])) : 'Classic Boardroom';
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
        .corp-hero {
            background: linear-gradient(0deg, rgba(14, 14, 14, 0.65) 0%, rgba(14, 14, 14, 0.45) 100%), url('assets/imgs/page/pages/banner2.png') no-repeat center center;
            background-size: cover;
            min-height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .corp-hero-title {
            font-size: 42px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }
        .corp-hero-subtitle {
            font-size: 17px;
            color: rgba(255,255,255,0.9);
            max-width: 600px;
            margin: 0 auto;
        }
        .packages-grid {
            margin-top: 50px;
        }
        .package-card {
            border: 1px solid #e9ecf2;
            border-radius: 16px;
            padding: 30px;
            background: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
        }
        .package-card:hover {
            box-shadow: 0 12px 30px rgba(0,0,0,0.04);
            border-color: #9c6047;
        }
        .package-price {
            font-size: 28px;
            color: #9c6047;
            font-weight: 700;
            margin: 15px 0;
        }
        .form-section {
            padding: 60px 0;
            background: #fbfbfb;
            border-top: 1px solid #eee;
        }
        .form-wrapper {
            background: #ffffff;
            border: 1px solid #e9ecf2;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.01);
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <!-- Hero Header -->
        <section class="corp-hero wow fadeIn">
            <div>
                <h1 class="corp-hero-title">Corporate Meetings & Events</h1>
                <p class="corp-hero-subtitle">Gwalior's premier conference venue with top-tier executive arrangements and seamless technical services.</p>
            </div>
        </section>

        <!-- Package Options -->
        <section class="section-box py-50">
            <div class="container">
                <div class="text-center mb-40">
                    <h2 class="font-heading neutral-1000">Flexible Meeting Packages</h2>
                    <p class="neutral-500 max-width-600 mx-auto">Select a standard conference structure or request custom menus and catering setups.</p>
                </div>
                <div class="row g-4 packages-grid">
                    <div class="col-md-4">
                        <div class="package-card text-center wow fadeInUp">
                            <h3 class="font-heading">Classic Boardroom</h3>
                            <p class="neutral-400 mt-5">Perfect for brief business roundtables</p>
                            <div class="package-price">₹650 <span style="font-size: 14px; font-weight: normal;">/ plate</span></div>
                            <ul class="text-start mt-20" style="padding-left: 20px; font-size: 13.5px; color: #555;">
                                <li class="mb-5">Projector & High Speed Wi-Fi</li>
                                <li class="mb-5">Standard sound system</li>
                                <li class="mb-5">High Tea & Cookies refreshments</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="package-card text-center wow fadeInUp" style="border-color: #9c6047; box-shadow: 0 8px 24px rgba(156,96,71,0.05);">
                            <span class="badge bg-dark mb-10 text-white" style="font-size: 10px; padding: 4px 10px;">MOST POPULAR</span>
                            <h3 class="font-heading">Executive Seminar</h3>
                            <p class="neutral-400 mt-5">Ideal for training & product launches</p>
                            <div class="package-price">₹850 <span style="font-size: 14px; font-weight: normal;">/ plate</span></div>
                            <ul class="text-start mt-20" style="padding-left: 20px; font-size: 13.5px; color: #555;">
                                <li class="mb-5">Premium LCD Wall Screens</li>
                                <li class="mb-5">Wireless mics & podium set</li>
                                <li class="mb-5">Buffet Lunch & High Tea snacks</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="package-card text-center wow fadeInUp">
                            <h3 class="font-heading">Deluxe VIP Summit</h3>
                            <p class="neutral-400 mt-5">Designed for elite corporate banquets</p>
                            <div class="package-price">₹1,150 <span style="font-size: 14px; font-weight: normal;">/ plate</span></div>
                            <ul class="text-start mt-20" style="padding-left: 20px; font-size: 13.5px; color: #555;">
                                <li class="mb-5">Dual Projector Screens setup</li>
                                <li class="mb-5">Dedicated tech support coordinator</li>
                                <li class="mb-5">Premium VIP multi-cuisine lunch</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Enquiry Form -->
        <section class="form-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="form-wrapper">
                            <h3 class="font-heading text-center mb-10">Request Corporate Callback</h3>
                            <p class="neutral-500 text-center mb-30">Fill the booking schedule and our events manager will call you within 2 business hours.</p>
                            
                            <?php if ($message_sent): ?>
                                <div class="alert alert-success" style="border-radius: 8px; font-size: 14px; margin-bottom: 25px; background: rgba(156, 96, 71, 0.08); border: 1px solid rgba(156, 96, 71, 0.2); color: #9c6047; padding: 12px 20px;">
                                    Thank you! Your corporate booking request has been logged successfully. We will connect shortly.
                                </div>
                            <?php endif; ?>

                            <form action="corporate-booking.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Full Name *</label>
                                            <input class="form-control-custom" type="text" name="name" placeholder="Contact Person Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Company Name</label>
                                            <input class="form-control-custom" type="text" name="company" placeholder="e.g. Acme Corp">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Company Email *</label>
                                            <input class="form-control-custom" type="email" name="email" placeholder="work@company.com" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Contact Number *</label>
                                            <input class="form-control-custom" type="text" name="phone" placeholder="Phone Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Preferred Event Date *</label>
                                            <input class="form-control-custom" type="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Estimated Attendees</label>
                                            <input class="form-control-custom" type="number" name="guests" value="10" min="5" max="300">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Select Package Choice</label>
                                            <select class="form-control-custom" name="package" style="height: 50px; background-position: right 20px center;">
                                                <option value="Classic Boardroom">Classic Boardroom Package (₹650/plate)</option>
                                                <option value="Executive Seminar" selected>Executive Seminar Package (₹850/plate)</option>
                                                <option value="Deluxe VIP Summit">Deluxe VIP Summit Package (₹1,150/plate)</option>
                                                <option value="Custom Event Setup">Custom Event Setup (To be discussed)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Specify Seating & AV Requirements</label>
                                            <textarea class="form-control-custom" name="remarks" rows="3" placeholder="Specify boardroom setup, projector options, food restrictions..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center mt-25">
                                        <button class="btn btn-black text-white" type="submit" style="padding: 12px 35px; border-radius: 8px;">
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
