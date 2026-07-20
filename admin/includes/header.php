<?php
require_once __DIR__ . '/../../db.php';

// Verify session authentication
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Get current active file basename for menu highlights
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/imgs/template/favicon.png">
    <link href="../assets/css/stylee209.css?v=1.0.0" rel="stylesheet">
    <title>Hotel Destin - Admin Panel</title>

    <style>
        body {
            background-color: #f7f9fc;
            color: #1e293b;
            min-height: 100vh;
            margin: 0;
            display: flex;
            font-family: var(--bs-font-sans-serif, Arial, sans-serif);
        }

        /* Premium Light Sidebar */
        .admin-sidebar {
            width: 260px;
            background: #ffffff;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 100;
            padding: 25px 0;
            border-right: 1px solid #e2e8f0;
        }

        .sidebar-brand {
            padding: 0 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: #475569;
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 550;
            transition: all 0.25s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-link svg {
            width: 18px;
            height: 18px;
            color: #64748b;
            transition: color 0.25s ease;
        }

        .sidebar-link:hover {
            color: #9c6047;
            background: rgba(156, 96, 71, 0.04);
            border-left-color: rgba(156, 96, 71, 0.4);
        }

        .sidebar-link:hover svg {
            color: #9c6047;
        }

        .sidebar-link.active {
            color: #9c6047;
            background: rgba(156, 96, 71, 0.08);
            border-left-color: #9c6047;
            font-weight: 600;
        }

        .sidebar-link.active svg {
            color: #9c6047;
        }

        /* Main Panel Contents Layout */
        .admin-main {
            margin-left: 260px;
            padding: 24px 30px;
            width: calc(100% - 260px);
            min-height: 100vh;
        }

        .panel-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
            margin-bottom: 24px;
        }

        .panel-title {
            font-size: 24px;
            font-weight: 600;
            color: #0f172a;
        }

        /* Helper Utility to Tighten Vertical Gaps */
        .mb-35 {
            margin-bottom: 20px !important;
        }

        /* Compact, Snug Form Controls for Admin Editors */
        .form-control-custom {
            display: block;
            width: 100%;
            height: 42px !important;
            padding: 8px 14px !important;
            font-size: 13.5px !important;
            font-weight: 400;
            line-height: 1.5;
            color: #334155;
            background-color: #ffffff;
            background-clip: padding-box;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            box-shadow: none !important;
        }

        .form-control-custom:focus {
            border-color: #9c6047 !important;
            outline: 0;
            box-shadow: 0 0 0 2px rgba(156, 96, 71, 0.1) !important;
        }

        textarea.form-control-custom {
            height: auto !important;
            min-height: 80px;
        }

        .form-label-custom {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #475569 !important;
            margin-bottom: 6px !important;
            display: inline-block;
        }

        .form-group {
            margin-bottom: 12px !important;
        }

        /* Upgraded Edit and Delete Action Buttons Styling */
        .btn-edit {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
            padding: 5px 12px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            box-shadow: none !important;
        }

        .btn-edit:hover {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            border-color: #3b82f6 !important;
        }

        .btn-delete {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
            padding: 5px 12px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            cursor: pointer !important;
            box-shadow: none !important;
        }

        .btn-delete:hover {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            border-color: #dc2626 !important;
        }

        /* Metric cards custom styling */
        .metric-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
            transition: transform 0.3s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
        }

        .metric-val {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 5px;
        }

        /* Clean Tables */
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }

        .table-custom th {
            background: #f8fafc;
            padding: 14px 16px;
            font-weight: 600;
            font-size: 13.5px;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .table-custom td {
            padding: 15px 16px;
            font-size: 14px;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .status-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.pending {
            background: #fffbeb;
            color: #b45309;
        }

        .status-badge.contacted {
            background: #f0fdf4;
            color: #166534;
        }

        .status-badge.converted {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-badge.rejected {
            background: #fef2f2;
            color: #991b1b;
        }

        /* Booking specific badges */
        .status-badge.paid {
            background: #ecfdf5;
            color: #047857;
        }

        .status-badge.cancelled {
            background: #fef2f2;
            color: #b91c1c;
        }

        @media (max-width: 991px) {
            body {
                flex-direction: column;
            }

            .admin-sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
                padding: 15px 0;
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
            }

            .sidebar-brand {
                margin-bottom: 15px;
            }

            .sidebar-menu {
                flex-direction: row;
                justify-content: center;
                flex-wrap: wrap;
            }

            .sidebar-link {
                padding: 8px 16px;
                font-size: 13px;
                border-left: none;
                border-bottom: 3px solid transparent;
            }

            .sidebar-link:hover,
            .sidebar-link.active {
                border-left-color: transparent;
                border-bottom-color: #9c6047;
                background: none;
            }

            .admin-main {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar Menu Nav -->
    <nav class="admin-sidebar">
        <div class="sidebar-brand">
            <!-- Destin Brand Logo -->
            <img src="../assets/imgs/template/logo-destin.png" alt="Hotel Destin Logo" style="max-height: 75px;">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="sidebar-link <?= $active_page === 'dashboard.php' ? 'active' : '' ?>">
                    <!-- Home Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard Stats
                </a>
            </li>
            <li>
                <a href="bookings.php" class="sidebar-link <?= $active_page === 'bookings.php' ? 'active' : '' ?>">
                    <!-- Bookings Ticket Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                    </svg>
                    Manage Bookings
                </a>
            </li>
            <li>
                <a href="room-calendar.php" class="sidebar-link <?= $active_page === 'room-calendar.php' ? 'active' : '' ?>">
                    <!-- Calendar Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Occupancy Calendar
                </a>
            </li>
            <li>
                <a href="rate-calendar.php" class="sidebar-link <?= $active_page === 'rate-calendar.php' ? 'active' : '' ?>">
                    <!-- Dollar Icon/Calendar -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Rate Calendar
                </a>
            </li>
            <li>
                <a href="rooms.php" class="sidebar-link <?= $active_page === 'rooms.php' ? 'active' : '' ?>">
                    <!-- Bed Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Manage Rooms
                </a>
            </li>

            <li>
                <a href="coupons.php" class="sidebar-link <?= $active_page === 'coupons.php' ? 'active' : '' ?>">
                    <!-- Tag/Percentage Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M6 20h12a2 2 0 002-2V8a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2zM9 16h6m-6-4h6"></path>
                    </svg>
                    Manage Coupons
                </a>
            </li>
            <li>
                <a href="enquiries.php" class="sidebar-link <?= $active_page === 'enquiries.php' ? 'active' : '' ?>">
                    <!-- Support Envelope Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    Enquiries Log
                </a>
            </li>
            <li>
                <a href="gallery.php" class="sidebar-link <?= $active_page === 'gallery.php' ? 'active' : '' ?>">
                    <!-- Camera/Photo Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Manage Gallery
                </a>
            </li>
            <li>
                <a href="blogs.php" class="sidebar-link <?= $active_page === 'blogs.php' ? 'active' : '' ?>">
                    <!-- News Book Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1M5 20a2 2 0 002 2h10a2 2 0 002-2v-1m-14 0a2 2 0 012-2h12a2 2 0 012 2M15 4V1m0 0l-3 3m3-3l3 3"></path>
                    </svg>
                    Manage Blogs
                </a>
            </li>
            <li>
                <a href="home-settings.php" class="sidebar-link <?= $active_page === 'home-settings.php' ? 'active' : '' ?>">
                    <!-- Template/Page Layout Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                    Home Page Editor
                </a>
            </li>
            <li>
                <a href="settings.php" class="sidebar-link <?= $active_page === 'settings.php' ? 'active' : '' ?>">
                    <!-- Sliders Adjust icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings Config
                </a>
            </li>
            <li>
                <a href="change-password.php" class="sidebar-link <?= $active_page === 'change-password.php' ? 'active' : '' ?>">
                    <!-- Key Lock Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Security Reset
                </a>
            </li>
            <li>
                <a href="logout.php" class="sidebar-link">
                    <!-- Log out Icon -->
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Log Out
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content Area wrapper -->
    <main class="admin-main">