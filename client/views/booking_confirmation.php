<?php
if(!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header('Location: index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get booking details
$stmt = $db->prepare("
    SELECT * FROM bookings 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$_GET['booking_id'], $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

// Required documents with descriptions
$required_docs = [
    'baptismal' => [
        'name' => 'Baptismal Certificate',
        'description' => 'Recent copy with annotation for marriage',
        'required' => true
    ],
    'confirmation' => [
        'name' => 'Confirmation Certificate',
        'description' => 'Must be authenticated',
        'required' => true
    ],
    'marriage_license' => [
        'name' => 'Marriage License',
        'description' => 'Valid government-issued license',
        'required' => true
    ],
    'birth_certificate' => [
        'name' => 'Birth Certificate',
        'description' => 'PSA authenticated copy',
        'required' => true
    ],
    'cenomar' => [
        'name' => 'CENOMAR',
        'description' => 'Certificate of No Marriage Record from PSA',
        'required' => true
    ]
];

// Check uploaded documents
$stmt = $db->prepare("
    SELECT document_type, status 
    FROM documents 
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$uploaded_docs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="confirmation-page">
    <div class="success-message">
        <i class="fas fa-check-circle"></i>
        <h2>Booking Submitted Successfully!</h2>
        <p>Your wedding booking has been received and is pending approval.</p>
    </div>

    <div class="booking-details card">
        <h3>Booking Details</h3>
        <div class="details-grid">
            <div class="detail-item">
                <label>Wedding Date:</label>
                <span><?= date('F d, Y', strtotime($booking['wedding_date'])) ?></span>
            </div>
            <div class="detail-item">
                <label>Time:</label>
                <span><?= date('h:i A', strtotime($booking['preferred_time'])) ?></span>
            </div>
            <div class="detail-item">
                <label>Status:</label>
                <span class="status-badge status-<?= $booking['status'] ?>">
                    <?= ucfirst($booking['status']) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="documents-checklist card">
        <h3>Required Documents</h3>
        <p class="text-muted">Please upload all required documents to proceed with your booking.</p>
        
        <div class="checklist-grid">
            <?php foreach($required_docs as $doc_key => $doc): ?>
                <div class="checklist-item">
                    <div class="doc-status">
                        <?php if(isset($uploaded_docs[$doc_key])): ?>
                            <i class="fas fa-check-circle text-success"></i>
                        <?php else: ?>
                            <i class="fas fa-exclamation-circle text-warning"></i>
                        <?php endif; ?>
                    </div>
                    <div class="doc-info">
                        <h4><?= $doc['name'] ?></h4>
                        <p><?= $doc['description'] ?></p>
                        <?php if(isset($uploaded_docs[$doc_key])): ?>
                            <span class="status-badge status-<?= $uploaded_docs[$doc_key] ?>">
                                <?= ucfirst($uploaded_docs[$doc_key]) ?>
                            </span>
                        <?php else: ?>
                            <button onclick="location.href='index.php?page=documents&booking_id=<?= $booking['id'] ?>'" 
                                    class="btn btn-sm btn-primary">
                                Upload Now
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="next-steps card">
        <h3>Next Steps</h3>
        <ol class="steps-list">
            <li class="<?= count($uploaded_docs) === count($required_docs) ? 'completed' : 'pending' ?>">
                Upload all required documents
            </li>
            <li class="<?= $booking['status'] === 'approved' ? 'completed' : 'pending' ?>">
                Wait for admin approval
            </li>
            <li class="pending">Schedule pre-wedding interview</li>
            <li class="pending">Complete payment</li>
        </ol>
    </div>

    <div class="action-buttons">
        <a href="index.php?page=documents" class="btn btn-primary">
            Upload Documents
        </a>
        <a href="index.php?page=bookings" class="btn btn-secondary">
            View All Bookings
        </a>
    </div>
</div>

<style>
.confirmation-page {
    max-width: 800px;
    margin: 2rem auto;
}

.success-message {
    text-align: center;
    margin-bottom: 2rem;
}

.success-message i {
    font-size: 4rem;
    color: #28a745;
}

.card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 1.5rem;
}

.details-grid, .checklist-grid {
    display: grid;
    gap: 1rem;
}

.checklist-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.steps-list {
    padding-left: 1.5rem;
}

.steps-list li {
    margin: 0.5rem 0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
}
</style> 