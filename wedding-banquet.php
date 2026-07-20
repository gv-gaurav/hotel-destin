<?php
require_once __DIR__ . '/db.php';

$message_sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $date = isset($_POST['date']) ? htmlspecialchars(trim($_POST['date'])) : '';
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 150;
    $package = isset($_POST['package']) ? htmlspecialchars(trim($_POST['package'])) : 'Standard Wedding Royale';
    $remarks = isset($_POST['remarks']) ? htmlspecialchars(trim($_POST['remarks'])) : '';

    if (empty($name)) {
        $errors['name'] = 'Full name is required';
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Valid email is required';
    }
    if (empty($phone)) {
        $errors['phone'] = 'Contact phone is required';
    }
    if (empty($date)) {
        $errors['date'] = 'Wedding date is required';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, guests, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $req_summary = "Package: " . sanitize($package) . " | Remarks: " . sanitize($remarks);
            $stmt->execute(['wedding', $name, $email, $phone, $date, $guests, $req_summary]);
            $message_sent = true;

            // Send email alert to admin
            require_once __DIR__ . '/mail-helper.php';
            send_enquiry_alert('wedding', $name, $email, $phone, $date, $guests, [
                'Package Preferred' => $package,
                'Remarks/Special Requests' => $remarks
            ]);
        } catch (Exception $e) {
            error_log("Wedding booking DB error: " . $e->getMessage());
            $message_sent = true;
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
    <title>Dream Weddings & Celebrations - Hotel Destin Gwalior</title>
    
    <style>
        .wedding-hero {
            background: linear-gradient(0deg, rgba(14, 14, 14, 0.6) 0%, rgba(14, 14, 14, 0.35) 100%), url('assets/imgs/page/pages/banner.png') no-repeat center center;
            background-size: cover;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .wedding-hero-title {
            font-size: 46px;
            font-weight: 500;
            color: #ffffff;
            font-family: var(--bs-font-serif, "Playfair Display", serif);
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }
        .wedding-hero-subtitle {
            font-size: 18px;
            color: rgba(255,255,255,0.95);
            max-width: 600px;
            margin: 0 auto;
        }
        .wedding-pkg-card {
            border: 1px solid #f2e6e1;
            border-radius: 16px;
            padding: 30px;
            background: #fffdfb;
            transition: all 0.3s ease;
            height: 100%;
        }
        .wedding-pkg-card:hover {
            box-shadow: 0 15px 35px rgba(156,96,71,0.08);
            border-color: #9c6047;
        }
        .pkg-price {
            font-size: 30px;
            color: #9c6047;
            font-weight: 700;
            margin: 15px 0;
        }
        .form-section {
            padding: 60px 0;
            background: #fdfaf8;
            border-top: 1px solid #f2e6e1;
        }
        .form-wrapper {
            background: #ffffff;
            border: 1px solid #f2e6e1;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(156,96,71,0.02);
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <!-- Hero Header -->
        <section class="wedding-hero wow fadeIn">
            <div>
                <h1 class="wedding-hero-title">Elegant Weddings & Banquets</h1>
                <p class="wedding-hero-subtitle">Celebrate your special day with Gwalior's premium decorations, fine hospitality, and culinary excellence.</p>
            </div>
        </section>

        <!-- Package Options -->
        <section class="section-box py-50">
            <div class="container">
                <div class="text-center mb-40">
                    <h2 class="font-heading neutral-1000">Exquisite Wedding Packages</h2>
                    <p class="neutral-500 max-width-600 mx-auto">We offer comprehensive arrangements including gourmet menus, floral design themes, and luxury rooms for families.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="wedding-pkg-card text-center wow fadeInUp">
                            <h3 class="font-heading">Standard Royale</h3>
                            <p class="neutral-400 mt-5">Perfect for intimate family weddings</p>
                            <div class="pkg-price">₹450 <span style="font-size: 14px; font-weight: normal;">/ plate</span></div>
                            <ul class="text-start mt-20" style="padding-left: 20px; font-size: 13.5px; color: #555;">
                                <li class="mb-5">Fully Decorated Banquet Entry</li>
                                <li class="mb-5">Standard Stage Decor & lighting</li>
                                <li class="mb-5">Complimentary Deluxe Room (1 night)</li>
                                <li class="mb-5">Full Buffet Dinner (Veg)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="wedding-pkg-card text-center wow fadeInUp" style="border-color: #9c6047; background: #fffcf9; box-shadow: 0 8px 24px rgba(156,96,71,0.06);">
                            <span class="badge bg-dark mb-10 text-white" style="font-size: 10px; padding: 4px 10px; background-color: #9c6047 !important;">SIGNATURE SELECTION</span>
                            <h3 class="font-heading">Elite Imperial</h3>
                            <p class="neutral-400 mt-5">Ideal for premium grand receptions</p>
                            <div class="pkg-price">₹550 <span style="font-size: 14px; font-weight: normal;">/ plate</span></div>
                            <ul class="text-start mt-20" style="padding-left: 20px; font-size: 13.5px; color: #555;">
                                <li class="mb-5">Themed Floral decorations</li>
                                <li class="mb-5">Premium carpeted stage setup & DJ spot</li>
                                <li class="mb-5">2 Complimentary Executive Rooms (1 night)</li>
                                <li class="mb-5">Expanded Dinner menu with live counters</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="wedding-pkg-card text-center wow fadeInUp">
                            <h3 class="font-heading">Majestic Destination</h3>
                            <p class="neutral-400 mt-5">Ultimate luxury celebration package</p>
                            <div class="pkg-price">₹700 <span style="font-size: 14px; font-weight: normal;">/ plate</span></div>
                            <ul class="text-start mt-20" style="padding-left: 20px; font-size: 13.5px; color: #555;">
                                <li class="mb-5">Custom designer walkthrough passage</li>
                                <li class="mb-5">Specialized photography platform setup</li>
                                <li class="mb-5">Complimentary Premium Suite (1 night)</li>
                                <li class="mb-5">Multi-cuisine live catering counters</li>
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
                            <h3 class="font-heading text-center mb-10">Plan Your Celebration</h3>
                            <p class="neutral-500 text-center mb-30">Let us help you structure a beautiful event. Fill details below and our wedding coordinator will contact you.</p>
                            
                            <?php if ($message_sent): ?>
                                <div class="alert alert-success" style="border-radius: 8px; font-size: 14px; margin-bottom: 25px; background: rgba(156, 96, 71, 0.08); border: 1px solid rgba(156, 96, 71, 0.2); color: #9c6047; padding: 12px 20px;">
                                    Congratulations! Your wedding inquiry has been logged successfully. Our events coordinator will contact you shortly.
                                </div>
                            <?php endif; ?>

                            <form action="wedding-banquet.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Full Name *</label>
                                            <input class="form-control-custom" type="text" name="name" placeholder="Contact Person Name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Email Address *</label>
                                            <input class="form-control-custom" type="email" name="email" placeholder="contact@domain.com" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Phone Number *</label>
                                            <input class="form-control-custom" type="text" name="phone" placeholder="Phone Number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Proposed Event Date *</label>
                                            <input class="form-control-custom" type="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Estimated Guests Count</label>
                                            <input class="form-control-custom" type="number" name="guests" value="150" min="50" max="500">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Select Wedding Package</label>
                                            <select class="form-control-custom" name="package" style="height: 50px; background-position: right 20px center;">
                                                <option value="Standard Wedding Royale">Standard Royale Package (₹450/plate)</option>
                                                <option value="Elite Imperial Wedding" selected>Elite Imperial Package (₹550/plate)</option>
                                                <option value="Majestic Destination Wedding">Majestic Destination Package (₹700/plate)</option>
                                                <option value="Custom Floral Plan">Custom Floral & Event Planning</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Describe Your Wedding Theme, Catering & Decoration Needs</label>
                                            <textarea class="form-control-custom" name="remarks" rows="4" placeholder="Let us know about floral choices, stage setups, sound requirements, non-veg options..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center mt-25">
                                        <button class="btn btn-black text-white" type="submit" style="padding: 12px 35px; border-radius: 8px; background-color: #9c6047 !important; border-color: #9c6047 !important;">
                                            Send Wedding Enquiry
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
