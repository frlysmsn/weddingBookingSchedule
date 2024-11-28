<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get all pending bookings
$stmt = $db->prepare("
    SELECT 
        b.*,
        u.name as client_name,
        u.email as client_email,
        (SELECT COUNT(*) FROM documents d WHERE d.booking_id = b.id AND d.status = 'approved') as approved_docs
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    WHERE b.status = 'pending'
    ORDER BY b.wedding_date ASC
");
$stmt->execute();
$pending_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Wedding Bookings</h2>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Wedding Date</th>
                            <th>Time</th>
                            <th>Couple</th>
                            <th>Documents</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending_bookings as $booking): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($booking['client_name']) ?>
                                    <small class="d-block text-muted"><?= $booking['client_email'] ?></small>
                                </td>
                                <td><?= date('M d, Y', strtotime($booking['wedding_date'])) ?></td>
                                <td><?= date('h:i A', strtotime($booking['preferred_time'])) ?></td>
                                <td>
                                    <strong>Groom:</strong> <?= htmlspecialchars($booking['groom_name']) ?><br>
                                    <strong>Bride:</strong> <?= htmlspecialchars($booking['bride_name']) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $booking['approved_docs'] >= 4 ? 'success' : 'warning' ?>">
                                        <?= $booking['approved_docs'] ?>/4 Approved
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $booking['status'] === 'approved' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($booking['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($booking['approved_docs'] >= 4): ?>
                                        <button class="btn btn-sm btn-success approve-booking" 
                                                data-id="<?= $booking['id'] ?>"
                                                data-client="<?= htmlspecialchars($booking['client_name']) ?>"
                                                data-email="<?= $booking['client_email'] ?>"
                                                data-date="<?= date('M d, Y', strtotime($booking['wedding_date'])) ?>"
                                                data-time="<?= date('h:i A', strtotime($booking['preferred_time'])) ?>">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-clock"></i> Waiting for Documents
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($pending_bookings)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-calendar-check text-success fa-2x mb-3"></i>
                                    <p class="mb-0">No pending bookings</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.approve-booking').click(function() {
        const bookingId = $(this).data('id');
        const clientName = $(this).data('client');
        const clientEmail = $(this).data('email');
        const weddingDate = $(this).data('date');
        const weddingTime = $(this).data('time');
        
        Swal.fire({
            title: 'Approve Wedding Booking?',
            html: `
                <p>Approve wedding booking for ${clientName}?</p>
                <p><strong>Date:</strong> ${weddingDate}<br>
                <strong>Time:</strong> ${weddingTime}</p>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/approve-booking.php', {
                    booking_id: bookingId
                })
                .done(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Wedding booking has been approved and email sent.'
                    }).then(() => {
                        location.reload();
                    });
                })
                .fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to approve booking.'
                    });
                });
            }
        });
    });
});
</script> 