<?php
// index.php - Entry point yang redirect ke launcher untuk auto-patching
// Modifikasi dari index.php yang sudah ada

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    // User sudah login, redirect ke dashboard admin
    header("Location: admin/dashboard.php");
    exit();
} else {
    // User belum login, redirect ke launcher untuk auto-patching
    // Launcher akan check update dulu, baru redirect ke login.php
    header("Location: launcher.php");
    exit();
}
?>