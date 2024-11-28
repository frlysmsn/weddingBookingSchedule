<?php
$booking_id = $_GET['booking_id'];
$db = Database::getInstance()->getConnection();

// Get booking details with status history
$stmt = $db->prepare("
    SELECT b.*, 
           bs.status,
           bs.created_at as status_date,
           bs.remarks,
           u.name as updated_by
    FROM bookings b
    LEFT JOIN booking_status_history bs ON b.id = bs.booking_id
    LEFT JOIN users u ON bs.updated_by = u.id
    WHERE b.id = ? AND b.user_id = ?
    ORDER BY bs.created_at DESC
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$status_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get document status
$stmt = $db->prepare("
    SELECT document_type, status, uploaded_at, reviewed_at
    FROM documents
    WHERE user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="status-tracking">
    <div class="timeline">
        <?php foreach($status_history as $status): ?>
            <div class="timeline-item">
                <div class="timeline-marker"></div>
                <div class="timeline-content">
                    <h4><?= ucfirst($status['status']) ?></h4>
                    <p class="timestamp">
                        <?= date('M d, Y h:i A', strtotime($status['status_date'])) ?>
                    </p>
                    <?php if($status['remarks']): ?>
                        <p class="remarks"><?= $status['remarks'] ?></p>
                    <?php endif; ?>
                    <p class="updated-by">Updated by: <?= $status['updated_by'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="documents-status">
        <h3>Documents Status</h3>
        <div class="documents-grid">
            <?php foreach($documents as $doc): ?>
                <div class="document-status-item">
                    <h4><?= ucfirst(str_replace('_', ' ', $doc['document_type'])) ?></h4>
                    <span class="status-badge status-<?= $doc['status'] ?>">
                        <?= ucfirst($doc['status']) ?>
                    </span>
                    <p>Uploaded: <?= date('M d, Y', strtotime($doc['uploaded_at'])) ?></p>
                    <?php if($doc['reviewed_at']): ?>
                        <p>Reviewed: <?= date('M d, Y', strtotime($doc['reviewed_at'])) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    padding-left: 70px;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: 42px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #007bff;
    border: 2px solid #fff;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.document-status-item {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style> 