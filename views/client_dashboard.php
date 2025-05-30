<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: index.php?page=login');
    exit;
} 

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT * FROM bookings WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard">
    <div class="dashboard-header">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h2>
        <a href="index.php?page=booking" class="btn btn-primary">New Booking</a>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>My Bookings</h3>
            <?php if(empty($bookings)): ?>
                <p>No bookings found.</p>
            <?php else: ?>
                <div class="booking-list">
                    <?php foreach($bookings as $booking): ?>
                        <div class="booking-item">
                            <div class="booking-date">
                                <?= date('F d, Y', strtotime($booking['wedding_date'])) ?>
                            </div>
                            <div class="booking-status <?= $booking['status'] ?>">
                                <?= ucfirst($booking['status']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-card">
            <h3>Required Documents</h3>
            <div class="document-checklist">
                <!-- Add your document checklist here -->
            </div>
        </div>
    </div>
</div>
