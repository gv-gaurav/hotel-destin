<?php
// Gallery Items Array - easy to make dynamic with a database in the future
require_once __DIR__ . '/db.php';

$static_gallery = [
    [
        'title' => 'Luxury Suite Room',
        'category' => 'rooms',
        'image' => 'assets/imgs/page/hotel/hotelRoom.png',
        'description' => 'Spacious room with king bed and premium view'
    ],
    [
        'title' => 'Grand Ballroom Banquet',
        'category' => 'banquet',
        'image' => 'assets/imgs/page/room/banner-room.png',
        'description' => 'Premium event setup for corporate and family gatherings'
    ],
    [
        'title' => 'Fine Dining Restaurant',
        'category' => 'restaurant',
        'image' => 'assets/imgs/page/pages/banner2.png',
        'description' => 'Elegant dining experience featuring global cuisines'
    ],
    [
        'title' => 'Deluxe Ocean View Room',
        'category' => 'rooms',
        'image' => 'assets/imgs/page/hotel/hotelRoom2.png',
        'description' => 'Breathtaking ocean views with modern amenities'
    ],
    [
        'title' => 'Conference & Meeting Hall',
        'category' => 'banquet',
        'image' => 'assets/imgs/page/room/banner-room2.png',
        'description' => 'State-of-the-art conference space with high-speed connectivity'
    ],
    [
        'title' => 'Lounge & Cocktail Bar',
        'category' => 'restaurant',
        'image' => 'assets/imgs/page/room/banner-room3.png',
        'description' => 'Relaxing ambiance with hand-crafted cocktails'
    ],
    [
        'title' => 'Premium Twin Bed Room',
        'category' => 'rooms',
        'image' => 'assets/imgs/page/hotel/hotelRoom3.png',
        'description' => 'Comfortable twin bed setups for friends and colleagues'
    ],
    [
        'title' => 'Outdoor Lawn & Catering',
        'category' => 'banquet',
        'image' => 'assets/imgs/page/pages/banner.png',
        'description' => 'Lush green lawns perfect for grand weddings and receptions'
    ],
    [
        'title' => 'Open-Air Rooftop Cafe',
        'category' => 'restaurant',
        'image' => 'assets/imgs/page/room/banner-room4.png',
        'description' => 'Charming outdoor cafe with panoramic city views'
    ]
];

$gallery_items = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM gallery ORDER BY id DESC");
    $stmt->execute();
    $db_gallery = $stmt->fetchAll();

    if (count($db_gallery) > 0) {
        foreach ($db_gallery as $g) {
            $gallery_items[] = [
                'title' => $g['title'],
                'category' => $g['category'],
                'image' => $g['image_path'],
                'description' => $g['description']
            ];
        }
    } else {
        $gallery_items = [];
    }
} catch (Exception $e) {
    error_log("Database gallery fetch fallback: " . $e->getMessage());
    $gallery_items = [];
}

// Extract unique categories for filter tabs
$categories = array_unique(array_column($gallery_items, 'category'));

// Mapping category slug to human-readable titles
$category_names = [
    'rooms' => 'Rooms & Suites',
    'banquet' => 'Banquet & Events',
    'restaurant' => 'Restaurants & Dining'
];
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <meta name="description" content="Explore the premium gallery of our luxury hotel, banquet halls, and fine dining restaurants.">
    <meta name="keywords" content="gallery, hotel room gallery, banquet images, restaurant gallery, travila">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Gallery - Premium Hotel Booking & Events</title>

    <style>
        /* Custom Modern Gallery Styling */
        .gallery-title-main {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #0e0e0e;
            text-transform: uppercase;
        }

        .box-gallery-header p {
            font-size: 16px !important;
            line-height: 1.7 !important;
            color: #64748b !important;
            max-width: 600px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .gallery-title-main {
                font-size: 26px;
                letter-spacing: -0.3px;
            }

            .box-gallery-header p {
                font-size: 11px !important;
                line-height: 1.6 !important;
            }
        }

        /* Category Filters */
        .gallery-filter-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 40px;
        }

        .gallery-filter-btn {
            background-color: #ffffff;
            color: #1e293b;
            border: 1px solid #e2e8f0;
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            outline: none;
        }

        .gallery-filter-btn:hover,
        .gallery-filter-btn.active {
            background-color: #a17a42 !important;
            color: #ffffff !important;
            border-color: #a17a42 !important;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(161, 122, 66, 0.2);
        }

        @media (max-width: 575px) {
            .gallery-filter-container {
                gap: 8px;
                margin-bottom: 30px;
                padding: 0 10px;
            }

            .gallery-filter-btn {
                padding: 3px 8px;
                font-size: 10px;
            }
        }

        /* Gallery Cards */
        .gallery-card {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            margin-bottom: 30px;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        .gallery-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .gallery-img-wrapper {
            position: relative;
            width: 100%;
            height: 250px;
            overflow: hidden;
            background-color: #f1f2f6;
        }

        .gallery-img-wrapper img,
        .gallery-img-wrapper video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .gallery-card:hover .gallery-img-wrapper img,
        .gallery-card:hover .gallery-img-wrapper video {
            transform: scale(1.06);
        }

        /* Glassmorphism Hover Overlay */
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(14, 14, 14, 0.95) 0%, rgba(14, 14, 14, 0.3) 60%, rgba(14, 14, 14, 0.05) 100%);
            opacity: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 24px;
            transition: all 0.4s ease;
            z-index: 2;
        }

        .gallery-card:hover .gallery-overlay {
            opacity: 1;
        }

        .gallery-tag {
            align-self: flex-start;
            background: rgba(161, 122, 66, 0.2);
            color: #a17a42;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(161, 122, 66, 0.4);
            padding: 4px 14px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            transform: translateY(15px);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) 0.05s;
        }

        .gallery-title {
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
            transform: translateY(15px);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) 0.1s;
        }

        .gallery-desc {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13.5px;
            margin-bottom: 0;
            line-height: 1.5;
            transform: translateY(15px);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) 0.15s;
        }

        .gallery-card:hover .gallery-tag,
        .gallery-card:hover .gallery-title,
        .gallery-card:hover .gallery-desc {
            transform: translateY(0);
        }

        /* Video Badge & Pulse Play Overlay */
        .video-play-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background: rgba(161, 122, 66, 0.9);
            border-radius: 50%;
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            box-shadow: 0 4px 15px rgba(161, 122, 66, 0.4);
            z-index: 3;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .gallery-card:hover .video-play-btn {
            transform: translate(-50%, -50%) scale(1);
            background: #a17a42;
        }

        /* Modal Popup Styles */
        .white-popup-block {
            background: #0e0e0e;
            padding: 24px;
            text-align: left;
            max-width: 800px;
            margin: 40px auto;
            position: relative;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .white-popup-block video {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        /* Close button style for Magnific Popup */
        .mfp-close-btn-in .mfp-close {
            color: #fff !important;
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>

<body>

    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">
        <!-- Breadcrumb Section -->
        <section class="box-section box-breadcrumb background-100">
            <div class="container">
                <ul class="breadcrumbs">
                    <li> <a href="index.php">Home</a><span class="arrow-right">
                            <svg width="7" height="12" viewbox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 11L6 6L1 1" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg></span></li>
                    <li> <span class="text-breadcrumb">Gallery</span></li>
                </ul>
            </div>
        </section>

        <!-- Gallery Header -->
        <section class="section-box box-gallery-header background-body pt-10 pb-15">
            <div class="container">
                <div class="text-center wow fadeInUp">
                    <h1 class="gallery-title-main mb-20 font-heading">Explore Our Premium Spaces</h1>
                    <p class="text-lg neutral-500 max-width-600 mx-auto">
                        Take a virtual tour of our beautifully designed rooms, expansive banquet halls, and ambient restaurants where fine memories are made.
                    </p>
                </div>
            </div>
        </section>

        <!-- Gallery Grid & Filters -->
        <section class="section-box box-gallery-grid background-body pb-80">
            <div class="container">
                <?php if (count($gallery_items) > 0): ?>
                    <!-- Category Filters -->
                    <div class="gallery-filter-container wow fadeInUp" id="galleryFilters">
                        <button class="gallery-filter-btn active" data-filter="all">All Photos</button>
                        <?php foreach ($categories as $cat): ?>
                            <button class="gallery-filter-btn" data-filter="<?php echo $cat; ?>">
                                <?php echo isset($category_names[$cat]) ? $category_names[$cat] : ucfirst($cat); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Gallery Items Grid -->
                    <div class="row gallery-grid">
                        <?php foreach ($gallery_items as $index => $item): ?>
                            <?php
                            $file_path = $item['image'];
                            $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                            $is_video = in_array($ext, ['mp4', 'webm', 'mov', 'm4v']);
                            ?>
                            <div class="col-lg-4 col-md-6 col-sm-12 gallery-grid-item" data-category="<?php echo $item['category']; ?>">
                                <div class="gallery-card wow fadeInUp">
                                    <?php if ($is_video): ?>
                                        <!-- Video Popup Link targeting inline hidden modal -->
                                        <a href="#video-modal-<?= $index ?>" class="gallery-video-popup" title="<?php echo htmlspecialchars($item['title']); ?>">
                                            <div class="gallery-img-wrapper">
                                                <video src="<?php echo $file_path; ?>" muted loop autoplay playsinline></video>

                                                <div class="video-play-btn">
                                                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M8 5v14l11-7z" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <div class="gallery-overlay">
                                                <h3 class="gallery-title" style="transform: translateY(0); margin-bottom: 0;"><?php echo htmlspecialchars($item['title']); ?></h3>
                                            </div>
                                        </a>

                                        <!-- Hidden Inline Video Modal Markup -->
                                        <div id="video-modal-<?= $index ?>" class="mfp-hide white-popup-block">
                                            <video src="<?php echo $file_path; ?>" controls style="width: 100%; height: auto; display: block; border-radius: 8px;"></video>
                                            <h3 style="color: #fff; margin-top: 15px; font-size: 18px; font-weight: 700;"><?= htmlspecialchars($item['title']) ?></h3>
                                            <?php if (!empty($item['description'])): ?>
                                                <p style="color: #ccc; font-size: 14.5px; margin-top: 8px; line-height: 1.5;"><?= htmlspecialchars($item['description']) ?></p>
                                            <?php endif; ?>
                                        </div>

                                    <?php else: ?>
                                        <!-- Image Popup Link -->
                                        <a href="<?php echo $item['image']; ?>" class="gallery-image-popup" title="<?php echo htmlspecialchars($item['title']); ?>">
                                            <div class="gallery-img-wrapper">
                                                <img src="<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                                            </div>

                                            <div class="gallery-overlay">
                                                <h3 class="gallery-title" style="transform: translateY(0); margin-bottom: 0;"><?php echo htmlspecialchars($item['title']); ?></h3>
                                            </div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5 wow fadeInUp">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="1.5" style="margin-bottom: 20px; opacity: 0.8;">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                            <circle cx="8.5" cy="8.5" r="1.5" />
                            <polyline points="21 15 16 10 5 21" />
                        </svg>
                        <h3 class="neutral-1000 font-heading mb-10" style="font-size: 20px;">No Gallery Items Found</h3>
                        <p class="neutral-500 max-width-400 mx-auto" style="font-size: 14.5px;">We are currently updating our gallery with new photos. Please check back soon!</p>
                    </div>
                <?php endif; ?>
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
    <script src="assets/js/plugins/masonry.min.js"></script>
    <script src="assets/js/plugins/scrollup.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/plugins/waypoints.js"></script>
    <script src="assets/js/plugins/counterup.js"></script>
    <script src="assets/js/plugins/bootstrap-datepicker.js"></script>
    <script src="assets/js/plugins/dark.js"></script>
    <!-- Count down-->
    <script src="assets/js/vendor/jquery.countdown.min.js"></script>
    <script src="assets/js/plugins/noUISlider.js"></script>
    <script src="assets/js/plugins/slider.js"></script>
    <!-- Custom template script -->
    <script src="assets/js/maine209.js?v=1.0.0"></script>

    <!-- Dynamic Filter and Magnific Lightbox Script -->
    <script>
        $(document).ready(function() {
            // Initialize Magnific Popup for Images
            function initImageLightbox() {
                $('a.gallery-image-popup').off('click');
                $('a.gallery-image-popup:visible').magnificPopup({
                    type: 'image',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0, 1]
                    },
                    image: {
                        tError: '<a href="%url%">The image</a> could not be loaded.',
                        titleSrc: function(item) {
                            return item.el.closest('.gallery-card').find('.gallery-title').text();
                        }
                    },
                    mainClass: 'mfp-fade',
                    removalDelay: 300
                });
            }

            // Initialize Magnific Popup for Inline Videos
            function initVideoLightbox() {
                $('a.gallery-video-popup').off('click');
                $('a.gallery-video-popup:visible').magnificPopup({
                    type: 'inline',
                    gallery: {
                        enabled: true
                    },
                    mainClass: 'mfp-fade',
                    removalDelay: 300,
                    callbacks: {
                        open: function() {
                            var video = this.content.find('video')[0];
                            if (video) {
                                video.currentTime = 0;
                                video.play();
                            }
                        },
                        close: function() {
                            $('video').each(function() {
                                this.pause();
                            });
                        }
                    }
                });
            }

            // Trigger Lightboxes
            initImageLightbox();
            initVideoLightbox();

            // Filtering Logic
            $('#galleryFilters .gallery-filter-btn').on('click', function() {
                // Toggle active class
                $('#galleryFilters .gallery-filter-btn').removeClass('active');
                $(this).addClass('active');

                var filterValue = $(this).attr('data-filter');

                if (filterValue === 'all') {
                    $('.gallery-grid-item').fadeIn(400);
                } else {
                    $('.gallery-grid-item').hide();
                    $('.gallery-grid-item[data-category="' + filterValue + '"]').fadeIn(400);
                }

                // Re-initialize lightboxes to scan only currently visible elements
                setTimeout(function() {
                    initImageLightbox();
                    initVideoLightbox();
                }, 450);
            });
        });
    </script>
</body>

</html>