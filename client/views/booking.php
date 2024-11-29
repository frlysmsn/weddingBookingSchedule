<?php
// Get user's current booking
$query = "SELECT * FROM bookings WHERE client_id = ? ORDER BY created_at DESC LIMIT 1";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
mysqli_stmt_execute($stmt);
$booking = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($booking) {
    switch($booking['status']) {
        case 'pending':
            // Show confirmation form
            ?>
            <div class="alert alert-info">
                <h5>Complete Your Wedding Details</h5>
                <p>Please fill in all required details and confirm your booking.</p>
            </div>
            <!-- Your booking form here -->
            <?php
            break;
            
        case 'waiting_for_confirmation':
            ?>
            <div class="alert alert-warning">
                <h5>Booking Awaiting Approval</h5>
                <p>Your wedding booking has been confirmed and is waiting for admin approval.</p>
            </div>
            <?php
            break;
            
        case 'confirmed':
        case 'approved':
            ?>
            <div class="alert alert-success">
                <h5>Booking Approved</h5>
                <p>Your wedding booking for <?= date('F d, Y', strtotime($booking['wedding_date'])) ?> has been approved.</p>
            </div>
            <?php
            break;
            
        case 'rejected':
            ?>
            <div class="alert alert-danger">
                <h5>Booking Rejected</h5>
                <p>Your wedding booking was rejected. Reason: <?= htmlspecialchars($booking['rejection_reason']) ?></p>
            </div>
            <?php
            break;
    }
}
?> 