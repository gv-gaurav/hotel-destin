<?php
require_once __DIR__ . '/db.php';

// Static Fallback Blog Posts (in case DB has no records yet)
$static_blog_posts = [
    [
        'id' => 1,
        'title' => 'Gwalior Fort Guide: Exploring the Gibraltar of India',
        'category' => 'Local Attractions',
        'image' => 'assets/imgs/page/homepage1/news.png',
        'date' => '05 Jul 2026',
        'read_time' => '8 min read',
        'excerpt' => 'Discover the rich history, magnificent palaces, and stunning temple carvings inside Gwalior\'s historic fort.'
    ],
    [
        'id' => 2,
        'title' => 'Top 5 Dining Spots on Sachin Tendulkar Road',
        'category' => 'Dining Guide',
        'image' => 'assets/imgs/page/homepage1/news2.png',
        'date' => '02 Jul 2026',
        'read_time' => '5 min read',
        'excerpt' => 'A curated list of local Gwalior specialties, fine dining, and cafe favorites located just steps from Hotel Destin.'
    ],
    [
        'id' => 3,
        'title' => 'Planning the Perfect Event or Wedding in Gwalior',
        'category' => 'Event Planning',
        'image' => 'assets/imgs/page/homepage1/news3.png',
        'date' => '28 Jun 2026',
        'read_time' => '10 min read',
        'excerpt' => 'From picking themes and menus to managing guest blocks, here is our ultimate checklist for stress-free banquets.'
    ],
    [
        'id' => 4,
        'title' => 'Why Business Travelers Choose Kailash Nagar',
        'category' => 'Corporate Travel',
        'image' => 'assets/imgs/page/homepage1/news.png',
        'date' => '20 Jun 2026',
        'read_time' => '4 min read',
        'excerpt' => 'Explore the connectivity benefits, workspace amenities, and central transit points that make business travel in Gwalior seamless.'
    ]
];

$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$blog_posts = [];
$total_posts = 0;
$total_pages = 0;

try {
    // Get total posts count for pagination checks
    $count_stmt = $pdo->query("SELECT COUNT(*) FROM blogs");
    $total_posts = intval($count_stmt->fetchColumn() ?: 0);
    $total_pages = ceil($total_posts / $limit);

    if ($total_posts > 0) {
        $stmt = $pdo->prepare("SELECT * FROM blogs ORDER BY id DESC LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $db_posts = $stmt->fetchAll();
        
        foreach ($db_posts as $b) {
            $blog_posts[] = [
                'id' => $b['id'],
                'slug' => $b['slug'],
                'title' => $b['title'],
                'category' => $b['category'],
                'image' => $b['image_path'],
                'date' => $b['date'],
                'read_time' => $b['read_time'],
                'excerpt' => $b['excerpt'],
                'content' => $b['content']
            ];
        }
    } else {
        // Fallback to static lists
        $total_posts = count($static_blog_posts);
        $total_pages = ceil($total_posts / $limit);
        $sliced = array_slice($static_blog_posts, $offset, $limit);
        foreach ($sliced as $b) {
            $blog_posts[] = [
                'id' => $b['id'],
                'slug' => '',
                'title' => $b['title'],
                'category' => $b['category'],
                'image' => $b['image'],
                'date' => $b['date'],
                'read_time' => $b['read_time'],
                'excerpt' => $b['excerpt'],
                'content' => ''
            ];
        }
    }
} catch (Exception $e) {
    error_log("Database blog posts load failure: " . $e->getMessage());
    $total_posts = count($static_blog_posts);
    $total_pages = ceil($total_posts / $limit);
    $sliced = array_slice($static_blog_posts, $offset, $limit);
    foreach ($sliced as $b) {
        $blog_posts[] = [
            'id' => $b['id'],
            'slug' => '',
            'title' => $b['title'],
            'category' => $b['category'],
            'image' => $b['image'],
            'date' => $b['date'],
            'read_time' => $b['read_time'],
            'excerpt' => $b['excerpt'],
            'content' => ''
        ];
    }
}

// Fetch 4 trending recent posts for the sidebar
$trending_posts = [];
try {
    $trending_stmt = $pdo->query("SELECT * FROM blogs ORDER BY id DESC LIMIT 4");
    $trending_db = $trending_stmt->fetchAll();
    
    if (count($trending_db) > 0) {
        foreach ($trending_db as $t) {
            $trending_posts[] = [
                'id' => $t['id'],
                'slug' => $t['slug'],
                'title' => $t['title'],
                'date' => $t['date'],
                'image' => $t['image_path']
            ];
        }
    } else {
        $trending_posts = array_slice($static_blog_posts, 0, 4);
    }
} catch (Exception $e) {
    error_log("Trending posts load failure: " . $e->getMessage());
    $trending_posts = array_slice($static_blog_posts, 0, 4);
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
    <meta name="description" content="Explore Gwalior travel guides, local attraction tips, dining spots, and hotel services from Hotel Destin Blog.">
    <meta name="keywords" content="Hotel Destin blog, Gwalior travel guides, hotels in Gwalior, Gwalior attractions">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Blog - Hotel Destin Gwalior</title>

    <style>
        /* Custom Premium Blog Styles */
        .blog-banner {
            background-color: var(--neutral-100, #f8f9fa);
            padding: 40px 0;
            border-bottom: 1px solid var(--neutral-200, #e9ecef);
        }

        .blog-banner-title {
            font-size: 38px;
            font-weight: 500;
            letter-spacing: -0.8px;
            color: var(--neutral-1000, #0E0E0E);
            margin-bottom: 8px;
        }

        .blog-banner-subtitle {
            font-size: 15px;
            color: var(--neutral-500, #6c757d);
            margin-bottom: 0;
        }

        /* Post Card Styles */
        .blog-post-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            margin-bottom: 30px;
        }

        .blog-post-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.06);
            border-color: rgba(14,14,14,0.08);
        }

        .post-img-wrapper {
            position: relative;
            height: 240px;
            overflow: hidden;
            background-color: #f1f2f6;
        }

        .post-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .blog-post-card:hover .post-img-wrapper img {
            transform: scale(1.05);
        }

        .post-cat-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: #ffffff;
            color: var(--neutral-1000, #0E0E0E);
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            z-index: 2;
        }

        .post-info-box {
            padding: 24px;
        }

        .post-meta-details {
            display: flex;
            gap: 15px;
            font-size: 12px;
            color: var(--neutral-400, #adb5bd);
            margin-bottom: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .post-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .post-title {
            font-size: 19px;
            font-weight: 600;
            line-height: 1.4;
            color: var(--neutral-1000, #0E0E0E);
            margin-bottom: 12px;
            transition: color 0.3s ease;
        }

        .post-title a {
            color: inherit;
            text-decoration: none;
        }

        .post-title a:hover {
            color: var(--primary, #0E0E0E);
        }

        .post-excerpt {
            font-size: 14px;
            color: var(--neutral-500, #6c757d);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .post-footer-area {
            margin-top: 15px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .keep-reading-link {
            font-size: 13px;
            font-weight: 700;
            color: var(--neutral-1000, #0E0E0E);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: transform 0.3s ease;
        }

        .keep-reading-link:hover {
            transform: translateX(4px);
        }

        /* Sidebar Widgets */
        .sidebar-widget-box {
            background-color: #ffffff;
            border: 1px solid rgba(0,0,0,0.04);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
        }

        .sidebar-widget-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--neutral-1000, #0E0E0E);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .search-widget-input {
            width: 100%;
            height: 44px;
            border: 1px solid var(--neutral-200, #e9ecef);
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        .search-widget-input:focus {
            border-color: var(--primary, #0E0E0E);
        }

        .trending-post-item {
            display: flex;
            gap: 12px;
            margin-bottom: 15px;
        }

        .trending-post-item:last-child {
            margin-bottom: 0;
        }

        .trending-img-wrapper {
            width: 65px;
            height: 65px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
            background-color: #f1f2f6;
        }

        .trending-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trending-post-title {
            font-size: 13.5px;
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 4px;
        }

        .trending-post-title a {
            color: var(--neutral-1000, #0E0E0E);
            text-decoration: none;
        }

        .trending-post-title a:hover {
            color: var(--primary, #0E0E0E);
        }

        .trending-post-date {
            font-size: 11px;
            color: var(--neutral-400, #adb5bd);
        }

        /* Pill tags */
        .tag-pill-container {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .tag-pill {
            background-color: var(--neutral-100, #f8f9fa);
            color: var(--neutral-600, #4f5e71);
            font-size: 12.5px;
            font-weight: 500;
            padding: 5px 12px;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.25s ease;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .tag-pill:hover {
            background-color: var(--neutral-1000, #0E0E0E);
            color: #ffffff;
        }

        /* Premium Minimalist Pagination */
        .pagination {
            display: flex;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
            background: transparent !important;
            box-shadow: none !important;
        }

        .pagination .page-item {
            margin: 0;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50% !important;
            border: 1px solid var(--neutral-200, #e9ecef);
            background-color: #ffffff;
            color: var(--neutral-800, #4f5e71);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            padding: 0;
            outline: none;
            box-shadow: none;
        }

        .pagination .page-link:hover,
        .pagination .page-item .page-link.active {
            background-color: var(--primary, #0E0E0E);
            color: #ffffff !important;
            border-color: var(--primary, #0E0E0E);
        }

        .pagination .page-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(14, 14, 14, 0.15);
        }

        .pagination .page-item:last-child .page-link {
            width: auto;
            padding: 0 18px;
            border-radius: 20px !important;
        }

        /* Tight section utility */
        .py-tight {
            padding-top: 40px !important;
            padding-bottom: 40px !important;
        }

        @media (max-width: 768px) {
            .blog-banner {
                padding: 30px 0;
            }
            .blog-banner-title {
                font-size: 28px;
            }
            .py-tight {
                padding-top: 30px !important;
                padding-bottom: 30px !important;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
</head>
<body>

    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">

        <!-- Banner Header Section -->
        <section class="blog-banner wow fadeIn">
            <div class="container">
                <ul class="breadcrumbs mb-10" style="padding: 0; background: transparent; display: flex;">
                    <li><a href="index.php">Home</a><span class="arrow-right"><svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></li>
                    <li><span class="text-breadcrumb">Blog</span></li>
                </ul>
                <h1 class="blog-banner-title">Hotel Destin Blog</h1>
                <p class="blog-banner-subtitle">Local Gwalior travel guides, local sightseeing tips, and events insights.</p>
            </div>
        </section>

        <!-- Main Blog Content Section -->
        <section class="section-box background-body py-tight">
            <div class="container">
                <div class="row g-5">
                    
                    <!-- Left Column: Blog Grid -->
                    <div class="col-lg-8 col-12">
                        <div class="row g-4">
                            <?php foreach ($blog_posts as $post): 
                                $detail_link = !empty($post['slug']) ? 'blog-detail.php?slug=' . urlencode($post['slug']) : 'blog-detail.php?id=' . $post['id'];
                            ?>
                                <div class="col-md-6 col-12">
                                    <div class="blog-post-card wow fadeInUp">
                                        <div class="post-img-wrapper">
                                            <a href="<?php echo $detail_link; ?>">
                                                <img src="<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" loading="lazy">
                                            </a>
                                        </div>
                                        
                                        <div class="post-info-box">
                                            <div class="post-meta-details">
                                                <span class="post-meta-item">
                                                    <!-- Calendar Icon -->
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                                    <?php echo $post['date']; ?>
                                                </span>
                                                <span class="post-meta-item">
                                                    <!-- Clock Icon -->
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                    <?php echo $post['read_time']; ?>
                                                </span>
                                            </div>
                                            
                                            <h3 class="post-title">
                                                <a href="<?php echo $detail_link; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                                            </h3>
                                            
                                            <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                                            
                                            <div class="post-footer-area">
                                                <a class="keep-reading-link" href="<?php echo $detail_link; ?>">
                                                    Keep Reading
                                                    <!-- Right Arrow Icon -->
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Dynamic Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="d-flex justify-content-center mt-30">
                                <nav>
                                    <ul class="pagination">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item"><a class="page-link" href="blog.php?page=<?= $page - 1 ?>" style="width: auto; padding: 0 15px; border-radius: 20px !important;">Prev</a></li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item"><a class="page-link <?= $i === $page ? 'active' : '' ?>" href="blog.php?page=<?= $i ?>"><?= $i ?></a></li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item"><a class="page-link" href="blog.php?page=<?= $page + 1 ?>" style="width: auto; padding: 0 15px; border-radius: 20px !important;">Next</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Right Column: Sidebar -->
                    <div class="col-lg-4 col-12">

                        <!-- Trending Posts Widget -->
                        <div class="sidebar-widget-box wow fadeInUp" data-wow-delay="0.05s">
                            <h4 class="sidebar-widget-title">Trending Articles</h4>
                            <?php foreach ($trending_posts as $trend): 
                                $trend_link = !empty($trend['slug']) ? 'blog-detail.php?slug=' . urlencode($trend['slug']) : 'blog-detail.php?id=' . $trend['id'];
                            ?>
                                <div class="trending-post-item">
                                    <div class="trending-img-wrapper">
                                        <img src="<?php echo $trend['image']; ?>" alt="Trending post cover image">
                                    </div>
                                    <div>
                                        <h5 class="trending-post-title">
                                            <a href="<?php echo $trend_link; ?>"><?php echo htmlspecialchars($trend['title']); ?></a>
                                        </h5>
                                        <span class="trending-post-date"><?php echo $trend['date']; ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Categories Widget -->
                        <div class="sidebar-widget-box wow fadeInUp" data-wow-delay="0.1s">
                            <h4 class="sidebar-widget-title">Categories</h4>
                            <div class="tag-pill-container">
                                <a href="#" class="tag-pill">Local Attractions</a>
                                <a href="#" class="tag-pill">Dining Guide</a>
                                <a href="#" class="tag-pill">Event Planning</a>
                                <a href="#" class="tag-pill">Corporate Travel</a>
                                <a href="#" class="tag-pill">Inside Destin</a>
                            </div>
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
