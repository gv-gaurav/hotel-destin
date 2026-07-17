<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/template/favicon.png">
  <link href="assets/css/stylee209.css?v=1.0.1" rel="stylesheet">
  <title>Welcome to Hotel Destin</title>
  <style>
    .list-ticks-green {
      list-style: none;
      padding-left: 0;
      margin-top: 16px
    }

    .list-ticks-green li {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .list-ticks-green li svg {
      flex-shrink: 0;
      margin-right: 10px;
    }

    /* Custom Native Date Inputs styling */
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
    }

    /* ── Mobile date placeholder fix ───────────────────────────────
     On mobile, appearance:none strips the native "dd-mm-yyyy" hint.
     We inject a <span class="date-ph"> via JS and show/hide it.
  ──────────────────────────────────────────────────────────────── */
    .date-ph {
      display: none;
      /* hidden on desktop — browser shows native hint */
      position: absolute;
      left: 24px;
      /* icon(16px) + gap(8px) default; overridden precisely by JS */
      top: 50%;
      transform: translateY(-50%);
      color: #a0aec0;
      font-size: 13px;
      font-weight: 500;
      pointer-events: none;
      user-select: none;
      white-space: nowrap;
      z-index: 2;
    }

    @media (max-width: 767px) {

      /* Show the injected placeholder only on mobile */
      .date-ph {
        display: block;
      }

      /* When input has a value, placeholder is hidden by JS (display:none inline) */
      /* When input is focused, we also hide it via JS */
      /* Make empty input text transparent so only our span shows */
      .box-calendar-date input[type="date"].date-empty {
        color: transparent !important;
      }

      .box-calendar-date input[type="date"].date-empty:focus {
        color: inherit !important;
        /* show cursor position when typing */
      }
    }

    /* Homepage search form layout overrides for 4 columns */
    .box-search-advance .box-bottom-search {
      position: relative;
      display: flex;
      flex-wrap: nowrap !important;
      align-items: center;
      justify-content: space-between;
      padding: 12px 20px !important;
      border-radius: 16px;
      border: 1px solid transparent;
      background: rgba(6, 9, 14, 0.82) !important;
      backdrop-filter: blur(10px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);

      box-shadow:
        0 10px 35px rgba(0, 0, 0, 0.55),
        inset 0 1px 0 rgba(255, 255, 255, 0.06),
        inset 0 -1px 0 rgba(224, 184, 93, 0.08);

      z-index: 1;
    }

    .box-search-advance .box-bottom-search::before {
      content: "";
      position: absolute;
      inset: 0;
      padding: 1.5px;
      border-radius: inherit;

      background: linear-gradient(120deg,
          #F2D487,
          #EBC878,
          #E0B85C,
          #F2D487,
          #EBC878,
          #E0B85C);

      background-size: 300% 300%;
      animation: borderGlow 6s linear infinite;

      -webkit-mask:
        linear-gradient(#fff 0 0) content-box,
        linear-gradient(#fff 0 0);
      -webkit-mask-composite: xor;
      mask-composite: exclude;

      pointer-events: none;
    }

    @keyframes borderGlow {
      0% {
        background-position: 0% 50%;
      }

      100% {
        background-position: 300% 50%;
      }
    }

    .box-search-advance .box-bottom-search .item-search {
      width: 27% !important;
      padding: 5px 20px !important;
      border: none !important;
      position: relative;
      border-right: 1px solid #e2e8f0 !important;
    }

    .box-search-advance .box-bottom-search .item-search::before {
      display: none !important;
    }

    .box-search-advance .box-bottom-search .item-search:nth-child(3) {
      width: 35% !important;
      border-right: none !important;
    }

    @media (max-width: 991px) {
      .box-search-advance .box-bottom-search {
        flex-direction: column !important;
        align-items: stretch !important;
        padding: 15px !important;
      }

      .box-search-advance .box-bottom-search .item-search {
        border-right: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 4px 10px !important;
        width: 100% !important;
      }

      .box-search-advance .box-bottom-search .item-search.bd-none {
        border-bottom: none !important;
        width: 100% !important;
        justify-content: center !important;
        margin-top: 15px;
        padding: 0 !important;
      }

      .box-search-advance .box-bottom-search .btn-black-lg {
        width: 100% !important;
      }
    }
  </style>
  <?php include("include/head-scripts.php"); ?>
</head>

<body>

  <?php include "include/header.php"; ?>
  <main class="main">
    <section class="section-box box-banner-home7 background-body">
      <div class="container"></div>
      <div class="container-banner-home7">
        <div class="box-swiper">
          <div class="item-banner-slide" style="background-image: url(<?= htmlspecialchars(get_setting('hero_bg_image', 'assets/imgs/page/homepage7/banner.png')) ?>)">
            <div class="container upper-space">
              <p class="upper-top-bar">Welcome to Hotel Destin</p>
              <h1 class="mt-10 color-white"><?= htmlspecialchars(get_setting('hero_title', 'Experience Luxury & Comfort')) ?></h1>
              <h5 class="heading-sub mt-1"><?= htmlspecialchars(get_setting('hero_subtitle', 'in the heart of Gwalior')) ?></h5>
              <ul class="list-ticks-green">
                <li>
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#28a745" />
                    <path d="M8 12.5L10.5 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  Elegant Rooms
                </li>
                <li>
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#28a745" />
                    <path d="M8 12.5L10.5 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  Fine Dining
                </li>
                <li>
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#28a745" />
                    <path d="M8 12.5L10.5 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  Banquet
                </li>
                <li>
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="12" fill="#28a745" />
                    <path d="M8 12.5L10.5 15L16 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  Luxury Amenities
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="container-search-advance">
        <div class="container">
          <div class="box-search-advance wow fadeInUp">
            <form method="GET" action="rooms.php" style="width: 100%;">
              <input type="hidden" name="adults" id="hidden_adults" value="2">
              <input type="hidden" name="children" id="hidden_children" value="0">

              <div class="box-bottom-search background-card">
                <div class="item-search">
                  <label class="text-sm-bold neutral-500">Check-in Date</label>
                  <div class="box-calendar-date d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input class="search-input" type="date" id="checkin_date" name="checkin" min="<?php echo date('Y-m-d'); ?>" style="padding-left: 0; background: transparent; border: none; font-weight: 700; color: #e5e5e5; cursor: pointer; outline: none; width: 100%;">
                  </div>
                </div>
                <div class="item-search item-search-2">
                  <label class="text-sm-bold neutral-500">Check-out Date</label>
                  <div class="box-calendar-date d-flex align-items-center gap-2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                      <line x1="16" y1="2" x2="16" y2="6"></line>
                      <line x1="8" y1="2" x2="8" y2="6"></line>
                      <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input class="search-input" type="date" id="checkout_date" name="checkout" min="<?php echo date('Y-m-d'); ?>" style="padding-left: 0; background: transparent; border: none; font-weight: 700; color: #e5e5e5; cursor: pointer; outline: none; width: 100%;">
                  </div>
                </div>
                <div class="item-search item-search-3">
                  <label class="text-sm-bold neutral-500">Guests & Rooms</label>
                  <div class="dropdown dropdown-guests-rooms">
                    <button class="btn btn-secondary dropdown-toggle btn-dropdown-search d-flex align-items-center gap-2" type="button" id="dropdownGuestsBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="padding-left: 0; background: transparent; border: none; width: 100%;">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#a17a42" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                      </svg>
                      <span class="guests-summary-text" style="font-weight: 700; color: #e5e5e5;">1 Room, 2 Guests</span>
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
                              <span class="text-muted" style="font-size: 11px; display: block; text-align: left;">(Above 12 years)</span>
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
                              <span class="text-muted" style="font-size: 11px; display: block; text-align: left;">(Below 12 years)</span>
                            </div>
                            <div class="d-flex align-items-center border rounded overflow-hidden">
                              <button class="btn btn-sm btn-light py-1 px-3 dec-btn" type="button" style="border: none; font-weight: bold; background: #f8fafc; font-size: 14px;">−</button>
                              <span class="px-3 py-1 child-count" style="font-weight: 600; min-width: 30px; text-align: center;">0</span>
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
                <div class="item-search bd-none d-flex justify-content-end align-items-center" style="padding: 0 10px; min-width: 170px;">
                  <button type="submit" class="btn btn-black-lg text-nowrap d-flex align-items-center justify-content-center" style="width: 100%; border-radius: 10px; font-weight: 700; padding: 12px 20px; border: none; height: 46px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                      <path d="M9 11L12 14L22 4M21 12V19C21 20.1 20.1 21 19 21H5C3.9 21 3 20.1 3 19V5C3 3.9 3.9 3 5 3H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>Check Availability
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>










    <section class="section-box box-top-rated-3 background-body">
      <div class="container">
        <div class="row align-items-end mt-3">
          <div class="col-md-6 wow fadeInUp">
            <h2 class="neutral-1000">Our Luxury Rooms & Suites</h2>
            <p class="text-xl-medium neutral-500">Spacious, clean, and centrally located comfort in Gwalior.</p>
          </div>
          <div class="col-md-6 wow fadeInUp">
            <div class="d-flex justify-content-end align-items-center">
              <a class="btn btn-black-lg" href="rooms.php">View More
                <svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 15L15 8L8 1M15 8L1 8" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="container mt-30">
        <div class="box-swiper position-relative">
          <div class="swiper-container swiper-group-rooms-custom">
            <div class="swiper-wrapper">
              <!-- Room 1: Standard Room -->
              <div class="swiper-slide">
                <div class="card-room-destin">
                  <div class="image-box">
                    <span class="badge-premier">Standard</span>
                    <span class="badge-discount">Best Seller</span>

                    <!-- Navigation Arrows Overlay -->
                    <div class="nav-arrow left">&lt;</div>
                    <div class="nav-arrow right">&gt;</div>

                    <!-- Pagination Dots Overlay -->
                    <div class="dots-container">
                      <div class="dot active"></div>
                      <div class="dot"></div>
                    </div>

                    <img src="assets/imgs/page/room/room.png" alt="Standard Room">
                  </div>
                  <div class="content-box">
                    <div class="title-row">
                      <a href="room-details.html">Standard Room - Hotel Destin</a>
                    </div>

                    <div class="meta-row">
                      <div class="location-text">
                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M6 0C2.68 0 0 2.68 0 6C0 10.5 6 14 6 14C6 14 12 10.5 12 6C12 2.68 9.32 0 6 0ZM6 8.5C4.62 8.5 3.5 7.38 3.5 6C3.5 4.62 4.62 3.5 6 3.5C7.38 3.5 8.5 4.62 8.5 6C8.5 7.38 7.38 8.5 6 8.5Z" fill="#64748B" />
                        </svg>
                        Sachin Tendulkar Rd, Gwalior
                      </div>
                      <div class="rating-box">
                        <span>G</span> <span>4.8</span> <span>★</span>
                      </div>
                    </div>

                    <div class="tags-row">
                      <span class="tag-pill">Free Wi-Fi</span>
                      <span class="tag-pill">Mineral Water</span>
                      <span class="tag-pill gym">Standard Only</span>
                      <span class="tag-pill more">+3 more</span>
                    </div>

                    <div class="amenities-row">
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/air-conditioner.svg" alt="AC">
                        AC
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/wifi.svg" alt="Wifi">
                        Free Wi-Fi
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/loundry.svg" alt="Laundry">
                        Laundry
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/bed.svg" alt="King Bed">
                        King Bed
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/safety-box.svg" alt="Safe">
                        Safe Box
                      </div>
                    </div>

                    <div class="banner-box">
                      <i class="fa fa-star">⭐</i> Get Destin, and get 25% off (up to ₹1,000) on your booking
                    </div>

                    <div class="footer-row">
                      <div class="price-area">
                        <div class="old-price-line">
                          <span class="old-price">₹2,300</span>
                          <span class="discount-lbl">26% off</span>
                          <span class="brand-lbl">DESTIN</span>
                        </div>
                        <div class="current-price">₹1,700 <span>/ night</span></div>
                      </div>
                      <a href="room-detail.php?room=standard-room&checkin=&checkout=&adults=2&children=0" class="book-btn">Book Room</a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Room 2: Executive Room -->
              <div class="swiper-slide">
                <div class="card-room-destin">
                  <div class="image-box">
                    <span class="badge-premier">Executive</span>
                    <span class="badge-discount">New Launch Discount</span>

                    <!-- Navigation Arrows Overlay -->
                    <div class="nav-arrow left">&lt;</div>
                    <div class="nav-arrow right">&gt;</div>

                    <!-- Pagination Dots Overlay -->
                    <div class="dots-container">
                      <div class="dot active"></div>
                      <div class="dot"></div>
                    </div>

                    <img src="assets/imgs/page/room/room2.png" alt="Executive Room">
                  </div>
                  <div class="content-box">
                    <div class="title-row">
                      <a href="room-details.html">Executive Room - Hotel Destin</a>
                    </div>

                    <div class="meta-row">
                      <div class="location-text">
                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M6 0C2.68 0 0 2.68 0 6C0 10.5 6 14 6 14C6 14 12 10.5 12 6C12 2.68 9.32 0 6 0ZM6 8.5C4.62 8.5 3.5 7.38 3.5 6C3.5 4.62 4.62 3.5 6 3.5C7.38 3.5 8.5 4.62 8.5 6C8.5 7.38 7.38 8.5 6 8.5Z" fill="#64748B" />
                        </svg>
                        Sachin Tendulkar Rd, Gwalior
                      </div>
                      <div class="rating-box">
                        <span>G</span> <span>4.9</span> <span>★</span>
                      </div>
                    </div>

                    <div class="tags-row">
                      <span class="tag-pill">Free Wi-Fi</span>
                      <span class="tag-pill">Mineral Water</span>
                      <span class="tag-pill gym">Executive Lounge</span>
                      <span class="tag-pill more">+4 more</span>
                    </div>

                    <div class="amenities-row">
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/air-conditioner.svg" alt="AC">
                        AC
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/wifi.svg" alt="Wifi">
                        Free Wi-Fi
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/loundry.svg" alt="Laundry">
                        Laundry
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/bed.svg" alt="King Bed">
                        King Bed
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/safety-box.svg" alt="Safe">
                        Safe Box
                      </div>
                    </div>

                    <div class="banner-box">
                      <i class="fa fa-star">⭐</i> Get Destin, and get 25% off (up to ₹1,000) on your booking
                    </div>

                    <div class="footer-row">
                      <div class="price-area">
                        <div class="old-price-line">
                          <span class="old-price">₹2,000</span>
                          <span class="discount-lbl">15% off</span>
                          <span class="brand-lbl">DESTIN</span>
                        </div>
                        <div class="current-price">₹1,700 <span>/ night</span></div>
                      </div>
                      <a href="room-detail.php?room=executive-room&checkin=&checkout=&adults=2&children=0" class="book-btn">Book Room</a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Room 3: Premium Room -->
              <div class="swiper-slide">
                <div class="card-room-destin">
                  <div class="image-box">
                    <span class="badge-premier">Premium</span>
                    <span class="badge-discount">Luxury Choice</span>

                    <!-- Navigation Arrows Overlay -->
                    <div class="nav-arrow left">&lt;</div>
                    <div class="nav-arrow right">&gt;</div>

                    <!-- Pagination Dots Overlay -->
                    <div class="dots-container">
                      <div class="dot active"></div>
                      <div class="dot"></div>
                    </div>

                    <img src="assets/imgs/page/room/room3.png" alt="Premium Room">
                  </div>
                  <div class="content-box">
                    <div class="title-row">
                      <a href="room-details.html">Premium Room - Hotel Destin</a>
                    </div>

                    <div class="meta-row">
                      <div class="location-text">
                        <svg width="12" height="14" viewBox="0 0 12 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M6 0C2.68 0 0 2.68 0 6C0 10.5 6 14 6 14C6 14 12 10.5 12 6C12 2.68 9.32 0 6 0ZM6 8.5C4.62 8.5 3.5 7.38 3.5 6C3.5 4.62 4.62 3.5 6 3.5C7.38 3.5 8.5 4.62 8.5 6C8.5 7.38 7.38 8.5 6 8.5Z" fill="#64748B" />
                        </svg>
                        Sachin Tendulkar Rd, Gwalior
                      </div>
                      <div class="rating-box">
                        <span>G</span> <span>5.0</span> <span>★</span>
                      </div>
                    </div>

                    <div class="tags-row">
                      <span class="tag-pill">Free Wi-Fi</span>
                      <span class="tag-pill">Mineral Water</span>
                      <span class="tag-pill gym">Free Breakfast</span>
                      <span class="tag-pill more">+5 more</span>
                    </div>

                    <div class="amenities-row">
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/air-conditioner.svg" alt="AC">
                        AC
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/wifi.svg" alt="Wifi">
                        Free Wi-Fi
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/loundry.svg" alt="Laundry">
                        Laundry
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/bed.svg" alt="King Bed">
                        King Bed
                      </div>
                      <div class="amenity-col">
                        <img src="assets/imgs/page/room/safety-box.svg" alt="Safe">
                        Safe Box
                      </div>
                    </div>

                    <div class="banner-box">
                      <i class="fa fa-star">⭐</i> Get Destin, and get 25% off (up to ₹1,000) on your booking
                    </div>

                    <div class="footer-row">
                      <div class="price-area">
                        <div class="old-price-line">
                          <span class="old-price">₹3,000</span>
                          <span class="discount-lbl">15% off</span>
                          <span class="brand-lbl">DESTIN</span>
                        </div>
                        <div class="current-price">₹2,550 <span>/ night</span></div>
                      </div>
                      <a href="room-detail.php?room=premium-room&checkin=&checkout=&adults=2&children=0" class="book-btn">Book Room</a>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Mobile/Tablet navigation buttons centered under the cards -->
              <div class="d-flex justify-content-center align-items-center gap-3 mt-20 d-lg-none">
                <div class="swiper-button-prev swiper-button-prev-style-1 swiper-button-prev-rooms" style="position: static; margin: 0; width: 44px; height: 44px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M7.99992 3.33325L3.33325 7.99992M3.33325 7.99992L7.99992 12.6666M3.33325 7.99992H12.6666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </div>
                <div class="swiper-button-next swiper-button-next-style-1 swiper-button-next-rooms" style="position: static; margin: 0; width: 44px; height: 44px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M7.99992 12.6666L12.6666 7.99992L7.99992 3.33325M12.6666 7.99992L3.33325 7.99992" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>






    <section class="section-box box-slide-banner background-body pt-30">
      <div class="container">
        <div class="row">
          <!-- Card 1: Fine Dining Experience -->
          <div class="col-lg-4 col-md-6 mb-30 wow fadeInUp">
            <div class="card-slide-banner-custom" style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=600&auto=format&fit=crop&q=80');">
              <div class="card-custom-content">
                <h4 class="text-white mb-10">Fine Dining Experience</h4>
                <p class="text-white mb-20 opacity-75">Savor the finest multi-cuisine dishes prepared by expert chefs in a luxurious ambiance.</p>
                <a class="btn-explore-custom" href="restaurant.php">EXPLORE RESTAURANT</a>
              </div>
            </div>
          </div>
          <!-- Card 2: Banquet & Events -->
          <div class="col-lg-4 col-md-6 mb-30 wow fadeInUp">
            <div class="card-slide-banner-custom" style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=600&auto=format&fit=crop&q=80');">
              <div class="card-custom-content">
                <h4 class="text-white mb-10">Banquet & Events</h4>
                <p class="text-white mb-20 opacity-75">Perfect venues for weddings, corporate events, parties and gatherings of all sizes.</p>
                <a class="btn-explore-custom" href="banquet.php">EXPLORE BANQUET</a>
              </div>
            </div>
          </div>
          <!-- Card 3: Airport Transfer -->
          <div class="col-lg-4 col-md-6 mb-30 wow fadeInUp">
            <div class="card-slide-banner-custom" style="background-image: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.65)), url('https://images.unsplash.com/photo-1563720223185-11003d516935?w=600&auto=format&fit=crop&q=80');">
              <div class="card-custom-content">
                <h4 class="text-white mb-10">Airport & Station Transfer</h4>
                <p class="text-white mb-20 opacity-75">Luxury pick-up & drop facility from airport or railway station. Safe, reliable & on-time.</p>
                <a class="btn-explore-custom" href="airport-transfer.php">RESERVE NOW</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>










    <section class="section-box box-flights background-body">
      <div class="container">
        <div class="row align-items-end">
          <div class="col-md-12 wow fadeInUp text-center">
            <h2 class="neutral-1000">Hotel Destin Amenities & Features</h2>
            <p class="text-xl-medium neutral-500">Everything you need for a comfortable, clean, and luxurious stay in Gwalior.</p>
          </div>
        </div>
        <div class="row mt-30">
          <!-- Amenity 1: Restaurant -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-1" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/food.svg" alt="Restaurant" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Restaurant & Dining</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Delicious in-room dining & restaurant choices nearby.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 2: Free Wi-Fi -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-2" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/wifi.svg" alt="Wi-Fi" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Free High-Speed Wi-Fi</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Stay connected with high-speed internet throughout.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 3: Centralized AC -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-3" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/air-conditioner.svg" alt="AC" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Centralized AC</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Fully air-conditioned rooms for a pleasant stay.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 4: Secure Parking -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-4" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/safety-box.svg" alt="Parking" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Secure Parking</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Private and secure parking spaces for all guests.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 5: Banquet Hall -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-5" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/living.svg" alt="Banquet" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Banquet & Events</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Spacious halls perfect for weddings, parties, & events.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 6: CCTV & Security -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-6" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/safety-box.svg" alt="Security" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">CCTV & Guards</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">24/7 security guarding and round-the-clock CCTV.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 7: Business Center -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-7" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/airport.svg" alt="Business Support" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Business Services</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Printer, photocopying, and meeting spaces available.</p>
              </div>
            </div>
          </div>
          <!-- Amenity 8: Power Backup & Lift -->
          <div class="col-lg-3 col-md-6 col-sm-6 mb-20 wow fadeInUp">
            <div class="card-amenity-compact d-flex align-items-center p-3 background-card rounded-4 border hover-up">
              <div class="card-icon-circle p-2 rounded-circle me-3 d-flex align-items-center justify-content-center background-8" style="width: 48px; height: 48px; min-width: 48px;">
                <img src="assets/imgs/page/room/airport.svg" alt="Power Backup & Lift" style="width: 24px; height: 24px;">
              </div>
              <div class="card-text-side">
                <h6 class="text-md-bold neutral-1000 mb-1" style="font-size: 15px;">Power Backup & Lift</h6>
                <p class="text-xs neutral-500 mb-0" style="font-size: 12px; line-height: 1.4;">Uninterrupted power supply and modern elevator lift.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-box box-why-book-travila-4 background-body">
      <div class="container">
        <div class="text-center mb-40 pt-30 wow fadeInUp">
          <h2 class="neutral-1000">Why Choose Hotel Destin?</h2>
          <p class="text-xl-medium neutral-500">We value your comfort, cleanliness, and convenience above all.</p>
        </div>
        <div class="row">
          <!-- Card 1: Central Location -->
          <div class="col-lg-3 col-sm-6 mb-30">
            <div class="card-why-premium wow fadeInUp hover-up">
              <div class="icon-wrapper-premium">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </div>
              <h5>Central Location</h5>
              <p>Located on Sachin Tendulkar Road near Gwalior railway and business stations.</p>
            </div>
          </div>

          <!-- Card 2: Luxury & Comfort -->
          <div class="col-lg-3 col-sm-6 mb-30">
            <div class="card-why-premium wow fadeInUp hover-up">
              <div class="icon-wrapper-premium">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9"></path>
                </svg>
              </div>
              <h5>Luxury & Comfort</h5>
              <p>Well-conditioned, premium rooms with cozy bedding and full AC comfort.</p>
            </div>
          </div>

          <!-- Card 3: Polite Staff -->
          <div class="col-lg-3 col-sm-6 mb-30">
            <div class="card-why-premium wow fadeInUp hover-up">
              <div class="icon-wrapper-premium">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <h5>Polite Staff</h5>
              <p>Highly rated for prompt, friendly, and round-the-clock responsive service.</p>
            </div>
          </div>

          <!-- Card 4: Essential Facilities -->
          <div class="col-lg-3 col-sm-6 mb-30">
            <div class="card-why-premium wow fadeInUp hover-up">
              <div class="icon-wrapper-premium">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="9" y1="9" x2="15" y2="9"></line>
                  <line x1="9" y1="13" x2="15" y2="13"></line>
                  <line x1="9" y1="17" x2="15" y2="17"></line>
                </svg>
              </div>
              <h5>Essential Facilities</h5>
              <p>Complimentary high-speed Wi-Fi, secure parking, and 24/7 power backup.</p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <?php
    // Fetch latest 3 active coupons
    $active_coupons = [];
    $coupon_banner_status = get_setting('coupon_banner_status', 'active');

    if ($coupon_banner_status === 'active') {
      try {
        $stmt = $pdo->query("SELECT * FROM coupons WHERE status = 'active' AND expiry_date >= CURDATE() ORDER BY created_at DESC, id DESC LIMIT 3");
        $active_coupons = $stmt->fetchAll();
      } catch (Exception $e) {
        error_log("Failed to load active coupons for homepage: " . $e->getMessage());
      }
    }

    if ($coupon_banner_status === 'active' && count($active_coupons) > 0):
      $coupon_banner_title = get_setting('coupon_banner_title', 'Flat ₹500 Cashback on bookings above ₹5,000!');
      $coupon_banner_subtitle = get_setting('coupon_banner_subtitle', 'Celebrate your stay at Hotel Destin with exclusive savings. Use the coupon code below at checkout.');
      $coupon_banner_terms = get_setting('coupon_banner_terms', '*Terms & conditions apply. Valid for standard, executive, and premium room reservations.');
    ?>
      <style>
        .coupon-card-dynamic {
          transition: all 0.3s ease;
        }

        .coupon-card-dynamic:hover {
          transform: translateY(-3px);
          background: rgba(255, 255, 255, 0.08) !important;
          border-color: rgba(234, 179, 8, 0.6) !important;
        }
      </style>
      <section class="section-box box-banner-ads background-body">
        <div class="container">
          <div class="box-coupon-banner text-center p-5 rounded-4 shadow-sm wow fadeInUp" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white; border: 1px solid rgba(255,255,255,0.08);">
            <div class="row align-items-center justify-content-center">
              <div class="col-lg-10">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill text-uppercase text-xs-bold mb-10">Special Promotion</span>
                <h3 class="neutral-0"><?= htmlspecialchars($coupon_banner_title) ?></h3>
                <p class="text-lg-medium neutral-300 mb-25"><?= htmlspecialchars($coupon_banner_subtitle) ?></p>

                <div class="d-flex flex-wrap align-items-stretch justify-content-center gap-3 mb-10">
                  <?php foreach ($active_coupons as $coupon): ?>
                    <div class="coupon-card-dynamic px-4 py-3 rounded-3 d-flex flex-column align-items-center justify-content-center" style="background: rgba(255, 255, 255, 0.04); border: 2px dashed rgba(255,255,255,0.2); min-width: 250px; flex: 1 1 250px; max-width: 320px;">
                      <span class="text-xs text-uppercase text-warning font-semibold mb-1" style="letter-spacing: 1px;"><?= htmlspecialchars($coupon['title']) ?></span>
                      <div class="d-flex align-items-center gap-2 my-1">
                        <span class="text-sm neutral-300">CODE:</span>
                        <strong class="text-lg-bold text-warning" style="letter-spacing: 1.5px;"><?= htmlspecialchars($coupon['code']) ?></strong>
                      </div>
                      <span class="text-xs neutral-400"><?= htmlspecialchars($coupon['discount_percent']) ?>% Discount</span>
                    </div>
                  <?php endforeach; ?>
                </div>

                <?php if (!empty($coupon_banner_terms)): ?>
                  <p class="text-xs neutral-500 mt-20"><?= htmlspecialchars($coupon_banner_terms) ?></p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </section>
    <?php endif; ?>
    <section class="section-box" style="padding-top: 30px; padding-bottom: 30px; border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
      <style>
        /* Attraction Cards Section Styles */
        .attraction-card {
          background: #ffffff;
          border-radius: 16px;
          overflow: hidden;
          border: 1px solid #e9ecf2;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
          transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
          margin-bottom: 30px;
          display: flex;
          flex-direction: column;
          height: calc(100% - 30px);
        }

        .attraction-card:hover {
          transform: translateY(-6px);
          box-shadow: 0 20px 45px rgba(14, 14, 14, 0.07);
          border-color: #cbd5e1;
        }

        .attraction-img-wrapper {
          position: relative;
          height: 200px;
          overflow: hidden;
          background-color: #f1f2f6;
        }

        .attraction-img-wrapper img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .attraction-card:hover .attraction-img-wrapper img {
          transform: scale(1.05);
        }

        .attraction-info {
          padding: 20px;
          flex-grow: 1;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }

        .attraction-title {
          font-size: 18px;
          font-weight: 600;
          color: var(--neutral-1000, #0E0E0E);
          margin-bottom: 4px;
        }

        .attraction-type {
          font-size: 13.5px;
          color: var(--neutral-500, #6c757d);
          margin-bottom: 15px;
        }

        .attraction-footer {
          border-top: 1px solid rgba(0, 0, 0, 0.05);
          padding-top: 12px;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .attraction-distance {
          font-size: 12px;
          font-weight: 600;
          color: var(--neutral-400, #adb5bd);
          margin-bottom: 0;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .attraction-btn {
          font-size: 13px;
          font-weight: 700;
          color: var(--neutral-1000, #0E0E0E);
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          gap: 4px;
          transition: transform 0.3s ease;
        }

        .attraction-btn:hover {
          transform: translateX(4px);
          color: var(--primary, #0E0E0E);
        }

        /* Responsive Scroll snap for mobile */
        @media (max-width: 767px) {
          .attraction-scroll-container {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            gap: 16px;
            padding-bottom: 20px;
            padding-left: 15px;
            padding-right: 15px;
            margin-left: -15px;
            margin-right: -15px;
          }

          .attraction-scroll-container::-webkit-scrollbar {
            height: 4px;
          }

          .attraction-scroll-container::-webkit-scrollbar-thumb {
            background-color: rgba(14, 14, 14, 0.15);
            border-radius: 4px;
          }

          .attraction-col {
            width: 85% !important;
            flex-shrink: 0 !important;
            scroll-snap-align: start;
            padding: 0 !important;
          }

          .attraction-card {
            margin-bottom: 0px !important;
            height: 100% !important;
          }
        }
      </style>

      <div class="container">
        <div class="mb-35 wow fadeInUp">
          <h2 class="neutral-1000 font-heading" style="font-size: 32px; font-weight: 500;">Explore Gwalior</h2>
          <p class="text-md neutral-500 mt-5">Discover popular tourist spots and landmarks near Hotel Destin.</p>
        </div>

        <div class="row g-4 attraction-scroll-container wow fadeInUp" data-wow-delay="0.1s">

          <!-- Attraction 1: Gwalior Fort -->
          <div class="col-lg-4 col-md-6 col-12 attraction-col">
            <div class="attraction-card">
              <div class="attraction-img-wrapper">
                <a href="explore-gwalior.php#gwalior-fort">
                  <img src="assets/imgs/page/gwalior_fort.png" alt="Gwalior Fort near Hotel Destin" loading="lazy">
                </a>
              </div>
              <div class="attraction-info">
                <div>
                  <h3 class="attraction-title">Gwalior Fort</h3>
                  <p class="attraction-type">Historical Fort | Gwalior</p>
                </div>
                <div class="attraction-footer">
                  <p class="attraction-distance">7-8 km from Hotel</p>
                  <a class="attraction-btn" href="explore-gwalior.php#gwalior-fort">
                    Explore
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="5" y1="12" x2="19" y2="12" />
                      <polyline points="12 5 19 12 12 19" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Attraction 2: Jai Vilas Palace -->
          <div class="col-lg-4 col-md-6 col-12 attraction-col">
            <div class="attraction-card">
              <div class="attraction-img-wrapper">
                <a href="explore-gwalior.php#jai-vilas-palace">
                  <img src="assets/imgs/page/jai_vilas_palace.png" alt="Jai Vilas Palace near Hotel Destin" loading="lazy">
                </a>
              </div>
              <div class="attraction-info">
                <div>
                  <h3 class="attraction-title">Jai Vilas Palace</h3>
                  <p class="attraction-type">Royal Museum | Gwalior</p>
                </div>
                <div class="attraction-footer">
                  <p class="attraction-distance">5-6 km from Hotel</p>
                  <a class="attraction-btn" href="explore-gwalior.php#jai-vilas-palace">
                    Explore
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="5" y1="12" x2="19" y2="12" />
                      <polyline points="12 5 19 12 12 19" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Attraction 3: Sun Temple -->
          <div class="col-lg-4 col-md-6 col-12 attraction-col">
            <div class="attraction-card">
              <div class="attraction-img-wrapper">
                <a href="explore-gwalior.php#sun-temple">
                  <img src="assets/imgs/page/sun_temple.png" alt="Sun Temple Gwalior near Hotel Destin" loading="lazy">
                </a>
              </div>
              <div class="attraction-info">
                <div>
                  <h3 class="attraction-title">Sun Temple</h3>
                  <p class="attraction-type">Famous Temple | Gwalior</p>
                </div>
                <div class="attraction-footer">
                  <p class="attraction-distance">3-4 km from Hotel</p>
                  <a class="attraction-btn" href="explore-gwalior.php#sun-temple">
                    Explore
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="5" y1="12" x2="19" y2="12" />
                      <polyline points="12 5 19 12 12 19" />
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="section-box box-testimonials-2 box-testimonials-5 background-body">
      <style>
        /* Testimonials Equal Height and Custom Toggles */
        .swiper-group-journey .swiper-slide {
          height: auto !important;
        }

        .swiper-group-journey .card-testimonial {
          height: 100% !important;
          display: flex !important;
          flex-direction: column !important;
          justify-content: space-between !important;
        }

        .swiper-group-journey .card-info {
          flex-grow: 1;
        }

        .testimonial-text-wrapper {
          font-size: 14.5px;
          line-height: 1.6;
          color: var(--neutral-500, #4b5563);
          display: block;
        }

        .testimonial-text {
          display: -webkit-box;
          -webkit-box-orient: vertical;
          -webkit-line-clamp: 4;
          /* limit to 4 lines */
          overflow: hidden;
          margin-bottom: 0px !important;
          line-height: 1.6;
          min-height: 92px;
          /* forces uniform height of 4 lines */
          transition: all 0.3s ease;
        }

        .testimonial-text.expanded {
          display: block !important;
          overflow: visible !important;
          -webkit-line-clamp: none !important;
          min-height: auto !important;
        }

        .read-more-toggle {
          color: var(--neutral-1000, #0E0E0E) !important;
          font-weight: 700;
          font-size: 12.5px;
          text-decoration: underline;
          margin-top: 8px;
          display: inline-block;
          cursor: pointer;
          white-space: nowrap;
        }

        .read-more-toggle.d-none {
          display: none !important;
        }

        @media (max-width: 991.98px) {
          .block-testimonials {
            display: block !important;
            width: 100% !important;
            padding-left: 16px !important;
            padding-right: 0px !important;
          }

          .container-testimonials {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
          }

          .container-slider {
            display: block !important;
            width: 100% !important;
            padding-left: 0px !important;
            padding-right: 0px !important;
          }

          .swiper-container {
            width: 100% !important;
          }

          .swiper-group-journey .swiper-slide {
            width: auto !important;
          }

          .card-testimonial {
            width: 320px !important;
            margin: 0 !important;
          }
        }

        @media (max-width: 499.98px) {
          .card-testimonial {
            width: 280px !important;
            padding: 18px !important;
            border-radius: 12px !important;
          }

          .card-testimonial .card-title {
            font-size: 14.5px !important;
            max-width: 70% !important;
          }

          .card-testimonial .testimonial-text {
            font-size: 12px !important;
            line-height: 1.5 !important;
            min-height: auto !important;
          }

          .card-testimonial .card-top {
            padding-bottom: 12px !important;
            margin-bottom: 15px !important;
          }

          .card-testimonial .card-author img {
            width: 36px !important;
            height: 36px !important;
          }

          .card-testimonial .card-author .text-md-bold {
            font-size: 13px !important;
          }

          .card-testimonial .card-rate img {
            width: 11px !important;
            height: 11px !important;
          }
        }
      </style>

      <div class="container">
        <div class="box-author-testimonials button-bg-2 wow fadeInUp">
          Testimonials
        </div>
        <h2 class="mt-8 neutral-1000">Guest Experiences</h2>
      </div>
      <div class="block-testimonials">
        <div class="container-testimonials wow fadeInUp">
          <div class="container-slider">
            <div class="box-swiper mt-0">
              <div class="swiper-container swiper-group-animate swiper-group-journey">
                <div class="swiper-wrapper">
                  <?php
                  $active_testimonials = [];
                  try {
                    $active_testimonials = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY id DESC")->fetchAll();
                  } catch (Exception $e) {
                    error_log("Failed to load testimonials: " . $e->getMessage());
                  }
                  ?>
                  <?php if (count($active_testimonials) > 0): ?>
                    <?php foreach ($active_testimonials as $t): ?>
                      <div class="swiper-slide">
                        <div class="card-testimonial background-card p-4 rounded-4 shadow-sm" style="border: 1px solid var(--bs-border-color);">
                          <div class="card-info">
                            <div class="d-flex justify-content-between align-items-start mb-15">
                              <p class="text-xl-bold card-title neutral-1000 mb-0" style="max-width: 65%; line-height: 1.3;">
                                <?= htmlspecialchars(mb_strimwidth($t['review_text'], 0, 32, '...')) ?>
                              </p>
                              <div style="display: flex; align-items: center; gap: 6px; background: rgba(0,0,0,0.03); padding: 4px 10px; border-radius: 20px; flex-shrink: 0; white-space: nowrap;">
                                <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M17.64 9.2c0-.63-.06-1.25-.16-1.84H9v3.49h4.84c-.21 1.12-.84 2.07-1.79 2.7l2.86 2.22c1.67-1.54 2.63-3.8 2.63-6.57z" fill="#4285F4" />
                                  <path d="M9 18c2.43 0 4.47-.8 5.96-2.23l-2.86-2.22A5.57 5.57 0 0 1 9 14.5c-2.3 0-4.25-1.55-4.94-3.64L1.13 13.1C2.61 16 5.58 18 9 18z" fill="#34A853" />
                                  <path d="M4.06 10.86A5.4 5.4 0 0 1 3.75 9c0-.65.11-1.29.31-1.86L1.13 4.9C.41 6.3 0 7.89 0 9.5c0 1.61.41 3.2 1.13 4.6l2.93-2.24z" fill="#FBBC05" />
                                  <path d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.8 11.43 0 9 0 5.58 0 2.61 2 1.13 4.9l2.93 2.24C4.75 5.13 6.7 3.58 9 3.58z" fill="#EA4335" />
                                </svg>
                                <span style="font-size: 10px; font-weight: 700; color: #5f6368; line-height: 1;">Google Review</span>
                              </div>
                            </div>
                            <div class="testimonial-text-wrapper mb-20">
                              <p class="neutral-500 testimonial-text mb-0">"<?= htmlspecialchars($t['review_text']) ?>"</p>
                              <a class="read-more-toggle d-none" onclick="toggleTestimonialText(this); return false;">Read more</a>
                            </div>
                          </div>
                          <div class="card-top d-flex align-items-center justify-content-between pt-15" style="border-top: 1px solid var(--bs-border-color);">
                            <div class="card-author d-flex align-items-center">
                              <div class="card-image me-10">
                                <img src="<?= htmlspecialchars($t['image_path']) ?>" alt="<?= htmlspecialchars($t['client_name']) ?>" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #e2e8f0; background: #f8fafc; padding: 2px; object-fit: cover;">
                              </div>
                              <div class="card-info">
                                <p class="text-md-bold neutral-1000 mb-0"><?= htmlspecialchars($t['client_name']) ?></p>
                                <p class="text-xs neutral-500 mb-0"><?= htmlspecialchars($t['location']) ?></p>
                              </div>
                            </div>
                            <div class="card-rate">
                              <?php for ($i = 0; $i < $t['rating']; $i++): ?>
                                <img src="assets/imgs/template/icons/star.svg" alt="star">
                              <?php endfor; ?>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <!-- Testimonial 1: Rahul Sharma -->
                    <div class="swiper-slide">
                      <div class="card-testimonial background-card p-4 rounded-4 shadow-sm" style="border: 1px solid var(--bs-border-color);">
                        <div class="card-info">
                          <div class="d-flex justify-content-between align-items-start mb-15">
                            <p class="text-xl-bold card-title neutral-1000 mb-0" style="max-width: 65%; line-height: 1.3;">Excellent family stay at Hotel Destin</p>
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(0,0,0,0.03); padding: 4px 10px; border-radius: 20px; flex-shrink: 0; white-space: nowrap;">
                              <svg width="14" height="14" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.64 9.2c0-.63-.06-1.25-.16-1.84H9v3.49h4.84c-.21 1.12-.84 2.07-1.79 2.7l2.86 2.22c1.67-1.54 2.63-3.8 2.63-6.57z" fill="#4285F4" />
                                <path d="M9 18c2.43 0 4.47-.8 5.96-2.23l-2.86-2.22A5.57 5.57 0 0 1 9 14.5c-2.3 0-4.25-1.55-4.94-3.64L1.13 13.1C2.61 16 5.58 18 9 18z" fill="#34A853" />
                                <path d="M4.06 10.86A5.4 5.4 0 0 1 3.75 9c0-.65.11-1.29.31-1.86L1.13 4.9C.41 6.3 0 7.89 0 9.5c0 1.61.41 3.2 1.13 4.6l2.93-2.24z" fill="#FBBC05" />
                                <path d="M9 3.58c1.32 0 2.5.45 3.44 1.35l2.58-2.58C13.46.8 11.43 0 9 0 5.58 0 2.61 2 1.13 4.9l2.93 2.24C4.75 5.13 6.7 3.58 9 3.58z" fill="#EA4335" />
                              </svg>
                              <span style="font-size: 10px; font-weight: 700; color: #5f6368; line-height: 1;">Google Review</span>
                            </div>
                          </div>
                          <div class="testimonial-text-wrapper mb-20">
                            <p class="neutral-500 testimonial-text mb-0">"We stayed at Hotel Destin for a family trip to Gwalior. The rooms were clean, the Wi-Fi was fast, and the staff was extremely polite. We got flat ₹500 cashback too! Gwalior Fort is very close by."</p>
                            <a class="read-more-toggle d-none" onclick="toggleTestimonialText(this); return false;">Read more</a>
                          </div>
                        </div>
                        <div class="card-top d-flex align-items-center justify-content-between pt-15" style="border-top: 1px solid var(--bs-border-color);">
                          <div class="card-author d-flex align-items-center">
                            <div class="card-image me-10">
                              <img src="assets/imgs/page/homepage1/avatar-placeholder.svg" alt="Rahul Sharma" style="width: 48px; height: 48px; border-radius: 50%; border: 2px solid #e2e8f0; background: #f8fafc; padding: 2px;">
                            </div>
                            <div class="card-info">
                              <p class="text-md-bold neutral-1000 mb-0">Rahul Sharma</p>
                              <p class="text-xs neutral-500 mb-0">New Delhi, India</p>
                            </div>
                          </div>
                          <div class="card-rate">
                            <img src="assets/imgs/template/icons/star.svg" alt="star">
                            <img src="assets/imgs/template/icons/star.svg" alt="star">
                            <img src="assets/imgs/template/icons/star.svg" alt="star">
                            <img src="assets/imgs/template/icons/star.svg" alt="star">
                            <img src="assets/imgs/template/icons/star.svg" alt="star">
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
      </div>

      <!-- Inline script to toggle expanded text, check clamp conditions and update Swiper sizes -->
      <script>
        function toggleTestimonialText(btn) {
          var wrapper = btn.closest('.testimonial-text-wrapper');
          var textEl = wrapper.querySelector('.testimonial-text');

          if (textEl.classList.contains('expanded')) {
            textEl.classList.remove('expanded');
            btn.textContent = 'Read more';
          } else {
            textEl.classList.add('expanded');
            btn.textContent = 'Read less';
          }

          // Trigger Swiper slide height updates dynamically
          if (typeof Swiper !== 'undefined') {
            var sliders = document.querySelectorAll('.swiper-container');
            sliders.forEach(function(sliderEl) {
              if (sliderEl.swiper) {
                sliderEl.swiper.update();
              }
            });
          }
        }

        // After page loads, run check to only display 'Read more' buttons if paragraph exceeds 4 lines
        document.addEventListener("DOMContentLoaded", function() {
          // We add a tiny delay to ensure layouts are fully rendered and scrollHeight is active
          setTimeout(function() {
            var testimonialParagraphs = document.querySelectorAll('.testimonial-text');
            testimonialParagraphs.forEach(function(pEl) {
              var parentWrapper = pEl.closest('.testimonial-text-wrapper');
              var btn = parentWrapper.querySelector('.read-more-toggle');

              // scrollHeight represents true content height, offsetHeight represents current clamped height
              if (pEl.scrollHeight > pEl.offsetHeight) {
                if (btn) {
                  btn.classList.remove('d-none');
                }
              }
            });
          }, 100);
        });
      </script>
    </section>






    <section class="section-box box-faqs background-body">
      <div class="box-faqs-inner">
        <div class="container">
          <div class="text-center">
            <h2 class="neutral-1000 wow fadeInLeft">Frequently Asked Questions</h2>
            <p class="text-xl-medium neutral-500 wow fadeInLeft">Find answers to common questions about your stay at Hotel Destin.</p>

          </div>
          <div class="block-faqs">
            <div class="accordion" id="accordionFAQ">
              <div class="accordion-item wow fadeInUp">
                <h5 class="accordion-header" id="headingOne">
                  <button class="accordion-button text-heading-5" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <h3>01</h3>
                    <p>What are the check-in and check-out timings at Hotel Destin?</p>
                  </button>
                </h5>
                <div class="accordion-collapse collapse show" id="collapseOne" aria-labelledby="headingOne" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">Our standard check-in time is from 12:00 PM, and check-out time is until 11:00 AM. Early check-in or late check-out is subject to room availability and may incur nominal charges.</div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp">
                <h5 class="accordion-header" id="headingTwo">
                  <button class="accordion-button text-heading-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <h3>02</h3>
                    <p>Where exactly is Hotel Destin located?</p>
                  </button>
                </h5>
                <div class="accordion-collapse collapse" id="collapseTwo" aria-labelledby="headingTwo" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">Hotel Destin is located at Sachin Tendulkar Road, Kailash Nagar, Ramanuj Nagar, Gwalior, Madhya Pradesh 474011. We are situated in a prime area with easy access to railway stations, shopping areas, and major historical monuments.</div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp">
                <h5 class="accordion-header" id="headingThree">
                  <button class="accordion-button text-heading-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <h3>03</h3>
                    <p>What is the child booking policy at your hotel?</p>
                  </button>
                </h5>
                <div class="accordion-collapse collapse" id="collapseThree" aria-labelledby="headingThree" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">Complimentary stay is provided for children under 5 years of age sharing the room with parents without an extra bed. For older children or extra beds, standard adult/child charges will apply.</div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp">
                <h5 class="accordion-header" id="headingFour">
                  <button class="accordion-button text-heading-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <h3>04</h3>
                    <p>Is high-speed Wi-Fi and parking free for guests?</p>
                  </button>
                </h5>
                <div class="accordion-collapse collapse" id="collapseFour" aria-labelledby="headingFour" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">Yes! High-speed Wi-Fi internet access and private parking are completely free for all registered hotel guests.</div>
                </div>
              </div>
              <div class="accordion-item wow fadeInUp">
                <h5 class="accordion-header" id="headingFive">
                  <button class="accordion-button text-heading-5 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <h3>05</h3>
                    <p>What is the cancellation policy for room bookings?</p>
                  </button>
                </h5>
                <div class="accordion-collapse collapse" id="collapseFive" aria-labelledby="headingFive" data-bs-parent="#accordionFAQ">
                  <div class="accordion-body">Cancellation policies vary depending on the booking channel and the selected rate plan. Please review the booking terms during checkout. Bookings made with special promotional codes or discounts are generally non-refundable.</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Recent Blog Section -->
    <section class="section-box background-body py-tight" style="border-top: 1px solid rgba(0, 0, 0, 0.05); padding-top: 50px !important; padding-bottom: 50px !important;">
      <style>
        /* Desktop layout matches blog.php */
        .home-blog-card {
          background: #ffffff;
          border-radius: 16px;
          overflow: hidden;
          border: 1px solid rgba(0, 0, 0, 0.04);
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
          transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
          margin-bottom: 30px;
          display: flex;
          flex-direction: column;
          height: calc(100% - 30px);
        }

        .home-blog-card:hover {
          transform: translateY(-6px);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
          border-color: rgba(14, 14, 14, 0.08);
        }

        .home-post-img-wrapper {
          position: relative;
          height: 220px;
          overflow: hidden;
          background-color: #f1f2f6;
        }

        .home-post-img-wrapper img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        }

        .home-blog-card:hover .home-post-img-wrapper img {
          transform: scale(1.05);
        }

        .home-post-info-box {
          padding: 24px;
          flex-grow: 1;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
        }

        .home-post-meta-details {
          display: flex;
          gap: 15px;
          font-size: 12px;
          color: var(--neutral-400, #adb5bd);
          margin-bottom: 12px;
          font-weight: 600;
          text-transform: uppercase;
          letter-spacing: 0.5px;
        }

        .home-post-meta-item {
          display: flex;
          align-items: center;
          gap: 4px;
        }

        .home-post-title {
          font-size: 18px;
          font-weight: 600;
          line-height: 1.4;
          color: var(--neutral-1000, #0E0E0E);
          margin-bottom: 12px;
          transition: color 0.3s ease;
        }

        .home-post-title a {
          color: inherit;
          text-decoration: none;
        }

        .home-post-title a:hover {
          color: var(--primary, #0E0E0E);
        }

        .home-post-excerpt {
          font-size: 14px;
          color: var(--neutral-500, #6c757d);
          line-height: 1.6;
          margin-bottom: 20px;
        }

        .home-post-footer-area {
          margin-top: 15px;
          display: flex;
          justify-content: flex-start;
          align-items: center;
        }

        .home-keep-reading-link {
          font-size: 13px;
          font-weight: 700;
          color: var(--neutral-1000, #0E0E0E);
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          gap: 4px;
          transition: transform 0.3s ease;
        }

        .home-keep-reading-link:hover {
          transform: translateX(4px);
        }

        /* Mobile slidable container rules */
        @media (max-width: 991px) {
          .blog-home-container {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            gap: 16px;
            padding-bottom: 20px;
            padding-left: 15px;
            /* padding-right: 15px; */
            /* margin-left: -15px; */
            margin-right: -15px;
          }

          .blog-home-container::-webkit-scrollbar {
            height: 4px;
          }

          .blog-home-container::-webkit-scrollbar-thumb {
            background-color: rgba(14, 14, 14, 0.15);
            border-radius: 4px;
          }

          .blog-home-col {
            width: 80% !important;
            max-width: 320px !important;
            flex-shrink: 0 !important;
            scroll-snap-align: start;
            padding: 0 !important;
          }

          .home-blog-card {
            margin-bottom: 0px !important;
            height: 100% !important;
          }
        }
      </style>

      <div class="container">
        <div class="row align-items-end mb-40">
          <div class="col-md-8 col-12 wow fadeInUp">
            <h2 class="neutral-1000 font-heading" style="font-size: 32px; font-weight: 500;">Latest From Our Blog</h2>
            <p class="text-md neutral-500 mt-5">Discover local Gwalior travel guides, event tips, and guest stories.</p>
          </div>
          <div class="col-md-4 col-12 text-md-end text-start mt-15 mt-md-0 wow fadeInUp" data-wow-delay="0.1s">
            <a class="btn btn-default" href="blog.php">
              View All Articles
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 5px;">
                <line x1="5" y1="12" x2="19" y2="12" />
                <polyline points="12 5 19 12 12 19" />
              </svg>
            </a>
          </div>
        </div>

        <?php
        $home_blogs = [];
        try {
          $home_blogs = $pdo->query("SELECT * FROM blogs ORDER BY id DESC LIMIT 3")->fetchAll();
        } catch (Exception $e) {
          error_log("Failed to load home page blogs: " . $e->getMessage());
        }

        if (count($home_blogs) === 0) {
          $home_blogs = [
            [
              'id' => 1,
              'slug' => 'gwalior-fort-guide',
              'title' => 'Gwalior Fort Guide: Exploring the Gibraltar of India',
              'image_path' => 'assets/imgs/page/homepage1/news.png',
              'date' => '05 Jul 2026',
              'read_time' => '8 min read',
              'excerpt' => 'Discover the rich history, magnificent palaces, and stunning temple carvings inside Gwalior\'s historic fort.'
            ],
            [
              'id' => 2,
              'slug' => 'dining-spots-sachin-tendulkar-road',
              'title' => 'Top 5 Dining Spots on Sachin Tendulkar Road',
              'image_path' => 'assets/imgs/page/homepage1/news2.png',
              'date' => '02 Jul 2026',
              'read_time' => '5 min read',
              'excerpt' => 'A curated list of local Gwalior specialties, fine dining, and cafe favorites located just steps from Hotel Destin.'
            ],
            [
              'id' => 3,
              'slug' => 'planning-perfect-event-wedding',
              'title' => 'Planning the Perfect Event or Wedding in Gwalior',
              'image_path' => 'assets/imgs/page/homepage1/news3.png',
              'date' => '28 Jun 2026',
              'read_time' => '10 min read',
              'excerpt' => 'From picking themes and menus to managing guest blocks, here is our ultimate checklist for stress-free banquets.'
            ]
          ];
        }
        ?>

        <div class="row g-4 blog-home-container wow fadeInUp" data-wow-delay="0.2s">
          <?php foreach ($home_blogs as $blog):
            $blog_img = !empty($blog['image_path']) ? htmlspecialchars($blog['image_path']) : 'assets/imgs/page/homepage1/news.png';
            $blog_link = !empty($blog['slug']) ? 'blog-detail.php?slug=' . urlencode($blog['slug']) : 'blog-detail.php?id=' . $blog['id'];
          ?>
            <div class="col-lg-4 col-md-6 col-12 blog-home-col">
              <div class="home-blog-card">
                <div class="home-post-img-wrapper">
                  <a href="<?php echo $blog_link; ?>">
                    <img src="<?php echo $blog_img; ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" loading="lazy">
                  </a>
                </div>
                <div class="home-post-info-box">
                  <div>
                    <div class="home-post-meta-details">
                      <span class="home-post-meta-item">
                        <!-- Calendar Icon -->
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                          <line x1="16" y1="2" x2="16" y2="6" />
                          <line x1="8" y1="2" x2="8" y2="6" />
                          <line x1="3" y1="10" x2="21" y2="10" />
                        </svg>
                        <?php echo htmlspecialchars($blog['date']); ?>
                      </span>
                      <span class="home-post-meta-item">
                        <!-- Clock Icon -->
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10" />
                          <polyline points="12 6 12 12 16 14" />
                        </svg>
                        <?php echo htmlspecialchars($blog['read_time']); ?>
                      </span>
                    </div>
                    <h3 class="home-post-title">
                      <a href="<?php echo $blog_link; ?>"><?php echo htmlspecialchars($blog['title']); ?></a>
                    </h3>
                    <p class="home-post-excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                  </div>
                  <div class="home-post-footer-area">
                    <a class="home-keep-reading-link" href="<?php echo $blog_link; ?>">
                      Keep Reading
                      <!-- Right Arrow Icon -->
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  </main>

  <?php include('include/footer.php'); ?>

  <div class="popup-signin">
    <div class="popup-container">
      <div class="popup-content"> <a class="close-popup-signin"></a>
        <div class="d-flex gap-2 align-items-center"><a href="#"><img src="assets/imgs/template/popup/logo.svg" alt="Travila"></a>
          <h4 class="neutral-1000">Hello there !</h4>
        </div>
        <div class="box-button-logins"> <a class="btn btn-login btn-google mr-10" href="#"><img src="assets/imgs/template/popup/google.svg" alt="Travila"><span class="text-sm-bold">Sign in with Google</span></a><a class="btn btn-login mr-10" href="#"><img src="assets/imgs/template/popup/facebook.svg" alt="Travila"></a><a class="btn btn-login" href="#"><img src="assets/imgs/template/popup/apple.svg" alt="Travila"></a></div>
        <div class="form-login">
          <form action="#">
            <div class="form-group">
              <label class="text-sm-medium">User name</label>
              <input class="form-control username" type="text" placeholder="Email / Username">
            </div>
            <div class="form-group">
              <label class="text-sm-medium">Password</label>
              <input class="form-control password" type="password" placeholder="">
            </div>
            <div class="form-group">
              <div class="box-remember-forgot">
                <div class="remeber-me">
                  <label class="text-xs-medium neutral-500">
                    <input class="cb-remember" type="checkbox">Remember me
                  </label>
                </div>
                <div class="forgotpass"> <a class="text-xs-medium neutral-500" href="#">Forgot password?</a></div>
              </div>
            </div>
            <div class="form-group mt-45 mb-30"> <a class="btn btn-black-lg" href="#">Login
                <svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 15L15 8L8 1M15 8L1 8" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg></a></div>
            <p class="text-sm-medium neutral-500">Don’t have an account? <a class="neutral-1000 btn-signup" href="#">Register Here !</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="popup-signup">
    <div class="popup-container">
      <div class="popup-content"> <a class="close-popup-signup"></a>
        <div class="d-flex gap-2 align-items-center"><a href="#"><img src="assets/imgs/template/popup/logo.svg" alt="Travila"></a>
          <h4 class="neutral-1000">Register</h4>
        </div>
        <div class="box-button-logins"> <a class="btn btn-login btn-google mr-10" href="#"><img src="assets/imgs/template/popup/google.svg" alt="Travila"><span class="text-sm-bold">Sign up with Google</span></a><a class="btn btn-login mr-10" href="#"><img src="assets/imgs/template/popup/facebook.svg" alt="Travila"></a><a class="btn btn-login" href="#"><img src="assets/imgs/template/popup/apple.svg" alt="Travila"></a></div>
        <div class="form-login">
          <form action="#">
            <div class="form-group">
              <label class="text-sm-medium">Username *</label>
              <input class="form-control username" type="text" placeholder="Email / Username">
            </div>
            <div class="form-group">
              <label class="text-sm-medium">Your email *</label>
              <input class="form-control email" type="text" placeholder="Email / Username">
            </div>
            <div class="row">
              <div class="col-6">
                <div class="form-group">
                  <label class="text-sm-medium">Password *</label>
                  <input class="form-control password" type="password" placeholder="***********">
                </div>
              </div>
              <div class="col-6">
                <div class="form-group">
                  <label class="text-sm-medium">Confirm Password *</label>
                  <input class="form-control password" type="password" placeholder="***********">
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="box-remember-forgot">
                <div class="remeber-me">
                  <label class="text-xs-medium neutral-500">
                    <input class="cb-remember" type="checkbox">I agree to term and conditions
                  </label>
                </div>
              </div>
            </div>
            <div class="form-group mt-45 mb-30"> <a class="btn btn-black-lg" href="#">Create New Account
                <svg width="16" height="16" viewbox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 15L15 8L8 1M15 8L1 8" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg></a></div>
            <p class="text-sm-medium neutral-500">Already have an account <a class="neutral-1000 btn-signin" href="#">Login Here !</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!--Vendors Scripts-->
  <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
  <script src="assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
  <script src="assets/js/vendor/bootstrap.bundle.min.js"></script>
  <!--Other-->
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
  <!--Custom script for this template-->
  <script src="assets/js/maine209.js?v=1.0.2"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      if (typeof Swiper !== "undefined") {
        new Swiper('.swiper-group-rooms-custom', {
          spaceBetween: 30,
          slidesPerView: 3,
          slidesPerGroup: 1,
          loop: false,
          navigation: {
            nextEl: '.swiper-button-next-rooms',
            prevEl: '.swiper-button-prev-rooms',
          },
          breakpoints: {
            1199: {
              slidesPerView: 3
            },
            800: {
              slidesPerView: 2
            },
            250: {
              slidesPerView: 1
            }
          }
        });
      }

      // Room Type Select Event
      $(document).on('click', '.room-type-item', function(e) {
        e.preventDefault();
        var value = $(this).data('value');
        $('.room-type-text').text(value);
      });

      // Guests & Rooms Dropdown Counters Logic
      $(document).on('click', '.inc-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var target = $(this).siblings('span');
        var count = parseInt(target.text());
        if (count < 5) {
          target.text(count + 1);
          updateGuestsRoomsSummary();
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
                  <a class="remove-room-link" href="#">Remove</a>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <div>
                    <span style="font-weight: 600; font-size: 13px; color: #333; display: block; text-align: left;">Adult</span>
                    <span class="text-muted" style="font-size: 11px; display: block; text-align: left;">(Above 12 years)</span>
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
                    <span class="text-muted" style="font-size: 11px; display: block; text-align: left;">(Below 12 years)</span>
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

        // Update hidden fields
        $('#hidden_adults').val(totalAdults);
        $('#hidden_children').val(totalChildren);

        var totalGuests = totalAdults + totalChildren;
        var roomsText = totalRooms + (totalRooms === 1 ? ' Room' : ' Rooms');
        var guestsText = totalGuests + (totalGuests === 1 ? ' Guest' : ' Guests');

        $('#dropdownGuestsBtn .guests-summary-text').text(roomsText + ', ' + guestsText);
      }
      // ── Mobile Date Placeholder Fix ────────────────────────────────────
      // On mobile, appearance:none hides the native "dd-mm-yyyy" hint.
      // We inject a <span> to simulate it and toggle it on value/focus.
      // ──────────────────────────────────────────────────────────────────
      (function initDatePlaceholders() {
        document.querySelectorAll('.box-calendar-date input[type="date"]').forEach(function(inp) {
          var wrapper = inp.closest('.box-calendar-date');
          if (!wrapper) return;

          // Create the placeholder span if it doesn't exist yet
          var ph = wrapper.querySelector('.date-ph');
          if (!ph) {
            ph = document.createElement('span');
            ph.className = 'date-ph';
            ph.textContent = 'dd-mm-yyyy';
            wrapper.appendChild(ph);
          }

          function refresh() {
            if (!inp.value) {
              ph.style.display = '';
              inp.classList.add('date-empty');
            } else {
              ph.style.display = 'none';
              inp.classList.remove('date-empty');
            }
          }

          inp.addEventListener('change', refresh);
          inp.addEventListener('input', refresh);
          inp.addEventListener('focus', function() {
            ph.style.display = 'none';
            inp.classList.remove('date-empty');
          });
          inp.addEventListener('blur', refresh);
          refresh(); // run on page load
        });
      })();

    });
  </script>
</body>

</html>