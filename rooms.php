<?php
// PHP backend to process room booking enquiry form submissions
require_once __DIR__ . '/db.php';

$message_sent = false;
$errors = [];

$search_checkin = isset($_GET['checkin']) ? trim($_GET['checkin']) : '';
$search_checkout = isset($_GET['checkout']) ? trim($_GET['checkout']) : '';
$search_adults = isset($_GET['adults']) ? intval($_GET['adults']) : 2;
$search_children = isset($_GET['children']) ? intval($_GET['children']) : 0;
$has_search_dates = (!empty($search_checkin) && !empty($search_checkout));


// Static Fallback Room Categories (if DB connection has no table records yet)
$static_rooms = [
    [
        'id' => 'standard',
        'name' => 'Standard Room - Hotel Destin',
        'type_badge' => 'STANDARD',
        'status_badge' => 'BEST SELLER',
        'rating' => 'G 4.8 ★',
        'location' => 'Sachin Tendulkar Rd, Gwalior',
        'tags' => [
            ['text' => 'Free Wi-Fi', 'style' => 'orange'],
            ['text' => 'Mineral Water', 'style' => 'orange'],
            ['text' => 'Standard Only', 'style' => 'blue'],
            ['text' => '+3 more', 'style' => 'green']
        ],
        'specs' => [
            ['icon' => 'check', 'label' => 'AC'],
            ['icon' => 'check', 'label' => 'Free Wi-Fi'],
            ['icon' => 'check', 'label' => 'Laundry'],
            ['icon' => 'check', 'label' => 'King Bed'],
            ['icon' => 'check', 'label' => 'Safe Box']
        ],
        'struck_price' => 4000,
        'discount' => '58% off',
        'code' => 'DESTIN',
        'price' => 1690,
        'image' => 'assets/imgs/page/room/banner-room.png'
    ],
    [
        'id' => 'executive',
        'name' => 'Executive Room - Hotel Destin',
        'type_badge' => 'EXECUTIVE',
        'status_badge' => 'POPULAR',
        'rating' => 'G 4.8 ★',
        'location' => 'Sachin Tendulkar Rd, Gwalior',
        'tags' => [
            ['text' => 'Free Wi-Fi', 'style' => 'orange'],
            ['text' => 'Mineral Water', 'style' => 'orange'],
            ['text' => 'Executive Only', 'style' => 'blue'],
            ['text' => '+4 more', 'style' => 'green']
        ],
        'specs' => [
            ['icon' => 'check', 'label' => 'AC'],
            ['icon' => 'check', 'label' => 'Free Wi-Fi'],
            ['icon' => 'check', 'label' => 'Laundry'],
            ['icon' => 'check', 'label' => 'King Bed'],
            ['icon' => 'check', 'label' => 'Safe Box']
        ],
        'struck_price' => 4200,
        'discount' => '58% off',
        'code' => 'DESTIN',
        'price' => 1774,
        'image' => 'assets/imgs/page/room/banner-room2.png'
    ],
    [
        'id' => 'premium',
        'name' => 'Premium Room - Hotel Destin',
        'type_badge' => 'PREMIUM',
        'status_badge' => 'LUXURY CHOICE',
        'rating' => 'G 4.9 ★',
        'location' => 'Sachin Tendulkar Rd, Gwalior',
        'tags' => [
            ['text' => 'Free Wi-Fi', 'style' => 'orange'],
            ['text' => 'Mineral Water', 'style' => 'orange'],
            ['text' => 'Premium Suite', 'style' => 'blue'],
            ['text' => '+5 more', 'style' => 'green']
        ],
        'specs' => [
            ['icon' => 'check', 'label' => 'AC'],
            ['icon' => 'check', 'label' => 'Free Wi-Fi'],
            ['icon' => 'check', 'label' => 'Laundry'],
            ['icon' => 'check', 'label' => 'King Bed'],
            ['icon' => 'check', 'label' => 'Safe Box']
        ],
        'struck_price' => 4400,
        'discount' => '58% off',
        'code' => 'DESTIN',
        'price' => 1858,
        'image' => 'assets/imgs/page/pages/banner.png'
    ]
];

$rooms = [];
try {
    // Attempt database retrieval
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE status = 'active' ORDER BY price ASC");
    $stmt->execute();
    $db_rooms = $stmt->fetchAll();

    if (count($db_rooms) > 0) {
        foreach ($db_rooms as $r) {
            // Count active inventory rooms in this category
            $total_inventory = (int)$r['inventory'];

            $available_count = $total_inventory;
            if ($has_search_dates) {
                $booked_stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = ? AND check_in < ? AND check_out > ? AND booking_status != 'cancelled'");
                $booked_stmt->execute([$r['id'], $search_checkout, $search_checkin]);
                $booked_count = (int)$booked_stmt->fetchColumn();
                $available_count = max(0, $total_inventory - $booked_count);
            }

            // Fetch facilities for this room
            $f_stmt = $pdo->prepare("SELECT facility_name FROM room_facilities WHERE room_id = ?");
            $f_stmt->execute([$r['id']]);
            $facilities = $f_stmt->fetchAll(PDO::FETCH_COLUMN);

            $specs = [];

            // Limit facilities to maximum 5, or pad to 5 if fewer
            $card_facilities = $facilities;
            if (count($card_facilities) > 5) {
                $card_facilities = array_slice($card_facilities, 0, 5);
            } else {
                $standard_defaults = ['AC', 'Free Wi-Fi', 'Laundry', 'King Bed', 'Safe Box'];
                foreach ($standard_defaults as $def) {
                    if (count($card_facilities) >= 5) break;
                    if (!in_array($def, $card_facilities)) {
                        $card_facilities[] = $def;
                    }
                }
            }

            foreach ($card_facilities as $f) {
                $specs[] = ['icon' => 'check', 'label' => $f];
            }

            // Fetch gallery images for this room to build cards slider
            $all_images = [];
            if (!empty($r['image_path'])) {
                $all_images[] = $r['image_path'];
            }
            $g_stmt = $pdo->prepare("SELECT image_path FROM room_images WHERE room_id = ?");
            $g_stmt->execute([$r['id']]);
            $gallery_imgs = $g_stmt->fetchAll(PDO::FETCH_COLUMN);
            $all_images = array_merge($all_images, $gallery_imgs);

            // Limit to a maximum of 3 images
            $all_images = array_slice($all_images, 0, 3);
            if (empty($all_images)) {
                $all_images[] = 'assets/imgs/page/room/banner-room.png';
            }

            $type_badge = strtoupper($r['type']);
            $status_badge = $r['status_badge'] ?: 'POPULAR';
            $rating = $r['rating'] ?: 'G 4.8 ★';

            $tags = [];
            if (count($facilities) > 0) {
                $tags[] = ['text' => $facilities[0], 'style' => 'orange'];
            } else {
                $tags[] = ['text' => 'Free Wi-Fi', 'style' => 'orange'];
            }
            if (count($facilities) > 1) {
                $tags[] = ['text' => $facilities[1], 'style' => 'orange'];
            } else {
                $tags[] = ['text' => 'Mineral Water', 'style' => 'orange'];
            }
            $tags[] = ['text' => $r['type'] . ' Space', 'style' => 'blue'];
            if (count($facilities) > 2) {
                $more_count = count($facilities) - 2;
                $tags[] = ['text' => '+' . $more_count . ' more', 'style' => 'green'];
            }

            $price = (float)$r['price'];
            if ($has_search_dates) {
                $date1 = new DateTime($search_checkin);
                $date2 = new DateTime($search_checkout);
                $nights = $date2->diff($date1)->format("%a");
                $nights = max(1, (int)$nights);

                $total_base_price = 0.00;
                $curr_date_ptr = clone $date1;
                while ($curr_date_ptr < $date2) {
                    $date_str = $curr_date_ptr->format('Y-m-d');
                    $total_base_price += get_resolved_room_price($pdo, $r['id'], $date_str, 'EP', $search_adults, $r);
                    $curr_date_ptr->modify('+1 day');
                }
                $price = round($total_base_price / $nights, 2);
            }

            $rooms[] = [
                'id' => $r['slug'],
                'db_id' => $r['id'],
                'name' => $r['title'],
                'type_badge' => $type_badge,
                'status_badge' => ($has_search_dates && $available_count <= 0) ? 'SOLD OUT' : $status_badge,
                'rating' => $rating,
                'location' => 'Sachin Tendulkar Rd, Gwalior',
                'tags' => $tags,
                'specs' => $specs,
                'struck_price' => $r['struck_price'],
                'discount' => $r['discount'],
                'code' => $r['code'],
                'banner_text' => isset($r['banner_text']) ? $r['banner_text'] : '',
                'price' => $price,
                'image' => $r['image_path'],
                'images' => $all_images,
                'available_count' => $available_count,
                'total_active' => $total_inventory
            ];
        }
    } else {
        $rooms = $static_rooms;
    }
} catch (Exception $e) {
    // If table doesn't exist yet, fall back to static
    error_log("Database room fetch fallback: " . $e->getMessage());
    $rooms = $static_rooms;
}

// Standardize sorting order: Standard, Executive, Premium
usort($rooms, function ($a, $b) {
    $order = ['standard' => 1, 'executive' => 2, 'premium' => 3];
    $typeA = strtolower($a['type_badge'] ?? '');
    $typeB = strtolower($b['type_badge'] ?? '');
    $valA = isset($order[$typeA]) ? $order[$typeA] : 99;
    $valB = isset($order[$typeB]) ? $order[$typeB] : 99;
    return $valA <=> $valB;
});

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <meta name="description" content="Explore Gwalior\'s finest rooms and suites at Hotel Destin Gwalior. Premium amenities, 5% GST taxes, tea kettle trays, and flexible EP/CP/MAP pricing plans.">
    <meta name="keywords" content="hotel destin rooms, standard room, executive room, premium suite Gwalior, rooms tariff, room booking Gwalior">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Rooms & Suites - Hotel Destin Gwalior</title>

    <style>
        /* Custom Premium Rooms Styles */
        :root {
            --rm-primary: #9c6047;
            --rm-primary-rgb: 156, 96, 71;
            --rm-dark: #0e0e0e;
            --rm-accent: #c5a880;
            --rm-border: #e9ecf2;
        }

        .card-images-wrapper {
            display: flex;
            height: 100%;
            transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }

        .card-images-wrapper img {
            flex: 0 0 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-room-destin:hover .image-box img {
            transform: none !important;
        }

        .rooms-hero {
            position: relative;
            background: linear-gradient(rgba(14, 14, 14, 0.55), rgba(14, 14, 14, 0.75)), url('assets/imgs/page/room/banner-room.png') no-repeat center center;
            background-size: cover;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            text-align: center;
        }

        @media (min-width: 768px) {
            .rooms-hero {
                height: 350px;
            }
        }

        .rooms-hero-content {
            max-width: 800px;
            padding: 20px;
        }

        .rooms-hero-title {
            color: #ffffff;
            font-size: 26px;
            font-weight: 500;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }

        @media (min-width: 576px) {
            .rooms-hero-title {
                font-size: 38px;
            }
        }

        @media (min-width: 992px) {
            .rooms-hero-title {
                font-size: 54px;
            }
        }

        .rooms-hero-subtitle {
            font-size: 16px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            line-height: 1.5;
        }

        /* Classy Inclusions / Amenities Bar */
        .inclusions-strip {
            background: #ffffff;
            border-bottom: 1px solid #f2f2f2;
            padding: 24px 0;
            position: relative;
        }

        .inclusion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            color: #334155;
            font-weight: 600;
            padding: 10px 16px;
            background: #fafaf9;
            border: 1px solid #f1f1f0;
            border-radius: 30px;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            justify-content: center;
        }

        .inclusion-item:hover {
            transform: translateY(-2px);
            background: #ffffff;
            border-color: #c5a880;
            box-shadow: 0 8px 20px rgba(197, 168, 128, 0.12);
            color: #9c6047;
        }

        .inclusion-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(156, 96, 71, 0.08);
            color: #9c6047;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .inclusion-item:hover .inclusion-icon {
            background: #9c6047;
            color: #ffffff;
        }

        @media (min-width: 768px) {
            .inclusions-strip [class*="col-"] {
                flex: 0 0 20% !important;
                max-width: 20% !important;
            }
        }

        @media (max-width: 767.98px) {
            .inclusions-strip {
                padding: 12px 0;
                overflow: hidden;
            }

            /* Visual fade-out effect on the right to indicate more content */
            .inclusions-strip::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 40px;
                background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.95));
                pointer-events: none;
                z-index: 5;
            }

            .inclusions-strip .row {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                justify-content: flex-start !important;
                padding: 4px 20px 10px 20px !important;
                /* Added bottom padding for custom scrollbar, side padding for buffer */
                margin-left: -15px !important;
                margin-right: -15px !important;
                gap: 10px;
                scrollbar-width: thin;
                /* Firefox */
                scrollbar-color: #c5a880 rgba(0, 0, 0, 0.05);
                /* Firefox thumb/track */
            }

            .inclusions-strip .row::-webkit-scrollbar {
                display: block !important;
                height: 3px !important;
                /* Elegant thin scrollbar */
            }

            .inclusions-strip .row::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.03) !important;
                border-radius: 10px !important;
            }

            .inclusions-strip .row::-webkit-scrollbar-thumb {
                background: #c5a880 !important;
                /* Theme gold accent */
                border-radius: 10px !important;
            }

            .inclusions-strip [class*="col-"] {
                flex: 0 0 auto !important;
                width: auto !important;
                max-width: none !important;
                padding: 0 !important;
            }

            .inclusion-item {
                padding: 8px 14px;
                font-size: 13px;
                white-space: nowrap;
            }
        }

        /* Removed custom card-room CSS in favor of global card-room-destin CSS */

        .section-title-responsive {
            font-size: 24px !important;
            font-weight: 700;
            color: var(--rm-dark);
            line-height: 1.3 !important;
        }

        @media (min-width: 768px) {
            .section-title-responsive {
                font-size: 32px !important;
            }
        }


        .form-box-centered {
            background-color: #fafafa;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(0, 0, 0, 0.06);
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
        }

        @media (min-width: 768px) {
            .inquiry-form-title {
                font-size: 28px !important;
            }
        }

        /* Hide browser default date picker icons */
        .box-calendar-date {
            position: relative;
            width: 100%;
        }

        .box-calendar-date input[type="date"] {
            position: relative;
            padding-right: 24px;
            -webkit-appearance: none;
            appearance: none;
        }

        .box-calendar-date input[type="date"]::-webkit-calendar-picker-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 5;
        }

        /* ── Mobile date placeholder fix ──────────────────────────────────
           On mobile, appearance:none strips the native "dd-mm-yyyy" hint.
           JS injects <span class="date-ph"> which is shown/hidden via class.
        ────────────────────────────────────────────────────────────────── */
        .date-ph {
            display: none;
            /* hidden on desktop — browser shows native hint */
            position: absolute;
            left: 24px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 12px;
            font-weight: 500;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
            z-index: 2;
        }

        @media (max-width: 767px) {
            .date-ph {
                display: block;
            }

            .box-calendar-date input[type="date"].date-empty {
                color: transparent !important;
            }

            .box-calendar-date input[type="date"].date-empty:focus {
                color: inherit !important;
            }
        }


        /* Mobile-only header strip \u2014 hidden on desktop, shown in mobile @media block */
        .mobile-search-header {
            display: none;
        }

        /* Isolated layout styling for search advance container in rooms.php */
        .rooms-search-wrapper {
            margin-top: 30px;
            margin-bottom: 30px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 15px;
            width: 100%;
            position: relative;
            z-index: 10;
        }

        .rooms-search-wrapper .box-search-advance {
            top: 0 !important;
            margin-bottom: 0 !important;
            padding: 0 !important;
            border: 1px solid rgba(161, 122, 66, 0.2) !important;
            border-radius: 16px !important;
            box-shadow: 0 12px 36px rgba(161, 122, 66, 0.08), 0 4px 12px rgba(0, 0, 0, 0.03) !important;
            overflow: visible !important;
        }

        .rooms-search-wrapper .box-bottom-search {
            display: flex;
            flex-wrap: nowrap !important;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px !important;
            border-radius: 16px;
            border: none !important;
            background: #ffffff !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search {
            width: 27% !important;
            padding: 5px 20px !important;
            border: none !important;
            position: relative;
            border-right: 1px solid rgba(161, 122, 66, 0.12) !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search::before {
            display: none !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search:nth-child(3) {
            width: 29% !important;
            border-right: none !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search label {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #a17a42 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px !important;
            display: block !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search .search-input,
        .rooms-search-wrapper .box-bottom-search .item-search input[type="date"] {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
            background: transparent !important;
            border: none !important;
            outline: none !important;
            padding: 0 !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search .guests-summary-text {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #1e293b !important;
        }

        .rooms-search-wrapper .box-bottom-search .item-search svg {
            stroke: #a17a42 !important;
        }

        .rooms-search-wrapper .box-bottom-search .btn-black-lg {
            background: linear-gradient(135deg, #a17a42 0%, #bd9961 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(161, 122, 66, 0.25) !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            height: 46px !important;
            border: none !important;
            transition: all 0.2s ease !important;
        }

        .rooms-search-wrapper .box-bottom-search .btn-black-lg:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 18px rgba(161, 122, 66, 0.35) !important;
        }

        .rooms-search-wrapper .box-bottom-search .btn-black-lg:active {
            transform: translateY(0) !important;
        }

        /* ===================================================
           MOBILE FILTER SECTION — Perfectly Contained
           Only applies on screens <= 767px
        =================================================== */
        @media (max-width: 767px) {

            /* Box-sizing fix for all filter elements */
            .rooms-search-wrapper *,
            .rooms-search-wrapper *::before,
            .rooms-search-wrapper *::after {
                box-sizing: border-box !important;
            }

            /* Wrapper: full width, no horizontal overflow */
            .rooms-search-wrapper {
                padding: 0 12px !important;
                margin-top: 20px !important;
                margin-bottom: 20px !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow: hidden !important;
                z-index: 100 !important;
            }

            /* Outer card: overflow:hidden clips visual corners.
               The guests dropdown is position:fixed via JS so it won't be clipped.
               The native date picker opens as an OS-level overlay, also not clipped. */
            .rooms-search-wrapper .box-search-advance {
                border-radius: 20px !important;
                border: 1px solid rgba(161, 122, 66, 0.2) !important;
                box-shadow: 0 12px 36px rgba(161, 122, 66, 0.08), 0 4px 12px rgba(0, 0, 0, 0.03) !important;
                background: #ffffff !important;
                overflow: hidden !important;
                width: 100% !important;
                max-width: 100% !important;
                position: relative !important;
            }

            /* Mobile header gradient bar */
            .mobile-search-header {
                display: block !important;
                background: linear-gradient(135deg, #a17a42 0%, #c29d66 100%) !important;
                color: #ffffff !important;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.5px;
                padding: 12px 16px;
                text-align: center;
                width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Grid layout for the search form */
            .rooms-search-wrapper .box-bottom-search {
                display: grid !important;
                grid-template-columns: 1fr 1fr !important;
                grid-template-rows: auto auto auto !important;
                gap: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-width: 0 !important;
                overflow: hidden !important;
                background: #ffffff !important;
                flex-wrap: unset !important;
            }

            /* ALL grid cells: min-width:0 is critical — prevents CSS Grid auto-sizing overflow */
            .rooms-search-wrapper .box-bottom-search .item-search,
            .rooms-search-wrapper .box-bottom-search .search-submit-wrapper {
                min-width: 0 !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
            }

            /* Each date/guest field cell */
            .rooms-search-wrapper .box-bottom-search .item-search {
                width: 100% !important;
                padding: 12px 12px !important;
                border: none !important;
                border-right: none !important;
                border-bottom: 1px solid rgba(161, 122, 66, 0.12) !important;
                background: #ffffff !important;
                position: relative;
            }

            /* Check-in: left column */
            .rooms-search-wrapper .box-bottom-search .item-search:nth-child(1) {
                grid-column: 1 !important;
                grid-row: 1 !important;
                border-right: 1px solid rgba(161, 122, 66, 0.12) !important;
            }

            /* Check-out: right column */
            .rooms-search-wrapper .box-bottom-search .item-search:nth-child(2) {
                grid-column: 2 !important;
                grid-row: 1 !important;
                border-right: none !important;
            }

            /* Guests & Rooms: full width second row */
            .rooms-search-wrapper .box-bottom-search .item-search:nth-child(3) {
                grid-column: 1 / -1 !important;
                grid-row: 2 !important;
                width: 100% !important;
                border-right: none !important;
                border-bottom: 1px solid rgba(161, 122, 66, 0.12) !important;
            }

            /* Hide the ::before pseudo-element on item-search */
            .rooms-search-wrapper .box-bottom-search .item-search::before {
                display: none !important;
            }

            /* Labels — compact and colored */
            .rooms-search-wrapper .box-bottom-search .item-search label {
                font-size: 9px !important;
                font-weight: 800 !important;
                color: #a17a42 !important;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px !important;
                display: block !important;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                width: 100%;
            }

            /* Date input row (icon + input) — must not overflow cell */
            .rooms-search-wrapper .box-bottom-search .item-search .box-calendar-date {
                display: flex !important;
                align-items: center !important;
                gap: 6px !important;
                width: 100% !important;
                min-width: 0 !important;
                overflow: hidden !important;
            }

            /* SVG calendar icon — fixed size, never shrinks */
            .rooms-search-wrapper .box-bottom-search .item-search .box-calendar-date svg {
                flex-shrink: 0 !important;
                width: 14px !important;
                height: 14px !important;
                stroke: #a17a42 !important;
            }

            /* Date input — fills remaining space, never overflows */
            .rooms-search-wrapper .box-bottom-search .item-search .search-input,
            .rooms-search-wrapper .box-bottom-search .item-search input[type="date"] {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                font-size: 12px !important;
                font-weight: 700 !important;
                color: #1e293b !important;
                background: transparent !important;
                border: none !important;
                outline: none !important;
                padding: 0 !important;
                overflow: hidden !important;
            }

            /* Guests dropdown trigger button — constrained to cell */
            .rooms-search-wrapper .box-bottom-search .item-search .btn-dropdown-search {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                font-size: 12px !important;
                font-weight: 700 !important;
                padding-left: 0 !important;
                text-align: left !important;
                overflow: hidden !important;
            }

            .rooms-search-wrapper .box-bottom-search .item-search .dropdown-toggle svg {
                stroke: #a17a42 !important;
            }

            .rooms-search-wrapper .box-bottom-search .item-search .guests-summary-text {
                font-size: 12px !important;
                font-weight: 700 !important;
                color: #1e293b !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                max-width: 100% !important;
            }

            /* Submit button row — full width, last row */
            .rooms-search-wrapper .box-bottom-search .search-submit-wrapper {
                grid-column: 1 / -1 !important;
                grid-row: 3 !important;
                width: 100% !important;
                display: block !important;
                padding: 12px 12px !important;
                min-width: 0 !important;
            }

            /* CTA button — perfectly full-width */
            .rooms-search-wrapper .box-bottom-search .btn-black-lg {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100% !important;
                height: 48px !important;
                border-radius: 12px !important;
                font-size: 13px !important;
                font-weight: 800 !important;
                letter-spacing: 0.3px;
                background: linear-gradient(135deg, #a17a42 0%, #bd9961 100%) !important;
                box-shadow: 0 4px 14px rgba(161, 122, 66, 0.25) !important;
                border: none !important;
                color: #ffffff !important;
                white-space: nowrap !important;
            }

            .rooms-search-wrapper .box-bottom-search .btn-black-lg:active {
                transform: scale(0.98) !important;
            }

            /* Guests dropdown — positioned by JS, CSS sets appearance only */
            .rooms-search-wrapper .dropdown-menu-guests {
                max-height: 70vh !important;
                overflow-y: auto !important;
                z-index: 99999 !important;
                border-radius: 16px !important;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18) !important;
                border: 1px solid #e2e8f0 !important;
                pointer-events: all !important;
            }
        }

        /* Tablet: 768px - 991px — single column clean stacking */
        @media (min-width: 768px) and (max-width: 991px) {
            .rooms-search-wrapper .box-bottom-search {
                flex-direction: column !important;
                align-items: stretch !important;
                padding: 16px !important;
                gap: 4px;
            }

            .rooms-search-wrapper .box-bottom-search .item-search {
                border-right: none !important;
                border-bottom: 1px solid #e2e8f0 !important;
                padding: 14px 12px !important;
                width: 100% !important;
            }

            .rooms-search-wrapper .box-bottom-search .item-search:last-of-type {
                border-bottom: none !important;
            }

            .rooms-search-wrapper .box-bottom-search .search-submit-wrapper {
                width: 100% !important;
                justify-content: center !important;
                margin-top: 12px;
                padding: 0 !important;
                min-width: unset !important;
            }

            .rooms-search-wrapper .box-bottom-search .btn-black-lg {
                width: 100% !important;
                height: 48px !important;
                border-radius: 12px !important;
                font-weight: 700 !important;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>

<body>

    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">





        <!-- Room Listing Showcase Section -->
        <section class="section-box pt-60 pb-60">
            <div class="container">
                <div class="text-center mb-40 wow fadeInUp">
                    <h2 class="section-title-responsive">Choose Your Room Category</h2>
                    <p class="text-md neutral-500 max-width-600 mx-auto mt-10">
                        Book direct online to lock in the absolute lowest nightly rates and enjoy flexible stay cancellation.
                    </p>

                    <!-- Search availability form -->
                    <div class="rooms-search-wrapper">
                        <div class="box-search-advance background-card">
                            <!-- Mobile-only gradient header bar (CSS controlled via @media) -->
                            <div class="mobile-search-header">
                                🏨&nbsp; Check Room Availability
                            </div>
                            <form method="GET" action="rooms.php">
                                <!-- Hidden counter inputs -->
                                <input type="hidden" name="adults" id="hidden_adults" value="<?= htmlspecialchars($search_adults) ?>">
                                <input type="hidden" name="children" id="hidden_children" value="<?= htmlspecialchars($search_children) ?>">

                                <div class="box-bottom-search background-card">
                                    <div class="item-search">
                                        <label class="text-sm-bold neutral-500" style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">Check-in Date</label>
                                        <div class="box-calendar-date d-flex align-items-center gap-2">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <input class="search-input" type="date" name="checkin" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($search_checkin) ?>" required style="padding-left: 0; background: transparent; border: none; font-weight: 700; color: var(--bs-neutral-1000); cursor: pointer; outline: none; width: 100%;">
                                        </div>
                                    </div>

                                    <div class="item-search">
                                        <label class="text-sm-bold neutral-500" style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">Check-out Date</label>
                                        <div class="box-calendar-date d-flex align-items-center gap-2">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                                <line x1="3" y1="10" x2="21" y2="10"></line>
                                            </svg>
                                            <input class="search-input" type="date" name="checkout" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($search_checkout) ?>" required style="padding-left: 0; background: transparent; border: none; font-weight: 700; color: var(--bs-neutral-1000); cursor: pointer; outline: none; width: 100%;">
                                        </div>
                                    </div>

                                    <div class="item-search">
                                        <label class="text-sm-bold neutral-500" style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; display: block;">Guests & Rooms</label>
                                        <div class="dropdown dropdown-guests-rooms">
                                            <button class="btn btn-secondary dropdown-toggle btn-dropdown-search d-flex align-items-center gap-2" type="button" id="dropdownGuestsBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="padding-left: 0; background: transparent; border: none; width: 100%; text-align: left;">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                                <span class="guests-summary-text" style="font-weight: 700; color: var(--bs-neutral-1000);">1 Room, <?= htmlspecialchars($search_adults + $search_children) ?> Guests</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-guests p-4" aria-labelledby="dropdownGuestsBtn" style="min-width: 280px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border-radius: 12px; border: 1px solid #e2e8f0;">
                                                <div id="roomsContainer">
                                                    <!-- Room 1 Block -->
                                                    <div class="room-block mb-3" data-room-id="1">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <h6 class="text-sm-bold text-primary mb-0" style="font-size: 14px; font-weight: 700; color: #a17a42 !important;">Room 1</h6>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <div>
                                                                <span style="font-weight: 600; font-size: 13px; color: #333; display: block; text-align: left;">Adult</span>
                                                            </div>
                                                            <div class="d-flex align-items-center border rounded overflow-hidden">
                                                                <button class="btn btn-sm btn-light py-1 px-3 dec-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">−</button>
                                                                <span class="px-3 py-1 adult-count" style="font-weight: 600; min-width: 30px; text-align: center;"><?= htmlspecialchars($search_adults) ?></span>
                                                                <button class="btn btn-sm btn-light py-1 px-3 inc-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                <span style="font-weight: 600; font-size: 13px; color: #333; display: block; text-align: left;">Child</span>
                                                                <span class="text-muted" style="font-size: 11px; display: block; text-align: left;">(Under 10 years)</span>
                                                            </div>
                                                            <div class="d-flex align-items-center border rounded overflow-hidden">
                                                                <button class="btn btn-sm btn-light py-1 px-3 dec-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">−</button>
                                                                <span class="px-3 py-1 child-count" style="font-weight: 600; min-width: 30px; text-align: center;"><?= htmlspecialchars($search_children) ?></span>
                                                                <button class="btn btn-sm btn-light py-1 px-3 inc-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top">
                                                    <button class="btn btn-sm btn-outline-success add-room-btn" type="button" style="border-color: #28a745; color: #28a745; border-radius: 20px; font-size: 12px; font-weight: 600; padding: 6px 16px;">Add Room</button>
                                                    <button class="btn btn-sm btn-done-guests close-dropdown-btn" type="button" style="background: #fd5c22; color: #fff; border-radius: 20px; border: none; font-size: 12px; font-weight: 600; padding: 6px 20px;">Done</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center search-submit-wrapper" style="padding: 0 10px; min-width: 170px;">
                                        <button type="submit" class="btn btn-black-lg text-nowrap d-flex align-items-center justify-content-center" style="background: #0f172a; color: #ffffff; border-radius: 10px; font-weight: 700; padding: 12px 20px; border: none; height: 46px; width: 100%;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                                                <path d="M9 11L12 14L22 4M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Check Availability
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php if ($has_search_dates): ?>
                        <div class="mt-15 text-start text-sm text-neutral-500 d-flex justify-content-between align-items-center">
                            <span>Showing availability for: <strong><?= date('d M Y', strtotime($search_checkin)) ?></strong> to <strong><?= date('d M Y', strtotime($search_checkout)) ?></strong></span>
                            <a href="rooms.php" class="text-danger" style="text-decoration: underline; font-weight:600;">Clear Dates</a>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Responsive Grid: stacks on mobile, 2 cols on tablets, 3 cols on desktop -->
                <div class="row g-4">
                    <?php foreach ($rooms as $room): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card-room-destin wow fadeInUp" style="margin-bottom: 0;">
                                <div class="image-box">
                                    <span class="badge-premier"><?= htmlspecialchars($room['type_badge']) ?></span>
                                    <span class="badge-discount"><?= htmlspecialchars($room['status_badge']) ?></span>

                                    <?php if (count($room['images']) > 1): ?>
                                        <!-- Navigation Arrows Overlay -->
                                        <div class="nav-arrow left" onclick="prevCardImg(this, event)">&lt;</div>
                                        <div class="nav-arrow right" onclick="nextCardImg(this, event)">&gt;</div>

                                        <!-- Pagination Dots Overlay -->
                                        <div class="dots-container">
                                            <?php foreach ($room['images'] as $idx => $img_path): ?>
                                                <div class="dot <?= $idx === 0 ? 'active' : '' ?>" onclick="setCardImg(this, <?= $idx ?>, event)"></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Sliding images wrapper -->
                                    <a href="room-detail.php?room=<?= urlencode($room['id']) ?>&checkin=<?= urlencode($search_checkin) ?>&checkout=<?= urlencode($search_checkout) ?>&adults=<?= $search_adults ?>&children=<?= $search_children ?>" class="card-images-wrapper" data-current-index="0" style="display: flex; transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); height: 100%; width: 100%;">
                                        <?php foreach ($room['images'] as $img_path): ?>
                                            <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($room['name']) ?>" style="flex: 0 0 100%; width: 100%; height: 100%; object-fit: cover; transition: none;">
                                        <?php endforeach; ?>
                                    </a>
                                </div>
                                <div class="content-box">
                                    <div class="title-row">
                                        <a href="room-detail.php?room=<?= urlencode($room['id']) ?>&checkin=<?= urlencode($search_checkin) ?>&checkout=<?= urlencode($search_checkout) ?>&adults=<?= $search_adults ?>&children=<?= $search_children ?>"><?= htmlspecialchars($room['name']) ?></a>
                                    </div>

                                    <div class="meta-row">
                                        <div class="location-text">
                                            <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 0C2.68 0 0 2.68 0 6C0 10.5 6 14 6 14C6 14 12 10.5 12 6C12 2.68 9.32 0 6 0ZM6 8.5C4.62 8.5 3.5 7.38 3.5 6C3.5 4.62 4.62 3.5 6 3.5C7.38 3.5 8.5 4.62 8.5 6C8.5 7.38 7.38 8.5 6 8.5Z" fill="#64748B" />
                                            </svg>
                                            <?= htmlspecialchars($room['location']) ?>
                                        </div>
                                        <div class="rating-box">
                                            <span>G</span> <span><?= str_replace(['G', '★', ' '], '', $room['rating']) ?></span> <span>★</span>
                                        </div>
                                    </div>

                                    <div class="tags-row">
                                        <?php foreach ($room['tags'] as $tag): ?>
                                            <span class="tag-pill <?= ($tag['style'] === 'blue') ? 'gym' : (($tag['style'] === 'green') ? 'more' : '') ?>"><?= htmlspecialchars($tag['text']) ?></span>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="amenities-row">
                                        <?php foreach ($room['specs'] as $spec): ?>
                                            <div class="amenity-col">
                                                <img src="<?= htmlspecialchars(get_amenity_icon($spec['label'])) ?>" alt="<?= htmlspecialchars($spec['label']) ?>">
                                                <?= htmlspecialchars($spec['label']) ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <?php if (!empty($room['banner_text'])): ?>
                                        <div class="banner-box">
                                            <i class="fa fa-star">⭐</i> <?= htmlspecialchars($room['banner_text']) ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="footer-row">
                                        <div class="price-area">
                                            <?php if (!empty($room['struck_price']) && $room['struck_price'] > 0): ?>
                                                <div class="old-price-line">
                                                    <span class="old-price">₹<?= number_format($room['struck_price']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="current-price">₹<?= number_format($room['price']) ?> <span>/ night</span></div>
                                        </div>

                                        <?php if (isset($room['available_count']) && $room['available_count'] <= 0): ?>
                                            <button class="book-btn" disabled style="background-color: #cbd5e1 !important; color: #64748b !important; border: none; cursor: not-allowed; padding: 10px 20px !important;">Sold Out</button>
                                        <?php else: ?>
                                            <a href="room-detail.php?room=<?= urlencode($room['id']) ?>&checkin=<?= urlencode($search_checkin) ?>&checkout=<?= urlencode($search_checkout) ?>&adults=<?= $search_adults ?>&children=<?= $search_children ?>" class="book-btn">Book Room</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Group booking notice banner -->
                <div class="bulk-booking-card" style="margin-top: 40px; margin-bottom: 20px;">
                    <div class="bulk-card-wrapper">
                        <div class="bulk-card-content">
                            <div class="bulk-card-icon">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                </svg>
                            </div>
                            <div class="bulk-card-text">
                                <h3>Looking to Book the Entire Hotel?</h3>
                                <p>Host group events, weddings, or corporate stays. Get custom packages, dedicated service, and exclusive rates for booking all room categories together.</p>
                            </div>
                        </div>
                        <div class="bulk-card-action">
                            <button class="bulk-booking-btn" data-bs-toggle="modal" data-bs-target="#bulkBookingModal">
                                Request Group Stay Quote
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <style>
            .bulk-booking-card {
                background: linear-gradient(135deg, #1e1b18 0%, #12100e 100%);
                border: 1px solid rgba(161, 122, 66, 0.35);
                border-radius: 16px;
                padding: 24px 30px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                position: relative;
                overflow: hidden;
            }

            .bulk-booking-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: #a17a42;
                /* Premium Gold Theme */
            }

            .bulk-card-wrapper {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 24px;
                flex-wrap: wrap;
            }

            .bulk-card-content {
                display: flex;
                align-items: center;
                gap: 20px;
                flex: 1;
                min-width: 280px;
            }

            .bulk-card-icon {
                background: rgba(161, 122, 66, 0.15);
                border: 1px solid rgba(161, 122, 66, 0.3);
                color: #a17a42;
                width: 56px;
                height: 56px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            .bulk-card-text {
                text-align: left;
            }

            .bulk-card-text h3 {
                margin: 0 0 6px 0 !important;
                color: #ffffff !important;
                font-size: 19px !important;
                font-weight: 700 !important;
                font-family: 'Outfit', sans-serif;
                letter-spacing: 0.2px;
            }

            .bulk-card-text p {
                margin: 0 !important;
                color: #b0b0b0 !important;
                font-size: 14px !important;
                line-height: 1.5 !important;
            }

            .bulk-booking-btn {
                background: linear-gradient(135deg, #a17a42 0%, #8c6734 100%);
                color: #ffffff !important;
                font-weight: 700 !important;
                font-size: 14.5px !important;
                padding: 14px 28px !important;
                border-radius: 10px !important;
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                cursor: pointer !important;
                transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
                box-shadow: 0 4px 15px rgba(161, 122, 66, 0.25) !important;
                white-space: nowrap;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .bulk-booking-btn:hover {
                transform: translateY(-2px);
                background: linear-gradient(135deg, #b58b4f 0%, #9e763f 100%);
                box-shadow: 0 6px 20px rgba(161, 122, 66, 0.4) !important;
            }

            .bulk-booking-btn:active {
                transform: translateY(0);
            }

            @media (max-width: 991px) {
                .bulk-card-wrapper {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 20px;
                }

                .bulk-card-action {
                    width: 100%;
                }

                .bulk-booking-btn {
                    width: 100%;
                    text-align: center;
                }
            }

            @media (max-width: 575px) {
                .bulk-booking-card {
                    padding: 20px;
                }

                .bulk-card-content {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }

                .bulk-card-text h3 {
                    font-size: 17.5px !important;
                }

                .bulk-card-text p {
                    font-size: 13px !important;
                }
            }
        </style>









        <!-- General Amenities Bar -->
        <section class="inclusions-strip">
            <div class="container">
                <div class="row g-3 justify-content-center">
                    <div class="col-6 col-md-3 col-lg-2-4">
                        <div class="inclusion-item">
                            <span class="inclusion-icon">☕</span>
                            <span>Kettle & Tea Tray</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2-4">
                        <div class="inclusion-item">
                            <span class="inclusion-icon">💧</span>
                            <span>Free 1L Water Daily</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2-4">
                        <div class="inclusion-item">
                            <span class="inclusion-icon">📶</span>
                            <span>Free High-speed Wi-Fi</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2-4">
                        <div class="inclusion-item">
                            <span class="inclusion-icon">❄️</span>
                            <span>Air Conditioned</span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2-4">
                        <div class="inclusion-item">
                            <span class="inclusion-icon">🚿</span>
                            <span>Premium Bathroom</span>
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

    <script>
        function prevCardImg(btn, event) {
            event.preventDefault();
            event.stopPropagation();
            var box = $(btn).closest('.image-box');
            var wrapper = box.find('.card-images-wrapper');
            var imgs = wrapper.find('img');
            var dots = box.find('.dot');
            var index = parseInt(wrapper.data('current-index')) || 0;

            index--;
            if (index < 0) {
                index = imgs.length - 1;
            }

            slideCardTo(wrapper, dots, index);
        }

        function nextCardImg(btn, event) {
            event.preventDefault();
            event.stopPropagation();
            var box = $(btn).closest('.image-box');
            var wrapper = box.find('.card-images-wrapper');
            var imgs = wrapper.find('img');
            var dots = box.find('.dot');
            var index = parseInt(wrapper.data('current-index')) || 0;

            index++;
            if (index >= imgs.length) {
                index = 0;
            }

            slideCardTo(wrapper, dots, index);
        }

        function setCardImg(dot, index, event) {
            event.preventDefault();
            event.stopPropagation();
            var box = $(dot).closest('.image-box');
            var wrapper = box.find('.card-images-wrapper');
            var dots = box.find('.dot');

            slideCardTo(wrapper, dots, index);
        }

        function slideCardTo(wrapper, dots, index) {
            wrapper.data('current-index', index);
            wrapper.css('transform', 'translateX(-' + (index * 100) + '%)');

            dots.removeClass('active');
            dots.eq(index).addClass('active');
        }
    </script>

    <script>
        $(document).ready(function() {
            // Guests & Rooms Dropdown Counters Logic
            $(document).on('click', '.inc-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(this).siblings('span');
                var count = parseInt(target.text());
                var roomBlock = $(this).closest('.room-block');

                if (target.hasClass('adult-count')) {
                    if (count < 3) { // Rule 1: Adult limit is only 3
                        var newAdults = count + 1;
                        target.text(newAdults);
                        // Rule 2: If customer chooses 3 adults, child count drops to 0
                        if (newAdults === 3) {
                            roomBlock.find('.child-count').text(0);
                        }
                        updateGuestsRoomsSummary();
                    }
                } else if (target.hasClass('child-count')) {
                    var adults = parseInt(roomBlock.find('.adult-count').text()) || 0;
                    // Rule 3: Child allowed only with adults <= 2
                    if (adults < 3) {
                        if (count < 4) {
                            target.text(count + 1);
                            updateGuestsRoomsSummary();
                        }
                    }
                }
            });

            $(document).on('click', '.dec-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(this).siblings('span');
                var count = parseInt(target.text());
                var isAdult = target.hasClass('adult-count');
                var minVal = isAdult ? 1 : 0;
                if (count > minVal) {
                    target.text(count - 1);
                    updateGuestsRoomsSummary();
                }
            });

            $('.add-room-btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var roomCount = $('#roomsContainer .room-block').length;
                if (roomCount < 4) {
                    var nextRoomId = roomCount + 1;
                    var roomHtml = `
                        <div class="room-block mb-3 pt-3 border-top" data-room-id="${nextRoomId}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="text-sm-bold text-primary mb-0" style="font-size: 14px; font-weight: 700; color: #a17a42 !important;">Room ${nextRoomId}</h6>
                                <a class="remove-room-link" href="#" style="font-size:12px; color:#dc2626; font-weight:600;">Remove</a>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <span style="font-weight: 600; font-size: 13px; color: #333; display: block; text-align: left;">Adult</span>
                                </div>
                                <div class="d-flex align-items-center border rounded overflow-hidden">
                                    <button class="btn btn-sm btn-light py-1 px-3 dec-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">−</button>
                                    <span class="px-3 py-1 adult-count" style="font-weight: 600; min-width: 30px; text-align: center;">2</span>
                                    <button class="btn btn-sm btn-light py-1 px-3 inc-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">+</button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span style="font-weight: 600; font-size: 13px; color: #333; display: block; text-align: left;">Child</span>
                                    <span class="text-muted" style="font-size: 11px; display: block; text-align: left;">(Under 10 years)</span>
                                </div>
                                <div class="d-flex align-items-center border rounded overflow-hidden">
                                    <button class="btn btn-sm btn-light py-1 px-3 dec-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">−</button>
                                    <span class="px-3 py-1 child-count" style="font-weight: 600; min-width: 30px; text-align: center;">0</span>
                                    <button class="btn btn-sm btn-light py-1 px-3 inc-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">+</button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#roomsContainer').append(roomHtml);
                    updateGuestsRoomsSummary();
                }
            });

            $(document).on('click', '.remove-room-link', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).closest('.room-block').remove();

                // Re-index remaining rooms
                $('#roomsContainer .room-block').each(function(index) {
                    var newId = index + 1;
                    $(this).attr('data-room-id', newId);
                    $(this).find('h6').text('Room ' + newId);
                });

                updateGuestsRoomsSummary();
            });

            $('.close-dropdown-btn').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#dropdownGuestsBtn').dropdown('hide');
            });

            function updateGuestsRoomsSummary() {
                var totalRooms = $('#roomsContainer .room-block').length;
                var totalAdults = 0;
                var totalChildren = 0;

                $('#roomsContainer .room-block').each(function() {
                    var adults = parseInt($(this).find('.adult-count').text()) || 0;
                    var children = parseInt($(this).find('.child-count').text()) || 0;
                    totalAdults += adults;
                    totalChildren += children;
                });

                // Update hidden inputs
                $('#hidden_adults').val(totalAdults);
                $('#hidden_children').val(totalChildren);

                var totalGuests = totalAdults + totalChildren;
                var roomsText = totalRooms + (totalRooms === 1 ? ' Room' : ' Rooms');
                var guestsText = totalGuests + (totalGuests === 1 ? ' Guest' : ' Guests');

                $('#dropdownGuestsBtn .guests-summary-text').text(roomsText + ', ' + guestsText);
            }
            // =========================================================
            // MOBILE FIX: Guests Dropdown — DOM Teleport Pattern
            // overflow:hidden on parent containers traps position:fixed on mobile.
            // Solution: physically MOVE the dropdown to <body> when it opens,
            // then move it back to its original position when it closes.
            // =========================================================
            function isMobileView() {
                return window.innerWidth <= 767;
            }

            var $guestsBtn = $('#dropdownGuestsBtn');
            var $guestsMenu = $guestsBtn.next('.dropdown-menu-guests');
            var $menuOriginalParent = null; // stores original DOM parent
            var $menuNextSibling = null; // stores original DOM position

            // BEFORE dropdown opens — intercept Bootstrap's show event
            $guestsBtn.on('show.bs.dropdown', function(e) {
                if (!isMobileView()) return;

                // Save original position in DOM
                $menuOriginalParent = $guestsMenu.parent();
                $menuNextSibling = $guestsMenu.next();

                // Teleport dropdown to <body> so it escapes ALL overflow:hidden parents
                $('body').append($guestsMenu.detach());

                // Initially hide it until positioning is calculated (prevent flash)
                $guestsMenu.css('visibility', 'hidden');
            });

            // AFTER dropdown is shown — calculate and apply position
            $guestsBtn.on('shown.bs.dropdown', function() {
                if (!isMobileView()) return;

                var btnRect = $guestsBtn[0].getBoundingClientRect();
                var viewportHeight = window.innerHeight;
                var viewportWidth = window.innerWidth;
                var menuWidth = Math.min(viewportWidth - 24, 360);
                var leftPos = (viewportWidth - menuWidth) / 2;

                // Measure menu height after it's in the DOM
                $guestsMenu.css({
                    'visibility': 'hidden',
                    'display': 'block',
                    'position': 'fixed',
                    'left': leftPos + 'px',
                    'top': '-9999px',
                    'width': menuWidth + 'px',
                    'min-width': menuWidth + 'px',
                    'z-index': '999999',
                    'transform': 'none',
                    'pointer-events': 'all'
                });

                var menuHeight = $guestsMenu.outerHeight();
                var spaceBelow = viewportHeight - btnRect.bottom - 10;
                var spaceAbove = btnRect.top - 10;
                var topPos;

                if (spaceBelow >= menuHeight || spaceBelow >= spaceAbove) {
                    topPos = btnRect.bottom + 6;
                } else {
                    topPos = btnRect.top - menuHeight - 6;
                }

                // Clamp within viewport
                topPos = Math.max(10, Math.min(topPos, viewportHeight - menuHeight - 10));

                // Apply final position and reveal
                $guestsMenu.css({
                    'top': topPos + 'px',
                    'visibility': 'visible'
                });
            });

            // WHEN dropdown closes — teleport menu back to its original position
            $guestsBtn.on('hide.bs.dropdown', function() {
                if (!isMobileView()) return;
                if (!$menuOriginalParent) return;

                // Re-insert at original DOM location
                if ($menuNextSibling && $menuNextSibling.length) {
                    $guestsMenu.detach().insertBefore($menuNextSibling);
                } else {
                    $menuOriginalParent.append($guestsMenu.detach());
                }

                // Reset all inline styles
                $guestsMenu.css({
                    'position': '',
                    'top': '',
                    'left': '',
                    'width': '',
                    'min-width': '',
                    'transform': '',
                    'z-index': '',
                    'pointer-events': '',
                    'visibility': '',
                    'display': ''
                });

                $menuOriginalParent = null;
                $menuNextSibling = null;
            });

            // Reposition on scroll/resize while open
            $(window).on('scroll resize orientationchange', function() {
                if ($guestsBtn.attr('aria-expanded') === 'true' && isMobileView()) {
                    $guestsBtn.trigger('shown.bs.dropdown');
                }
            });

        });

        // ── Mobile Date Placeholder Fix ────────────────────────────────────
        // On mobile, appearance:none hides the native "dd-mm-yyyy" hint.
        // We inject a <span> to simulate it and toggle it on value/focus.
        // ──────────────────────────────────────────────────────────────────
        (function initDatePlaceholders() {
            document.querySelectorAll('.box-calendar-date input[type="date"]').forEach(function(inp) {
                var wrapper = inp.closest('.box-calendar-date');
                if (!wrapper) return;

                // Inject placeholder span
                var ph = document.createElement('span');
                ph.className = 'date-ph';
                ph.textContent = 'dd-mm-yyyy';
                wrapper.appendChild(ph);

                function refresh() {
                    if (inp.value) {
                        ph.style.display = 'none';
                        inp.classList.remove('date-empty');
                    } else {
                        ph.style.display = '';
                        inp.classList.add('date-empty');
                    }
                }

                inp.addEventListener('change', refresh);
                inp.addEventListener('input', refresh);
                inp.addEventListener('focus', function() {
                    ph.style.display = 'none';
                    inp.classList.remove('date-empty');
                });
                inp.addEventListener('blur', refresh);
                refresh(); // run immediately on page load
            });
        })();
    </script>


</body>

</html>