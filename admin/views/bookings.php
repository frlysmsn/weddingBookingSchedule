<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Check document approval status
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_docs,
        SUM(CASE WHEN d.status = 'approved' THEN 1 ELSE 0 END) as approved_docs
    FROM documents d
    WHERE d.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$doc_status = $stmt->fetch(PDO::FETCH_ASSOC);

$all_docs_approved = ($doc_status['total_docs'] > 0 && 
                     $doc_status['approved_docs'] == $doc_status['total_docs']);

// Progress tracker value (2 for registered users with pending docs, 3 for approved docs)
$progress = 1;
if ($doc_status['total_docs'] > 0) {
    $progress = 2;
    if ($all_docs_approved) {
        $progress = 3;
    }
}

// Get all bookings with user details
$stmt = $db->prepare("
    SELECT 
        b.*,
        u.name as client_name,
        u.email as client_email,
        (SELECT COUNT(*) FROM documents d WHERE d.user_id = u.id AND d.status = 'approved') as approved_docs
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.wedding_date ASC
");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get users with pending documents
$stmt = $db->query("
    SELECT DISTINCT 
        u.id,
        u.name,
        u.email,
        b.wedding_date,
        (SELECT COUNT(*) FROM documents d2 WHERE d2.user_id = u.id AND d2.status = 'pending') as pending_docs,
        (SELECT COUNT(*) FROM documents d3 WHERE d3.user_id = u.id) as total_docs
    FROM users u
    JOIN bookings b ON u.id = b.user_id
    JOIN documents d ON d.user_id = u.id
    WHERE d.status = 'pending'
    ORDER BY b.wedding_date ASC
");
$users_with_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Wedding Bookings</h2>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="bookingsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Couple Names</th>
                            <th>Wedding Date</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bookings as $booking): ?>
                            <tr>
                                <td><?= $booking['id'] ?></td>
                                <td>
                                    <strong>Groom:</strong> <?= htmlspecialchars($booking['groom_name']) ?><br>
                                    <strong>Bride:</strong> <?= htmlspecialchars($booking['bride_name']) ?>
                                </td>
                                <td>
                                    <?= date('F d, Y', strtotime($booking['wedding_date'])) ?><br>
                                    <small><?= date('h:i A', strtotime($booking['preferred_time'])) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getStatusBadgeClass($booking['status']) ?>">
                                        <?= ucwords(str_replace('_', ' ', $booking['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= $booking['document_progress'] ?>%">
                                            <?= $booking['document_progress'] ?>%
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-info btn-sm view-details" 
                                            data-id="<?= $booking['id'] ?>"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewDetailsModal">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <?php if($booking['status'] === 'pending'): ?>
                                        <button class="btn btn-danger btn-sm reject-booking" 
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
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#bookingsTable').DataTable({
        order: [[2, 'desc']], // Sort by wedding date
        responsive: true
    });

    // View Details
    $('.view-details').click(function() {
        const bookingId = $(this).data('id');
        $.get('../api/get-booking-details.php', { booking_id: bookingId })
            .done(function(response) {
                $('#viewDetailsModal .modal-body').html(response);
            });
    });

    // Reject Booking
    $('.reject-booking').click(function() {
        const bookingId = $(this).data('id');
        Swal.fire({
            title: 'Reject Booking?',
            text: 'Please provide a reason for rejection:',
            input: 'text',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Reject',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to provide a reason!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/update-booking-status.php', {
                    booking_id: bookingId,
                    status: 'rejected',
                    reason: result.value
                })
                .done(function(response) {
                    Swal.fire('Rejected!', 'Booking has been rejected.', 'success')
                        .then(() => location.reload());
                })
                .fail(function() {
                    Swal.fire('Error!', 'Failed to reject booking.', 'error');
                });
            }
        });
    });
});
</script> 
