<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
if(!$user_id) {
    http_response_code(400);
    exit('Invalid user ID');
}

$db = Database::getInstance()->getConnection();

// Get user's documents
$stmt = $db->prepare("
    SELECT 
        d.*,
        dr.name as document_name
    FROM documents d
    JOIN bookings b ON d.booking_id = b.id
    JOIN document_requirements dr ON d.document_type = dr.document_type
    WHERE b.user_id = ?
    ORDER BY d.status DESC, d.created_at DESC
");
$stmt->execute([$user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Output documents list
?>
<div class="list-group">
    <?php foreach($documents as $doc): ?>
        <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1"><?= htmlspecialchars($doc['document_name']) ?></h6>
                    <small class="text-muted">
                        Uploaded: <?= date('M d, Y h:i A', strtotime($doc['created_at'])) ?>
                    </small>
                    <?php if($doc['remarks']): ?>
                        <p class="mb-1 text-danger">
                            <small>Remarks: <?= htmlspecialchars($doc['remarks']) ?></small>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center">
                    <a href="../<?= htmlspecialchars($doc['file_path']) ?>" 
                       class="btn btn-sm btn-info me-2" 
                       target="_blank">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <?php if($doc['status'] === 'pending'): ?>
                        <button class="btn btn-sm btn-success me-2" 
                                onclick="approveDocument(<?= $doc['id'] ?>)">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-sm btn-danger" 
                                onclick="rejectDocument(<?= $doc['id'] ?>)">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    <?php else: ?>
                        <span class="badge bg-<?= $doc['status'] === 'approved' ? 'success' : 'danger' ?>">
                            <?= ucfirst($doc['status']) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div> 