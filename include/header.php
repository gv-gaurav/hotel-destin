    <div id="preloader-active">
      <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
          <div class="text-center">
            <div class="spinning-coin-fall"></div>
          </div>
        </div>
      </div>
    </div>
    <script>
      // Auto-hide page preloader on window load
      window.addEventListener('load', function() {
        var loader = document.getElementById('preloader-active');
        if (loader) {
          loader.style.transition = 'opacity 0.5s ease-out';
          loader.style.opacity = '0';
          setTimeout(function() {
            loader.style.display = 'none';
          }, 500);
        }
      });
      // Safety timeout: dismiss preloader after 1.2s to prevent locking the screen on script issues
      setTimeout(function() {
        var loader = document.getElementById('preloader-active');
        if (loader && loader.style.display !== 'none') {
          loader.style.transition = 'opacity 0.4s ease-out';
          loader.style.opacity = '0';
          setTimeout(function() {
            loader.style.display = 'none';
          }, 400);
        }
      }, 1200);
    </script>



    <?php
    $current_file = basename($_SERVER['PHP_SELF']);
    $is_transparent = ($current_file === 'index.php' || $current_file === 'restaurant.php');
    ?>
    <header class="header sticky-bar <?= $is_transparent ? 'transparent-header' : '' ?>">


      <?php
      $announcement_status = get_setting('announcement_status', 'active');
      $announcement_text = get_setting('announcement_text', '🌟 Experience Comfort & Luxury at Hotel Destin • Book Your Stay Today');
      if ($announcement_status === 'active'):
      ?>
        <div class="top-bar">
          <div class="container-fluid">
            <div class="text-header">
              <div class="text-unlock text-sm-bold"><?= htmlspecialchars($announcement_text) ?></div>
            </div>
          </div>
        </div>
      <?php endif; ?>
      <div class="container-fluid header-color">
        <div class="main-header">
          <div class="header-left">
            <div class="header-logo"><a class="d-flex" href="index.php"><img class="light-mode" alt="Hotel Destin" src="assets/imgs/template/logo-destin.png" style="max-height: 80px; width: auto;"><img class="dark-mode" alt="Hotel Destin" src="assets/imgs/template/logo-destin.png" style="max-height: 80px; width: auto;"></a></div>
            <div class="header-nav">
              <nav class="nav-main-menu">
                <ul class="main-menu">
                  <li><a class="active" href="index.php">Home</a></li>
                  <li><a href="about.php">About Us</a></li>
                  <li><a href="rooms.php">Rooms</a></li>
                  <li><a href="banquet.php">Banquet & Events</a></li>
                  <li><a href="restaurant.php">Restaurant</a></li>
                  <li><a href="contact.php">Contact Us</a></li>
                </ul>
              </nav>
            </div>
          </div>
          <div class="header-right d-flex align-items-center">
            <div class="align-middle mr-15">
              <?php
              $wa_number = get_setting('hotel_whatsapp') ?: '917000000000';
              $wa_url = preg_replace('/[^0-9]/', '', $wa_number);
              ?>
              <a class="btn-whatsapp-header" href="https://wa.me/<?= htmlspecialchars($wa_url) ?>" target="_blank">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 1.62c4.57 0 8.29 3.72 8.29 8.29s-3.72 8.29-8.29 8.29c-1.55 0-3.03-.43-4.31-1.24l-.31-.18-3.09.81.82-3.01-.2-.32a8.23 8.23 0 0 1-1.27-4.35c0-4.57 3.72-8.29 8.29-8.29zm-3.66 4.28c-.18 0-.44.07-.67.33-.23.26-.88.86-.88 2.1s.9 2.44 1.03 2.61c.13.17 1.77 2.7 4.28 3.78.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.48-.6 1.69-1.19.21-.58.21-1.09.15-1.19-.06-.1-.23-.17-.48-.29l-2.52-1.24c-.25-.13-.44-.19-.63.09l-.72.9c-.16.2-.33.22-.58.09-.26-.13-1.09-.4-2.07-1.28-.76-.68-1.27-1.52-1.42-1.78-.15-.26-.02-.4.11-.53l.51-.6c.13-.15.17-.26.26-.43a.47.47 0 0 0-.02-.45c-.06-.13-.59-1.42-.81-1.95-.21-.52-.43-.45-.59-.46l-.5-.01z" />
                </svg>
                <span>WhatsApp Chat</span>
              </a>
            </div>
            <div class="align-middle mr-15">
              <?php
              $hotel_phone = get_setting('hotel_phone') ?: '09203509944';
              $phone_url = preg_replace('/[^0-9+]/', '', $hotel_phone);
              ?>
              <a class="btn-call-header" href="tel:<?= htmlspecialchars($phone_url) ?>">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                <span>Call Us</span>
              </a>
            </div>
            <div class="d-none d-xxl-inline-block align-middle mr-15"><button class="btn btn-default" onclick="openEnquiryModal()" style="border: none;">Enquiry Now</button></div>

            <div class="burger-icon burger-icon-white"><span class="burger-icon-top"></span><span class="burger-icon-mid"></span><span class="burger-icon-bottom"></span></div>
          </div>
        </div>
      </div>
    </header>
    <div class="mobile-header-active mobile-header-wrapper-style perfect-scrollbar">
      <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-logo">
          <a class="d-flex" href="index.php">
            <img class="light-mode" alt="Hotel Destin" src="assets/imgs/template/logo-destin.png" style="max-height: 48px; width: auto;">
            <img class="dark-mode" alt="Hotel Destin" src="assets/imgs/template/logo-destin.png" style="max-height: 48px; width: auto;">
          </a>
          <div class="burger-icon burger-icon-white"></div>
        </div>

        <div class="mobile-header-content-area">
          <div class="perfect-scroll">
            <div class="mobile-menu-wrap mobile-header-border">
              <nav>
                <ul class="mobile-menu font-heading">
                  <?php
                  $current_page = basename($_SERVER['PHP_SELF']);
                  ?>
                  <li><a class="<?= ($current_page === 'index.php') ? 'active' : '' ?>" href="index.php">Home</a></li>
                  <li><a class="<?= ($current_page === 'about.php') ? 'active' : '' ?>" href="about.php">About Us</a></li>
                  <li><a class="<?= ($current_page === 'rooms.php' || $current_page === 'room-detail.php') ? 'active' : '' ?>" href="rooms.php">Rooms</a></li>
                  <li><a class="<?= ($current_page === 'banquet.php') ? 'active' : '' ?>" href="banquet.php">Banquet & Events</a></li>
                  <li><a class="<?= ($current_page === 'restaurant.php') ? 'active' : '' ?>" href="restaurant.php">Restaurant</a></li>
                  <li><a class="<?= ($current_page === 'contact.php') ? 'active' : '' ?>" href="contact.php">Contact Us</a></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <style>
      /* Professional Sidebar & Overlay Custom Styles */
      @media (max-width: 767px) {
        .mobile-header-active.mobile-header-wrapper-style {
          width: 60% !important;
          max-width: 60% !important;
          background: rgba(255, 255, 255, 0.88) !important;
          backdrop-filter: blur(25px) !important;
          -webkit-backdrop-filter: blur(25px) !important;
          border-left: 1px solid rgba(161, 122, 66, 0.18) !important;
          box-shadow: -10px 0 35px rgba(0, 0, 0, 0.15) !important;
          right: 0 !important;
          left: auto !important;
          display: block !important;
        }

        /* Blurred dark background behind sidebar */
        .mobile-menu-active .body-overlay-1 {
          background: rgba(6, 9, 14, 0.55) !important;
          backdrop-filter: blur(8px) !important;
          -webkit-backdrop-filter: blur(8px) !important;
          opacity: 1 !important;
          visibility: visible !important;
          z-index: 1005 !important;
        }

        /* Inner sidebar alignments */
        .mobile-header-active.mobile-header-wrapper-style .mobile-header-logo {
          display: flex !important;
          justify-content: space-between !important;
          align-items: center !important;
          padding: 16px 20px !important;
          border-bottom: 1px solid rgba(161, 122, 66, 0.12) !important;
          background: transparent !important;
          margin-bottom: 10px !important;
        }

        .mobile-header-active.mobile-header-wrapper-style .mobile-header-logo img {
          max-height: 48px !important;
          width: auto !important;
        }

        .mobile-header-active.mobile-header-wrapper-style .mobile-header-content-area {
          padding: 10px 20px !important;
          background: transparent !important;
        }

        .mobile-header-active.mobile-header-wrapper-style .mobile-menu-wrap {
          border: none !important;
        }

        .mobile-header-active.mobile-header-wrapper-style .mobile-menu-wrap nav .mobile-menu li {
          margin-bottom: 16px !important;
          list-style: none !important;
        }

        .mobile-header-active.mobile-header-wrapper-style .mobile-menu-wrap nav .mobile-menu li a {
          font-size: 15px !important;
          font-weight: 700 !important;
          color: #a17a42 !important;
          /* Premium branding gold/bronze */
          transition: all 0.25s ease !important;
          padding: 4px 0 !important;
          display: inline-block !important;
          border-bottom: 2px solid transparent !important;
        }

        /* Active page menu highlighting */
        .mobile-header-active.mobile-header-wrapper-style .mobile-menu-wrap nav .mobile-menu li a.active {
          color: #c29d66 !important;
          border-bottom: 2px solid #a17a42 !important;
          padding-bottom: 2px !important;
        }

        .mobile-header-active.mobile-header-wrapper-style .mobile-menu-wrap nav .mobile-menu li a:hover {
          color: #c29d66 !important;
          transform: translateX(4px) !important;
        }
      }

      /* Transparent Header overrides for Home Page */
      .header.transparent-header {
        position: absolute !important;
        background: transparent !important;
        border-bottom: none !important;
        width: 100% !important;
        left: 0 !important;
        top: 0 !important;
        z-index: 1000 !important;
      }

      .header.transparent-header .header-color {
        background: rgba(6, 9, 14, 0.4) !important;
        backdrop-filter: blur(15px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(10px) saturate(180%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
      }

      /* High contrast white navigation links for transparent header */
      .header.transparent-header .main-menu li a {
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4) !important;
      }

      .header.transparent-header .main-menu li a:hover,
      .header.transparent-header .main-menu li a.active {
        color: #E0B85D !important;
      }

      /* Ensure mobile header elements are styled correctly */
      .header.transparent-header .burger-icon span {
        background-color: #ffffff !important;
      }

      /* Scroll stick state overrides: transparent header turns into elegant translucent dark bar */
      .header.transparent-header.sticky-bar.stick {
        position: fixed !important;
        top: 0 !important;
      }

      .header.transparent-header.sticky-bar.stick .header-color {
        background: rgba(6, 9, 14, 0.85) !important;
        border-bottom: 1px solid rgba(224, 184, 93, 0.15) !important;
      }

      .header.transparent-header.sticky-bar.stick .main-menu li a {
        color: #e5e5e5 !important;
      }

      .header.transparent-header.sticky-bar.stick .main-menu li a:hover,
      .header.transparent-header.sticky-bar.stick .main-menu li a.active {
        color: #E0B85D !important;
      }

      /* Adjust homepage hero spacing so text content is pushed below absolute header */
      .transparent-header+.main .box-slide-banner .item-banner-slide,
      .transparent-header~.main .box-slide-banner .item-banner-slide {
        padding-top: 300px !important;
      }

      .transparent-header+.main .box-slide-banner .item-banner-slide .container,
      .transparent-header~.main .box-slide-banner .item-banner-slide .container {
        padding-top: 20px !important;
      }

      @media (max-width: 767px) {

        .transparent-header+.main .box-slide-banner .item-banner-slide,
        .transparent-header~.main .box-slide-banner .item-banner-slide {
          padding-top: 180px !important;
          min-height: 650px !important;
          padding-bottom: 80px !important;
        }

        .transparent-header+.main .box-slide-banner .item-banner-slide .container,
        .transparent-header~.main .box-slide-banner .item-banner-slide .container {
          padding-top: 20px !important;
        }

        .upper-top-bar {
          margin-top: 15px !important;
          margin-bottom: 12px !important;
          display: block !important;
        }

        .list-ticks-green {
          /* margin-bottom: 40px !important; */
        }

        /* Reduce logo size on mobile screens */
        .header .header-logo img {
          max-height: 70px !important;
        }
      }

      /* Adjust restaurant hero spacing and height for transparent header */
      .transparent-header+.main .res-hero,
      .transparent-header~.main .res-hero {
        padding-top: 60px !important;
        min-height: 480px !important;
        height: auto !important;
      }

      @media (min-width: 768px) {

        .transparent-header+.main .res-hero,
        .transparent-header~.main .res-hero {
          padding-top: 10px !important;
          min-height: 580px !important;
          height: auto !important;
        }
      }

      /* Fix header buttons design wrapping issue */
      .header .main-header .header-right {
        width: auto !important;
        min-width: max-content !important;
        flex-shrink: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
      }
      .header .main-header .header-left {
        width: auto !important;
        flex-grow: 1 !important;
      }
      .btn-whatsapp-header,
      .btn-call-header,
      .header-right .btn,
      .header-right .btn-default {
        white-space: nowrap !important;
        flex-shrink: 0 !important;
      }

      /* Prevent horizontal scroll / header overflow on medium desktop viewports */
      @media (min-width: 1200px) and (max-width: 1475px) {
        .header .container-fluid {
          padding-left: 24px !important;
          padding-right: 24px !important;
        }
        .header .main-menu li a {
          padding-left: 6px !important;
          padding-right: 6px !important;
          font-size: 14px !important;
        }
        .header .main-header .header-left .header-logo {
          min-width: 110px !important;
        }
        .header .main-header .header-left .header-logo img {
          max-height: 65px !important;
        }
      }

      /* Call Us Modal Overlay */
      .call-modal-overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(15, 23, 42, 0.55);
          backdrop-filter: blur(8px);
          -webkit-backdrop-filter: blur(8px);
          z-index: 999999;
          display: flex;
          align-items: center;
          justify-content: center;
          animation: callFadeIn 0.3s ease forwards;
      }

      /* Modal Card */
      .call-modal-card {
          background: #ffffff;
          border-radius: 16px;
          padding: 30px 24px;
          width: 90%;
          max-width: 420px;
          box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
          border: 1px solid #e2e8f0;
          position: relative;
          transform: scale(0.95);
          animation: callScaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
      }

      /* Close Button */
      .call-modal-close {
          position: absolute;
          top: 16px;
          right: 18px;
          background: none;
          border: none;
          font-size: 26px;
          color: #94a3b8;
          cursor: pointer;
          line-height: 1;
          transition: color 0.2s ease;
          padding: 0;
      }
      .call-modal-close:hover {
          color: #0f172a;
      }

      /* Title */
      .call-modal-title {
          font-size: 22px;
          font-weight: 700;
          color: #0f172a;
          text-align: center;
          margin-bottom: 25px;
          letter-spacing: 0.5px;
          font-family: var(--bs-font-sans-serif, Arial, sans-serif);
      }

      /* Modal rows */
      .call-modal-body {
          display: flex;
          flex-direction: column;
          gap: 16px;
      }

      .call-modal-row {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding-bottom: 14px;
          border-bottom: 1px solid #f1f5f9;
      }
      .call-modal-row:last-child {
          border-bottom: none;
          padding-bottom: 0;
      }

      .call-modal-info {
          display: flex;
          flex-direction: column;
          gap: 2px;
          text-align: left;
      }

      .call-modal-label {
          font-size: 11px;
          font-weight: 700;
          color: #94a3b8;
          text-transform: uppercase;
          letter-spacing: 0.5px;
      }

      .call-modal-number {
          font-size: 16.5px;
          font-weight: 600;
          color: #9c6047; /* Theme brown/gold color */
          text-decoration: none !important;
          transition: color 0.2s ease;
      }
      .call-modal-number:hover {
          color: #834f39;
      }

      .call-modal-icon-btn {
          width: 38px;
          height: 38px;
          background-color: rgba(156, 96, 71, 0.08);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #9c6047;
          transition: all 0.25s ease;
      }
      .call-modal-icon-btn:hover {
          background-color: #9c6047;
          color: #ffffff;
          transform: scale(1.05);
      }

      @keyframes callFadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
      }
      @keyframes callScaleUp {
          from { transform: scale(0.95); opacity: 0; }
          to { transform: scale(1); opacity: 1; }
      }

      /* Enquiry Modal Overlay */
      .enquiry-modal-overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(15, 23, 42, 0.55);
          backdrop-filter: blur(8px);
          -webkit-backdrop-filter: blur(8px);
          z-index: 999999;
          display: flex;
          align-items: center;
          justify-content: center;
          animation: callFadeIn 0.3s ease forwards;
      }

      /* Modal Card */
      .enquiry-modal-card {
          background: #ffffff;
          border-radius: 16px;
          padding: 30px 24px;
          width: 90%;
          max-width: 440px;
          box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
          border: 1px solid #e2e8f0;
          position: relative;
          transform: scale(0.95);
          animation: callScaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
      }

      /* Close Button */
      .enquiry-modal-close {
          position: absolute;
          top: 16px;
          right: 18px;
          background: none;
          border: none;
          font-size: 26px;
          color: #94a3b8;
          cursor: pointer;
          line-height: 1;
          transition: color 0.2s ease;
          padding: 0;
      }
      .enquiry-modal-close:hover {
          color: #0f172a;
      }

      /* Title */
      .enquiry-modal-title {
          font-size: 22px;
          font-weight: 700;
          color: #0f172a;
          text-align: center;
          margin-bottom: 20px;
          letter-spacing: 0.5px;
          font-family: var(--bs-font-sans-serif, Arial, sans-serif);
      }

      .enquiry-label-custom {
          font-size: 11px !important;
          text-transform: uppercase !important;
          letter-spacing: 0.6px !important;
          font-weight: 700 !important;
          color: #475569 !important;
          margin-bottom: 4px !important;
          display: block;
          text-align: left;
      }

      .enquiry-control-custom {
          display: block;
          width: 100%;
          height: 42px !important;
          padding: 8px 14px !important;
          font-size: 13.5px !important;
          font-weight: 500 !important;
          color: #0f172a !important;
          background-color: #f8fafc !important;
          border: 1px solid #cbd5e1 !important;
          border-radius: 8px !important;
          transition: all 0.2s ease;
      }
      .enquiry-control-custom:focus {
          border-color: #9c6047 !important;
          background-color: #ffffff !important;
          box-shadow: 0 0 0 3px rgba(156, 96, 71, 0.15) !important;
          outline: none !important;
      }

      .enquiry-btn-submit {
          width: 100%;
          height: 44px !important;
          background-color: #9c6047 !important;
          border: none !important;
          border-radius: 8px !important;
          color: #ffffff !important;
          font-weight: 700 !important;
          font-size: 14.5px !important;
          transition: all 0.25s ease !important;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          box-shadow: 0 4px 12px rgba(156, 96, 71, 0.2) !important;
          margin-top: 10px;
      }
      .enquiry-btn-submit:hover {
          background-color: #834f39 !important;
          transform: translateY(-1px) !important;
          box-shadow: 0 6px 16px rgba(156, 96, 71, 0.3) !important;
      }
    </style>

    <!-- Call Us Modal Popup (Desktop only) -->
    <div class="call-modal-overlay" id="callModalOverlay" style="display: none;">
        <div class="call-modal-card">
            <button type="button" class="call-modal-close" onclick="closeCallModal()">&times;</button>
            <h3 class="call-modal-title">CALL US</h3>
            <div class="call-modal-body">
                <?php
                $hotel_phone = get_setting('hotel_phone') ?: '09203509944';
                $phones = array_map('trim', explode(',', $hotel_phone));
                $labels = ['Front Desk & Reservations', 'Direct Support Helpline', 'Alternative Inquiry Contact'];
                foreach ($phones as $idx => $phone):
                    $clean_phone = preg_replace('/[^0-9+]/', '', $phone);
                    $label = isset($labels[$idx]) ? $labels[$idx] : 'Inquiry Support';
                ?>
                    <div class="call-modal-row">
                        <div class="call-modal-info">
                            <span class="call-modal-label"><?= htmlspecialchars($label) ?></span>
                            <a href="tel:<?= htmlspecialchars($clean_phone) ?>" class="call-modal-number"><?= htmlspecialchars($phone) ?></a>
                        </div>
                        <a href="tel:<?= htmlspecialchars($clean_phone) ?>" class="call-modal-icon-btn" aria-label="Call Now">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Enquiry Modal Popup -->
    <div class="enquiry-modal-overlay" id="enquiryModalOverlay" style="display: none;">
        <div class="enquiry-modal-card">
            <button type="button" class="enquiry-modal-close" onclick="closeEnquiryModal()">&times;</button>
            <h3 class="enquiry-modal-title">Enquiry Now</h3>
            
            <!-- Status Notification Box -->
            <div id="enquiryAlertBox" style="display: none; border-radius: 8px; font-size: 13px; padding: 10px 14px; margin-bottom: 16px;"></div>

            <form id="headerEnquiryForm" onsubmit="submitHeaderEnquiry(event)">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="form-group mb-15">
                    <label class="enquiry-label-custom">Full Name *</label>
                    <input class="enquiry-control-custom" type="text" name="name" placeholder="Enter your name" required>
                </div>
                
                <div class="form-group mb-15">
                    <label class="enquiry-label-custom">Phone Number *</label>
                    <input class="enquiry-control-custom" type="tel" name="phone" placeholder="Enter your phone number" required>
                </div>

                <div class="form-group mb-15">
                    <label class="enquiry-label-custom">Enquiry Type *</label>
                    <select class="enquiry-control-custom" name="enquiry_type" id="enquiryTypeSelect" onchange="toggleEnquiryFields()" required>
                        <option value="rooms">Room Enquiry</option>
                        <option value="banquet">Banquet Hall Enquiry</option>
                    </select>
                </div>

                <!-- Conditional Rooms Fields -->
                <div id="roomsEnquiryFields">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="form-group mb-15">
                                <label class="enquiry-label-custom">Check-in Date *</label>
                                <input class="enquiry-control-custom" type="date" name="checkin" id="enq_checkin">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-15">
                                <label class="enquiry-label-custom">Check-out Date *</label>
                                <input class="enquiry-control-custom" type="date" name="checkout" id="enq_checkout">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conditional Banquet Fields (hidden by default) -->
                <div id="banquetEnquiryFields" style="display: none;">
                    <div class="form-group mb-15">
                        <label class="enquiry-label-custom">Event Date *</label>
                        <input class="enquiry-control-custom" type="date" name="event_date" id="enq_event_date">
                    </div>
                </div>

                <button type="submit" class="enquiry-btn-submit" id="enquirySubmitBtn">Submit Enquiry</button>
            </form>
        </div>
    </div>

    <script>
        function openCallModal() {
            document.getElementById('callModalOverlay').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // prevent page scroll
        }

        function closeCallModal() {
            document.getElementById('callModalOverlay').style.display = 'none';
            document.body.style.overflow = ''; // restore page scroll
        }

        function openEnquiryModal() {
            document.getElementById('enquiryModalOverlay').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // prevent page scroll
            toggleEnquiryFields();
        }

        function closeEnquiryModal() {
            document.getElementById('enquiryModalOverlay').style.display = 'none';
            document.body.style.overflow = ''; // restore page scroll
            
            // Reset form and alerts
            document.getElementById('headerEnquiryForm').reset();
            document.getElementById('enquiryAlertBox').style.display = 'none';
            toggleEnquiryFields();
        }

        function toggleEnquiryFields() {
            var type = document.getElementById('enquiryTypeSelect').value;
            var roomsFields = document.getElementById('roomsEnquiryFields');
            var banquetFields = document.getElementById('banquetEnquiryFields');
            
            var enqCheckin = document.getElementById('enq_checkin');
            var enqCheckout = document.getElementById('enq_checkout');
            var enqEventDate = document.getElementById('enq_event_date');

            if (type === 'rooms') {
                roomsFields.style.display = 'block';
                banquetFields.style.display = 'none';
                enqCheckin.required = true;
                enqCheckout.required = true;
                enqEventDate.required = false;
            } else {
                roomsFields.style.display = 'none';
                banquetFields.style.display = 'block';
                enqCheckin.required = false;
                enqCheckout.required = false;
                enqEventDate.required = true;
            }
        }

        function submitHeaderEnquiry(e) {
            e.preventDefault();
            
            var form = document.getElementById('headerEnquiryForm');
            var alertBox = document.getElementById('enquiryAlertBox');
            var submitBtn = document.getElementById('enquirySubmitBtn');
            
            submitBtn.disabled = true;
            submitBtn.innerText = 'Submitting...';
            
            var formData = new FormData(form);
            
            fetch('submit-general-enquiry.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Submit Enquiry';
                
                alertBox.style.display = 'block';
                if (data.success) {
                    alertBox.className = 'alert alert-success';
                    alertBox.style.backgroundColor = 'rgba(16, 185, 129, 0.1)';
                    alertBox.style.border = '1px solid rgba(16, 185, 129, 0.2)';
                    alertBox.style.color = '#047857';
                    alertBox.innerText = data.message;
                    
                    // Clear the form fields
                    form.reset();
                    
                    // Close modal after 2.5 seconds
                    setTimeout(closeEnquiryModal, 2500);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
                    alertBox.style.border = '1px solid rgba(239, 68, 68, 0.2)';
                    alertBox.style.color = '#b91c1c';
                    alertBox.innerText = data.message;
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Submit Enquiry';
                alertBox.style.display = 'block';
                alertBox.className = 'alert alert-danger';
                alertBox.style.backgroundColor = 'rgba(239, 68, 68, 0.1)';
                alertBox.style.border = '1px solid rgba(239, 68, 68, 0.2)';
                alertBox.style.color = '#b91c1c';
                alertBox.innerText = 'An unexpected network error occurred.';
                console.error(err);
            });
        }

        // Intercept click on the Call Us button for desktop width (>= 992px)
        document.addEventListener('DOMContentLoaded', function() {
            var callBtns = document.querySelectorAll('.btn-call-header');
            callBtns.forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    if (window.innerWidth >= 992) {
                        e.preventDefault();
                        openCallModal();
                    }
                });
            });

            // Close on clicking outside the modal card
            var overlay = document.getElementById('callModalOverlay');
            if (overlay) {
                overlay.addEventListener('click', function(e) {
                    if (e.target === overlay) {
                        closeCallModal();
                    }
                });
            }

            var enqOverlay = document.getElementById('enquiryModalOverlay');
            if (enqOverlay) {
                enqOverlay.addEventListener('click', function(e) {
                    if (e.target === enqOverlay) {
                        closeEnquiryModal();
                    }
                });
            }

            toggleEnquiryFields();
        });
    </script>