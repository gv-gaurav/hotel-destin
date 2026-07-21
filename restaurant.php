<?php
// Dynamic data arrays for future database connection or CMS integration
require_once __DIR__ . '/db.php';

$restaurant_hero_bg = get_setting('restaurant_hero_bg', 'assets/imgs/page/restaurant/hero.png');
$restaurant_name = get_setting('restaurant_hero_title', 'The Heights Rooftop & Club Bar');
$restaurant_tagline = get_setting('restaurant_hero_tagline', 'Elevated Gastronomy & Celestial Libations');
$restaurant_hours = get_setting('restaurant_hero_hours', '07:00 AM to 11:30 PM');
$food_types_text = get_setting('restaurant_food_types', 'We have both veg and non-veg food available with club bar facility at rooftop');
$room_service_text = get_setting('restaurant_room_service_text', 'Room Service & Restaurant Facilities Available');

// Facilities Section heading
$restaurant_facilities_title = get_setting('restaurant_facilities_title', 'Restaurant Facilities');
$restaurant_facilities_desc = get_setting('restaurant_facilities_desc', 'Indulge in our luxurious hospitality features that combine great taste with a premium lounge experience.');

// Amenities showcase
$amenities = [
    [
        'title' => get_setting('restaurant_facility_1_title', 'Rooftop Club & Bar'),
        'description' => get_setting('restaurant_facility_1_desc', 'Unwind under the stars with our signature cocktails, handpicked spirits, and deep house beats at Gwalior\'s premier rooftop club facility.'),
        'image' => get_setting('restaurant_facility_1_image', 'assets/imgs/page/restaurant/rooftop_bar.png'),
        'badge' => get_setting('restaurant_facility_1_badge', 'Open Daily'),
        'icon' => get_setting('restaurant_facility_1_icon', '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 22H6M12 15v7M12 15l7-10H5l7 10zM12 9l-2-2h4l-2 2z"/></svg>')
    ],
    [
        'title' => get_setting('restaurant_facility_2_title', 'Fine Dining Restaurant'),
        'description' => get_setting('restaurant_facility_2_desc', 'A sophisticated family dining atmosphere offering an exquisite spread of pure vegetarian and gourmet non-vegetarian options prepared by master chefs.'),
        'image' => get_setting('restaurant_facility_2_image', 'assets/imgs/page/restaurant/fine_dining.png'),
        'badge' => get_setting('restaurant_facility_2_badge', 'Family Friendly'),
        'icon' => get_setting('restaurant_facility_2_icon', '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 20h20M12 4v3M12 7a8 8 0 0 0-8 8h16a8 8 0 0 0-8-8zM5 15h14"/></svg>')
    ],
    [
        'title' => get_setting('restaurant_facility_3_title', 'In-Room Dining'),
        'description' => get_setting('restaurant_facility_3_desc', 'Experience restaurant-quality hot meals delivered directly to the comfort of your executive room or suite at any hour during operating times.'),
        'image' => get_setting('restaurant_facility_3_image', 'assets/imgs/page/restaurant/room_service.png'),
        'badge' => get_setting('restaurant_facility_3_badge', 'For In-House Guests'),
        'icon' => get_setting('restaurant_facility_3_icon', '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/><path d="M12 2v2M7 8h10M12 12h.01M3 20h18"/></svg>')
    ]
];

// Ambience Section heading
$restaurant_ambience_title = get_setting('restaurant_ambience_title', 'Ambience & Moments');
$restaurant_ambience_desc = get_setting('restaurant_ambience_desc', 'Take a visual tour through our celestial rooftop club and warm indoor dining halls.');

// Ambience Items
$ambience_1_image = get_setting('restaurant_ambience_1_image', 'assets/imgs/page/restaurant/hero.png');
$ambience_1_title = get_setting('restaurant_ambience_1_title', 'Rooftop Skyline Dining');
$ambience_1_desc = get_setting('restaurant_ambience_1_desc', 'Unparalleled city views at dusk');

$ambience_2_image = get_setting('restaurant_ambience_2_image', 'assets/imgs/page/restaurant/rooftop_bar.png');
$ambience_2_title = get_setting('restaurant_ambience_2_title', 'Signature Bar Lounge');

$ambience_3_image = get_setting('restaurant_ambience_3_image', 'assets/imgs/page/restaurant/fine_dining.png');
$ambience_3_title = get_setting('restaurant_ambience_3_title', 'Gourmet Masterpieces');

$ambience_4_image = get_setting('restaurant_ambience_4_image', 'assets/imgs/page/restaurant/room_service.png');
$ambience_4_title = get_setting('restaurant_ambience_4_title', 'Luxury Suite Room Service');

// Success message handling
$message_sent = false;
$errors = [];

if (isset($_SESSION['restaurant_success']) && $_SESSION['restaurant_success'] === true) {
    $message_sent = true;
    $name = isset($_SESSION['res_name']) ? $_SESSION['res_name'] : '';
    $email = isset($_SESSION['res_email']) ? $_SESSION['res_email'] : '';
    $phone = isset($_SESSION['res_phone']) ? $_SESSION['res_phone'] : '';
    $guests = isset($_SESSION['res_guests']) ? $_SESSION['res_guests'] : 2;
    $date = isset($_SESSION['res_date']) ? $_SESSION['res_date'] : '';
    $time = isset($_SESSION['res_time']) ? $_SESSION['res_time'] : '';

    // Clear session data immediately
    unset($_SESSION['restaurant_success']);
    unset($_SESSION['res_name']);
    unset($_SESSION['res_email']);
    unset($_SESSION['res_phone']);
    unset($_SESSION['res_guests']);
    unset($_SESSION['res_date']);
    unset($_SESSION['res_time']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $date = isset($_POST['date']) ? htmlspecialchars(trim($_POST['date'])) : '';
    $time = isset($_POST['time']) ? htmlspecialchars(trim($_POST['time'])) : '';
    $guests = isset($_POST['guests']) ? intval($_POST['guests']) : 2;
    $seating = isset($_POST['seating']) ? htmlspecialchars(trim($_POST['seating'])) : '';
    $preference = isset($_POST['preference']) ? htmlspecialchars(trim($_POST['preference'])) : '';
    
    // Server-side validations
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
        $errors['date'] = 'Date is required';
    }
    if (empty($time)) {
        $errors['time'] = 'Time is required';
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO enquiries (category, name, email, phone, date, guests, requirements) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $req_summary = "Time: " . sanitize($time) . " | Seating: " . sanitize($seating) . " | Preference: " . sanitize($preference);
            $stmt->execute(['restaurant', $name, $email, $phone, $date, $guests, $req_summary]);

            // Send email alert to admin
            require_once __DIR__ . '/mail-helper.php';
            send_enquiry_alert('restaurant', $name, $email, $phone, $date, $guests, [
                'Time Preferred' => $time,
                'Seating Option' => $seating,
                'Food/Bar Preference' => $preference
            ]);

            $_SESSION['restaurant_success'] = true;
            $_SESSION['res_name'] = $name;
            $_SESSION['res_email'] = $email;
            $_SESSION['res_phone'] = $phone;
            $_SESSION['res_guests'] = $guests;
            $_SESSION['res_date'] = $date;
            $_SESSION['res_time'] = $time;
            
            header("Location: restaurant.php#book-table");
            exit;
        } catch (Exception $e) {
            error_log("Restaurant reservation DB error: " . $e->getMessage());
            $_SESSION['restaurant_success'] = true;
            $_SESSION['res_name'] = $name;
            $_SESSION['res_email'] = $email;
            $_SESSION['res_phone'] = $phone;
            $_SESSION['res_guests'] = $guests;
            $_SESSION['res_date'] = $date;
            $_SESSION['res_time'] = $time;
            
            header("Location: restaurant.php#book-table");
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
    <meta name="description" content="Experience fine rooftop dining at its best. Open from 07:00 AM to 11:30 PM offering exquisite veg, non-veg, and premium club bar facilities in Gwalior.">
    <meta name="keywords" content="Rooftop bar Gwalior, best restaurant Gwalior, fine dining, veg non veg food, hotel destin restaurant">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Rooftop Restaurant & Club Bar - Hotel Destin Gwalior</title>

    <style>
        /* Premium Restaurant Theme CSS Rules */
        :root {
            --res-primary: #9c6047;
            --res-primary-rgb: 156, 96, 71;
            --res-dark: #0e0e0e;
            --res-light: #F8F9FA;
            --res-veg: #3DC262;
            --res-nonveg: #D63E29;
            --res-accent: #c5a880;
        }

        /* Hero Banner style */
        .res-hero {
            position: relative;
            background: linear-gradient(rgba(14, 14, 14, 0.5), rgba(14, 14, 14, 0.75)), url('<?= htmlspecialchars($restaurant_hero_bg) ?>') no-repeat center center;
            background-size: cover;
            height: 380px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-align: center;
        }

        @media (min-width: 768px) {
            .res-hero {
                height: 520px;
            }
        }

        .res-hero-content {
            max-width: 800px;
            padding: 20px;
        }

        .res-hero-badge {
            background: rgba(var(--res-primary-rgb), 0.18);
            border: 1px solid var(--res-primary);
            color: #ffffff;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-block;
            margin-bottom: 20px;
            backdrop-filter: blur(8px);
        }

        .res-hero-title {
            color: #ffffff;
            font-size: 36px;
            font-weight: 500;
            letter-spacing: -1px;
            margin-bottom: 12px;
            line-height: 1.2;
            font-family: 'Merienda One', cursive, Georgia, serif;
        }

        @media (min-width: 768px) {
            .res-hero-title {
                font-size: 44px;
            }
        }

        .res-hero-subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.95);
            font-weight: 400;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        /* Facility Cards */
        .facility-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            height: 100%;
        }

        .facility-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: rgba(var(--res-primary-rgb), 0.2);
        }

        .facility-img-wrapper {
            position: relative;
            height: 220px;
            overflow: hidden;
            background-color: #f1f2f6;
        }

        .facility-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .facility-card:hover .facility-img-wrapper img {
            transform: scale(1.08);
        }

        .facility-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--res-primary);
            color: #ffffff;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 50px;
            z-index: 10;
        }

        .facility-body {
            padding: 24px;
            position: relative;
        }

        .facility-icon-badge {
            position: absolute;
            top: -24px;
            right: 24px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            color: var(--res-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .facility-card:hover .facility-icon-badge {
            background: var(--res-primary);
            color: #ffffff;
            transform: translateY(-4px);
        }

        .facility-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--res-dark);
        }

        .facility-text {
            font-size: 14px;
            color: var(--bs-neutral-500);
            line-height: 1.5;
            margin-bottom: 0;
        }


        /* Reservation Section & Cards */
        .res-booking-section {
            padding: 80px 0;
            background: #faf9f6;
        }

        .booking-card-container {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        .booking-info-panel {
            background: var(--res-dark);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .booking-form-panel {
            background: #ffffff;
        }

        .booking-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--res-dark);
            font-family: 'Merienda One', cursive, Georgia, serif;
        }

        .booking-subtitle {
            font-size: 14.5px;
            color: var(--bs-neutral-500);
        }

        .form-group-custom {
            margin-bottom: 20px;
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
            color: var(--res-primary);
        }

        .form-control-custom {
            width: 100%;
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            color: var(--res-dark);
            background: #fafafa;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .form-control-custom:focus {
            outline: none;
            border-color: var(--res-primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(var(--res-primary-rgb), 0.1);
        }

        .form-error {
            color: var(--res-nonveg);
            font-size: 12px;
            margin-top: 4px;
            font-weight: 500;
        }

        .btn-reserve-table {
            background: var(--res-dark);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 14px 25px;
            font-size: 14.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            margin-top: 15px;
            cursor: pointer;
        }

        .btn-reserve-table:hover {
            background: var(--res-primary);
            box-shadow: 0 8px 20px rgba(var(--res-primary-rgb), 0.25);
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

        @media (min-width: 992px) {
            .booking-info-panel {
                border-radius: 20px 0 0 20px;
            }
            .booking-form-panel {
                border-radius: 0 20px 20px 0;
            }
        }

        @media (max-width: 991px) {
            .booking-info-panel {
                border-radius: 20px 20px 0 0;
            }
            .booking-form-panel {
                border-radius: 0 0 20px 20px;
            }
        }

        /* Timing / Location Grid Box */
        .info-strip-box {
            background: var(--res-dark);
            border-radius: 12px;
            padding: 24px 30px;
            color: #ffffff;
            margin-top: -60px;
            position: relative;
            z-index: 10;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .info-strip-item {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        @media (max-width: 767px) {
            .info-strip-item {
                margin-bottom: 20px;
            }
            .info-strip-item:last-child {
                margin-bottom: 0;
            }
        }

        .info-strip-icon {
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--res-accent);
            font-size: 20px;
            flex-shrink: 0;
        }

        .info-strip-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--bs-neutral-400);
            margin-bottom: 2px;
        }

        .info-strip-val {
            font-size: 15px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0;
        }

        /* Gallery Section styles */
        .gallery-item-wrap:hover img {
            transform: scale(1.08);
        }
        .gallery-item-wrap:hover .gallery-overlay {
            opacity: 1 !important;
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>
    
    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">
        
        <!-- Hero Section -->
        <section class="res-hero">
            <div class="res-hero-content wow fadeInUp">
                <span class="res-hero-badge">Open Daily: <?= $restaurant_hours ?></span>
                <h1 class="res-hero-title"><?= $restaurant_name ?></h1>
                <p class="res-hero-subtitle"><?= $restaurant_tagline ?>. <?= $food_types_text ?>. <?= $room_service_text ?>.</p>
                <a href="#book-table" class="btn btn-default res-hero-btn">Reserve A Table</a>
            </div>
        </section>

        <!-- Details Strip -->
        <div class="container">
            <div class="info-strip-box wow fadeInUp" data-wow-delay="0.1s">
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-strip-item">
                            <div class="info-strip-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            </div>
                            <div>
                                <p class="info-strip-title">Operating Hours</p>
                                <p class="info-strip-val"><?= $restaurant_hours ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-strip-item">
                            <div class="info-strip-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a8 8 0 00-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 00-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div>
                                <p class="info-strip-title">Our Location</p>
                                <p class="info-strip-val">Rooftop Level, Hotel Destin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-strip-item">
                            <div class="info-strip-icon">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="info-strip-title">Phone & Support</p>
                                <p class="info-strip-val">+91 92035 09944</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facilities Section -->
        <section class="section-box pt-80 pb-50">
            <div class="container">
                <div class="text-center mb-50">
                    <h2 class="heading-2 color-neutral-1000 mb-15 wow fadeInUp"><?= htmlspecialchars($restaurant_facilities_title) ?></h2>
                    <p class="text-lg neutral-500 max-width-600 mx-auto wow fadeInUp" data-wow-delay="0.1s"><?= htmlspecialchars($restaurant_facilities_desc) ?></p>
                </div>
                <div class="row g-4">
                    <?php foreach ($amenities as $idx => $item): ?>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="facility-card wow fadeInUp" data-wow-delay="<?= $idx * 0.15 ?>s">
                            <div class="facility-img-wrapper">
                                <span class="facility-badge"><?= $item['badge'] ?></span>
                                <img src="<?= $item['image'] ?>" alt="<?= $item['title'] ?>">
                            </div>
                            <div class="facility-body">
                                <div class="facility-icon-badge">
                                    <?= $item['icon'] ?>
                                </div>
                                <h4 class="facility-title"><?= $item['title'] ?></h4>
                                <p class="facility-text"><?= $item['description'] ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>


        <!-- Gallery Section -->
        <section class="section-box pt-80">
            <div class="container">
                <div class="text-center mb-50">
                    <h2 class="heading-2 color-neutral-1000 mb-15 wow fadeInUp"><?= htmlspecialchars($restaurant_ambience_title) ?></h2>
                    <p class="text-lg neutral-500 max-width-600 mx-auto wow fadeInUp" data-wow-delay="0.1s"><?= htmlspecialchars($restaurant_ambience_desc) ?></p>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 col-sm-12 wow fadeInUp">
                        <div class="gallery-item-wrap" style="position: relative; height: 350px; overflow: hidden; border-radius: 12px;">
                            <img src="<?= htmlspecialchars($ambience_1_image) ?>" alt="<?= htmlspecialchars($ambience_1_title) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                            <div class="gallery-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%); display: flex; align-items: flex-end; padding: 24px; opacity: 0; transition: opacity 0.3s ease;">
                                <div>
                                    <h5 style="color: #fff; margin-bottom: 4px;"><?= htmlspecialchars($ambience_1_title) ?></h5>
                                    <p style="color: var(--bs-neutral-300); font-size: 13px; margin: 0;"><?= htmlspecialchars($ambience_1_desc) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="row g-3">
                            <div class="col-6 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="gallery-item-wrap" style="position: relative; height: 168px; overflow: hidden; border-radius: 12px;">
                                    <img src="<?= htmlspecialchars($ambience_2_image) ?>" alt="<?= htmlspecialchars($ambience_2_title) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <div class="gallery-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 70%); display: flex; align-items: flex-end; padding: 15px; opacity: 0; transition: opacity 0.3s ease;">
                                        <div>
                                            <h6 style="color: #fff; margin-bottom: 2px; font-size: 14px;"><?= htmlspecialchars($ambience_2_title) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 wow fadeInUp" data-wow-delay="0.2s">
                                <div class="gallery-item-wrap" style="position: relative; height: 168px; overflow: hidden; border-radius: 12px;">
                                    <img src="<?= htmlspecialchars($ambience_3_image) ?>" alt="<?= htmlspecialchars($ambience_3_title) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <div class="gallery-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 70%); display: flex; align-items: flex-end; padding: 15px; opacity: 0; transition: opacity 0.3s ease;">
                                        <div>
                                            <h6 style="color: #fff; margin-bottom: 2px; font-size: 14px;"><?= htmlspecialchars($ambience_3_title) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="gallery-item-wrap" style="position: relative; height: 166px; overflow: hidden; border-radius: 12px;">
                                    <img src="<?= htmlspecialchars($ambience_4_image) ?>" alt="<?= htmlspecialchars($ambience_4_title) ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
                                    <div class="gallery-overlay" style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 70%); display: flex; align-items: flex-end; padding: 15px; opacity: 0; transition: opacity 0.3s ease;">
                                        <div>
                                            <h6 style="color: #fff; margin-bottom: 2px; font-size: 14px;"><?= htmlspecialchars($ambience_4_title) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Section -->
        <section id="book-table" class="res-booking-section">
            <div class="container">
                <div class="booking-card-container wow fadeInUp">
                    <div class="row g-0">
                        <!-- Left Info Column -->
                        <div class="col-lg-5 col-12 booking-info-panel p-4 p-md-5">
                            <div>
                                <span class="res-hero-badge" style="font-size: 11px; margin-bottom: 15px; border-color: rgba(255,255,255,0.35);">Hotel Destin Gwalior</span>
                                <h3 class="booking-title" style="color: #ffffff; margin-bottom: 20px; font-size: 26px;">Table Reservation</h3>
                                <p style="font-size: 14.5px; color: var(--bs-neutral-400); line-height: 1.6; margin-bottom: 30px;">
                                    Secure your premium dining slot at Gwalior's highest rated rooftop destination. Enjoy our rooftop club bar vibes or indoor fine dining.
                                </p>
                                
                                <ul style="list-style: none; padding: 0; margin: 0 0 30px 0;">
                                    <li class="d-flex align-items-start mb-20">
                                        <div class="info-strip-icon" style="background: rgba(255,255,255,0.06); width: 36px; height: 36px; font-size: 16px; margin-right: 12px; border-radius: 6px; flex-shrink: 0;">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                        </div>
                                        <div>
                                            <h6 style="color: #fff; font-size: 13px; margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">Operating Hours</h6>
                                            <p style="color: var(--bs-neutral-400); font-size: 14px; margin: 0;"><?= $restaurant_hours ?></p>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-start mb-20">
                                        <div class="info-strip-icon" style="background: rgba(255,255,255,0.06); width: 36px; height: 36px; font-size: 16px; margin-right: 12px; border-radius: 6px; flex-shrink: 0;">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2a8 8 0 00-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 00-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                        </div>
                                        <div>
                                            <h6 style="color: #fff; font-size: 13px; margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">Location</h6>
                                            <p style="color: var(--bs-neutral-400); font-size: 14px; margin: 0;">Rooftop Level, Sachin Tendulkar Road, Gwalior</p>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-start">
                                        <div class="info-strip-icon" style="background: rgba(255,255,255,0.06); width: 36px; height: 36px; font-size: 16px; margin-right: 12px; border-radius: 6px; flex-shrink: 0;">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                                        </div>
                                        <div>
                                            <h6 style="color: #fff; font-size: 13px; margin: 0 0 2px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700;">Features Available</h6>
                                            <p style="color: var(--bs-neutral-400); font-size: 14px; margin: 0;">Veg & Non-Veg, Rooftop Club Bar, Room Service</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <div style="background: rgba(var(--res-primary-rgb), 0.1); border: 1px solid rgba(var(--res-primary-rgb), 0.25); border-radius: 12px; padding: 18px; margin-top: 20px;">
                                <h6 style="color: var(--res-accent); font-size: 13px; font-weight: 700; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Direct Inquiries</h6>
                                <p style="color: #ffffff; font-size: 15px; font-weight: 700; margin: 0 0 4px 0;">+91 92035 09944</p>
                                <p style="color: var(--bs-neutral-400); font-size: 12.5px; margin: 0;">For groups larger than 10, please contact us directly.</p>
                            </div>
                        </div>

                        <!-- Right Form Column -->
                        <div class="col-lg-7 col-12 booking-form-panel p-4 p-md-5">
                            <?php if ($message_sent): ?>
                                <div class="success-card">
                                    <div class="success-circle">
                                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <h3 class="heading-3 mb-10">Reservation Request Received</h3>
                                    <p class="text-md neutral-500 mb-20">Thank you, <strong><?= $name ?></strong>. We have received your booking request for <strong><?= $guests ?> guests</strong> on <strong><?= $date ?></strong> at <strong><?= $time ?></strong>.</p>
                                    <p class="text-sm neutral-400">Our restaurant staff will contact you shortly via email (<?= $email ?>) or phone (<?= $phone ?>) to confirm your seating availability.</p>
                                    <a href="restaurant.php" class="btn btn-default mt-20" style="width: auto;">Book Another Table</a>
                                </div>
                            <?php else: ?>
                                <div class="booking-heading" style="text-align: left;">
                                    <h3 class="booking-title" style="font-size: 26px; margin-bottom: 5px;">Book A Table</h3>
                                    <p class="booking-subtitle" style="margin-bottom: 0;">Select your preferences to request restaurant seating.</p>
                                </div>
                                <form action="restaurant.php#book-table" method="POST" id="reservationForm" style="margin-top: 25px;">
                                    <div class="row">
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_name">Your Name *</label>
                                            <input type="text" class="form-control-custom" id="res_name" name="name" value="<?= isset($name) ? $name : '' ?>" placeholder="e.g. Alice Roses" required>
                                            <?php if (isset($errors['name'])): ?>
                                                <div class="form-error"><?= $errors['name'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_email">Email Address *</label>
                                            <input type="email" class="form-control-custom" id="res_email" name="email" value="<?= isset($email) ? $email : '' ?>" placeholder="e.g. alice@example.com" required>
                                            <?php if (isset($errors['email'])): ?>
                                                <div class="form-error"><?= $errors['email'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_phone">Phone Number *</label>
                                            <input type="tel" class="form-control-custom" id="res_phone" name="phone" value="<?= isset($phone) ? $phone : '' ?>" placeholder="e.g. +91 92035 09944" required>
                                            <?php if (isset($errors['phone'])): ?>
                                                <div class="form-error"><?= $errors['phone'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_guests">Number of Guests *</label>
                                            <select class="form-control-custom" id="res_guests" name="guests">
                                                <option value="1">1 Guest</option>
                                                <option value="2" selected>2 Guests</option>
                                                <option value="3">3 Guests</option>
                                                <option value="4">4 Guests</option>
                                                <option value="5">5 Guests</option>
                                                <option value="6">6 Guests</option>
                                                <option value="8">8 Guests (Group)</option>
                                                <option value="10">10+ Guests (Event)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_date">Select Date *</label>
                                            <input type="date" class="form-control-custom" id="res_date" name="date" value="<?= isset($date) ? $date : '' ?>" min="<?= date('Y-m-d') ?>" required>
                                            <?php if (isset($errors['date'])): ?>
                                                <div class="form-error"><?= $errors['date'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_time">Select Time *</label>
                                            <select class="form-control-custom" id="res_time" name="time" required>
                                                <option value="">-- Choose Seating Time --</option>
                                                <optgroup label="Breakfast (07:00 AM - 11:00 AM)">
                                                    <option value="07:00 AM">07:00 AM</option>
                                                    <option value="08:00 AM">08:00 AM</option>
                                                    <option value="09:00 AM">09:00 AM</option>
                                                    <option value="10:00 AM">10:00 AM</option>
                                                </optgroup>
                                                <optgroup label="Lunch (12:00 PM - 04:00 PM)">
                                                    <option value="12:00 PM">12:00 PM</option>
                                                    <option value="01:00 PM">01:00 PM</option>
                                                    <option value="02:00 PM">02:00 PM</option>
                                                    <option value="03:00 PM">03:00 PM</option>
                                                </optgroup>
                                                <optgroup label="Dinner & Rooftop Lounge (06:00 PM - 11:30 PM)">
                                                    <option value="06:00 PM">06:00 PM</option>
                                                    <option value="07:00 PM">07:00 PM</option>
                                                    <option value="08:00 PM">08:00 PM</option>
                                                    <option value="09:00 PM">09:00 PM</option>
                                                    <option value="10:00 PM">10:00 PM</option>
                                                    <option value="10:30 PM">10:30 PM</option>
                                                    <option value="11:00 PM">11:00 PM</option>
                                                </optgroup>
                                            </select>
                                            <?php if (isset($errors['time'])): ?>
                                                <div class="form-error"><?= $errors['time'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_seating">Seating Preference</label>
                                            <select class="form-control-custom" id="res_seating" name="seating">
                                                <option value="Rooftop Lounge">Rooftop Lounge & Club Bar</option>
                                                <option value="Indoor Main Dining" selected>Indoor Fine Dining Room</option>
                                                <option value="Private Dining Area">Private Suite Dining</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom" for="res_preference">Dietary Preference</label>
                                            <select class="form-control-custom" id="res_preference" name="preference">
                                                <option value="Veg Only">Vegetarian Only</option>
                                                <option value="Non-Veg Only">Non-Vegetarian Only</option>
                                                <option value="No Preference" selected>No Dietary Restrictions (Both)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn-reserve-table">Request Seating</button>
                                </form>
                            <?php endif; ?>
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
