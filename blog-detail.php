<?php
require_once __DIR__ . '/db.php';

$post_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$post = null;

try {
    if (!empty($slug)) {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ?");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    } elseif ($post_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();
    }
} catch (Exception $e) {
    error_log("Database error loading blog details: " . $e->getMessage());
}

// Fallback static posts if database connection is empty or post not found
if (!$post) {
    $static_posts = [
        1 => [
            'id' => 1,
            'slug' => 'gwalior-fort-guide',
            'title' => 'Gwalior Fort Guide: Exploring the Gibraltar of India',
            'category' => 'Local Attractions',
            'image_path' => 'assets/imgs/page/homepage1/news.png',
            'date' => '05 Jul 2026',
            'read_time' => '8 min read',
            'excerpt' => 'Discover the rich history, magnificent palaces, and stunning temple carvings inside Gwalior\'s historic fort.',
            'content' => 'Gwalior Fort is one of the most famous tourist attractions in Madhya Pradesh, India. Built in the 8th century, it sits atop a steep hill overlooking the city. Inside, visitors can marvel at the stunning Man Mandir Palace with its signature turquoise blue tiles, the ancient Sas Bahu temples, and the magnificent rock-cut Jain sculptures carved along the cliffside paths. Plan a half-day tour to experience this majestic fort in all its glory.',
            'meta_title' => 'Gwalior Fort Sightseeing Guide & History - Hotel Destin',
            'meta_description' => 'Read our complete tourist guide to visiting Gwalior Fort in Madhya Pradesh, including ticket timings, palaces, temples, and historical details.'
        ],
        2 => [
            'id' => 2,
            'slug' => 'dining-spots-sachin-tendulkar-road',
            'title' => 'Top 5 Dining Spots on Sachin Tendulkar Road',
            'category' => 'Dining Guide',
            'image_path' => 'assets/imgs/page/homepage1/news2.png',
            'date' => '02 Jul 2026',
            'read_time' => '5 min read',
            'excerpt' => 'A curated list of local Gwalior specialties, fine dining, and cafe favorites located just steps from Hotel Destin.',
            'content' => 'Sachin Tendulkar Road is Gwalior\'s premier lifestyle and dining hub. When staying at Hotel Destin, you are surrounded by excellent choices. Here are our top 5 recommendations: 1) The Heights Rooftop Club (located inside Hotel Destin) for high-end dining, 2) local street food stalls for spicy Gwalior Bedai, 3) Indian accent fine dining restaurants, 4) modern espresso cafes, and 5) premium ice cream parlors.',
            'meta_title' => 'Top Restaurants & Cafes on Sachin Tendulkar Road - Hotel Destin',
            'meta_description' => 'Explore the best restaurants, local breakfast spots, and cafe lounges on Gwalior\'s Sachin Tendulkar Road, located right next to Hotel Destin.'
        ]
    ];

    if (!empty($slug)) {
        foreach ($static_posts as $sid => $spost) {
            if ($spost['slug'] === $slug) {
                $post = $spost;
                $post_id = $sid;
                break;
            }
        }
    } elseif ($post_id > 0 && array_key_exists($post_id, $static_posts)) {
        $post = $static_posts[$post_id];
    }

    if (!$post) {
        // Redirect to blog listing if post is not found
        header("Location: blog.php");
        exit;
    }
} else {
    $post_id = $post['id'];
}

// Fetch trending posts for sidebar
$trending_posts = [];
try {
    $t_stmt = $pdo->prepare("SELECT id, slug, title, date, image_path FROM blogs WHERE id != ? ORDER BY id DESC LIMIT 4");
    $t_stmt->execute([$post_id]);
    $trending_posts = $t_stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error loading trending posts in details sidebar: " . $e->getMessage());
}

if (count($trending_posts) === 0) {
    $trending_posts = [];
    foreach ($static_posts as $sid => $spost) {
        if ($sid != $post_id) {
            $trending_posts[] = [
                'id' => $spost['id'],
                'slug' => $spost['slug'],
                'title' => $spost['title'],
                'date' => $spost['date'],
                'image_path' => $spost['image_path']
            ];
        }
    }
}

// Calculate canonical link tag dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$canonical_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?') . (!empty($post['slug']) ? '?slug=' . urlencode($post['slug']) : '?id=' . $post['id']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title><?= htmlspecialchars(!empty($post['meta_title']) ? $post['meta_title'] : $post['title']) ?></title>
    <meta name="description" content="<?= htmlspecialchars(!empty($post['meta_description']) ? $post['meta_description'] : $post['excerpt']) ?>">
    <link rel="canonical" href="<?= htmlspecialchars($canonical_url) ?>">
    
    <style>
        .blog-detail-container {
            padding: 35px 0 60px 0;
            background-color: #ffffff;
        }
        .post-header {
            margin-bottom: 20px;
        }
        .post-title {
            font-size: 38px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.8px;
            margin-bottom: 12px;
            text-transform: capitalize;
        }
        .post-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13.5px;
            color: #64748b;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .post-badge {
            background-color: rgba(156, 96, 71, 0.1);
            color: #9c6047;
            font-size: 11px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .post-banner-img {
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.015);
            border: 1px solid #f1f5f9;
        }
        .post-banner-img img {
            width: 100%;
            height: auto;
            max-height: 480px;
            object-fit: cover;
        }
        .post-content {
            font-size: 16px;
            line-height: 1.8;
            color: #334155;
            font-weight: 450;
        }
        .post-content p {
            /* margin-bottom: 24px; */
        }
        .post-content h3 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            /* margin-top: 40px; */
            /* margin-bottom: 16px; */
            letter-spacing: -0.4px;
        }
        .sidebar-card {
            background: #fdfbf9;
            border: 1px solid #f3ebe4;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
        }
        .sidebar-title {
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 16px;
            color: #0f172a;
            border-bottom: 1px solid #f1ece4;
            padding-bottom: 10px;
            letter-spacing: -0.3px;
        }
        .trending-item {
            display: flex;
            gap: 12px;
            padding: 8px 0;
            border-bottom: 1px dashed #f1ece4;
            transition: all 0.2s ease;
            align-items: center;
        }
        .trending-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .trending-item:hover {
            transform: translateX(4px);
        }
        .trending-item:hover h6 {
            color: #9c6047 !important;
        }
        .trending-img {
            width: 64px;
            height: 48px;
            border-radius: 6px;
            overflow: hidden;
            background: #f1ece4;
            flex-shrink: 0;
        }
        .trending-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .trending-item:hover .trending-img img {
            transform: scale(1.08);
        }
        .book-promo-card {
            background: linear-gradient(135deg, #fdf8f4, #fbf2eb);
            color: #475569;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.01);
            border: 1px solid #f5e3d7;
        }
        .book-promo-card h4 {
            font-size: 17px;
            font-weight: 850;
            color: #9c6047;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }
        .book-promo-card p {
            font-size: 12.5px;
            color: #64748b;
            margin-bottom: 16px;
            line-height: 1.45;
        }
        .book-promo-btn {
            background: #9c6047;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.2s ease;
            display: block;
            width: 100%;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(156, 96, 71, 0.15);
            border: none;
        }
        .book-promo-btn:hover {
            background: #7c4c36;
            transform: translateY(-1px);
            box-shadow: 0 6px 8px rgba(156, 96, 71, 0.2);
        }

        /* Mobile Adjustments */
        @media (max-width: 768px) {
            .blog-detail-container {
                padding: 40px 0 60px 0;
            }
            .post-title {
                font-size: 26px;
                letter-spacing: -0.5px;
            }
            .post-banner-img {
                margin-bottom: 25px;
            }
            .post-content {
                font-size: 15px;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>

    <?php include("include/header.php"); ?>

    <main class="main">
        <section class="blog-detail-container">
            <div class="container">
                <!-- Breadcrumbs -->
                <ul class="breadcrumbs mb-20" style="padding: 0; background: transparent; display: flex;">
                    <li><a href="index.php">Home</a><span class="arrow-right"><svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></li>
                    <li><a href="blog.php">Blog</a><span class="arrow-right"><svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></li>
                    <li><span class="text-breadcrumb">Article Details</span></li>
                </ul>

                <div class="row g-4">
                    <!-- Left: Article Content -->
                    <div class="col-lg-8">
                        <article class="post-header">
                            <div class="post-meta">
                                <span class="post-badge"><?= htmlspecialchars($post['category']) ?></span>
                                <span><?= htmlspecialchars($post['date']) ?></span>
                                <span>•</span>
                                <span><?= htmlspecialchars($post['read_time']) ?></span>
                            </div>
                            <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
                        </article>

                        <div class="post-banner-img">
                            <img src="<?= htmlspecialchars($post['image_path']) ?>" alt="Featured Image">
                        </div>

                        <div class="post-content">
                            <?= nl2br($post['content']) ?>
                        </div>
                    </div>

                    <!-- Right: Sidebar -->
                    <div class="col-lg-4">
                        <div class="sidebar-card">
                            <h4 class="sidebar-title">Recent Articles</h4>
                            <div class="d-flex flex-column">
                                <?php foreach ($trending_posts as $t): 
                                    $t_link = !empty($t['slug']) ? 'blog-detail.php?slug=' . urlencode($t['slug']) : 'blog-detail.php?id=' . $t['id'];
                                ?>
                                    <a href="<?= $t_link ?>" class="trending-item text-decoration-none">
                                        <div class="trending-img">
                                            <img src="<?= htmlspecialchars($t['image_path']) ?>" alt="Post image">
                                        </div>
                                        <div style="flex-grow: 1;">
                                            <h6 style="font-size: 13.5px; font-weight: 700; color: #1e293b; line-height: 1.4; margin-bottom: 4px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; transition: color 0.2s ease;"><?= htmlspecialchars($t['title']) ?></h6>
                                            <span style="font-size: 11px; color: #94a3b8; font-weight: 600;"><?= htmlspecialchars($t['date']) ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="book-promo-card">
                            <h4 class="font-heading">Book Your Stay</h4>
                            <p>Plan a comfortable trip in Gwalior. Explore executive rooms and premium amenities.</p>
                            <a href="rooms.php" class="book-promo-btn">Book Now</a>
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
