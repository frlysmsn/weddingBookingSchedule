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
            header('Location: client/index.php');
            exit;
        } else {
            header('Location: index.php?page=login');
            exit;
        }
        break;
    case 'admin-dashboard':
        if($auth->isLoggedIn() && $auth->isAdmin()) {
            header('Location: admin/index.php');
            exit;
        } else {
            header('Location: admin/login.php');
            exit;
        }
        break;
    default:
        include 'views/404.php';
}

// Footer
include 'views/includes/footer.php';
