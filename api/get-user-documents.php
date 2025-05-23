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
    LEFT JOIN bookings b ON u.id = b.user_id
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
    JOIN document_requirements dr ON d.document_type = dr.document_type
    WHERE d.user_id = ?
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
            <div class="col-md-6">
                <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($user_info['name']) ?></p>
                <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($user_info['email']) ?></p>
            </div>
            <div class="col-md-6">
                <?php if($user_info['wedding_date']): ?>
                    <p class="mb-1"><strong>Wedding Date:</strong> <?= date('M d, Y', strtotime($user_info['wedding_date'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Documents List -->
<div class="documents-list">
    <?php foreach($documents as $doc): ?>
        <div class="document-item card mb-3">
            <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($doc['document_name']) ?></h6>
                <p class="text-muted small"><?= htmlspecialchars($doc['description']) ?></p>
                
                <div class="document-status mb-2">
                    <span class="badge bg-<?= $doc['status'] === 'approved' ? 'success' : 'warning' ?>">
                        <?= ucfirst($doc['status']) ?>
                    </span>
                </div>
                
                <?php if($doc['remarks']): ?>
                    <div class="remarks text-danger small mb-2">
                        <i class="fas fa-info-circle"></i> <?= htmlspecialchars($doc['remarks']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="document-actions">
                    <button class="btn btn-sm btn-info me-2" 
                            onclick="previewDocument(<?= $doc['id'] ?>)">
                        <i class="fas fa-eye"></i> View
                    </button>
                    <?php if($doc['status'] === 'pending'): ?>
                        <button class="btn btn-sm btn-success me-2" 
                                onclick="approveDocument(<?= $doc['id'] ?>)">
                            <i class="fas fa-check"></i> Approve
                        </button>
                        <button class="btn btn-sm btn-danger me-2" 
                                onclick="rejectDocument(<?= $doc['id'] ?>)">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-danger" 
                            onclick="deleteDocument(<?= $doc['id'] ?>)">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add SweetAlert2 CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function deleteDocument(documentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this deletion!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                html: 'Please wait while we delete the document.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('../api/delete-document.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'document_id=' + documentId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the document card from the UI
                    const documentCard = document.querySelector(`.document-item:has(button[onclick="deleteDocument(${documentId})"])`);
                    if (documentCard) {
                        documentCard.remove();
                    }
                    
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Document has been deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting the document',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
}
</script> 