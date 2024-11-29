<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Get pending counts
$db = Database::getInstance()->getConnection();

$stmt = $db->prepare("SELECT COUNT(*) FROM documents WHERE status = 'pending'");
$stmt->execute();
$pending_docs = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM bookings WHERE status = 'pending'");
$stmt->execute();
$pending_bookings = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - St. Rita Parish</title>
    
    <!-- Frameworks and Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="assets/css/admin.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #ffffff;
            border-right: 1px solid #edf2f7;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
        }

        .sidebar-header .logo {
            width: 50px;
            height: 50px;
            margin-bottom: 0.5rem;
        }

        .sidebar-header h3 {
            font-size: 1rem;
            color: #1a202c;
            font-weight: 600;
            margin: 0;
        }

        .nav {
            padding: 1rem 0;
            flex: 1;
        }

        .nav-item {
            margin: 0.25rem 0.75rem;
        }

        .nav-link {
            color: #4a5568 !important;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .nav-link i {
            font-size: 1.1rem;
            color: #718096;
            transition: color 0.2s ease;
        }

        .nav-link span {
            flex: 1;
        }

        .nav-link:hover {
            background: #f7fafc;
            color: #2d3748 !important;
        }

        .nav-link:hover i {
            color: #4299e1;
        }

        .nav-link.active {
            background: #ebf8ff;
            color: #3182ce !important;
        }

        .nav-link.active i {
            color: #3182ce;
        }

        .badge {
            background: #fed7d7;
            color: #e53e3e;
            font-size: 0.75rem;
            padding: 0.25em 0.6em;
            border-radius: 0.375rem;
            font-weight: 500;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            }
        }

        /* Smooth Scrollbar */
        .sidebar {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #ffffff;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #ffffff;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 2px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }

        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1rem 1.25rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .main-content.sidebar-active {
                margin-left: var(--sidebar-width);
            }
        }

        /* DataTables Customization */
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #4e73df !important;
            border-color: #4e73df !important;
            color: white !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #224abe;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo.png" alt="St. Rita Parish" class="logo">
            <h3>St. Rita Parish</h3>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= !isset($_GET['page']) || $_GET['page'] === 'dashboard' ? 'active' : '' ?>" 
                   href="index.php">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['page']) && $_GET['page'] === 'document_approval' ? 'active' : '' ?>" 
                   href="index.php?page=document_approval">
                    <i class="fas fa-file-alt"></i>
                    <span>Documents</span>
                    <?php if($pending_docs > 0): ?>
                        <span class="badge"><?= $pending_docs ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['page']) && $_GET['page'] === 'bookings' ? 'active' : '' ?>" 
                   href="index.php?page=bookings">
                    <i class="fas fa-calendar"></i>
                    <span>Bookings</span>
                    <?php if($pending_bookings > 0): ?>
                        <span class="badge"><?= $pending_bookings ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['page']) && $_GET['page'] === 'users' ? 'active' : '' ?>" 
                   href="index.php?page=users">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>

            <li class="nav-item mt-auto">
                <a class="nav-link" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <?php
        $page = $_GET['page'] ?? 'dashboard';
        $valid_pages = ['dashboard', 'document_approval', 'bookings', 'users'];
        
        if(in_array($page, $valid_pages)) {
            include "views/{$page}.php";
        } else {
            include "views/dashboard.php";
        }
        ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>
    <script>
        // Hamburger menu functionality
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('sidebar-active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const hamburger = document.querySelector('.hamburger');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !hamburger.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                document.querySelector('.main-content').classList.remove('sidebar-active');
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html> 