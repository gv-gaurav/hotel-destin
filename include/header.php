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
                  <li><a href="gallery.php">Gallery</a></li>

                  <li><a href="blog.php">Blog</a></li>
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
            <div class="d-none d-xxl-inline-block align-middle mr-15"><a class="btn btn-default" href="rooms.php">Book Now</a></div>

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
                  <li><a class="<?= ($current_page === 'gallery.php') ? 'active' : '' ?>" href="gallery.php">Gallery</a></li>
                  <li><a class="<?= ($current_page === 'blog.php') ? 'active' : '' ?>" href="blog.php">Blog</a></li>
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
    </style>