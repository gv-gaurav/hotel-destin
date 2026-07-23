<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="msapplication-TileColor" content="#0E0E0E">
    <meta name="template-color" content="#0E0E0E">
    <meta name="description" content="Discover Hotel Destin, a welcoming Gwalior luxury hotel on Sachin Tendulkar Road offering comfort, hygiene, and excellent location.">
    <meta name="keywords" content="Hotel Destin Gwalior, hotels in Gwalior, luxury hotels Gwalior, Gwalior stay, booking partner">
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
    <link href="assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>About Us - Hotel Destin Gwalior</title>

    <style>
        /* Custom Premium About Page Styles */
        .about-banner {
            background: linear-gradient(rgba(14, 14, 14, 0.6), rgba(14, 14, 14, 0.6)), url('assets/imgs/page/hotel/banner-hotel.png') no-repeat center center;
            background-size: cover;
            padding: 80px 0;
            color: #ffffff;
            text-align: center;
        }

        .about-title {
            font-size: 42px;
            font-weight: 500;
            color: #ffffff;
            letter-spacing: -1px;
            margin-bottom: 12px;
        }

        .about-location-link {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .about-location-link:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Image Overlapping Gallery Section */
        .about-image-overlap-wrapper {
            position: relative;
            width: 100%;
            height: 420px;
            margin-bottom: 30px;
        }

        .about-img-main {
            position: absolute;
            top: 0;
            left: 0;
            width: 75%;
            height: 340px;
            z-index: 1;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .about-img-sub {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 50%;
            height: 240px;
            z-index: 2;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0,0,0,0.15);
            border: 5px solid #ffffff;
        }

        .about-image-overlap-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .about-image-overlap-wrapper div:hover img {
            transform: scale(1.06);
        }

        /* Highlights Cards */
        .about-highlight-item {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .about-highlight-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .highlight-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: var(--neutral-100, #f8f9fa);
            color: var(--neutral-1000, #0E0E0E);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .about-highlight-item:hover .highlight-icon {
            background-color: var(--primary, #0E0E0E);
            color: #ffffff;
            transform: scale(1.05);
        }

        .highlight-info h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--neutral-1000, #0E0E0E);
        }

        .highlight-info p {
            font-size: 14.5px;
            line-height: 1.5;
            color: var(--neutral-500, #6c757d);
            margin-bottom: 0;
        }

        /* Amenities Grid Styling */
        .amenities-section {
            background-color: var(--neutral-100, #f8f9fa);
            border-top: 1px solid var(--neutral-200, #e9ecef);
            border-bottom: 1px solid var(--neutral-200, #e9ecef);
        }

        .amenity-category-box {
            background: #ffffff;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid rgba(0,0,0,0.04);
            height: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.01);
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .amenity-category-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.06);
            border-color: rgba(14,14,14,0.08);
        }

        .amenity-category-title {
            font-size: 17px;
            font-weight: 600;
            color: var(--neutral-1000, #0E0E0E);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .amenity-category-title svg {
            color: var(--primary, #0E0E0E);
        }

        .amenity-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .amenity-item {
            font-size: 13.5px;
            color: var(--neutral-600, #4f5e71);
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            background-color: var(--neutral-100, #f8f9fa);
            border-radius: 8px;
            transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
            text-transform: capitalize;
            font-weight: 500;
        }

        .amenity-item:hover {
            background-color: var(--neutral-1000, #0E0E0E);
            color: #ffffff;
            transform: translateX(4px);
        }

        .amenity-item svg {
            color: var(--neutral-500, #6c757d);
            transition: color 0.25s ease;
            flex-shrink: 0;
        }

        .amenity-item:hover svg {
            color: #ffffff;
        }

        /* Tight section utility */
        .py-tight {
            padding-top: 50px !important;
            padding-bottom: 50px !important;
        }

        /* Responsive Media Queries */
        @media (max-width: 768px) {
            .about-banner {
                padding: 45px 0;
            }
            .about-title {
                font-size: 28px;
            }
            .about-location-link {
                font-size: 13.5px;
                flex-wrap: wrap;
                justify-content: center;
            }
            .about-image-overlap-wrapper {
                height: 320px;
                margin-top: 10px;
            }
            .about-img-main {
                width: 80%;
                height: 250px;
            }
            .about-img-sub {
                width: 55%;
                height: 170px;
                border-width: 3px;
            }
            .py-tight {
                padding-top: 30px !important;
                padding-bottom: 30px !important;
            }
            .about-highlight-item {
                gap: 15px;
                margin-bottom: 20px;
                padding-bottom: 15px;
            }
            .highlight-icon {
                width: 42px;
                height: 42px;
                font-size: 18px;
            }
            .highlight-info h4 {
                font-size: 16px;
            }
            .highlight-info p {
                font-size: 13.5px;
            }
            @media (max-width: 768px) {
                .map-container iframe {
                    height: 320px !important;
                }
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
        <section class="about-banner wow fadeIn">
            <div class="container">
                <ul class="breadcrumbs mb-15 justify-content-center" style="padding: 0; background: transparent; display: flex;">
                    <li><a href="index.php" style="color: rgba(255,255,255,0.8);">Home</a><span class="arrow-right" style="color: rgba(255,255,255,0.5);"><svg width="7" height="12" viewBox="0 0 7 12" fill="none"><path d="M1 11L6 6L1 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></span></li>
                    <li><span style="color: #ffffff;">About Us</span></li>
                </ul>
                <h1 class="about-title">Hotel Destin</h1>
                <a href="contact.php#map" class="about-location-link">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    Sachin Tendulkar Rd, Kailash Nagar, Ramanuj Nagar, Gwalior, MP 474011
                    <span style="text-decoration: underline; font-weight: 600; margin-left: 5px;">View on map</span>
                </a>
            </div>
        </section>

        <!-- Description Section with Highlights -->
        <section class="section-box background-body py-tight">
            <div class="container">
                <div class="row g-5 align-items-center">
                    
                    <!-- Left: Overlapping Layered Images -->
                    <div class="col-lg-6 col-12">
                        <div class="about-image-overlap-wrapper">
                            <div class="about-img-main wow fadeInUp">
                                <img src="assets/imgs/page/hotel/img-vision.png" alt="Hotel Destin Room Setup">
                            </div>
                            <div class="about-img-sub wow fadeInUp" data-wow-delay="0.2s">
                                <img src="assets/imgs/page/hotel/hotelRoom2.png" alt="Hotel Destin Suite Layout">
                            </div>
                        </div>
                    </div>

                    <!-- Right: Info Highlights -->
                    <div class="col-lg-6 col-12">
                        <div class="about-details-content wow fadeInUp" data-wow-delay="0.1s">
                            <h2 class="neutral-1000 font-heading mb-25" style="font-size: 30px; font-weight: 500; line-height: 1.25;">Comfort & Central Convenience In Gwalior</h2>
                            
                            <!-- Highlight Item 1 -->
                            <div class="about-highlight-item">
                                <div class="highlight-icon">
                                    <!-- Home/Hotel Icon -->
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <div class="highlight-info">
                                    <h4>Welcoming Luxury stay</h4>
                                    <p>Hotel Destin is designed for guests who value comfort and cleanliness. Located on Sachin Tendulkar Road, connect easily to Gwalior business hubs, shopping, and landmarks.</p>
                                </div>
                            </div>

                            <!-- Highlight Item 2 -->
                            <div class="about-highlight-item">
                                <div class="highlight-icon">
                                    <!-- Room/Hygiene Icon -->
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </div>
                                <div class="highlight-info">
                                    <h4>Spacious & Hygienic Rooms</h4>
                                    <p>Each room offers comfortable bedding, clean bathrooms, and centralized air conditioning. Daily housekeeping is prioritized for a safe, restful, and pleasant stay.</p>
                                </div>
                            </div>

                            <!-- Highlight Item 3 -->
                            <div class="about-highlight-item">
                                <div class="highlight-icon">
                                    <!-- Star Icon -->
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                </div>
                                <div class="highlight-info">
                                    <h4>Trusted Quality Service</h4>
                                    <p>Highly recommended by guests for our polite staff, smooth check-in process, secure parking, power backups, and convenient in-room dining options.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Amenities Section with SVG Icons -->
        <section class="amenities-section section-box py-tight">
            <div class="container">
                <div class="text-center mb-45 wow fadeInUp">
                    <h2 class="neutral-1000 font-heading" style="font-size: 30px; font-weight: 500;">Premium Services We Offer</h2>
                    <p class="text-md neutral-500 max-width-600 mx-auto mt-10">
                        Explore our extensive features, designed to make your corporate or leisure stay absolutely comfortable.
                    </p>
                </div>

                <div class="row g-4">
                    <!-- Dining & Leisure -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="amenity-category-box wow fadeInUp">
                            <h3 class="amenity-category-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                Dining & Leisure
                            </h3>
                            <ul class="amenity-list">
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    restaurant
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22h20M4 22V11a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v11M12 9V5a2 2 0 0 0-2-2H8"/></svg>
                                    lounge
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M12 2v9M8 5h8"/></svg>
                                    barbeque
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2M9 9h.01M15 9h.01"/></svg>
                                    kid's menu
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Hospitality & Care -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="amenity-category-box wow fadeInUp" data-wow-delay="0.05s">
                            <h3 class="amenity-category-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                Hospitality & Care
                            </h3>
                            <ul class="amenity-list">
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.58 16.14a7 7 0 0 1 10.84 0M12 20h.01"/></svg>
                                    free wi-fi
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z"/><path d="M12 6v6l4 2"/></svg>
                                    room service (limited)
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M7 12h10M12 7v10"/></svg>
                                    housekeeping
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8"/></svg>
                                    laundry
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M16 6V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                                    luggage assistance
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    multilingual staff
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                                    doctor on call
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                    concierge
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Room Comforts -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="amenity-category-box wow fadeInUp" data-wow-delay="0.1s">
                            <h3 class="amenity-category-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                Comfort & Rooms
                            </h3>
                            <ul class="amenity-list">
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    ac centralized
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 2v20M9 5h6M9 10h6"/></svg>
                                    refrigerator
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4zM4 9h16M9 4v16"/></svg>
                                    newspaper
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 10h-6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2z"/><path d="M14 6a3 3 0 0 1-3-3M6 18H2"/></svg>
                                    smoking rooms
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 0 1 10 10c0 5.523-4.477 10-10 10S2 17.523 2 12A10 10 0 0 1 12 2zM12 6v8h6"/></svg>
                                    balcony/ terrace
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M12 2a10 10 0 0 1 10 10M12 2A10 10 0 0 0 2 12"/></svg>
                                    umbrellas
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Safety & Security -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="amenity-category-box wow fadeInUp" data-wow-delay="0.15s">
                            <h3 class="amenity-category-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                Safety & Security
                            </h3>
                            <ul class="amenity-list">
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                                    CCTV
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M12 2v9M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                    security guard
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/><path d="M12 8v8M8 12h8"/></svg>
                                    smoke detector (lobby)
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    fire extinguishers
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                    security alarms
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Core Facilities -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="amenity-category-box wow fadeInUp" data-wow-delay="0.2s">
                            <h3 class="amenity-category-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                Core Facilities
                            </h3>
                            <ul class="amenity-list">
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="22" height="13" rx="2"/><path d="M12 16v6M8 22h8"/></svg>
                                    parking
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/><path d="M12 2v20M9 6h6"/></svg>
                                    power backup
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg>
                                    elevator/ lift
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 0 1 10 10c0 5.523-4.477 10-10 10S2 17.523 2 12A10 10 0 0 1 12 2zM12 6v8h6"/></svg>
                                    sun deck
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9z"/></svg>
                                    outdoor furniture
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 1 13v3c0 .6.4 1 1 1h2m10 0h4m-12 0a3 3 0 1 1 6 0m6 0a3 3 0 1 1 6 0"/></svg>
                                    airport transfers (paid)
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Business Events -->
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="amenity-category-box wow fadeInUp" data-wow-delay="0.25s">
                            <h3 class="amenity-category-title">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/><line x1="17" y1="2" x2="17" y2="22"/><line x1="2" y1="12" x2="22" y2="12"/><line x1="2" y1="7" x2="7" y2="7"/><line x1="2" y1="17" x2="7" y2="17"/><line x1="17" y1="17" x2="22" y2="17"/><line x1="17" y1="7" x2="22" y2="7"/></svg>
                                Business Events
                            </h3>
                            <ul class="amenity-list">
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="M7 12h10M12 7v10"/></svg>
                                    banquet
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/><line x1="7" y1="2" x2="7" y2="22"/></svg>
                                    conference room
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                                    business center
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
                                    printer
                                </li>
                                <li class="amenity-item">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22V4c0-.5.2-1 .6-1.4C5 2.2 5.5 2 6 2h8l6 6v14M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                                    photocopying
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Google Maps Location Section -->
        <section class="section-box location-section py-tight" id="map" style="background-color: #ffffff; border-top: 1px solid #e9ecef;">
            <div class="container">
                <div class="text-center mb-40 wow fadeInUp">
                    <h2 class="neutral-1000 font-heading" style="font-size: 30px; font-weight: 500;">Find Us In Gwalior</h2>
                    <p class="text-md neutral-500 max-width-600 mx-auto mt-10">
                        Conveniently located on Sachin Tendulkar Road. Check the map below for directions.
                    </p>
                </div>
                
                <div class="map-container wow fadeInUp" style="border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e9ecef; line-height: 0;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d894.9536802284084!2d78.20265274966889!3d26.202704763374193!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3976c38e096f2457%3A0xdf2b8e952f5cd731!2sHotel%20DESTIN%20GWALIOR!5e0!3m2!1sen!2sin!4v1783328622750!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
