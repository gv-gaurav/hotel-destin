-- schema.sql: MySQL Database Schema for Hotel Destin Gwalior

CREATE DATABASE IF NOT EXISTS `hotel_destin` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hotel_destin`;

-- 1. Admins Table
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(20) DEFAULT 'admin',
  `otp_code` VARCHAR(6) NULL,
  `otp_expires_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Rooms Table
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(100) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `struck_price` DECIMAL(10,2) NOT NULL,
  `discount` VARCHAR(20) NOT NULL,
  `code` VARCHAR(20) DEFAULT 'DESTIN',
  `inventory` INT NOT NULL DEFAULT 1,
  `capacity_adults` INT NOT NULL DEFAULT 2,
  `capacity_children` INT NOT NULL DEFAULT 1,
  `description` TEXT NOT NULL,
  `image_path` VARCHAR(255) DEFAULT 'assets/imgs/page/room/banner-room.png',
  `meta_title` VARCHAR(255) NULL,
  `meta_description` VARCHAR(255) NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Room Images Table
CREATE TABLE IF NOT EXISTS `room_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `is_featured` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Room Facilities Table
CREATE TABLE IF NOT EXISTS `room_facilities` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `facility_name` VARCHAR(100) NOT NULL,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Physical Rooms Table
CREATE TABLE IF NOT EXISTS `physical_rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_number` VARCHAR(10) NOT NULL UNIQUE,
  `category_id` INT NOT NULL,
  `status` ENUM('Available', 'Booked', 'Occupied', 'Cleaning', 'Maintenance', 'Out of Service') DEFAULT 'Available',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bookings Table
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_id` VARCHAR(50) NOT NULL UNIQUE,
  `invoice_no` VARCHAR(50) DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(100) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `guests` INT NOT NULL DEFAULT 1,
  `meal_plan` VARCHAR(50) DEFAULT 'EP',
  `adults` INT NOT NULL DEFAULT 2,
  `children` INT NOT NULL DEFAULT 0,
  `room_id` INT NOT NULL,
  `physical_room_id` INT DEFAULT NULL,
  `coupon_code` VARCHAR(50) DEFAULT NULL,
  `total_nights` INT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `base_amount` DECIMAL(10,2) NOT NULL,
  `tax_amount` DECIMAL(10,2) NOT NULL,
  `discount_amount` DECIMAL(10,2) DEFAULT 0.00,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Razorpay',
  `payment_status` ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
  `booking_status` ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled') DEFAULT 'pending',
  `special_request` TEXT NULL,
  `razorpay_order_id` VARCHAR(100) NULL,
  `razorpay_payment_id` VARCHAR(100) NULL,
  `refund_tx_id` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`physical_room_id`) REFERENCES `physical_rooms`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Coupons Table
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_percent` INT NOT NULL,
  `expiry_date` DATE NOT NULL,
  `status` ENUM('active', 'inactive') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Enquiries Table
CREATE TABLE IF NOT EXISTS `enquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category` VARCHAR(50) NOT NULL, -- 'banquet', 'wedding', 'corporate', 'contact', 'airport_transfer', 'long_stay'
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `date` DATE NULL,
  `guests` INT NULL,
  `requirements` TEXT NULL,
  `status` ENUM('pending', 'contacted', 'converted', 'rejected') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `key_name` VARCHAR(100) NOT NULL UNIQUE,
  `val_content` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Gallery Table
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `description` VARCHAR(255) NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================
-- PRE-POPULATE DATA
-- ==========================================

-- Insert Default Admin Account (Username: admin, Password: admin123)
INSERT INTO `admins` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@example.com', '$2y$10$YYGgEkFa.rInHgdTOHucIuWwkl3TjBKpnm0mArmmy4bDtFsupAEuC', 'admin')
ON DUPLICATE KEY UPDATE `username`=`username`;

-- Insert Default Rooms
INSERT INTO `rooms` (`id`, `slug`, `title`, `type`, `price`, `struck_price`, `discount`, `code`, `inventory`, `capacity_adults`, `capacity_children`, `description`, `image_path`) VALUES
(1, 'standard-room', 'Standard Room - Hotel Destin', 'Standard', 1690.00, 4000.00, '58% off', 'DESTIN', 20, 2, 1, 'Our Standard rooms offer a comfortable double-bed space, work desk, and private bathroom, perfect for standard traveler stays.', 'assets/imgs/page/room/banner-room.png'),
(2, 'executive-room', 'Executive Room - Hotel Destin', 'Executive', 1774.00, 4200.00, '58% off', 'DESTIN', 5, 2, 2, 'Spacious, functional, and modern executive space features comfortable beds and business features.', 'assets/imgs/page/room/banner-room2.png'),
(3, 'premium-room', 'Premium Room - Hotel Destin', 'Premium', 1858.00, 4400.00, '58% off', 'DESTIN', 1, 3, 2, 'Luxury choice accommodations offering premier city panoramas, high ceilings, luxury amenities, and fine design details.', 'assets/imgs/page/pages/banner.png')
ON DUPLICATE KEY UPDATE `slug`=`slug`;

-- Insert Facilities
INSERT INTO `room_facilities` (`room_id`, `facility_name`) VALUES
(1, 'AC'), (1, 'Free Wi-Fi'), (1, 'Laundry'), (1, 'King Bed'), (1, 'Safe Box'),
(2, 'AC'), (2, 'Free Wi-Fi'), (2, 'Laundry'), (2, 'King Bed'), (2, 'Safe Box'),
(3, 'AC'), (3, 'Free Wi-Fi'), (3, 'Laundry'), (3, 'King Bed'), (3, 'Safe Box');

-- Insert Initial Coupons
INSERT INTO `coupons` (`title`, `code`, `discount_percent`, `expiry_date`, `status`) VALUES
('Hotel Destin Special Coupon', 'DESTIN', 58, '2030-12-31', 'active')
ON DUPLICATE KEY UPDATE `code`=`code`;

-- Insert Script settings placeholders
INSERT INTO `settings` (`key_name`, `val_content`) VALUES
('gtm_code', ''),
('meta_pixel', ''),
('google_analytics', ''),
('hotel_name', 'Hotel Destin'),
('hotel_phone', '+91 70000 00000'),
('hotel_email', 'info@hoteldestin.com'),
('hotel_address', 'Sachin Tendulkar Road, Kailash Nagar, Gwalior, MP, India'),
('razorpay_key_id', 'rzp_test_YourKeyHere'),
('razorpay_key_secret', 'YourSecretHere')
ON DUPLICATE KEY UPDATE `key_name`=`key_name`;

-- Insert Default Gallery Items
INSERT INTO `gallery` (`title`, `category`, `image_path`, `description`) VALUES
('Luxury Suite Room', 'rooms', 'assets/imgs/page/hotel/hotelRoom.png', 'Spacious room with king bed and premium view'),
('Grand Ballroom Banquet', 'banquet', 'assets/imgs/page/room/banner-room.png', 'Premium event setup for corporate and family gatherings'),
('Fine Dining Restaurant', 'restaurant', 'assets/imgs/page/pages/banner2.png', 'Elegant dining experience featuring global cuisines'),
('Deluxe Ocean View Room', 'rooms', 'assets/imgs/page/hotel/hotelRoom2.png', 'Breathtaking ocean views with modern amenities'),
('Conference & Meeting Hall', 'banquet', 'assets/imgs/page/room/banner-room2.png', 'State-of-the-art conference space with high-speed connectivity'),
('Lounge & Cocktail Bar', 'restaurant', 'assets/imgs/page/room/banner-room3.png', 'Relaxing ambiance with hand-crafted cocktails'),
('Premium Twin Bed Room', 'rooms', 'assets/imgs/page/hotel/hotelRoom3.png', 'Comfortable twin bed setups for friends and colleagues'),
('Outdoor Lawn & Catering', 'banquet', 'assets/imgs/page/pages/banner.png', 'Lush green lawns perfect for grand weddings and receptions');

-- 11. Blogs Table
CREATE TABLE IF NOT EXISTS `blogs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `image_path` VARCHAR(255) DEFAULT 'assets/imgs/page/homepage1/news.png',
  `date` VARCHAR(50) NOT NULL,
  `read_time` VARCHAR(50) NOT NULL,
  `excerpt` TEXT NOT NULL,
  `content` TEXT NULL,
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Blog Posts
INSERT INTO `blogs` (`id`, `slug`, `title`, `category`, `image_path`, `date`, `read_time`, `excerpt`, `content`, `meta_title`, `meta_description`) VALUES
(1, 'gwalior-fort-guide', 'Gwalior Fort Guide: Exploring the Gibraltar of India', 'Local Attractions', 'assets/imgs/page/homepage1/news.png', '05 Jul 2026', '8 min read', 'Discover the rich history, magnificent palaces, and stunning temple carvings inside Gwalior\'s historic fort.', 'Gwalior Fort is one of the most famous tourist attractions in Madhya Pradesh, India. Built in the 8th century, it sits atop a steep hill overlooking the city. Inside, visitors can marvel at the stunning Man Mandir Palace with its signature turquoise blue tiles, the ancient Sas Bahu temples, and the magnificent rock-cut Jain sculptures carved along the cliffside paths. Plan a half-day tour to experience this majestic fort in all its glory.', 'Gwalior Fort Sightseeing Guide & History - Hotel Destin', 'Read our complete tourist guide to visiting Gwalior Fort in Madhya Pradesh, including ticket timings, palaces, temples, and historical details.'),
(2, 'dining-spots-sachin-tendulkar-road', 'Top 5 Dining Spots on Sachin Tendulkar Road', 'Dining Guide', 'assets/imgs/page/homepage1/news2.png', '02 Jul 2026', '5 min read', 'A curated list of local Gwalior specialties, fine dining, and cafe favorites located just steps from Hotel Destin.', 'Sachin Tendulkar Road is Gwalior\'s premier lifestyle and dining hub. When staying at Hotel Destin, you are surrounded by excellent choices. Here are our top 5 recommendations: 1) The Heights Rooftop Club (located inside Hotel Destin) for high-end dining, 2) local street food stalls for spicy Gwalior Bedai, 3) Indian accent fine dining restaurants, 4) modern espresso cafes, and 5) premium ice cream parlors.', 'Top Restaurants & Cafes on Sachin Tendulkar Road - Hotel Destin', 'Explore the best restaurants, local breakfast spots, and cafe lounges on Gwalior\'s Sachin Tendulkar Road, located right next to Hotel Destin.')
ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
