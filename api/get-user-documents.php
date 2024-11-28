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

// Get user and booking information
$stmt = $db->prepare("
    SELECT 
        u.name,
        u.email,
        b.wedding_date,
        b.document_progress
    FROM users u
    JOIN bookings b ON u.id = b.user_id
    WHERE u.id = ?
    ORDER BY b.wedding_date ASC
    LIMIT 1
");
$stmt->execute([$user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's documents
$stmt = $db->prepare("
    SELECT 
        d.*,
        dr.name as document_name,
        dr.description
    FROM documents d
    JOIN bookings b ON d.booking_id = b.id
    JOIN document_requirements dr ON d.document_type = dr.document_type
    WHERE b.user_id = ?
    ORDER BY d.status = 'pending' DESC, d.created_at DESC
");
$stmt->execute([$user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Client Information -->
<div class="client-info card mb-4">
    <div class="card-body">
        <h6 class="card-subtitle mb-2 text-muted">Client Information</h6>
        <div class="row">
            <div class="col-md-4">
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($user_info['name']) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-1"><strong>Wedding Date:</strong> <?= date('M d, Y', strtotime($user_info['wedding_date'])) ?></p>
            </div>
            <div class="col-md-4">
                <p class="mb-1"><strong>Document Progress:</strong></p>
                <div class="progress">
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: <?= $user_info['document_progress'] ?>%"
                         aria-valuenow="<?= $user_info['document_progress'] ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= number_format($user_info['document_progress'], 0) ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents List -->
<div class="documents-list">
    <?php foreach($documents as $doc): ?>
        <div class="document-item">
            <div class="document-header">
                <h6 class="mb-0"><?= htmlspecialchars($doc['document_name']) ?></h6>
                <span class="status-badge status-<?= $doc['status'] ?>">
                    <?= ucfirst($doc['status']) ?>
                </span>
            </div>
            <div class="document-info">
                <small class="text-muted">
                    Uploaded: <?= date('M d, Y h:i A', strtotime($doc['created_at'])) ?>
                </small>
                <?php if($doc['description']): ?>
                    <p class="mb-1 small"><?= htmlspecialchars($doc['description']) ?></p>
                <?php endif; ?>
                <?php if($doc['remarks']): ?>
                    <div class="remarks">
                        <i class="fas fa-info-circle"></i> <?= htmlspecialchars($doc['remarks']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="document-actions">
                <button class="btn btn-sm btn-info" 
                        onclick="viewDocument('<?= htmlspecialchars($doc['file_path']) ?>')"
                        data-bs-toggle="tooltip" 
                        title="View Document">
                    <i class="fas fa-eye"></i> View
                </button>
                <?php if($doc['status'] === 'pending'): ?>
                    <button class="btn btn-sm btn-success me-2" 
                            onclick="approveDocument(<?= $doc['id'] ?>)">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button class="btn btn-sm btn-danger" 
                            onclick="rejectDocument(<?= $doc['id'] ?>)">
                        <i class="fas fa-times"></i> Reject
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div> 