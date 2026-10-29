-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: hotel_destin
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'admin','info@hoteldestin.in','$2y$10$ebB43x8zzu9Fih0uN6TyD.7loo8b86fsLNL/ZVH/7K7mbCYmfiUBS','admin',NULL,NULL,'2026-07-07 09:25:24'),(3,'developer','developer@hoteldestin.in','$2y$10$xlDoRErK1bwaSSVdS153TuqXuekktcxZFk4PRqeodFrq2RelJHyp.','developer',NULL,NULL,'2026-08-09 16:59:40');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blogs`
--

DROP TABLE IF EXISTS `blogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blogs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/imgs/page/homepage1/news.png',
  `date` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_time` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blogs`
--

LOCK TABLES `blogs` WRITE;
/*!40000 ALTER TABLE `blogs` DISABLE KEYS */;
INSERT INTO `blogs` VALUES (1,'the-view-from-our-rooms-what-you-will-actually-see','The View From Our Rooms: What You will Actually See','Blog','uploads/blog_6aacfd7b82e797.06704704.png','18 Sep 2026','3 min read','We get this question a lot, usually right after someone books: What will I actually see from my room? It is a fair question. Hotel photos can sometim...','<p>We get this question a lot, usually right after someone books: \"What will I actually see from my room?\"</p>\r\n<p>It\'s a fair question. Hotel photos can sometimes promise more than they deliver, and we\'d rather you know exactly what to expect before you arrive, not be surprised (in a good or bad way) at check-in. So here\'s an honest look at the views from Hotel Destin, room by room, floor by floor.</p>\r\n<h2><strong>Mornings That Wake You Up Gently</strong></h2>\r\n<p>Most of our rooms face east, which means your mornings start with soft, natural light filtering through the curtains rather than a jarring alarm. Guests often tell us they end up waking before their alarm just because the light feels so pleasant. If you\'re someone who likes a slow, calm start to the day, this alone makes a difference.</p>\r\n<h2><strong>A Slice of Gwalior\'s Skyline</strong></h2>\r\n<p>From our higher floors, you get a genuine view of Gwalior\'s evolving skyline a mix of old and new, with rooftops, trees, and the city quietly going about its day. On clear evenings, you can even catch a glimpse of the hills that surround the city, especially as the sun starts to set and the sky turns shades of orange and pink.</p>\r\n<h2><strong>City Life, Right Outside Your Window</strong></h2>\r\n<p>Some of our rooms overlook the street and the neighbourhood around the hotel. If you enjoy people-watching or simply like feeling connected to the city rather than tucked away from it, these rooms give you exactly that. You\'ll see the everyday rhythm of Gwalior shopkeepers opening up, traffic easing by, and the city slowly coming alive each morning.</p>\r\n<h2><strong>Green Views for a Quieter Stay</strong></h2>\r\n<p>For guests who prefer a calmer visual, our garden-facing rooms look out over well-kept greenery and open space. It\'s a small thing, but waking up to green trees instead of concrete can genuinely change the mood of your stay, especially if you\'re here to unwind rather than rush through meetings.</p>\r\n<h2><strong>What You Won\'t See (And Why That\'s Okay)</strong></h2>\r\n<p>We won\'t pretend every room overlooks a monument or a river. Gwalior is a city with real character, not a postcard, and our rooms reflect an honest slice of that everyday charm rather than a heavily edited version of it. What you will get, in every room, is good natural light, clean windows, and a view that feels like the city, not a screen.</p>\r\n<h2><strong>Evenings With a Different Mood</strong></h2>\r\n<p>As the day winds down, the view from your window changes completely. Streetlights come on, the city quiets a little, and from the upper floors, you can often see the twinkle of Gwalior settling into its evening rhythm. It\'s a simple, understated kind of view, but one that many guests say they end up appreciating the most, especially with a cup of tea in hand.</p>\r\n<h2><strong>Choosing the Right Room for the Right View</strong></h2>\r\n<p>If a particular view matters to you, whether that\'s the skyline, the garden, or a quieter street-facing room, just let our front desk team know at the time of booking. We always try our best to match preferences with availability, because we\'d rather you look forward to opening your curtains each morning, not just close them.</p>\r\n<h3><strong>Want to see it for yourself?</strong></h3>\r\n<p>Book a stay at Hotel Destin and find out which view becomes your favourite part of the day. No filters, no exaggeration just an honest room with a view worth waking up to.</p>','The View From Our Rooms: What You will Actually See | Hotel Destin','Curious what you will really see from your window? Explore the real views from Hotel Destin rooms in Gwalior honest, no filters.','2026-07-07 12:46:29'),(2,'why-guests-keep-coming-back-hotel-destin','Why Guests Keep Coming Back to Hotel Destin','Blog','uploads/blog_6aa94db7f0f148.50140609.png','18 Sep 2026','4 min read','Ask anyone who has stayed with us more than once, and they will probably struggle to explain it in one line. It is not just the rooms, or the food, or...','<p>Ask anyone who has stayed with us more than once, and they will probably struggle to explain it in one line. It is not just the rooms, or the food, or the location. It\'s a feeling the kind that makes you book your next trip to Gwalior at the same place without even comparing options. That\'s exactly what we hear from so many of our guests, and it\'s why Hotel Destin has quietly become one of the most loved names among hotels in Gwalior.</p>\r\n<p>Here\'s what we think keeps bringing people back.</p>\r\n<h2><strong>A Warm Welcome, Every Single Time</strong></h2>\r\n<p>First impressions matter, but consistency matters more. Whether it\'s your first stay or your fifteenth, our team greets you the same way genuinely happy to see you. Many of our regular guests are recognised by name at the front desk, and small touches like remembering a preferred room or a favourite breakfast item go a long way in making people feel valued, not just accommodated.</p>\r\n<h2><strong>Comfort That Feels Like Home</strong></h2>\r\n<p>A good hotel gives you a room. A great one gives you a place to actually relax. Our rooms are kept clean, comfortable, and quiet, with the little essentials always in place, so you never have to call the front desk twice for the same thing. After a long day of work, travel, or sightseeing around Gwalior, that kind of comfort is what people remember.</p>\r\n<h2><strong>Food People Actually Talk About</strong></h2>\r\n<p>A lot of our repeat guests admit that our restaurant is one of the reasons they return. From comforting home-style meals to regional Gwalior flavours, our kitchen focuses on taste and freshness over anything fancy. Business travellers often tell us they plan their meetings around our breakfast timing, and families love that there\'s always something for the kids too.</p>\r\n<h2><strong>Location That Makes Life Easier</strong></h2>\r\n<p>Being conveniently located near Gwalior\'s key business areas, railway station, and historic attractions like Gwalior Fort makes a real difference, especially for guests visiting for work or short family trips. Less time spent commuting means more time for what actually matters, whether that\'s meetings, sightseeing, or simply resting.</p>\r\n<h2><strong>Service That Solves Problems Before You Notice Them</strong></h2>\r\n<p>Things don\'t always go perfectly during travel, and how a hotel handles those moments says a lot. Our staff are trained to anticipate needs rather than just react to complaints. An early check-in request, a late-night snack craving, a last-minute airport pickup our team treats these as normal requests, not inconveniences. That reliability builds trust, and trust is what brings guests back.</p>\r\n<h2><strong>A Team That Genuinely Cares</strong></h2>\r\n<p>At the heart of it, hospitality is about people. Many of our staff members have been with Hotel Destin for years, and they take real pride in making guests feel comfortable. That sense of ownership is hard to fake, and guests notice it immediately, whether it\'s the housekeeping team or the person managing your reservation.</p>\r\n<h2><strong>Value That Feels Fair</strong></h2>\r\n<p>Good hospitality shouldn\'t come with a guessing game on pricing. We aim to keep our rates honest and our service consistent, so guests feel they\'re getting real value for what they pay, every single time they stay with us.</p>\r\n<h2><strong>More Than Just a Hotel</strong></h2>\r\n<p>For many of our guests, Hotel Destin isn\'t just a place to sleep between meetings or outings. It\'s become a familiar, comfortable part of their trips to Gwalior. That\'s not something we take lightly, and it\'s what motivates our team to keep improving, one stay at a time.</p>\r\n<p><strong>Planning a visit to Gwalior?</strong></p>\r\n<p>Come experience the comfort, warmth, and reliability that keep our guests returning. Book your stay at Hotel Destin today, and see why so many travellers now call us their Gwalior home away from home.</p>','Why Guests Keep Coming Back to Hotel Destin, Gwalior','Discover why Hotel Destin is the best hotel in Gwalior for repeat guests warm hospitality, comfort, great food, and a stay that feels like home.','2026-07-07 12:46:29'),(6,'weddings-and-events-at-hotel-destin-what-goes-into-making-your-day-perfect','Weddings and Events at Hotel Destin: What Goes Into Making Your Day Perfect','Blog','uploads/blog_6aa93c523c0d33.29082447.jpeg','18 Sep 2026','4 min read','Every wedding tells a story. Some are grand and full of colour, others are small and intimate, but every one deserves to feel special. As one of the m...','<p>Every wedding tells a story. Some are grand and full of colour, others are small and intimate, but every one deserves to feel special. As one of the most trusted names for a wedding venue in Gwalior, Hotel Destin has had the privilege of hosting many such stories over the years. If there\'s one thing we\'ve learned, it\'s this: a perfect wedding isn\'t about how big the event is it\'s about how well every little detail comes together.</p>\r\n<p>So, what really goes into planning weddings and events in Gwalior that guests remember for years? Let\'s walk you through it.</p>\r\n<h2>It Starts With Listening</h2>\r\n<p>Before we talk about d&eacute;cor or menus, we sit down with you and simply listen. Every family has a different vision. Some want a traditional Gwalior wedding with all the rituals done just right. Others dream of a modern, minimal celebration with close friends and family. Our job is to understand what \"perfect\" means to you, not to us.</p>\r\n<h2>A Wedding Venue in Gwalior That Feels Like Home</h2>\r\n<p>Gwalior has such a rich history and charm, and we believe your wedding venue should reflect that warmth. Hotel Destin offers spacious lawns for joyful baraats and mehendi functions, along with elegant indoor banquet halls in Gwalior for receptions and intimate ceremonies. Whether your guest list is 50 or 500, we have a space that fits your celebration comfortably, without anyone feeling cramped or lost in a space too big.</p>\r\n<h2>Food That Guests Remember</h2>\r\n<p>Let\'s be honest half the conversations after a wedding are about the food. Our in-house catering team takes this seriously. From classic North Indian thalis to regional Gwalior specialities, live counters, and modern fusion dishes, we design menus that suit your taste and your guests\' preferences. We also happily accommodate special requests, whether that\'s a Jain menu, a specific sweet your grandmother insists on, or a live chaat counter for the evening crowd.</p>\r\n<h2>Decor That Tells Your Story</h2>\r\n<p>You shouldn\'t have to compromise on how your mandap looks or how your reception stage feels. Our d&eacute;cor partners work closely with you to bring your ideas to life, whether that\'s a floral theme, a royal Rajwada-inspired setting befitting Gwalior\'s palace heritage, or a simple, elegant look with fairy lights and candles. We handle the coordination so you don\'t have to run around checking on flower deliveries or lighting setups on your big day.</p>\r\n<h2>Comfortable Stays for Your Guests</h2>\r\n<p>Weddings usually mean relatives and friends travelling from different cities. Hotel Destin offers comfortable rooms right on the property, so your guests don\'t have to worry about commuting back and forth for different functions. This also means more time for everyone to enjoy the celebrations together, rather than rushing between hotels and venues making us a convenient choice for a destination wedding in Gwalior.</p>\r\n<h2>One Team, Handling Everything</h2>\r\n<p>Perhaps the most important part of planning a wedding is not having to manage a hundred vendors on your own. Our dedicated event management team in Gwalior coordinates everything catering, d&eacute;cor, sound and lighting, guest accommodation, and even last-minute changes. We understand that even the best-planned weddings need flexibility, and we\'re always prepared to adjust and support you as things evolve.</p>\r\n<h2>Making Room for the Small Moments</h2>\r\n<p>Beyond the big functions, we also love helping with the smaller, quieter moments a family lunch the day after the wedding, a sangeet rehearsal, or a simple morning tea for close relatives. These moments often become the ones people remember most fondly, and we make sure they get just as much care and attention as the main event.</p>\r\n<h2>Your Day, Your Way</h2>\r\n<p>At the end of it all, a wedding at Hotel Destin is about celebrating you and your family in a space that feels warm, festive, and stress-free. We\'re not just providing a wedding venue in Gwalior; we\'re helping you create memories that will be talked about for years to come.</p>\r\n<p>If you\'re planning your wedding or any special event in Gwalior, we\'d love to hear your story and help bring it to life. Come visit us, share your vision, and let\'s start planning a celebration that feels truly yours.</p>\r\n<h2>Ready to Plan Your Special Day?</h2>\r\n<p>Reach out to the Hotel Destin events team today for a personalised tour and consultation, and let\'s start planning the wedding of your dreams in Gwalior.</p>','Wedding Events at Hotel Destin Gwalior | Best Wedding Venue','Planning a wedding in Gwalior? Discover how Hotel Destin creates unforgettable weddings and events with elegant venues, catering, decor more.','2026-07-09 10:05:46');
/*!40000 ALTER TABLE `blogs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_no` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `guests` int NOT NULL DEFAULT '1',
  `meal_plan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'EP',
  `adults` int NOT NULL DEFAULT '2',
  `children` int NOT NULL DEFAULT '0',
  `child_ages` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `room_id` int NOT NULL,
  `physical_room_id` int DEFAULT NULL,
  `coupon_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_nights` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `base_amount` decimal(10,2) NOT NULL,
  `tax_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Razorpay',
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `booking_status` enum('pending','confirmed','checked_in','checked_out','cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `special_request` text COLLATE utf8mb4_unicode_ci,
  `razorpay_order_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refund_tx_id` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `cancellation_reason` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_id` (`booking_id`),
  KEY `room_id` (`room_id`),
  KEY `fk_bookings_physical_room` (`physical_room_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_bookings_physical_room` FOREIGN KEY (`physical_room_id`) REFERENCES `physical_rooms` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,'GV-20260828-68F307','INV-20260828-0001','fresh testing 28th august','gauravdhangar12@gmail.com','09373646951','2026-08-28','2026-08-29',2,'EP',2,0,'',1,NULL,NULL,1,2190.00,109.50,2190.00,109.50,0.00,2299.50,'Pay at Hotel','paid','checked_out','need extra blanket',NULL,NULL,NULL,'2026-08-28 12:31:17',NULL),(2,'GV-20260918-9B49C7',NULL,'Neetu Thomas','neeturonnthomas@rediffmail.com','9511000099','2026-09-19','2026-09-21',2,'CP',2,0,'',1,NULL,NULL,2,4400.00,220.00,4400.00,220.00,0.00,4620.00,'Pay at Hotel','pending','confirmed','',NULL,NULL,NULL,'2026-09-18 11:59:21',NULL),(3,'GV-20260921-A7D3B2',NULL,'Akshay Thakur','at1650000@gmail.com','9793129576','2026-09-21','2026-09-22',2,'EP',2,0,'',1,NULL,NULL,1,1800.00,90.00,1800.00,90.00,0.00,1890.00,'Pay at Hotel','pending','checked_in','',NULL,NULL,NULL,'2026-09-21 09:49:32',NULL),(4,'GV-20261010-058FF8',NULL,'Abhinay','abhisrit03@gmail.com','8595386019','2026-10-10','2026-10-11',4,'MAP',2,2,'5,5',1,NULL,NULL,1,2700.00,135.00,2700.00,135.00,0.00,2835.00,'Pay at Hotel','pending','confirmed','',NULL,NULL,NULL,'2026-10-10 12:14:58',NULL);
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_percent` int NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `show_in_checkout` tinyint DEFAULT '0',
  `start_date` date NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupons`
--

LOCK TABLES `coupons` WRITE;
/*!40000 ALTER TABLE `coupons` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupons` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enquiries`
--

DROP TABLE IF EXISTS `enquiries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enquiries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date DEFAULT NULL,
  `guests` int DEFAULT NULL,
  `requirements` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','contacted','converted','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `followup_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enquiries`
--

LOCK TABLES `enquiries` WRITE;
/*!40000 ALTER TABLE `enquiries` DISABLE KEYS */;
INSERT INTO `enquiries` VALUES (83,'restaurant','testing','testing@gmail.com','+919873646156','2026-08-28',1,'Time: 10:00 PM | Seating: Rooftop Lounge | Preference: Veg Only','contacted','Testing','2026-08-27 13:21:09'),(84,'banquet','testing whatsapp api','no-email@hoteldestin.in','919205104645','2026-09-09',NULL,'General Banquet Hall Enquiry via header.','pending',NULL,'2026-09-08 08:51:43');
/*!40000 ALTER TABLE `enquiries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
INSERT INTO `gallery` VALUES (36,'Premium rooms','rooms','uploads/img_6a5f808b593471.73056268.jpg','','2026-07-21 14:22:03'),(37,'Gallery','rooms','uploads/img_6a5f80a87a0837.99600106.jpg','','2026-07-21 14:22:32'),(38,'Common Area','rooms','uploads/img_6a5f80c1a28b92.58360312.jpg','','2026-07-21 14:22:57'),(39,'Reception','rooms','uploads/img_6a5f80df4e3812.00558020.jpg','','2026-07-21 14:23:27'),(40,'Entrance','rooms','uploads/img_6a5f80f443c3c5.16962372.jpg','','2026-07-21 14:23:48'),(47,'Entrance','restaurant','uploads/img_6a607c9c0b96b8.51497515.jpg','','2026-07-22 08:17:32'),(48,'Entry','rooms','uploads/img_6a607cab5ed365.90957401.jpg','','2026-07-22 08:17:47'),(49,'ambience (1)','rooms','uploads/img_6a7985e60a4fd8.66079636.jpeg','','2026-08-10 08:03:50'),(50,'ambience (2)','rooms','uploads/img_6a7985e60da8b5.84921104.jpeg','','2026-08-10 08:03:50'),(51,'ambience (3)','rooms','uploads/img_6a7985e60f5a54.06498738.jpeg','','2026-08-10 08:03:50'),(52,'ambience (4)','rooms','uploads/img_6a7985e6119236.47469922.jpeg','','2026-08-10 08:03:50'),(54,'ambience (6)','rooms','uploads/img_6a7985e61556d5.84831350.jpeg','','2026-08-10 08:03:50'),(55,'ambience (7)','rooms','uploads/img_6a7985e61745f2.45147737.jpeg','','2026-08-10 08:03:50'),(58,'Rooms','rooms','uploads/img_6a89a5c34e6012.17855036.jpeg','','2026-08-22 13:36:03'),(60,'Rooms','rooms','uploads/img_6a89a68963b8a2.59180669.jpeg','','2026-08-22 13:39:21'),(61,'Rooms','rooms','uploads/img_6a89a6d7c07c07.54143563.jpeg','','2026-08-22 13:40:39'),(63,'premium Rooms','rooms','uploads/img_6a89a805511cb4.26929358.jpeg','','2026-08-22 13:45:41'),(64,'Banquet','banquet','uploads/img_6a89a83d6b5452.04701714.jpeg','','2026-08-22 13:46:37'),(65,'Banquet','banquet','uploads/img_6a89a86399e727.51792690.jpeg','','2026-08-22 13:47:15'),(67,'Banquet','banquet','uploads/img_6a89aa60a56ce5.22644299.jpeg','','2026-08-22 13:55:44'),(68,'Banquet','banquet','uploads/img_6a89ac81db44f7.16623599.jpeg','','2026-08-22 14:04:49'),(69,'ambience5','rooms','uploads/img_6a89acdf14b657.15546353.jpeg','','2026-08-22 14:06:23'),(70,'Banquet Area','banquet','uploads/img_6a89ad3fd142e5.16118006.jpeg','','2026-08-22 14:07:59'),(71,'Washroom','rooms','uploads/img_6a89ad979fa206.76513022.jpeg','','2026-08-22 14:09:27'),(72,'Cafe','restaurant','uploads/img_6a89aee91800f3.34322450.jpeg','','2026-08-22 14:15:05'),(73,'Restaurant','restaurant','uploads/img_6a89af2619e204.68443805.jpeg','','2026-08-22 14:16:06'),(74,'Restaurant','restaurant','uploads/img_6a89af514764c9.12411211.jpeg','','2026-08-22 14:16:49');
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `booking_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `enquiry_id` int DEFAULT NULL,
  `status` enum('success','failed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
INSERT INTO `notification_logs` VALUES (1,'whatsapp',NULL,NULL,'success','🔔 *Hotel Destin - Secure Settings Test* 🔔\n\nThis confirms that your secure dynamic WhatsApp settings are functioning perfectly!','Message ID: 3EB04A38D99DC8BB14A90D','2026-07-24 10:22:14'),(2,'whatsapp',NULL,NULL,'success','🔔 *NEW ENQUIRY RECEIVED* 🔔\n\n*Category:* RESTAURANT\n*Name:* test\n*Email:* gauravdhangar12@gmail.com\n*Phone:* +91 9203509944\n*Date:* 2026-07-31\n*Guests:* 3\n\n*Additional Info:*\n- *Time Preferred:* 08:00 AM\n- *Seating Option:* Private Dining Area\n- *Food/Bar Preference:* Non-Veg Only\n','Message ID: 3EB0751DB5DD1C8498F87A','2026-07-24 10:27:35'),(3,'whatsapp',NULL,NULL,'success','🔔 *Hotel Destin - Secure Settings Test* 🔔\n\nThis confirms that your secure dynamic WhatsApp settings are functioning perfectly!\n\n_Ref: B3EE1B | 24-Jul 10:34_','Message ID: 3EB0C5D01F56C221816170','2026-07-24 10:34:51'),(4,'whatsapp',NULL,NULL,'success','🔔 *NEW ENQUIRY RECEIVED* 🔔\n\n*Category:* RESTAURANT\n*Name:* test\n*Email:* gauravdhangar12@gmail.com\n*Phone:* +91 9203509944\n*Date:* 2026-07-31\n*Guests:* 2\n\n*Additional Info:*\n- *Time Preferred:* 07:00 PM\n- *Seating Option:* Private Dining Area\n- *Food/Bar Preference:* Veg Only\n\n\n_Ref: A88229 | 24-Jul 10:39_','Message ID: 3EB0E4E3A43CA52B86CD28','2026-07-24 10:39:41');
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `physical_rooms`
--

DROP TABLE IF EXISTS `physical_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `physical_rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_number` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `status` enum('Available','Booked','Occupied','Cleaning','Maintenance','Out of Service') COLLATE utf8mb4_unicode_ci DEFAULT 'Available',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `room_number` (`room_number`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `physical_rooms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `physical_rooms`
--

LOCK TABLES `physical_rooms` WRITE;
/*!40000 ALTER TABLE `physical_rooms` DISABLE KEYS */;
INSERT INTO `physical_rooms` VALUES (1,'101',1,'Occupied','2026-07-08 08:20:23'),(2,'102',1,'Available','2026-07-08 08:20:23'),(3,'103',1,'Available','2026-07-08 08:20:23'),(4,'104',1,'Available','2026-07-08 08:20:23'),(5,'105',1,'Available','2026-07-08 08:20:23'),(6,'201',2,'Available','2026-07-08 08:20:23'),(7,'202',2,'Available','2026-07-08 08:20:23'),(8,'203',2,'Available','2026-07-08 08:20:23'),(9,'301',3,'Available','2026-07-08 08:20:23');
/*!40000 ALTER TABLE `physical_rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_facilities`
--

DROP TABLE IF EXISTS `room_facilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_facilities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `facility_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `room_facilities_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=715 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_facilities`
--

LOCK TABLES `room_facilities` WRITE;
/*!40000 ALTER TABLE `room_facilities` DISABLE KEYS */;
INSERT INTO `room_facilities` VALUES (687,3,'AC'),(688,3,'Free Wi-Fi'),(689,3,'Laundry'),(690,3,'King Bed'),(691,3,'Safe Box'),(702,1,'AC'),(703,1,'Free Wi-Fi'),(704,1,'Laundry'),(705,1,'King Bed'),(706,1,'Safe Box'),(707,1,'best room'),(708,1,'sofa set'),(709,1,'free water bottle'),(710,2,'AC'),(711,2,'Free Wi-Fi'),(712,2,'Laundry'),(713,2,'King Bed'),(714,2,'Safe Box');
/*!40000 ALTER TABLE `room_facilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_images`
--

DROP TABLE IF EXISTS `room_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_images` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_images`
--

LOCK TABLES `room_images` WRITE;
/*!40000 ALTER TABLE `room_images` DISABLE KEYS */;
INSERT INTO `room_images` VALUES (41,1,'uploads/rooms/a892480327a00682bf8e5cc5cd79cc0d.jpeg',0),(42,1,'uploads/rooms/77a97b1d6b553bd184444c9308f280fe.jpeg',0),(43,2,'uploads/rooms/c0006a56f4cd214c34fbe750812f73c9.jpeg',0),(44,2,'uploads/rooms/e3f7e8fad9484ab436e21de34c2c2287.jpeg',0),(45,3,'uploads/rooms/5e5e75274b25a16310192d1f0220779f.jpeg',0),(46,3,'uploads/rooms/a092daa358305a2a25f862aac440532e.jpeg',0),(47,3,'uploads/rooms/cadf202a1bd9182aafa63f81527dd358.jpeg',0),(48,1,'uploads/rooms/1d61a2c8a7d660dc0d56a8506fe37b37.jpeg',0),(49,2,'uploads/rooms/30ec05c4ba9dd4e167968ef7997611f1.jpeg',0);
/*!40000 ALTER TABLE `room_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `room_rate_calendars`
--

DROP TABLE IF EXISTS `room_rate_calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `room_rate_calendars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_category_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `ep_price` decimal(10,2) NOT NULL,
  `cp_price` decimal(10,2) NOT NULL,
  `map_price` decimal(10,2) NOT NULL,
  `price_single_ep` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_single_cp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_single_map` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_triple_ep` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_triple_cp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_triple_map` decimal(10,2) NOT NULL DEFAULT '0.00',
  `extra_child_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `struck_price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_category_id` (`room_category_id`),
  CONSTRAINT `room_rate_calendars_ibfk_1` FOREIGN KEY (`room_category_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `room_rate_calendars`
--

LOCK TABLES `room_rate_calendars` WRITE;
/*!40000 ALTER TABLE `room_rate_calendars` DISABLE KEYS */;
/*!40000 ALTER TABLE `room_rate_calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `struck_price` decimal(10,2) NOT NULL,
  `discount` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'DESTIN',
  `inventory` int NOT NULL DEFAULT '1',
  `capacity_adults` int NOT NULL DEFAULT '2',
  `capacity_children` int NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/imgs/page/room/banner-room.png',
  `meta_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price_single_ep` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_single_cp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_single_map` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_double_ep` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_double_cp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_double_map` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_triple_ep` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_triple_cp` decimal(10,2) NOT NULL DEFAULT '0.00',
  `price_triple_map` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status_badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'POPULAR',
  `rating` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'G 4.8 ★',
  `banner_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_adult_price` decimal(10,2) NOT NULL DEFAULT '1000.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'standard-room','Executive Room - Hotel Destin','Executive',1800.00,2500.00,'10% off','DESTIN',22,2,1,'<p>Our Executive rooms offer a comfortable double-bed space, work desk, and private bathroom, perfect for standard traveler stays.</p>','uploads/rooms/cda1fc919d354c81c22855918a137685.jpeg',NULL,NULL,'active','2026-07-07 09:25:24',1500.00,1800.00,2100.00,1800.00,2200.00,2700.00,2100.00,2600.00,3200.00,'BEST SELLER','G 4.8 ★','Get Destin, and get 25% off (up to ₹1,000) on your booking',1000.00),(2,'executive-room','Deluxe Room - Hotel Destin','Deluxe',2000.00,3200.00,'58% off','DESTIN',4,2,1,'<p>Spacious, functional, and modern executive space features comfortable beds and business features.</p>','uploads/rooms/4b7d85d819df2741b96f0e529a71e83c.jpeg',NULL,NULL,'active','2026-07-07 09:25:24',1800.00,2200.00,2600.00,2000.00,2500.00,3000.00,2200.00,2800.00,3400.00,'POPULAR','G 4.8 ★','Get Destin, and get 25% off (up to ₹1,000) on your booking',500.00),(3,'premium-room','Premium Room - Hotel Destin','Premium',2500.00,3500.00,'58% off','DESTIN',1,2,1,'<h2>&nbsp;</h2>\r\n<p>one of the best rooms, spacious, luxuries.</p>','uploads/rooms/8cee3f08e966912cfdd41d4f1fd2ac3e.jpeg',NULL,NULL,'active','2026-07-07 09:25:24',2200.00,2600.00,3000.00,2500.00,2800.00,3400.00,2800.00,3200.00,3800.00,'LUXURY CHOICE','G 4.9 ★','Get Destin, and get 25% off (up to ₹1,000) on your booking',500.00);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `val_content` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1208 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'gtm_code','','2026-07-07 09:25:24'),(2,'meta_pixel','','2026-07-07 09:25:24'),(3,'google_analytics','','2026-07-07 09:25:24'),(4,'hotel_name','Hotel Destin','2026-07-07 10:36:32'),(5,'hotel_phone','+91 9203509944','2026-07-07 10:36:32'),(6,'hotel_email','info.hoteldestingwalior@gmail.com','2026-07-07 10:36:32'),(7,'hotel_address','Hotel  destin Gwalior Sachin Tendulkar road Near Ram Vatika marriage garden Govindpuri Gwalior','2026-07-07 10:36:32'),(8,'razorpay_key_id','rzp_test_YourKeyHere','2026-07-07 10:36:32'),(9,'razorpay_key_secret','YourSecretHere','2026-07-07 10:36:32'),(46,'hotel_whatsapp','+91 9203509944','2026-07-07 13:35:03'),(197,'announcement_text','🌟 Experience Comfort & Luxury at Hotel Destin','2026-07-09 15:05:26'),(198,'announcement_status','inactive','2026-07-09 15:05:26'),(199,'hero_title','Experience Luxury & Comfort','2026-07-09 15:05:26'),(200,'hero_subtitle','in the heart of Gwalior','2026-07-09 15:05:26'),(233,'hero_bg_image','uploads/hero_bg_1_6a85d6ac085901.68060191.png','2026-07-09 16:03:57'),(249,'coupon_banner_title','Get 20% discount on every rooms just use Coupon code : \"Destin\"','2026-07-10 08:08:02'),(250,'coupon_banner_subtitle','Celebrate your stay at Hotel Destin with exclusive savings. Use the coupon code below at checkout.','2026-07-10 08:08:02'),(251,'coupon_banner_terms','*Terms & conditions apply. Valid for standard, executive, and premium room reservations.','2026-07-10 08:08:02'),(252,'coupon_banner_status','inactive','2026-07-10 08:08:02'),(376,'hero_bg_image_2','','2026-07-18 17:03:15'),(377,'hero_bg_image_3','uploads/hero_bg_3_6a830a1c1c1724.57070492.png','2026-07-18 17:03:15'),(385,'hero_slider_interval','4','2026-07-18 17:09:29'),(410,'restaurant_ambience_title','Ambience & Moments','2026-07-21 08:13:05'),(411,'restaurant_ambience_desc','Take a visual tour through our celestial rooftop cafe and warm indoor dining halls.','2026-07-21 08:13:05'),(412,'restaurant_ambience_1_title','Rooftop Skyline Dining','2026-07-21 08:13:05'),(413,'restaurant_ambience_1_desc','Unparalleled city views at dusk','2026-07-21 08:13:05'),(414,'restaurant_ambience_2_title','Rooftop Cafe Lounge','2026-07-21 08:13:05'),(415,'restaurant_ambience_3_title','Gourmet Masterpieces','2026-07-21 08:13:05'),(416,'restaurant_ambience_4_title','Luxury Suite Room Service','2026-07-21 08:13:05'),(417,'restaurant_ambience_1_image','uploads/restaurant_ambience_1_image_6a60c99abdf263.05722091.webp','2026-07-21 08:13:05'),(418,'restaurant_hero_title','Cafe By Soul, Rooftop Cafe','2026-07-21 08:13:55'),(419,'restaurant_hero_tagline','Elevated Gastronomy & Refreshing Brews','2026-07-21 08:13:55'),(420,'restaurant_hero_hours','07:00 AM to 11:30 PM','2026-07-21 08:13:55'),(421,'restaurant_food_types','We have both veg and non-veg food available at our rooftop cafe','2026-07-21 08:13:55'),(422,'restaurant_room_service_text','Room Service & Restaurant Facilities Available','2026-07-21 08:13:55'),(423,'restaurant_hero_bg','uploads/restaurant_hero_bg_6a5f2a43b44436.61939121.jpg','2026-07-21 08:13:55'),(431,'restaurant_ambience_4_image','uploads/restaurant_ambience_4_image_6a60c99ac1d359.05406235.webp','2026-07-21 08:16:34'),(504,'banquet_hall_name','','2026-07-22 10:09:18'),(505,'banquet_hall_capacity','','2026-07-22 10:09:18'),(506,'banquet_hall_size','','2026-07-22 10:09:18'),(507,'banquet_rental_charges','','2026-07-22 10:09:18'),(508,'banquet_decor_management_text','','2026-07-22 10:09:18'),(509,'banquet_description','','2026-07-22 10:09:18'),(510,'banquet_exact_location','','2026-07-22 10:09:18'),(532,'banquet_showcase_bg','','2026-07-22 10:10:25'),(541,'restaurant_ambience_2_image','uploads/restaurant_ambience_2_image_6a60c99abf9ae3.46127778.webp','2026-07-22 13:46:02'),(542,'restaurant_ambience_3_image','uploads/restaurant_ambience_3_image_6a60c99ac0a7a9.77772108.webp','2026-07-22 13:46:02'),(544,'restaurant_facilities_title','Restaurant Facilities','2026-07-22 13:46:40'),(545,'restaurant_facilities_desc','Indulge in our luxurious hospitality features that combine great taste with a premium lounge experience.','2026-07-22 13:46:40'),(546,'restaurant_facility_1_title','Rooftop Cafe','2026-07-22 13:46:40'),(547,'restaurant_facility_1_badge','Open Daily','2026-07-22 13:46:40'),(548,'restaurant_facility_1_desc','Unwind under the stars with our premium coffees, mocktails, and ambient tunes at Gwalior\'s premier rooftop cafe.','2026-07-22 13:46:40'),(549,'restaurant_facility_1_icon','<svg width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M18 22H6M12 15v7M12 15l7-10H5l7 10zM12 9l-2-2h4l-2 2z\"/></svg>','2026-07-22 13:46:40'),(550,'restaurant_facility_2_title','Fine Dining Restaurant','2026-07-22 13:46:40'),(551,'restaurant_facility_2_badge','Family Friendly','2026-07-22 13:46:40'),(552,'restaurant_facility_2_desc','A sophisticated family dining atmosphere offering an exquisite spread of pure vegetarian and gourmet non-vegetarian options prepared by master chefs.','2026-07-22 13:46:40'),(553,'restaurant_facility_2_icon','<svg width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M2 20h20M12 4v3M12 7a8 8 0 0 0-8 8h16a8 8 0 0 0-8-8zM5 15h14\"/></svg>','2026-07-22 13:46:40'),(554,'restaurant_facility_3_title','In-Room Dining','2026-07-22 13:46:40'),(555,'restaurant_facility_3_badge','For In-House Guests','2026-07-22 13:46:40'),(556,'restaurant_facility_3_desc','Experience restaurant-quality hot meals delivered directly to the comfort of your executive room or suite at any hour during operating times.','2026-07-22 13:46:40'),(557,'restaurant_facility_3_icon','<svg width=\"28\" height=\"28\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"1.8\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><rect x=\"3\" y=\"4\" width=\"18\" height=\"12\" rx=\"2\"/><path d=\"M12 2v2M7 8h10M12 12h.01M3 20h18\"/></svg>','2026-07-22 13:46:40'),(558,'restaurant_facility_1_image','uploads/restaurant_facility_1_image_6a60c9c0d19139.61321770.webp','2026-07-22 13:46:40'),(573,'restaurant_facility_3_image','uploads/restaurant_facility_3_image_6a798a73e15993.62096489.jpeg','2026-07-22 13:47:00'),(590,'hourly_rate_standard','500','2026-07-23 15:29:48'),(591,'hourly_rate_executive','550','2026-07-23 15:29:48'),(592,'hourly_rate_premium','700','2026-07-23 15:29:48'),(596,'hourly_3_standard','1000','2026-07-23 15:57:42'),(597,'hourly_6_standard','1300','2026-07-23 15:57:42'),(598,'hourly_9_standard','1600','2026-07-23 15:57:42'),(599,'hourly_12_standard','1800','2026-07-23 15:57:42'),(600,'hourly_3_executive','1200','2026-07-23 15:57:42'),(601,'hourly_6_executive','1500','2026-07-23 15:57:42'),(602,'hourly_9_executive','1800','2026-07-23 15:57:42'),(603,'hourly_12_executive','2000','2026-07-23 15:57:42'),(604,'hourly_3_premium','1400','2026-07-23 15:57:42'),(605,'hourly_6_premium','1700','2026-07-23 15:57:42'),(606,'hourly_9_premium','2000','2026-07-23 15:57:42'),(607,'hourly_12_premium','2200','2026-07-23 15:57:42'),(608,'green_api_url','https://7107.api.greenapi.com','2026-07-24 10:14:20'),(609,'green_api_instance_id','710722691057','2026-07-24 10:14:20'),(610,'green_api_token','a07b7e2211e746f8a1c880812fac35680fe1b601fc72416cbb','2026-07-24 10:14:20'),(611,'whatsapp_receiver_number','919873646156','2026-07-24 10:14:20'),(612,'whatsapp_notification_enabled','0','2026-07-24 10:14:20'),(628,'banquet_hero_bg','','2026-07-24 10:23:18'),(736,'restaurant_phone','+91 88268 56001','2026-07-27 08:40:06'),(803,'online_payment_enabled','0','2026-08-09 17:34:40');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `review_text` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'assets/imgs/page/homepage1/author.png',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (1,'Rahul Sharma','New Delhi, India',5,'We stayed at Hotel Destin for a family trip to Gwalior. The rooms were clean, the Wi-Fi was fast, and the staff was extremely polite. We got flat ₹500 cashback too! Gwalior Fort is very close by.','assets/imgs/page/homepage1/avatar-placeholder.svg','active','2026-07-09 15:05:26'),(2,'Priya Patel','Mumbai, India',5,'I visited Gwalior for a business conference and stayed in the Executive Room. Having high-speed Wi-Fi, round-the-clock power backup, and laundry services was a huge lifesaver. Highly recommended!','assets/imgs/page/homepage1/avatar-placeholder.svg','active','2026-07-09 15:05:26'),(3,'Amit Verma','Indore, India',4,'The location on Sachin Tendulkar Road is excellent. Rooms are modern, and the centralized AC works perfectly. Checking in was super fast. Absolute value for money.','assets/imgs/page/homepage1/avatar-placeholder.svg','active','2026-07-09 15:05:26'),(4,'Sneha Gupta','Bhopal, India',5,'Attended a cousin\'s wedding banquet here. The arrangement was beautiful, food was mouthwatering, and the rooms given to guests were very comfortable and tidy. Will definitely book again!','assets/imgs/page/homepage1/avatar-placeholder.svg','active','2026-07-09 15:05:26');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-22 19:11:05
