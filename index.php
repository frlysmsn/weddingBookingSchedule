<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db_connection.php';
require_once 'includes/Authentication.php';

$auth = new Authentication();

// Basic routing
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Header
include 'views/includes/header.php';

// Main content
switch($page) {
    case 'home':
        include 'views/landing_page.php';
        break;
    case 'login':
        include 'views/login.php';
        break;
    case 'register':
        include 'views/register.php';
        break;
    case 'client-dashboard':
        if($auth->isLoggedIn() && !$auth->isAdmin()) {
            include 'views/client_dashboard.php';
        } else {
            header('Location: index.php?page=login');
        }
        break;
    case 'admin-dashboard':
        if($auth->isLoggedIn() && $auth->isAdmin()) {
            include 'views/admin_dashboard.php';
        } else {
            header('Location: index.php?page=login');
        }
        break;
    case 'booking':
        if($auth->isLoggedIn()) {
            include 'views/booking_form.php';
        } else {
            header('Location: index.php?page=login');
        }
        break;
    case 'verify':
        if(isset($_SESSION['temp_user_id'])) {
            include 'views/verify_email.php';
        } else {
            header('Location: index.php');
        }
        break;
    default:
        include 'views/404.php';
}
// Footer
include 'views/includes/footer.php';

