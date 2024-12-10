<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Scheduler</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
     <!--Favicon-->
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.png">
    <link rel="icon" type="image/png" href="../assets/images/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="../assets/images/favicon-16x16.png" sizes="16x16">
    <link rel="apple-touch-icon" href="../assets/images/apple-touch-icon.png">
    <!-- Frameworks and Libraries -->
    <style>
        .header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            text-decoration: none;
        }

        .logo:hover {
            color: #007bff;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: #666;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #007bff;
            background: #f8f9fa;
        }

        .nav-link.active {
            color: #007bff;
            background: #e7f1ff;
        }

        .user-menu {
            position: relative;
        }

        .user-button {
            background: none;
            border: none;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: #666;
        }

        .user-button:hover {
            color: #007bff;
        }

        .user-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 4px;
            padding: 0.5rem;
            display: none;
        }

        .user-dropdown.show {
            display: block;
        }

        .dropdown-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            color: #666;
            text-decoration: none;
            white-space: nowrap;
        }

        .dropdown-link:hover {
            background: #f8f9fa;
            color: #007bff;
        }

        .main-content {
            margin-top: 80px;
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .nav-menu {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                padding: 1rem;
                flex-direction: column;
                gap: 1rem;
            }

            .nav-menu.show {
                display: flex;
            }

            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">
                <i class="fas fa-church"></i> Wedding Scheduler
            </a>

            <button class="menu-toggle d-md-none">
                <i class="fas fa-bars"></i>
            </button>

            <nav class="nav-menu">
                <a href="index.php?page=dashboard" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="index.php?page=booking_form" class="nav-link <?= $page === 'booking_form' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-plus"></i> Book Wedding
                </a>
                <a href="index.php?page=bookings" class="nav-link <?= $page === 'bookings' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> My Bookings
                </a>
                
                <div class="user-menu">
                    <button class="user-button" onclick="toggleUserMenu()">
                        <i class="fas fa-user"></i>
                        <?= htmlspecialchars($_SESSION['name']) ?>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="index.php?page=profile" class="dropdown-link">
                            <i class="fas fa-user-cog"></i> Profile
                        </a>
                        <a href="../logout.php" class="dropdown-link">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <!-- Content will be loaded here -->

    <!-- Scripts moved to bottom for better performance -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleUserMenu() {
            document.getElementById('userDropdown').classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.user-button')) {
                const dropdowns = document.getElementsByClassName('user-dropdown');
                for (const dropdown of dropdowns) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }
        }

        // Mobile menu toggle
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav-menu').classList.toggle('show');
        });
    </script>
</body>
</html>