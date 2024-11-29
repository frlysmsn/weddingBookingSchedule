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
        <?php foreach($bookings as $booking): ?>
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        Booking #<?= $booking['id'] ?>
                        <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?> ms-2">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </h5>
                    <small class="text-muted">
                        Submitted: <?= date('F j, Y g:i A', strtotime($booking['created_at'])) ?>
                    </small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Wedding Details -->
                        <div class="col-md-4">
                            <h6 class="border-bottom pb-2">Wedding Details</h6>
                            <p><strong>Date:</strong> <?= date('F j, Y', strtotime($booking['wedding_date'])) ?></p>
                            <p><strong>Time:</strong> <?= $booking['preferred_time'] ?></p>
                            <p><strong>Contact:</strong> <?= $booking['contact_number'] ?></p>
                            <p><strong>Email:</strong> <?= $booking['email'] ?></p>
                        </div>

                        <!-- Bride Details -->
                        <div class="col-md-4">
                            <h6 class="border-bottom pb-2">Bride's Information</h6>
                            <p><strong>Name:</strong> <?= $booking['bride_name'] ?></p>
                            <p><strong>Birth Date:</strong> <?= date('F j, Y', strtotime($booking['bride_dob'])) ?></p>
                            <p><strong>Birthplace:</strong> <?= $booking['bride_birthplace'] ?></p>
                            <p><strong>Mother's Name:</strong> <?= $booking['bride_mother'] ?></p>
                            <p><strong>Father's Name:</strong> <?= $booking['bride_father'] ?></p>
                            <p><strong>Pre-nuptial:</strong> 
                                <span class="badge bg-<?= $booking['bride_prenup'] === 'yes' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($booking['bride_prenup']) ?>
                                </span>
                            </p>
                            <p><strong>Pre-cana:</strong> 
                                <span class="badge bg-<?= $booking['bride_precana'] === 'yes' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($booking['bride_precana']) ?>
                                </span>
                            </p>
                        </div>

                        <!-- Groom Details -->
                        <div class="col-md-4">
                            <h6 class="border-bottom pb-2">Groom's Information</h6>
                            <p><strong>Name:</strong> <?= $booking['groom_name'] ?></p>
                            <p><strong>Birth Date:</strong> <?= date('F j, Y', strtotime($booking['groom_dob'])) ?></p>
                            <p><strong>Birthplace:</strong> <?= $booking['groom_birthplace'] ?></p>
                            <p><strong>Mother's Name:</strong> <?= $booking['groom_mother'] ?></p>
                            <p><strong>Father's Name:</strong> <?= $booking['groom_father'] ?></p>
                            <p><strong>Pre-nuptial:</strong> 
                                <span class="badge bg-<?= $booking['groom_prenup'] === 'yes' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($booking['groom_prenup']) ?>
                                </span>
                            </p>
                            <p><strong>Pre-cana:</strong> 
                                <span class="badge bg-<?= $booking['groom_precana'] === 'yes' ? 'success' : 'danger' ?>">
                                    <?= strtoupper($booking['groom_precana']) ?>
                                </span>
                            </p>
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