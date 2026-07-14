<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <meta name="description" content="Discover Gwalior's iconic tourist destinations near Hotel Destin Gwalior. Explore Gwalior Fort, Jai Vilas Palace, and the Sun Temple with custom local guidance.">
    <meta name="keywords" content="Explore Gwalior, Gwalior Fort, Jai Vilas Palace, Sun Temple Gwalior, Hotel Destin local attractions, Gwalior sightseeing">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Explore Gwalior Local Attractions - Hotel Destin Gwalior</title>

    <style>
        /* Custom Explore Page Styles */
        .explore-banner {
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.75)), url('assets/imgs/page/gwalior_fort.png') no-repeat center center;
            background-size: cover;
            padding: 80px 0;
            color: #ffffff;
            text-align: center;
        }

        .explore-title {
            font-size: 42px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .explore-lead {
            font-size: 16px !important;
            line-height: 1.6 !important;
            color: rgba(255, 255, 255, 0.8) !important;
            max-width: 750px;
            margin: 0 auto;
        }

        .container-narrow {
            max-width: 1000px !important;
            margin: 0 auto !important;
        }

        /* Destination Card Layout */
        .destination-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            margin-bottom: 40px;
        }

        .destination-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }

        .destination-img-wrapper {
            width: 100%;
            height: 100%;
            min-height: 350px;
            overflow: hidden;
            background-color: #f1f2f6;
        }

        .destination-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .destination-card:hover .destination-img-wrapper img {
            transform: scale(1.04);
        }

        .destination-info {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }

        .distance-tag {
            font-size: 12px !important;
            color: #a17a42 !important;
            font-weight: 700 !important;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .destination-heading {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 15px;
            font-family: inherit;
        }

        .destination-desc {
            font-size: 14.5px !important;
            line-height: 1.7 !important;
            color: #475569 !important;
            margin-bottom: 25px !important;
        }

        /* Guidance Section styling */
        .guidance-box {
            background: #faf8f5;
            border: 1px solid rgba(161, 122, 66, 0.15);
            border-radius: 20px;
            padding: 40px;
            margin-top: 50px;
            box-shadow: 0 4px 20px rgba(161, 122, 66, 0.03);
        }

        .guidance-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .guidance-desc {
            font-size: 14.5px !important;
            line-height: 1.6 !important;
            color: #475569 !important;
            margin-bottom: 30px !important;
        }

        .guidance-item {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .guidance-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(161, 122, 66, 0.08);
            color: #a17a42;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .guidance-content h5 {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .guidance-content p {
            font-size: 13px !important;
            line-height: 1.5 !important;
            color: #64748b !important;
            margin-bottom: 0;
        }

        /* Desktop constraints to control image and card heights */
        @media (min-width: 992px) {
            .destination-card {
                height: 400px;
            }
            .destination-card > .row,
            .destination-card > .row > div {
                height: 100%;
            }
            .destination-img-wrapper {
                height: 100%;
            }
        }

         @media (max-width: 991.98px) {
            .destination-info {
                padding: 30px;
            }
            .destination-img-wrapper {
                height: 280px;
            }
            .destination-heading {
                font-size: 24px;
            }
        }

        @media (max-width: 499.98px) {
            .explore-banner {
                padding: 50px 0;
            }
            .explore-title {
                font-size: 24px;
            }
            .explore-lead {
                font-size: 13px !important;
                line-height: 1.5 !important;
            }
            .destination-info {
                padding: 20px 15px;
            }
            .destination-img-wrapper {
                height: 200px;
            }
            .destination-heading {
                font-size: 20px;
                margin-bottom: 8px;
            }
            .destination-desc {
                font-size: 13px !important;
                line-height: 1.6 !important;
                margin-bottom: 18px !important;
            }
            .guidance-box {
                padding: 24px 15px;
                margin-top: 30px;
            }
            .guidance-title {
                font-size: 18px;
            }
            .guidance-desc {
                font-size: 12.5px !important;
                margin-bottom: 20px !important;
            }
            .guidance-item {
                gap: 10px;
                margin-bottom: 15px;
            }
            .guidance-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
            .guidance-content h5 {
                font-size: 13.5px;
            }
            .guidance-content p {
                font-size: 12px !important;
                line-height: 1.4 !important;
            }
        }
    </style>
    <?php include("include/head-scripts.php"); ?>
    
    <!-- Schema Markup JSON-LD for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "TouristAttractionList",
      "name": "Top Local Attractions near Hotel Destin Gwalior",
      "description": "Sightseeing guide of Gwalior Fort, Jai Vilas Palace, and Sun Temple located close to Hotel Destin Gwalior.",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "item": {
            "@type": "TouristAttraction",
            "name": "Gwalior Fort",
            "description": "Historic hill fort known for Man Mandir Palace and iconic blue tiles.",
            "distance": "7.5 km from Hotel Destin"
          }
        },
        {
          "@type": "ListItem",
          "position": 2,
          "item": {
            "@type": "TouristAttraction",
            "name": "Jai Vilas Palace",
            "description": "19th century palace features European architecture and Scindia Museum.",
            "distance": "5.5 km from Hotel Destin"
          }
        },
        {
          "@type": "ListItem",
          "position": 3,
          "item": {
            "@type": "TouristAttraction",
            "name": "Sun Temple Gwalior",
            "description": "Sandstone chariot-shaped temple dedicated to the Sun God.",
            "distance": "3.5 km from Hotel Destin"
          }
        }
      ]
    }
    </script>
</head>
<body>

    <!-- Header Include -->
    <?php include("include/header.php"); ?>

    <main class="main">

        <!-- Banner Header Section -->
        <section class="explore-banner wow fadeIn">
            <div class="container">
                <ul class="breadcrumbs mb-15 justify-content-center" style="padding: 0; background: transparent; display: flex; list-style: none;">
                    <li><a href="index.php" style="color: rgba(255,255,255,0.8); text-decoration: none;">Home</a><span class="arrow-right" style="color: rgba(255,255,255,0.5); margin: 0 8px;"><svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></li>
                    <li><span style="color: #ffffff;">Explore Gwalior</span></li>
                </ul>
                <h1 class="explore-title">Explore Gwalior from DESTIN</h1>
                <p class="explore-lead">
                    Staying at Hotel DESTIN means you're never far from Gwalior's most iconic landmarks. Whether you're here for history, heritage, or spirituality, our central location makes it easy to explore the city without long commutes. Here's what's waiting just minutes away—and yes, we'll help you get there too.
                </p>
            </div>
        </section>

        <!-- Destination Details Grid -->
        <section class="section-box background-body pt-60 pb-40">
            <div class="container container-narrow">
                
                <!-- Destination 1: Gwalior Fort (Image Left, Text Right) -->
                <div class="destination-card" id="gwalior-fort">
                    <div class="row g-0">
                        <div class="col-lg-6 col-12">
                            <div class="destination-img-wrapper">
                                <img src="assets/imgs/page/gwalior_fort.png" alt="Gwalior Fort hill fortress near Hotel Destin" loading="lazy">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="destination-info">
                                <span class="distance-tag">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    7-8 KM FROM DESTIN
                                </span>
                                <h2 class="destination-heading">Gwalior Fort</h2>
                                <p class="destination-desc">
                                    Gwalior Fort is built on top of a hill and is one of the best forts in India. It was built by different kings over many years. Inside, you will see Man Mandir Palace, known for its blue tiles and carvings of elephants and peacocks. The fort also has old Jain statues carved into the rocks nearby. From the fort walls, you can see the whole city of Gwalior below. The view looks best at sunrise or sunset. It is a great place for people who enjoy history, old buildings, and photography.
                                </p>
                                <div>
                                    <a class="btn btn-black-lg" href="rooms.php" style="border-radius: 8px; font-weight: 700; padding: 10px 24px; text-decoration: none;">Book Stay ➔</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination 2: Jai Vilas Palace (Text Left, Image Right on Desktop, Image Top on Mobile) -->
                <div class="destination-card" id="jai-vilas-palace">
                    <div class="row g-0">
                        <div class="col-lg-6 col-12 order-lg-1 order-2">
                            <div class="destination-info">
                                <span class="distance-tag">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    5-6 KM FROM DESTIN
                                </span>
                                <h2 class="destination-heading">Jai Vilas Palace</h2>
                                <p class="destination-desc">
                                    Jai Vilas Palace was built in 1874 and looks like a grand European palace, with tall pillars and rich decoration. Part of the palace is now the Scindia Museum, where visitors can see the famous Durbar Hall. This hall has two of the biggest crystal chandeliers in the world, hanging from a very high ceiling. The museum also shows old royal cars, weapons, and a small silver train once used to serve food to guests at dinner. It is a wonderful place for anyone who enjoys royal history and beautiful, grand architecture.
                                </p>
                                <div>
                                    <a class="btn btn-black-lg" href="rooms.php" style="border-radius: 8px; font-weight: 700; padding: 10px 24px; text-decoration: none;">Book Stay ➔</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12 order-lg-2 order-1">
                            <div class="destination-img-wrapper">
                                <img src="assets/imgs/page/jai_vilas_palace.png" alt="Jai Vilas Palace museum near Hotel Destin" loading="lazy">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Destination 3: Sun Temple (Image Left, Text Right) -->
                <div class="destination-card" id="sun-temple">
                    <div class="row g-0">
                        <div class="col-lg-6 col-12">
                            <div class="destination-img-wrapper">
                                <img src="assets/imgs/page/sun_temple.png" alt="Sun Temple Gwalior near Hotel Destin" loading="lazy">
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="destination-info">
                                <span class="distance-tag">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                    3-4 KM FROM DESTIN
                                </span>
                                <h2 class="destination-heading">Sun Temple</h2>
                                <p class="destination-desc">
                                    The Sun Temple in Gwalior is designed to look like the famous Sun Temple in Konark, Odisha. It is made of red stone and shaped like a chariot, with carved wheels and horses on the sides. Inside the temple, there is a golden statue of Surya, the Sun God. Around the temple, you will find neat gardens, clean paths, and quiet corners to sit and relax. It is a calm and peaceful place, perfect for a short visit, evening walk, or simply enjoying nature and simple architecture.
                                </p>
                                <div>
                                    <a class="btn btn-black-lg" href="rooms.php" style="border-radius: 8px; font-weight: 700; padding: 10px 24px; text-decoration: none;">Book Stay ➔</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guidance & Local Commute Assistance Block -->
                <div class="guidance-box wow fadeInUp">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-12">
                            <h3 class="guidance-title">Stay at DESTIN, Stay Close to It All</h3>
                            <p class="guidance-desc">
                                At Hotel DESTIN, convenience meets comfort. All three of Gwalior's top attractions are just a short ride away meaning less time traveling and more time experiencing the city.
                            </p>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="row">
                                <!-- Guide item 1 -->
                                <div class="col-sm-6 col-12">
                                    <div class="guidance-item">
                                        <div class="guidance-icon">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        </div>
                                        <div class="guidance-content">
                                            <h5>Cab Arrangements</h5>
                                            <p>Local cab and auto-rickshaw setups arranged directly at front desk.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Guide item 2 -->
                                <div class="col-sm-6 col-12">
                                    <div class="guidance-item">
                                        <div class="guidance-icon">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>
                                        </div>
                                        <div class="guidance-content">
                                            <h5>Sightseeing Guidance</h5>
                                            <p>Maps, ticket rates, timing information, and local route assistance.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Guide item 3 -->
                                <div class="col-sm-6 col-12">
                                    <div class="guidance-item">
                                        <div class="guidance-icon">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                        </div>
                                        <div class="guidance-content">
                                            <h5>Custom Itineraries</h5>
                                            <p>Tailored schedules based on your check-out times and personal travel plan.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Guide item 4 -->
                                <div class="col-sm-6 col-12">
                                    <div class="guidance-item">
                                        <div class="guidance-icon">
                                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <div class="guidance-content">
                                            <h5>Best Timing Help</h5>
                                            <p>Expert tips on the best times of day to visit to beat crowds and heat.</p>
                                        </div>
                                    </div>
                                </div>
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
    <script src="assets/js/plugins/masonry.min.js"></script>
    <script src="assets/js/plugins/scrollup.js"></script>
    <script src="assets/js/plugins/wow.js"></script>
    <script src="assets/js/plugins/waypoints.js"></script>
    <script src="assets/js/plugins/counterup.js"></script>
    <script src="assets/js/plugins/dark.js"></script>
    <!-- Custom template script -->
    <script src="assets/js/maine209.js?v=1.0.0"></script>
</body>
</html>
