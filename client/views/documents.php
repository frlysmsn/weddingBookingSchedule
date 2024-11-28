<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get user's active booking
$stmt = $db->prepare("
    SELECT id FROM bookings 
    WHERE user_id = ? 
    AND status != 'cancelled' 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

// Get document requirements
$stmt = $db->prepare("SELECT * FROM document_requirements ORDER BY is_required DESC, name ASC");
$stmt->execute();
$requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's uploaded documents
$uploaded_docs = [];
if ($booking) {
    $stmt = $db->prepare("
        SELECT * FROM documents 
        WHERE booking_id = ?
        ORDER BY created_at DESC
    ");
    $stmt->execute([$booking['id']]);
    while ($doc = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $uploaded_docs[$doc['document_type']] = $doc;
    }
}
?>

<div class="container mt-4">
    <h2>My Documents</h2>
    
    <div class="row">
        <?php foreach($requirements as $req): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <?= htmlspecialchars($req['name']) ?>
                            <?php if(!$req['is_required']): ?>
                                <span class="badge bg-info">Optional</span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            <?= htmlspecialchars($req['description']) ?>
                        </p>
                        
                        <?php if(isset($uploaded_docs[$req['document_type']])): ?>
                            <?php $doc = $uploaded_docs[$req['document_type']]; ?>
                            <div class="uploaded-document">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-<?= $doc['status'] === 'approved' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($doc['status']) ?>
                                    </span>
                                    <small class="text-muted">
                                        Uploaded: <?= date('M d, Y', strtotime($doc['created_at'])) ?>
                                    </small>
                                </div>
                                
                                <?php if($doc['remarks']): ?>
                                    <div class="alert alert-info small mb-2">
                                        <i class="fas fa-info-circle"></i> <?= htmlspecialchars($doc['remarks']) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex gap-2">
                                    <!-- View PDF button -->
                                    <a href="../<?= htmlspecialchars($doc['file_path']) ?>" 
                                       class="btn btn-primary btn-sm" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i> View PDF
                                    </a>
                                    
                                    <!-- Replace button -->
                                    <button type="button" 
                                            class="btn btn-warning btn-sm upload-btn" 
                                            data-type="<?= $req['document_type'] ?>">
                                        <i class="fas fa-sync-alt"></i> Replace
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-3">
                                <p class="text-muted mb-3">No document uploaded yet</p>
                                <button type="button" 
                                        class="btn btn-primary upload-btn" 
                                        data-type="<?= $req['document_type'] ?>">
                                    <i class="fas fa-upload"></i> Upload Document
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.card-header {
    background-color: #f8f9fa;
}
.uploaded-document {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
}
.gap-2 {
    gap: 0.5rem;
}
</style>

<script>
$(document).ready(function() {
    $('.upload-btn').click(function() {
        const docType = $(this).data('type');
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf';
        
        input.onchange = function() {
            const file = this.files[0];
            if (!file) return;
            
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Maximum file size is 5MB'
                });
                return;
            }
            
            const formData = new FormData();
            formData.append('document', file);
            formData.append('document_type', docType);
            
            $.ajax({
                url: '../api/upload-document.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Document uploaded successfully'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to upload document'
                    });
                }
            });
        };
        
        input.click();
    });
});
</script>