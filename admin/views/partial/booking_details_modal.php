<div class="booking-details">
    <div class="row">
        <div class="col-md-6">
            <h5>Couple Information</h5>
            <p><strong>Groom:</strong> <?= htmlspecialchars($booking['groom_name']) ?></p>
            <p><strong>Bride:</strong> <?= htmlspecialchars($booking['bride_name']) ?></p>
        </div>
        <div class="col-md-6">
            <h5>Contact Information</h5>
            <p><strong>Email:</strong> <?= htmlspecialchars($booking['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($booking['contact_number']) ?></p>
        </div>
    </div>
    
    <hr>
    
    <div class="row">
        <div class="col-12">
            <h5>Wedding Details</h5>
            <p><strong>Date:</strong> <?= date('F d, Y', strtotime($booking['wedding_date'])) ?></p>
            <p><strong>Time:</strong> <?= date('h:i A', strtotime($booking['preferred_time'])) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($booking['status']) ?></p>
        </div>
    </div>
</div>