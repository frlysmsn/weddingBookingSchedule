<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Modify query to separate pending and completed bookings
$stmt = $db->prepare("
    SELECT 
        b.*,
        COALESCE(u.name, 'N/A') as client_name,
        COALESCE(u.email, b.email) as client_email,
        CASE 
            WHEN b.status = 'approved' THEN 'completed'
            ELSE 'pending'
        END as booking_type
    FROM bookings b
    LEFT JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
");
$stmt->execute();
$allBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate bookings
$pendingBookings = array_filter($allBookings, fn($b) => $b['booking_type'] === 'pending');
$completedBookings = array_filter($allBookings, fn($b) => $b['booking_type'] === 'completed');
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Wedding Bookings</h1>
    
    <!-- Pending Bookings Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-clock me-1"></i>
            Pending Bookings
        </div>
        <div class="card-body">
            <table id="pendingBookingsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Preffered Wedding Date</th>
                        <th>Preferred Time</th>
                        <th>Booking Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingBookings as $booking): ?>
                        <tr>
                            <td>
                                <div class="client-info">
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($booking['client_email']) ?>
                                </div>
                            </td>
                            <td><?= date('M d, Y', strtotime($booking['wedding_date'])) ?></td>
                            <td><?= htmlspecialchars($booking['preferred_time']) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-info view-booking" 
                                            data-id="<?= htmlspecialchars($booking['id']) ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if($booking['status'] === 'pending'): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-success approve-booking" 
                                                data-id="<?= htmlspecialchars($booking['id']) ?>"
                                                data-action="approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger reject-booking" 
                                                data-id="<?= htmlspecialchars($booking['id']) ?>"
                                                data-action="reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-booking" 
                                            data-id="<?= htmlspecialchars($booking['id']) ?>"
                                            data-action="delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Completed Bookings Table -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <i class="fas fa-check-circle me-1"></i>
            Completed Bookings
        </div>
        <div class="card-body">
            <table id="completedBookingsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Preffered Wedding Date</th>
                        <th>Preferred Time</th>
                        <th>Booking Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedBookings as $booking): ?>
                        <tr>
                            <td>
                                <div class="client-info">
                                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($booking['client_email']) ?>
                                </div>
                            </td>
                            <td><?= date('M d, Y', strtotime($booking['wedding_date'])) ?></td>
                            <td><?= htmlspecialchars($booking['preferred_time']) ?></td>
                            <td>
                                <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?>">
                                    <?= ucfirst($booking['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-info view-booking" 
                                            data-id="<?= htmlspecialchars($booking['id']) ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger delete-booking" 
                                            data-id="<?= htmlspecialchars($booking['id']) ?>"
                                            data-action="delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
function getProgressBarClass($progress) {
    if ($progress >= 100) return 'bg-success';
    if ($progress >= 50) return 'bg-info';
    if ($progress >= 25) return 'bg-warning';
    return 'bg-danger';
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'approved': return 'success';
        case 'pending': return 'warning';
        case 'rejected': return 'danger';
        default: return 'secondary';
    }
}
?>

<!-- Move scripts to the bottom and ensure correct order -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="assets/js/bookings.js"></script>
