<?php
require_once __DIR__ . '/db.php';

$message_sent = false;
$errors = [];

if (isset($_SESSION['airport_success']) && $_SESSION['airport_success'] === true) {
    $message_sent = true;
    unset($_SESSION['airport_success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $date = isset($_POST['date']) ? htmlspecialchars(trim($_POST['date'])) : '';
    $time = isset($_POST['time']) ? htmlspecialchars(trim($_POST['time'])) : '';
    $vehicle = 'Airport Cab';
    $flight = isset($_POST['flight']) ? htmlspecialchars(trim($_POST['flight'])) : '';
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
        $errors['date'] = 'Date of transfer is required';
    }
    if (empty($time)) {
        $errors['time'] = 'Pickup time is required';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, requirements) VALUES (?, ?, ?, ?, ?, ?)");
            $req_summary = "Vehicle: " . sanitize($vehicle) . " | Flight: " . sanitize($flight) . " | Time: " . sanitize($time) . " | Remarks: " . sanitize($remarks);
            $stmt->execute(['airport_transfer', $name, $email, $phone, $date, $req_summary]);

            // Send email alert to admin
            require_once __DIR__ . '/mail-helper.php';
            send_enquiry_alert('airport_transfer', $name, $email, $phone, $date, null, [
                'Vehicle Preferred' => $vehicle,
                'Flight Number' => $flight,
                'Pickup Time' => $time,
                'Remarks' => $remarks
            ]);

            $_SESSION['airport_success'] = true;
            header("Location: airport-transfer.php");
            exit;
        } catch (Exception $e) {
            error_log("Airport transfer booking DB error: " . $e->getMessage());
            $_SESSION['airport_success'] = true;
            header("Location: airport-transfer.php");
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
    <title>Airport Pickup & Transfers - Hotel Destin Gwalior</title>
    
    <style>
        .ride-hero {
            background: linear-gradient(0deg, rgba(14, 14, 14, 0.7) 0%, rgba(14, 14, 14, 0.45) 100%), url('uploads/airport_transfer_hero.jpg') no-repeat center center;
            background-size: cover;
            min-height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
        }
        .ride-hero-title {
            font-size: 42px;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }
        .ride-hero-subtitle {
            font-size: 17px;
            color: rgba(255,255,255,0.9);
            max-width: 600px;
            margin: 0 auto;
        }
        .vehicle-card {
            border: 1px solid #e9ecf2;
            border-radius: 16px;
            padding: 24px;
            background: #ffffff;
            transition: all 0.3s ease;
            height: 100%;
        }
        .vehicle-card:hover {
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            border-color: #9c6047;
        }
        .vehicle-price {
            font-size: 24px;
            color: #9c6047;
            font-weight: 700;
            margin-top: 10px;
        }
        .form-section {
            padding: 60px 0;
            background: #fafafa;
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
        <section class="ride-hero wow fadeIn">
            <div>
                <h1 class="ride-hero-title">Airport Pickup & Transfers</h1>
                <p class="ride-hero-subtitle">Safe, secure, and premium chauffeured rides from Gwalior Airport (GWL) directly to Hotel Destin.</p>
            </div>
        </section>



        <!-- Booking Form -->
        <section class="form-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="form-wrapper">
                            <h3 class="font-heading text-center mb-10">Schedule A Ride</h3>
                            <p class="neutral-500 text-center mb-30">Provide pickup parameters and flight detail logs. Drivers monitor flights for delays automatically.</p>
                            
                            <?php if ($message_sent): ?>
                                <div class="alert alert-success" style="border-radius: 8px; font-size: 14px; margin-bottom: 25px; background: rgba(156, 96, 71, 0.08); border: 1px solid rgba(156, 96, 71, 0.2); color: #9c6047; padding: 12px 20px;">
                                    Thank you! Your airport pickup request has been received. Our concierge will confirm coordinates via phone shortly.
                                </div>
                            <?php endif; ?>

                            <form action="airport-transfer.php" method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Guest Full Name *</label>
                                            <input class="form-control-custom" type="text" name="name" placeholder="Guest Name" required>
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
                                            <label class="form-label-custom">Flight Number / Details</label>
                                            <input class="form-control-custom" type="text" name="flight" placeholder="e.g. AI-432 (Gwalior)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Arrival Date *</label>
                                            <input class="form-control-custom" type="date" name="date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label-custom">Expected Pickup Time *</label>
                                            <input class="form-control-custom" type="time" name="time" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label-custom">Special Travel Requests (Infant seats, heavy luggage etc.)</label>
                                            <textarea class="form-control-custom" name="remarks" rows="3" placeholder="Let us know if you require meet & greet signs or extra drop locations..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center mt-25">
                                        <button class="btn btn-black text-white" type="submit" style="padding: 12px 35px; border-radius: 8px;">
                                            Book Airport Transfer
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
