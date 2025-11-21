<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config_database.php';

// Ambil data user yang login dari session atau default ke superadmin
$user_id = $_SESSION['user_id'] ?? 1;

// Pakai $mysqli dari config_database.php
if (isset($mysqli)) {
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_user = $result->fetch_assoc();
} else {
    $current_user = null;
}

// Jika user tidak ditemukan, set default
if (!$current_user) {
    $current_user = ['nama_lengkap' => 'Guest'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANUBILL - INTERNET BILLING MANAGEMENT SYSTEM</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Helvetica, Arial, sans-serif;
            background-color: #f7f7f7;
            overflow-x: hidden;
        }

        /* Header/Top Navigation */
        .top-nav {
            background-color: #2A3F54;
            color: white;
            padding: 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 55px;
            border-bottom: 1px solid #1D2939;
        }

        .nav-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            padding: 15px 20px;
            cursor: pointer;
            float: left;
            height: 55px;
        }

        .nav-toggle:hover {
            background-color: #1D2939;
        }

        .nav-brand {
            color: white;
            font-size: 20px;
            font-weight: bold;
            padding: 15px 20px;
            text-decoration: none;
            display: inline-block;
        }

        .nav-brand:hover {
            color: #E7E7E7;
        }

        .nav-profile {
            float: right;
            padding: 8px 20px;
            position: relative;
        }

        .profile-dropdown {
            background: none;
            border: none;
            color: white;
            padding: 8px 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .profile-dropdown:hover {
            color: #E7E7E7;
        }

        .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #5A738E;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Sidebar */
        .sidebar {
            background-color: #2A3F54;
            position: fixed;
            top: 55px;
            left: 0;
            width: 230px;
            height: calc(100vh - 55px);
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 999;
            display: flex;
            flex-direction: column;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu li {
            border-bottom: 1px solid #425668;
            position: relative;
        }

        .sidebar-menu li:last-child {
            border-bottom: none;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #E7E7E7;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .sidebar-menu a:hover {
            background-color: #1D2939;
            color: white;
        }

        .sidebar-menu a.active {
            background-color: #1ABB9C;
            color: white;
        }

        .sidebar-menu .menu-icon {
            font-size: 18px;
            width: 30px;
            text-align: center;
            margin-right: 15px;
        }

        .sidebar-menu .menu-text {
            font-size: 14px;
            font-weight: 400;
        }

        .sidebar-menu .menu-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .sidebar-menu .menu-arrow.rotated {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .menu-text,
        .sidebar.collapsed .menu-arrow {
            display: none;
        }

        .sidebar.collapsed .menu-icon {
            margin-right: 0;
        }

        .sidebar.collapsed .sidebar-menu a {
            justify-content: center;
            padding: 15px 10px;
        }

        /* Submenu */
        .submenu {
            list-style: none;
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: #1D2939;
        }

        .submenu.show {
            max-height: 500px; /* Adjust based on your content */
        }

        .submenu a {
            padding-left: 50px;
            padding-top: 10px;
            padding-bottom: 10px;
            font-size: 13px;
        }

        .submenu a:hover {
            background-color: #1ABB9C;
        }

        .sidebar.collapsed .submenu {
            display: none;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            background-color: #1D2939;
            padding: 10px 0;
            display: flex;
            justify-content: space-around;
            border-top: 1px solid #425668;
        }

        .sidebar.collapsed .sidebar-footer {
            display: none;
        }

        .sidebar-footer a {
            color: #E7E7E7;
            padding: 10px;
            text-align: center;
            width: 25%;
            position: relative;
            transition: all 0.3s ease;
        }

        .sidebar-footer a:hover {
            color: white;
            background-color: #1ABB9C;
        }

        .sidebar-footer .tooltip-inner {
            background-color: #2A3F54;
        }

        .sidebar-footer .bs-tooltip-auto[data-popper-placement^=top] .tooltip-arrow::before,
        .sidebar-footer .bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #2A3F54;
        }

        /* Main Content */
        .main-content {
            margin-left: 230px;
            margin-top: 55px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: calc(100vh - 55px - 60px);
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        .page-title {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .page-title h1 {
            font-size: 24px;
            color: #2A3F54;
            margin: 0;
        }

        .content-card {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            background-color: white;
            padding: 20px;
            border-top: 1px solid #E5E5E5;
            margin-left: 230px;
            transition: margin-left 0.3s ease;
            color: #73879C;
            font-size: 12px;
        }

        .footer.expanded {
            margin-left: 70px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-links a {
            color: #73879C;
            text-decoration: none;
            margin-left: 20px;
        }

        .footer-links a:hover {
            color: #1ABB9C;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }

            .main-content {
                margin-left: 70px;
            }

            .footer {
                margin-left: 70px;
            }

            .sidebar .menu-text,
            .sidebar .menu-arrow {
                display: none;
            }

            .sidebar .menu-icon {
                margin-right: 0;
            }

            .sidebar .sidebar-menu a {
                justify-content: center;
                padding: 15px 10px;
            }

            .sidebar .sidebar-footer {
                display: none;
            }
        }

        /* Scrollbar untuk sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #2A3F54;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #425668;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #5A738E;
        }

        /* Dropdown menu */
        .dropdown-menu {
            background-color: #2A3F54;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .dropdown-menu .dropdown-item {
            color: #E7E7E7;
            padding: 10px 20px;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #1D2939;
            color: white;
        }
		/* Mobile Responsive Styles */
@media (max-width: 768px) {
    /* Header adjustments */
    .top-nav {
        height: 50px;
        display: flex;
        align-items: center;
    }

    .nav-toggle {
        padding: 15px;
        font-size: 16px;
    }

    .nav-brand {
        font-size: 16px;
        padding: 15px 10px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        flex-grow: 1;
    }

    .nav-profile {
        padding: 8px 10px;
    }

    .profile-dropdown span {
        display: none;
    }

    /* Sidebar behavior for mobile */
    .sidebar {
        width: 250px;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }

    .sidebar.show {
        transform: translateX(0);
    }

    .sidebar.collapsed {
        width: 250px;
        transform: translateX(-100%);
    }

    .sidebar.show.collapsed {
        transform: translateX(0);
    }

    /* Show menu text on mobile */
    .sidebar .menu-text {
        display: block !important;
    }

    /* Main content adjustments */
    .main-content {
        margin-left: 0 !important;
        padding: 15px;
    }

    .footer {
        margin-left: 0 !important;
        padding: 15px;
    }

    /* Hide arrows on mobile */
    .menu-arrow {
        display: none !important;
    }

    /* Adjust card padding */
    .content-card {
        padding: 15px;
    }

    /* Page title adjustments */
    .page-title {
        padding: 15px;
    }

    .page-title h1 {
        font-size: 20px;
    }
}

/* Tablet and desktop behavior */
@media (min-width: 769px) {
    .sidebar {
        transform: translateX(0) !important;
    }
    
    .main-content {
        margin-left: 230px;
    }
    
    .sidebar.collapsed + .main-content {
        margin-left: 70px;
    }
    
    .footer {
        margin-left: 230px;
    }
    
    .sidebar.collapsed ~ .footer {
        margin-left: 70px;
    }
}
    </style>
</head>
<body>
    <!-- Header/Top Navigation -->
    <nav class="top-nav">
        <button class="nav-toggle" id="navToggle">
            <i class="fas fa-bars"></i>
        </button>
<div id="dynamic-nav-brand-container">
    </div>
		</nav>
		
		<script>
// Mobile sidebar toggle functionality
(function() {
document.addEventListener('DOMContentLoaded', function() {
    const navToggle = document.getElementById('navToggle');
    const sidebar = document.getElementById('sidebar');
    
    // Check if mobile device
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    navToggle.addEventListener('click', function() {
        if (isMobile()) {
            sidebar.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
            document.getElementById('footer').classList.toggle('expanded');
        }
    });
    
    // Auto-close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (isMobile() && sidebar.classList.contains('show') && 
            !sidebar.contains(e.target) && 
            e.target !== navToggle) {
            sidebar.classList.remove('show');
        }
    });
})();
</script>