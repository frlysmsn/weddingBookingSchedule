<?php
// Strict admin-only access control
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch all bookings with user details
$stmt = $db->prepare("
    SELECT b.*, u.name, u.email 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-dashboard">
    <h2>Admin Dashboard</h2>
    
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Pending Bookings</h3>
            <p><?= count(array_filter($bookings, fn($b) => $b['status'] === 'pending')) ?></p>
        </div>
        <div class="stat-card">
            <h3>Approved Bookings</h3>
            <p><?= count(array_filter($bookings, fn($b) => $b['status'] === 'approved')) ?></p>
        </div>
    </div>

    <div class="booking-management">
        <h3>Booking Management</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Client Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= date('F d, Y', strtotime($booking['wedding_date'])) ?></td>
                        <td><?= htmlspecialchars($booking['name']) ?></td>
                        <td><?= htmlspecialchars($booking['email']) ?></td>
                        <td>
                            <span class="status-badge status-<?= $booking['status'] ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </td>
                        <td>
                            <button onclick="updateStatus(<?= $booking['id'] ?>, 'approved')" 
                                    class="btn btn-success btn-sm">
                                Approve
                            </button>
                            <button onclick="updateStatus(<?= $booking['id'] ?>, 'rejected')" 
                                    class="btn btn-danger btn-sm">
                                Reject
                            </button>
                            <button onclick="viewDetails(<?= $booking['id'] ?>)" 
                                    class="btn btn-info btn-sm">
                                View Details
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateStatus(bookingId, status) {
    if (confirm('Are you sure you want to ' + status + ' this booking?')) {
        $.post('api/update-booking-status.php', {
            booking_id: bookingId,
            status: status
        })
        .done(function(response) {
            Swal.fire('Success!', 'Booking status updated.', 'success')
            .then(() => location.reload());
        })
        .fail(function() {
            Swal.fire('Error!', 'Failed to update booking status.', 'error');
        });
    }
}

function viewDetails(bookingId) {
    // Load and display booking details in a modal
    $.get('api/get-booking-details.php', {
        booking_id: bookingId
    })
    .done(function(response) {
        // Display the details in a modal
        Swal.fire({
            title: 'Booking Details',
            html: response,
            width: '600px'
        });
    });
}
</script>
