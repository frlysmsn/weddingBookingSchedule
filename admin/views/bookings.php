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
        <div class="card-header">
            <h5 class="card-title">Wedding Bookings Management</h5>
        </div>
        <div class="card-body">
            <table id="bookingsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Couple Names</th>
                        <th>Wedding Date</th>
                        <th>Status</th>
                        <th>Documents</th>
                        <th>Wedding Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pending_bookings as $booking): ?>
                        <tr>
                            <td><?= $booking['id'] ?></td>
                            <td><?= htmlspecialchars($booking['client_name']) ?></td>
                            <td><?= date('F d, Y', strtotime($booking['wedding_date'])) ?></td>
                            <td><span class="badge bg-<?= $booking['status'] === 'approved' ? 'success' : 'warning' ?>"><?= ucwords(str_replace('_', ' ', $booking['status'])) ?></span></td>
                            <td>
                                <?php if($booking['approved_docs'] >= 4): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info view-details" 
                                        data-id="<?= $booking['id'] ?>"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#weddingDetailsModal">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                            <td>
                                <?php if($booking['status'] == 'waiting_for_confirmation'): ?>
                                    <button class="btn btn-sm btn-success approve-booking" 
                                            data-id="<?= $booking['id'] ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-booking" 
                                            data-id="<?= $booking['id'] ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Wedding Details Modal -->
<div class="modal fade" id="weddingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Wedding Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Details will be loaded here -->
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