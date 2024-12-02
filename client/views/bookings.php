<?php
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch all bookings for the current user
$query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">My Wedding Bookings</h1>

    <?php if(empty($bookings)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> You haven't made any wedding bookings yet.
        </div>
    <?php else: ?>
        <!-- Show latest booking status notification -->
        <?php 
        $latestBooking = reset($bookings); // Get the most recent booking
        if ($latestBooking['status'] === 'pending'): 
        ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-clock"></i>
                <strong>Booking Submitted!</strong> Your wedding booking request has been received and is pending approval.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php foreach($bookings as $booking): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Wedding Booking #<?= $booking['id'] ?>
                        <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?> ms-2">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </h5>
                    <small class="text-muted">
                        Submitted: <?= date('F j, Y g:i A', strtotime($booking['created_at'])) ?>
                    </small>
                </div>

                <div class="card-body">
                    <!-- Status Message Box -->
                    <div class="alert alert-<?= getStatusBadgeClass($booking['status']) ?> mb-4">
                        <?php if($booking['status'] === 'pending'): ?>
                            <i class="fas fa-info-circle"></i>
                            Your booking is being reviewed. We will notify you once it's processed.
                            <br>
                            <small>Please ensure all required documents are uploaded while waiting for approval.</small>
                        <?php elseif($booking['status'] === 'approved'): ?>
                            <i class="fas fa-check-circle"></i>
                            Your wedding booking has been approved! 
                            <br>
                            <small>Check your email for the next steps.</small>
                        <?php elseif($booking['status'] === 'rejected'): ?>
                            <i class="fas fa-times-circle"></i>
                            Your booking has been rejected.
                            <br>
                            <small>Please check your email for detailed information about the rejection.</small>
                        <?php endif; ?>
                    </div>

                    <!-- Booking Details -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="border-bottom pb-2">Wedding Details</h6>
                            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($booking['wedding_date'])) ?></p>
                            <p><strong>Time:</strong> <?= $booking['preferred_time'] ?></p>
                            <p><strong>Contact:</strong> <?= $booking['contact_number'] ?></p>
                            <p><strong>Email:</strong> <?= $booking['email'] ?></p>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="col-md-6 text-end">
                            <a href="index.php?page=documents&booking_id=<?= $booking['id'] ?>" 
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-file-upload"></i> Manage Documents
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
function getStatusBadgeClass($status) {
    switch($status) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'danger';
        case 'cancelled':
            return 'secondary';
        default:
            return 'primary';
    }
}
?>

<style>
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
.badge {
    font-size: 0.8em;
}
</style> 