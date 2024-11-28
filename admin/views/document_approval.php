<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get all pending documents
$stmt = $db->prepare("
    SELECT 
        d.*,
        u.name as client_name,
        u.email as client_email,
        dr.name as document_name
    FROM documents d
    JOIN bookings b ON d.booking_id = b.id
    JOIN users u ON b.user_id = u.id
    JOIN document_requirements dr ON d.document_type = dr.document_type
    WHERE d.status = 'pending'
    ORDER BY d.created_at DESC
");
$stmt->execute();
$pending_documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Document Approval</h2>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Document Type</th>
                            <th>Submitted Date</th>
                            <th>Document</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pending_documents as $doc): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($doc['client_name']) ?>
                                    <small class="d-block text-muted"><?= $doc['client_email'] ?></small>
                                </td>
                                <td><?= htmlspecialchars($doc['document_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($doc['created_at'])) ?></td>
                                <td>
                                    <a href="../<?= htmlspecialchars($doc['file_path']) ?>" 
                                       class="btn btn-sm btn-info" 
                                       target="_blank">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success approve-doc" 
                                            data-id="<?= $doc['id'] ?>"
                                            data-client="<?= htmlspecialchars($doc['client_name']) ?>">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger reject-doc" 
                                            data-id="<?= $doc['id'] ?>"
                                            data-client="<?= htmlspecialchars($doc['client_name']) ?>">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if(empty($pending_documents)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-check-circle text-success fa-2x mb-3"></i>
                                    <p class="mb-0">No pending documents for review</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle document approval
    $('.approve-doc').click(function() {
        const docId = $(this).data('id');
        const clientName = $(this).data('client');
        
        Swal.fire({
            title: 'Approve Document?',
            text: `Approve document for ${clientName}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/approve-document.php', {
                    document_id: docId,
                    action: 'approve'
                })
                .done(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Document has been approved.'
                    }).then(() => {
                        location.reload();
                    });
                })
                .fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to approve document.'
                    });
                });
            }
        });
    });

    // Handle document rejection
    $('.reject-doc').click(function() {
        const docId = $(this).data('id');
        const clientName = $(this).data('client');
        
        Swal.fire({
            title: 'Reject Document?',
            text: `Please provide a reason for rejection:`,
            input: 'textarea',
            inputPlaceholder: 'Enter rejection reason...',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Reject'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('../api/approve-document.php', {
                    document_id: docId,
                    action: 'reject',
                    remarks: result.value
                })
                .done(function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejected',
                        text: 'Document has been rejected.'
                    }).then(() => {
                        location.reload();
                    });
                })
                .fail(function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed to reject document.'
                    });
                });
            }
        });
    });
});
</script> 