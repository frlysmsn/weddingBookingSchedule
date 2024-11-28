<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

require_once '../includes/db_connection.php';

// Get the requested page
$page = $_GET['page'] ?? 'dashboard';

// Define allowed pages
$allowed_pages = [
    'dashboard',
    'booking_form',
    'documents',
    'bookings',
    'booking_confirmation',
    'profile'
];

// Validate requested page
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// Include header
include 'includes/header.php';

// Load the requested page
$page_path = "views/{$page}.php";
if (file_exists($page_path)) {
    include $page_path;
} else {
    echo "<div class='alert alert-danger'>Page not found!</div>";
}

// Include footer
include 'includes/footer.php';
?> 