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



    <header class="header sticky-bar">
      

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
      <div class="container-fluid background-body">
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
                  <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 1.62c4.57 0 8.29 3.72 8.29 8.29s-3.72 8.29-8.29 8.29c-1.55 0-3.03-.43-4.31-1.24l-.31-.18-3.09.81.82-3.01-.2-.32a8.23 8.23 0 0 1-1.27-4.35c0-4.57 3.72-8.29 8.29-8.29zm-3.66 4.28c-.18 0-.44.07-.67.33-.23.26-.88.86-.88 2.1s.9 2.44 1.03 2.61c.13.17 1.77 2.7 4.28 3.78.6.26 1.07.41 1.43.53.6.19 1.15.16 1.58.1.48-.07 1.48-.6 1.69-1.19.21-.58.21-1.09.15-1.19-.06-.1-.23-.17-.48-.29l-2.52-1.24c-.25-.13-.44-.19-.63.09l-.72.9c-.16.2-.33.22-.58.09-.26-.13-1.09-.4-2.07-1.28-.76-.68-1.27-1.52-1.42-1.78-.15-.26-.02-.4.11-.53l.51-.6c.13-.15.17-.26.26-.43a.47.47 0 0 0-.02-.45c-.06-.13-.59-1.42-.81-1.95-.21-.52-.43-.45-.59-.46l-.5-.01z"/>
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
    <div class="mobile-header-active mobile-header-wrapper-style perfect-scrollbar button-bg-2">
      <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-logo"> <a class="d-flex" href="index.php"><img class="light-mode" alt="Hotel Destin" src="assets/imgs/template/logo-destin.png" style="max-height: 60px; width: auto;"><img class="dark-mode" alt="Hotel Destin" src="assets/imgs/template/logo-destin.png" style="max-height: 60px; width: auto;"></a>
          <div class="burger-icon burger-icon-white"></div>
        </div>

        <div class="mobile-header-content-area">
          <div class="perfect-scroll">
            <div class="mobile-menu-wrap mobile-header-border">
              <nav>
                <ul class="mobile-menu font-heading">
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
        </div>
      </div>
    </div>
