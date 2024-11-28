<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Authentication.php';

$auth = new Authentication();

// Ensure only admins can access this area
if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get all users except admins
$stmt = $db->query("
    SELECT u.*, 
           COALESCE(u.active, 1) as status,
           COUNT(b.id) as total_bookings,
           MAX(b.created_at) as last_booking
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id
    WHERE u.role = 'client'
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - <?= SITE_NAME ?></title>
    <link href='../assets/css/style.css' rel='stylesheet'>
    <link href='../assets/css/admin.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css' rel='stylesheet'>
</head>
<body>
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <h2><?= SITE_NAME ?></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="bookings.php">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
                <a href="users.php" class="active">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="settings.php">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-header">
                <h1>Manage Users</h1>
            </div>

            <div class="admin-content">
                <!-- Search and Filter -->
                <div class="filters-section">
                    <input type="text" id="searchUser" placeholder="Search users..." 
                           onkeyup="searchUsers()" class="search-input">
                </div>

                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Total Bookings</th>
                                <th>Last Booking</th>
                                <th>Joined Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['name']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td><?= $user['total_bookings'] ?></td>
                                    <td>
                                        <?= $user['last_booking'] ? 
                                            date('M d, Y', strtotime($user['last_booking'])) : 
                                            'No bookings' ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= $user['status'] ? 'active' : 'inactive' ?>">
                                            <?= $user['status'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="viewUser(<?= $user['id'] ?>)" 
                                                class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="toggleUserStatus(<?= $user['id'] ?>)" 
                                                class="btn btn-sm btn-warning">
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                        <button onclick="resetPassword(<?= $user['id'] ?>)" 
                                                class="btn btn-sm btn-secondary">
                                            <i class="fas fa-key"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src='https://code.jquery.com/jquery-3.6.3.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js'></script>
    <script>
    function viewUser(id) {
        $.get('../api/get-user-details.php', { user_id: id })
            .done(function(response) {
                Swal.fire({
                    title: 'User Details',
                    html: response,
                    width: '600px'
                });
            });
    }

    function toggleUserStatus(id) {
        Swal.fire({
            title: 'Confirm Status Change',
            text: 'Are you sure you want to change this user\'s status?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/toggle-user-status.php', { user_id: id })
                    .done(function(response) {
                        Swal.fire('Updated!', 'User status has been changed.', 'success')
                        .then(() => location.reload());
                    })
                    .fail(function() {
                        Swal.fire('Error!', 'Failed to update user status.', 'error');
                    });
            }
        });
    }

    function resetPassword(id) {
        Swal.fire({
            title: 'Reset Password',
            text: 'Are you sure you want to reset this user\'s password?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, reset it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/reset-user-password.php', { user_id: id })
                    .done(function(response) {
                        Swal.fire('Reset!', 'Password has been reset.', 'success');
                    })
                    .fail(function() {
                        Swal.fire('Error!', 'Failed to reset password.', 'error');
                    });
            }
        });
    }

    function searchUsers() {
        let input = document.getElementById('searchUser');
        let filter = input.value.toUpperCase();
        let table = document.querySelector('.table');
        let tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName('td')[0];
            if (td) {
                let txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }
    </script>
</body>
</html> 