<?php
// Prevent direct file access if loaded independently
if (count(get_included_files()) === 1) {
    http_response_code(403);
    exit("Direct access not permitted.");
}

require_once __DIR__ . '/../db.php';

// 1. Robots Meta Tag (Enables indexing & rich snippet previews, supports custom override)
$robots_val = !empty($page_robots) ? $page_robots : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
echo '<meta name="robots" content="' . htmlspecialchars($robots_val) . '">' . "\n";

// 2. Canonical Tag (Prevents duplicate content issues and consolidates SEO signals)
if (empty($page_canonical)) {
    $canonical_base = 'https://hoteldestin.in';
    $raw_path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $clean_path = '/' . ltrim($raw_path, '/');
    
    // Normalize root and remove .php extensions for clean canonical URLs
    if ($clean_path === '/index.php' || $clean_path === '/index' || $clean_path === '/') {
        $page_canonical = $canonical_base . '/';
    } elseif (strpos($clean_path, 'room-detail') !== false && !empty($_GET['room'])) {
        $page_canonical = $canonical_base . '/room-detail?room=' . urlencode(trim($_GET['room']));
    } else {
        $clean_slug = preg_replace('/\.php$/i', '', $clean_path);
        $page_canonical = $canonical_base . $clean_slug;
    }
}
echo '<link rel="canonical" href="' . htmlspecialchars($page_canonical) . '">' . "\n";

// 3. Structured Data / Schema Markup (JSON-LD) for Rich Google Search Snippets
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Hotel",
  "@id": "https://hoteldestin.in/#hotel",
  "name": "Hotel Destin Gwalior",
  "alternateName": "Hotel Destin",
  "url": "https://hoteldestin.in/",
  "logo": "https://hoteldestin.in/assets/imgs/template/logo-destin.png",
  "image": "https://hoteldestin.in/assets/imgs/page/hotel/banner-hotel.png",
  "description": "Hotel Destin is a premier boutique luxury hotel in Gwalior located on Sachin Tendulkar Road near Govindpuri. Offering luxury rooms, Banquet Oh Saathi Re, rooftop restaurant, and 24/7 guest services.",
  "telephone": "+91-9203509944",
  "email": "info@hoteldestin.in",
  "priceRange": "₹1,500 - ₹5,000",
  "currenciesAccepted": "INR",
  "paymentAccepted": "Cash, Credit Card, Debit Card, UPI, Net Banking",
  "checkinTime": "11:30",
  "checkoutTime": "11:30",
  "hasMap": "https://maps.google.com/?cid=16081290616905586481",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Sachin Tendulkar Road, Near Ram Vatika Marriage Garden, Govindpuri",
    "addressLocality": "Gwalior",
    "addressRegion": "Madhya Pradesh",
    "postalCode": "474011",
    "addressCountry": "IN"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 26.2027048,
    "longitude": 78.2026527
  },
  "amenityFeature": [
    {
      "@type": "LocationFeatureSpecification",
      "name": "Free High-Speed Wi-Fi",
      "value": true
    },
    {
      "@type": "LocationFeatureSpecification",
      "name": "Air Conditioning",
      "value": true
    },
    {
      "@type": "LocationFeatureSpecification",
      "name": "24-Hour Front Desk",
      "value": true
    },
    {
      "@type": "LocationFeatureSpecification",
      "name": "Banquet Hall & Event Spaces",
      "value": true
    },
    {
      "@type": "LocationFeatureSpecification",
      "name": "Multi-Cuisine Rooftop Restaurant",
      "value": true
    },
    {
      "@type": "LocationFeatureSpecification",
      "name": "Airport & Railway Station Transfer",
      "value": true
    }
  ]
}
</script>
<?php

// 4. Analytics & Tracking Integrations (GTM, Meta Pixel, GA4)
$gtm = get_setting('gtm_code');
$pixel = get_setting('meta_pixel');
$ga4 = get_setting('google_analytics');

if (!empty($gtm)) {
    echo "\n<!-- Google Tag Manager -->\n" . $gtm . "\n<!-- End Google Tag Manager -->\n";
}

if (!empty($pixel)) {
    echo "\n<!-- Meta Pixel Code -->\n" . $pixel . "\n<!-- End Meta Pixel Code -->\n";
}

if (!empty($ga4)) {
    echo "\n<!-- Google Analytics (GA4) -->\n" . $ga4 . "\n<!-- End Google Analytics (GA4) -->\n";
}
?>
