<?php
require_once __DIR__ . '/db.php';

// Parse query parameters
$room_slug = isset($_GET['room']) ? trim($_GET['room']) : '';
$checkin = isset($_GET['checkin']) ? trim($_GET['checkin']) : '';
$checkout = isset($_GET['checkout']) ? trim($_GET['checkout']) : '';
$adults = isset($_GET['adults']) ? intval($_GET['adults']) : 2;
$children = isset($_GET['children']) ? intval($_GET['children']) : 0;

$room = null;
try {
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE slug = ? OR id = ? AND status = 'active'");
    $stmt->execute([$room_slug, $room_slug]);
    $room = $stmt->fetch();
} catch (Exception $e) {
    error_log("Failed to fetch room detail: " . $e->getMessage());
}

if (!$room) {
    // Attempt fallback query to see if slug matches standard/executive/premium
    try {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE slug LIKE ? AND status = 'active' LIMIT 1");
        $stmt->execute(['%' . $room_slug . '%']);
        $room = $stmt->fetch();
    } catch (Exception $e) {}
}

if (!$room) {
    // If no room is found, use a default fallback to prevent 404
    $room = [
        'id' => 1,
        'slug' => 'standard-room',
        'title' => 'Standard Room - Hotel Destin',
        'type' => 'Standard',
        'price' => 2000.00,
        'struck_price' => 4000.00,
        'discount' => '50% off',
        'description' => 'A beautifully designed cozy standard room featuring premium air conditioning, high speed Wi-Fi access, clean premium towels, custom tea kettle set, and comfortable bedding.',
        'image_path' => 'assets/imgs/page/room/banner-room.png'
    ];
}

// Fetch facilities
$facilities = [];
try {
    $f_stmt = $pdo->prepare("SELECT facility_name FROM room_facilities WHERE room_id = ?");
    $f_stmt->execute([$room['id']]);
    $facilities = $f_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $facilities = ['Free Wi-Fi', 'Mineral Water', 'AC', 'Tea/Coffee Maker', 'Premium Bathroom Amenities'];
}

// Fetch other images for gallery slider
$images = [];
try {
    $img_stmt = $pdo->prepare("SELECT image_path FROM room_images WHERE room_id = ?");
    $img_stmt->execute([$room['id']]);
    $images = $img_stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

if (empty($images)) {
    $images = [$room['image_path']];
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="description" content="<?= htmlspecialchars($room['meta_description'] ?: $room['description']) ?>">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title><?= htmlspecialchars($room['title']) ?> - Hotel Destin Gwalior</title>
    
    <style>
        :root {
            --primary-gold: #9c6047;
            --primary-gold-hover: #834f37;
            --primary-gold-light: #fdfaf7;
            --charcoal: #0f172a;
            --light-bg: #fafaf9;
            --border-color: #cbd5e1;
        }

        body {
            background-color: #faf9f6;
            color: #334155;
        }

        /* Hero Banner Info */
        .room-detail-hero-box {
            background: #ffffff;
            border-bottom: 1px solid #f1f1ee;
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.01);
        }

        .back-link {
            text-decoration: none;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .back-link:hover {
            color: var(--primary-gold);
            transform: translateX(-3px);
        }

        /* Gallery Layout */
        .gallery-main {
            border-radius: 16px;
            overflow: hidden;
            background: #eaeae7;
            margin-bottom: 12px;
            height: 380px;
            border: 1px solid #e5e5e0;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .gallery-main img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .gallery-main:hover img {
            transform: scale(1.02);
        }

        .gallery-thumbs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 6px;
        }

        .gallery-thumbs::-webkit-scrollbar {
            height: 3px;
        }
        
        .gallery-thumbs::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .gallery-thumb {
            width: 75px;
            height: 55px;
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            border: 2.5px solid transparent;
            opacity: 0.65;
            transition: all 0.25s ease;
            background: #eaeae7;
            flex-shrink: 0;
        }

        .gallery-thumb.active, .gallery-thumb:hover {
            border-color: var(--primary-gold);
            opacity: 1;
            transform: translateY(-1px);
        }

        .gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Cards and Details */
        .detail-card {
            background: #ffffff;
            border: 1px solid #f0f0ed;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.015);
            margin-bottom: 16px;
        }

        .detail-card h3 {
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--charcoal);
            border-bottom: 1px solid #f5f5f3;
            padding-bottom: 8px;
            letter-spacing: -0.3px;
            position: relative;
        }

        .detail-card h3::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 30px;
            height: 2px;
            background-color: var(--primary-gold);
            border-radius: 1px;
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #f1f1f0;
        }

        .spec-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fafaf9;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #f0f0ed;
            transition: all 0.2s ease;
        }

        .spec-item:hover {
            transform: translateY(-2px);
            border-color: var(--primary-gold);
            background: #ffffff;
        }

        .spec-icon {
            font-size: 18px;
            color: var(--primary-gold);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .spec-info {
            display: flex;
            flex-direction: column;
        }

        .spec-label {
            font-size: 9.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1px;
        }

        .spec-value {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--charcoal);
        }

        /* Amenities Grid */
        .amenity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .amenity-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            padding: 8px 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .amenity-item:hover {
            border-color: var(--primary-gold);
            background: #fafaf9;
        }

        .amenity-check {
            color: var(--primary-gold);
            font-weight: 800;
            font-size: 12px;
            width: 18px;
            height: 18px;
            background: rgba(156, 96, 71, 0.08);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* Config Stay and Booking Form Inputs */
        .form-control-custom {
            height: 40px !important;
            border-radius: 8px !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            padding: 6px 12px !important;
            font-size: 13px !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
            color: var(--charcoal) !important;
        }
        .form-control-custom:focus {
            border-color: var(--primary-gold) !important;
            box-shadow: 0 0 0 3px rgba(156, 96, 71, 0.1) !important;
            outline: none !important;
        }
        .form-label-custom {
            font-size: 10.5px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #64748b !important;
            font-weight: 700 !important;
            margin-bottom: 4px !important;
            display: block !important;
        }

        /* Config Stay and Booking Form */
        .tariff-option-card {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.25s ease;
            position: relative;
            background: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tariff-option-card:hover {
            border-color: var(--primary-gold);
            transform: translateY(-1px);
        }

        .tariff-option-card.selected {
            border-color: var(--primary-gold);
            background: var(--primary-gold-light);
            box-shadow: 0 4px 12px rgba(156, 96, 71, 0.04);
        }

        .tariff-content {
            padding-right: 15px;
            flex-grow: 1;
        }

        .tariff-title {
            font-weight: 800;
            font-size: 14px;
            color: var(--charcoal);
            margin-bottom: 2px;
        }

        .tariff-desc {
            font-size: 11.5px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 500;
            line-height: 1.4;
        }

        .tariff-price {
            font-size: 16px;
            font-weight: 800;
            color: #15803d;
        }

        .tariff-radio {
            position: relative;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .tariff-option-card.selected .tariff-radio {
            border-color: var(--primary-gold);
            background: var(--primary-gold);
        }

        .tariff-option-card.selected .tariff-radio::after {
            content: '';
            width: 6px;
            height: 6px;
            background: #ffffff;
            border-radius: 50%;
            display: block;
        }

        .tariff-option-card input[type="radio"] {
            display: none;
        }

        /* Sticky Form Bar */
        .sticky-form-sidebar {
            position: sticky;
            top: 100px;
        }

        .btn-book-online {
            background: var(--charcoal);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            padding: 12px 24px;
            transition: all 0.2s ease;
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-book-online:hover {
            background: var(--primary-gold);
            color: #ffffff;
            transform: translateY(-1px);
        }

        @media (max-width: 991px) {
            .gallery-main {
                height: 300px;
            }
            .sticky-form-sidebar {
                position: relative;
                top: 0;
                margin-top: 20px;
            }
            .specs-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 767px) {
            .gallery-main {
                height: 220px;
            }
            .specs-grid {
                grid-template-columns: 1fr;
                gap: 8px;
            }
            .detail-card {
                padding: 16px;
            }
            .room-detail-hero-box h1 {
                font-size: 24px !important;
            }
            .tariff-option-card {
                padding: 12px;
            }
            .tariff-title {
                font-size: 13.5px;
            }
            .tariff-desc {
                font-size: 11px;
            }
            .tariff-price {
                font-size: 15px;
            }
            .amenity-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>
    
    <?php include("include/header.php"); ?>

    <main class="main">
        <!-- Hero Title Banner Section -->
        <section class="room-detail-hero-box mb-20">
            <div class="container">
                <a href="rooms.php" class="back-link mb-10">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="margin-right:6px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to All Rooms
                </a>
                
                <div class="row align-items-end mt-10">
                    <div class="col-lg-8 col-12">
                        <span class="badge" style="background-color: rgba(156, 96, 71, 0.08); color: var(--primary-gold); font-weight: 700; padding: 6px 12px; font-size:11px; border-radius: 4px; letter-spacing: 0.6px; text-transform: uppercase; margin-bottom: 8px; display: inline-block;">
                            <?= htmlspecialchars($room['type']) ?> Accommodations
                        </span>
                        <h1 class="font-heading mb-10" style="font-size: 30px; font-weight: 800; color: var(--charcoal); letter-spacing: -0.5px; line-height:1.2;">
                            <?= htmlspecialchars($room['title']) ?>
                        </h1>
                        <p class="neutral-500 mb-0" style="font-size: 14px; font-weight: 600; color: #64748b;">
                            📍 Sachin Tendulkar Road, Kailash Nagar, Gwalior, MP, India
                        </p>
                    </div>
                    <div class="col-lg-4 col-12 text-lg-end mt-20 mt-lg-0">
                        <div class="rating-info-badge d-inline-flex align-items-center gap-2" style="background: #ffffff; border: 1px solid #e2e8f0; padding: 8px 16px; border-radius: 30px;">
                            <span style="color:#d97706; font-size:15px;">★</span>
                            <span style="font-weight: 700; font-size:13px; color: var(--charcoal);">G 4.8 / 5 Rating</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Room Detail and Booking Interface -->
        <section class="section-box pb-40">
            <div class="container">
                <div class="row g-4">
                    <!-- Left Side: Gallery & Specs -->
                    <div class="col-lg-7 col-12">
                        <!-- Image Slider Container -->
                        <div class="gallery-main" style="position: relative;">
                            <img id="mainGalleryImg" src="<?= htmlspecialchars($images[0]) ?>" alt="Featured Room Image">
                            <div id="galleryCountOverlay" style="position: absolute; bottom: 15px; right: 15px; background: rgba(15, 23, 42, 0.75); color: #fff; font-size: 11px; padding: 4px 10px; border-radius: 20px; font-weight: 700; letter-spacing: 0.5px;">
                                <span id="currentImgNum">1</span> / <?= count($images) ?> Images
                            </div>
                        </div>
                        <?php if (count($images) > 1): ?>
                            <div class="gallery-thumbs mb-15">
                                <?php foreach ($images as $index => $img_path): ?>
                                    <div class="gallery-thumb <?= $index === 0 ? 'active' : '' ?>" onclick="switchGalleryImg(this, '<?= htmlspecialchars($img_path) ?>')">
                                        <img src="<?= htmlspecialchars($img_path) ?>" alt="Thumbnail">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Overview Card -->
                        <div class="detail-card">
                            <h3>Room Overview</h3>
                            <div style="font-size: 15px; line-height: 1.75; color: #4b5563; font-weight:500; margin-bottom: 0;">
                                <?= $room['description'] ?>
                            </div>
                            
                            <!-- Key Technical Specifications -->
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <div class="spec-icon">👥</div>
                                    <div class="spec-info">
                                        <span class="spec-label">Capacity Limit</span>
                                        <span class="spec-value"><?= htmlspecialchars($room['capacity_adults'] ?? 2) ?> Adults, <?= htmlspecialchars($room['capacity_children'] ?? 1) ?> Child</span>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">🛏️</div>
                                    <div class="spec-info">
                                        <span class="spec-label">Bed Setup</span>
                                        <span class="spec-value">Comfort King Bed</span>
                                    </div>
                                </div>
                                <div class="spec-item">
                                    <div class="spec-icon">⚡</div>
                                    <div class="spec-info">
                                        <span class="spec-label">Reservations</span>
                                        <span class="spec-value">Instant Payment</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amenities List -->
                        <div class="detail-card">
                            <h3>Amenities Included</h3>
                            <div class="amenity-grid">
                                <?php foreach ($facilities as $fac): ?>
                                    <div class="amenity-item">
                                        <span class="amenity-check">✓</span>
                                        <span><?= htmlspecialchars($fac) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Config Stay Form -->
                    <div class="col-lg-5 col-12">
                        <div class="detail-card sticky-form-sidebar">
                            <h3 style="border-bottom:none; margin-bottom:15px; padding-bottom:0;">Configure Stay & Meal Plan</h3>
                            
                            <form id="stayConfigForm" method="GET" action="checkout.php">
                                <input type="hidden" name="room" value="<?= htmlspecialchars($room['slug']) ?>">
                                
                                <div class="row g-2 mb-10">
                                    <div class="col-6">
                                        <label class="form-label-custom" style="font-size:11.5px; font-weight:700;">Check-In Date *</label>
                                        <input id="detailCheckIn" class="form-control-custom" type="date" name="checkin" value="<?= htmlspecialchars($checkin) ?>" min="<?= date('Y-m-d') ?>" required style="height:38px; font-size:12.5px;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom" style="font-size:11.5px; font-weight:700;">Check-Out Date *</label>
                                        <input id="detailCheckOut" class="form-control-custom" type="date" name="checkout" value="<?= htmlspecialchars($checkout) ?>" min="<?= date('Y-m-d') ?>" required style="height:38px; font-size:12.5px;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom" style="font-size:11.5px; font-weight:700;">Adult Guests *</label>
                                        <input id="detailAdults" class="form-control-custom" type="number" name="adults" value="<?= htmlspecialchars($adults) ?>" min="1" max="5" required style="height:38px; font-size:12.5px;">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label-custom" style="font-size:11.5px; font-weight:700;">Child Guests</label>
                                        <input id="detailChildren" class="form-control-custom" type="number" name="children" value="<?= htmlspecialchars($children) ?>" min="0" max="4" required style="height:38px; font-size:12.5px;">
                                    </div>
                                </div>

                                <label class="form-label-custom mb-10" style="display:block; font-weight:700;">Select Tariff Meal Plan *</label>
                                
                                <!-- EP Card -->
                                <div class="tariff-option-card selected" onclick="selectTariffPlan(this, 'EP')">
                                    <div class="tariff-content">
                                        <div class="tariff-title">EP (European Plan)</div>
                                        <div class="tariff-desc">Room only inclusion. Access to hotel Wi-Fi and common properties.</div>
                                        <div class="tariff-price">₹<span id="rateEP">0.00</span> <span style="font-size:11px; font-weight:normal; color:#64748b;">/ night</span></div>
                                    </div>
                                    <div class="tariff-radio"></div>
                                    <input type="radio" name="meal_plan" value="EP" checked>
                                </div>

                                <!-- CP Card -->
                                <div class="tariff-option-card" onclick="selectTariffPlan(this, 'CP')">
                                    <div class="tariff-content">
                                        <div class="tariff-title">CP (Continental Plan)</div>
                                        <div class="tariff-desc">Stay includes standard room and delicious daily Morning Breakfast buffet.</div>
                                        <div class="tariff-price">₹<span id="rateCP">0.00</span> <span style="font-size:11px; font-weight:normal; color:#64748b;">/ night</span></div>
                                    </div>
                                    <div class="tariff-radio"></div>
                                    <input type="radio" name="meal_plan" value="CP">
                                </div>

                                <!-- MAP Card -->
                                <div class="tariff-option-card" onclick="selectTariffPlan(this, 'MAP')">
                                    <div class="tariff-content">
                                        <div class="tariff-title">MAP (Modified American Plan)</div>
                                        <div class="tariff-desc">Stay includes Morning Breakfast and either Lunch or Dinner buffet plan.</div>
                                        <div class="tariff-price">₹<span id="rateMAP">0.00</span> <span style="font-size:11px; font-weight:normal; color:#64748b;">/ night</span></div>
                                    </div>
                                    <div class="tariff-radio"></div>
                                    <input type="radio" name="meal_plan" value="MAP">
                                </div>

                                <!-- Stay Billing Cost Calculations Summary Panel -->
                                <div class="p-15 mt-15 mb-15" style="background:#fafaf9; border-radius:12px; border:1px solid #f0f0ed; font-size:13.5px; font-weight:600; color:#475569;">
                                    <div class="d-flex justify-content-between mb-8">
                                        <span style="color:#64748b;">Nights Count:</span>
                                        <span style="color:var(--charcoal);"><span id="stayNights">1</span> Night(s)</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-8">
                                        <span style="color:#64748b;">Stay Rate Subtotal:</span>
                                        <span style="color:var(--charcoal);">₹<span id="subtotalCost">0.00</span></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-12">
                                        <span style="color:#64748b;">GST Taxes (5%):</span>
                                        <span style="color:var(--charcoal);">₹<span id="taxCost">0.00</span></span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-12" style="border-top:1px dashed #cbd5e1; font-size:16px; font-weight:800; color:#15803d;">
                                        <span>Total Stay Price:</span>
                                        <span>₹<span id="totalCost">0.00</span></span>
                                    </div>
                                </div>

                                <button class="btn-book-online" type="submit">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    Book Room Online
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("include/footer.php"); ?>

    <!-- Scripts -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
    
    <script>
        // Build pricing matrix dynamically from database values
        const pricingMatrix = {
            single: {
                EP: <?= floatval($room['price_single_ep'] ?? 0) ?>,
                CP: <?= floatval($room['price_single_cp'] ?? 0) ?>,
                MAP: <?= floatval($room['price_single_map'] ?? 0) ?>
            },
            double: {
                EP: <?= floatval($room['price_double_ep'] ?? 0) ?>,
                CP: <?= floatval($room['price_double_cp'] ?? 0) ?>,
                MAP: <?= floatval($room['price_double_map'] ?? 0) ?>
            }
        };

        var galleryImages = <?= json_encode($images) ?>;
        var currentGalleryIdx = 0;
        
        function autoSlideGallery() {
            if (galleryImages.length <= 1) return;
            currentGalleryIdx++;
            if (currentGalleryIdx >= galleryImages.length) {
                currentGalleryIdx = 0;
            }
            var thumb = $('.gallery-thumb').eq(currentGalleryIdx);
            if (thumb.length > 0) {
                switchGalleryImgHelper(thumb[0], galleryImages[currentGalleryIdx]);
            }
        }
        
        var autoSlideInterval = setInterval(autoSlideGallery, 3000);

        function switchGalleryImgHelper(element, imgPath) {
            $('.gallery-thumb').removeClass('active');
            $(element).addClass('active');
            $('#mainGalleryImg').attr('src', imgPath);
            var idx = $('.gallery-thumb').index(element);
            $('#currentImgNum').text(idx + 1);
        }

        function switchGalleryImg(element, imgPath) {
            clearInterval(autoSlideInterval);
            switchGalleryImgHelper(element, imgPath);
            currentGalleryIdx = $('.gallery-thumb').index(element);
        }

        function selectTariffPlan(element, planCode) {
            $('.tariff-option-card').removeClass('selected');
            $(element).addClass('selected');
            $(element).find('input[type="radio"]').prop('checked', true);
            recalculateStayTotals();
        }

        function recalculateStayTotals() {
            var checkIn = $('#detailCheckIn').val();
            var checkOut = $('#detailCheckOut').val();
            var nights = 1;

            if (checkIn && checkOut) {
                var d1 = new Date(checkIn);
                var d2 = new Date(checkOut);
                var diffTime = Math.abs(d2 - d1);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                if (diffDays > 0) {
                    nights = diffDays;
                }
            }
            $('#stayNights').text(nights);

            var adults = parseInt($('#detailAdults').val()) || 2;
            var occupancy = (adults >= 2) ? 'double' : 'single';

            // Fetch matrix rates dynamically calculated
            var epRate = pricingMatrix[occupancy]['EP'];
            var cpRate = pricingMatrix[occupancy]['CP'];
            var mapRate = pricingMatrix[occupancy]['MAP'];

            $('#rateEP').text(epRate.toFixed(2));
            $('#rateCP').text(cpRate.toFixed(2));
            $('#rateMAP').text(mapRate.toFixed(2));

            // Calculate totals based on selected plan radio button
            var selectedPlan = $('input[name="meal_plan"]:checked').val() || 'EP';
            var unitRate = pricingMatrix[occupancy][selectedPlan];

            var subtotal = unitRate * nights;
            var tax = subtotal * 0.05; // 5% GST
            var total = subtotal + tax;

            $('#subtotalCost').text(subtotal.toFixed(2));
            $('#taxCost').text(tax.toFixed(2));
            $('#totalCost').text(total.toFixed(2));
        }

        $(document).ready(function() {
            // Recalculate cost when dates or guest counts change
            $('#detailCheckIn, #detailCheckOut, #detailAdults, #detailChildren').change(function() {
                recalculateStayTotals();
            });

            // Initial calculation run
            recalculateStayTotals();
        });
    </script>
</body>
</html>
