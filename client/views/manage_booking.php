<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get booking details
$booking_id = $_GET['id'] ?? 0;
$stmt = $db->prepare("
    SELECT * FROM bookings 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header('Location: index.php?page=bookings');
    exit;
}
?>

<div class="booking-management">
    <div class="booking-details">
        <h2>Booking Details</h2>
        
        <div class="detail-card">
            <div class="status-badge status-<?= $booking['status'] ?>">
                <?= ucfirst($booking['status']) ?>
            </div>
            
            <div class="detail-row">
                <strong>Wedding Date:</strong>
                <?= date('F d, Y', strtotime($booking['wedding_date'])) ?>
            </div>
            
            <div class="detail-row">
                <strong>Time:</strong>
                <?= date('h:i A', strtotime($booking['preferred_time'])) ?>
            </div>
            
            <div class="detail-row">
                <strong>Couple:</strong>
                <?= htmlspecialchars($booking['groom_name']) ?> & 
                <?= htmlspecialchars($booking['bride_name']) ?>
            </div>
            
            <div class="action-buttons">
                <?php if ($booking['status'] !== 'cancelled'): ?>
                    <button onclick="cancelBooking(<?= $booking['id'] ?>)" 
                            class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel Booking
                    </button>
                <?php endif; ?>
                
                <?php if ($booking['status'] === 'cancelled'): ?>
                    <button onclick="showRebookingForm(<?= $booking['id'] ?>)" 
                            class="btn btn-primary">
                        <i class="fas fa-redo"></i> Rebook Wedding
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.booking-management {
    max-width: 800px;
    margin: 2rem auto;
}

.detail-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.detail-row {
    margin: 1rem 0;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
}

.action-buttons {
    margin-top: 2rem;
    display: flex;
    gap: 1rem;
}
</style>

<script>
function cancelBooking(bookingId) {
    Swal.fire({
        title: 'Cancel Booking',
        text: 'Are you sure you want to cancel this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, cancel it',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/cancel-booking.php', { booking_id: bookingId })
                .done(function(response) {
                    Swal.fire({
                        title: 'Cancelled!',
                        text: 'Your booking has been cancelled successfully.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                })
                .fail(function(xhr) {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to cancel booking.',
                        icon: 'error'
                    });
                });
        }
    });
}

function showRebookingForm(bookingId) {
    Swal.fire({
        title: 'Rebook Wedding',
        html: `
            <form id="rebookingForm">
                <input type="hidden" name="original_booking_id" value="${bookingId}">
                <div class="form-group">
                    <label>New Wedding Date</label>
                    <input type="date" name="wedding_date" class="form-control" 
                           min="${new Date().toISOString().split('T')[0]}" required>
                </div>
                <div class="form-group">
                    <label>Preferred Time</label>
                    <select name="preferred_time" class="form-control" required>
                        <option value="08:00">8:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>
            </form>
        `,
        showCancelButton: true,
        confirmButtonText: 'Rebook',
        preConfirm: () => {
            const form = document.getElementById('rebookingForm');
            const formData = new FormData(form);
            
            return fetch('../api/rebook-wedding.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error}`);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Success!',
                text: 'Your wedding has been rebooked successfully.',
                icon: 'success'
            }).then(() => {
                window.location.href = 'index.php?page=bookings';
            });
        }
    });
}
</script> 