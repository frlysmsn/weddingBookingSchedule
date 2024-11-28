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

// Get all bookings with user details
$stmt = $db->query("
    SELECT b.*, u.name, u.email 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC
");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - <?= SITE_NAME ?></title>
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
                <a href="bookings.php" class="active">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
                <a href="users.php">
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
                <h1>Manage Bookings</h1>
            </div>

            <div class="admin-content">
                <!-- Filters -->
                <div class="filters-section">
                    <select id="statusFilter" onchange="filterBookings()">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                    
                    <input type="month" id="monthFilter" onchange="filterBookings()">
                    
                    <button onclick="exportBookings()" class="btn btn-success">
                        <i class="fas fa-download"></i> Export to Excel
                    </button>
                </div>

                <!-- Bookings Table -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Wedding Date</th>
                                <th>Couple Names</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Documents</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($booking['wedding_date'])) ?></td>
                                    <td>
                                        <strong>Groom:</strong> <?= htmlspecialchars($booking['groom_name']) ?><br>
                                        <strong>Bride:</strong> <?= htmlspecialchars($booking['bride_name']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($booking['email']) ?><br>
                                        <?= htmlspecialchars($booking['phone']) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $booking['status'] ?>">
                                            <?= ucfirst($booking['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button onclick="viewDocuments(<?= $booking['id'] ?>)" 
                                                class="btn btn-sm btn-info">
                                            <i class="fas fa-file"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <button onclick="viewBooking(<?= $booking['id'] ?>)" 
                                                class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="updateStatus(<?= $booking['id'] ?>, 'approved')" 
                                                class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="updateStatus(<?= $booking['id'] ?>, 'rejected')" 
                                                class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i>
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
    function viewBooking(id) {
        $.get('../api/get-booking-details.php', { booking_id: id })
            .done(function(response) {
                Swal.fire({
                    title: 'Booking Details',
                    html: response,
                    width: '600px'
                });
            });
    }

    function updateStatus(id, status) {
        Swal.fire({
            title: 'Confirm Status Update',
            text: `Are you sure you want to mark this booking as ${status}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/update-booking-status.php', {
                    booking_id: id,
                    status: status
                })
                .done(function(response) {
                    Swal.fire('Updated!', 'Booking status has been updated.', 'success')
                    .then(() => location.reload());
                })
                .fail(function() {
                    Swal.fire('Error!', 'Failed to update status.', 'error');
                });
            }
        });
    }

    function viewDocuments(id) {
        $.get('../api/get-booking-documents.php', { booking_id: id })
            .done(function(response) {
                Swal.fire({
                    title: 'Booking Documents',
                    html: response,
                    width: '600px'
                });
            });
    }

    function filterBookings() {
        const status = document.getElementById('statusFilter').value;
        const month = document.getElementById('monthFilter').value;
        
        // Implement filtering logic here
        location.href = `bookings.php?status=${status}&month=${month}`;
    }

    function exportBookings() {
        window.location.href = '../api/export-bookings.php';
    }
    </script>
</body>
</html> 