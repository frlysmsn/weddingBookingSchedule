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
    <link href="assets/css/sidebar.css" rel="stylesheet">
    
</head>
<body>
    <!-- Mobile Toggle -->
    <button class="mobile-toggle d-md-none" id="mobileToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
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
                        <span class="badge bg-danger"><?= $pending_docs ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= isset($_GET['page']) && $_GET['page'] === 'bookings' ? 'active' : '' ?>" 
                   href="index.php?page=bookings">
                    <i class="fas fa-calendar"></i>
                    <span>Bookings</span>
                    <?php if($pending_bookings > 0): ?>
                        <span class="badge bg-danger"><?= $pending_bookings ?></span>
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

    <!-- Overlay for mobile -->
    <div class="sidebar-overlay"></div>

    <!-- Main Content -->
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>
    <script src="assets/js/admin.js"></script>
    
    <script>
$(document).ready(function() {
    // Initialize DataTables
    $('#adminsTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10
    });
    
    $('#clientsTable').DataTable({
        order: [[0, 'asc']],
        pageLength: 10
    });

    // Admin form submission
    $('#addAdminForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        $.ajax({
            url: 'ajax/add_admin.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log('Response received:', response);
                try {
                    response = JSON.parse(response);
                    if (response.success) {
                        Swal.fire(
                            'Success!',
                            'Administrator added successfully.',
                            'success'
                        ).then(() => {
                            $('#addAdminModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            response.message || 'Failed to add administrator.',
                            'error'
                        );
                    }
                } catch (e) {
                    console.error('Parse error:', e);
                    Swal.fire('Error!', 'Invalid server response.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                Swal.fire(
                    'Error!',
                    'An error occurred while adding the administrator.',
                    'error'
                );
            }
        });
    });

    // Demote admin function
    window.demoteAdmin = function(userId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, demote it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/demote_admin.php',
                    type: 'POST',
                    data: { user_id: userId },
                    success: function(response) {
                        try {
                            response = JSON.parse(response);
                            if (response.success) {
                                Swal.fire('Success!', 'Administrator demoted successfully.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Error!', response.message || 'Failed to demote administrator.', 'error');
                            }
                        } catch (e) {
                            console.error('Parse error:', e);
                            Swal.fire('Error!', 'Invalid server response.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        Swal.fire('Error!', 'An error occurred while demoting the administrator.', 'error');
                    }
                });
            }
        });
    }
});
</script>
</body>
</html> 