<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get users with documents (both pending and approved)
$stmt = $db->query("
    SELECT DISTINCT 
        u.id,
        u.name,
        u.email,
        (SELECT COUNT(*) FROM documents d2 WHERE d2.user_id = u.id AND d2.status = 'approved') as approved_docs,
        (SELECT COUNT(*) FROM documents d3 WHERE d3.user_id = u.id) as total_docs
    FROM users u
    JOIN documents d ON d.user_id = u.id
    ORDER BY u.name ASC
");
$users_with_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Document Approval</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="documentsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="text-nowrap">Client Name</th>
                            <th class="text-nowrap">Email</th>
                            <th class="text-nowrap">Documents Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users_with_docs as $user): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </div>
                                        <div class="ms-3">
                                            <h6 class="mb-0"><?= htmlspecialchars($user['name']) ?></h6>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $progress = ($user['total_docs'] > 0) ? ($user['approved_docs'] / $user['total_docs']) * 100 : 0;
                                        $status_class = $progress == 100 ? 'bg-success' : ($progress > 0 ? 'bg-warning' : 'bg-danger');
                                        ?>
                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                            <div class="progress-bar <?= $status_class ?>" 
                                                 role="progressbar" 
                                                 style="width: <?= $progress ?>%" 
                                                 aria-valuenow="<?= $progress ?>" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="badge <?= $status_class ?> rounded-pill">
                                            <?= $user['approved_docs'] ?>/<?= $user['total_docs'] ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-primary btn-sm px-3" 
                                            onclick="viewDocuments(<?= $user['id'] ?>)">
                                        <i class="fas fa-file-alt me-1"></i> View Documents
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Documents Modal -->
<div class="modal fade" id="documentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-file-alt me-2"></i>
                    User Documents
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="documents-wrapper">
                    <!-- Documents will be loaded here -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Document Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf me-2"></i>
                    Document Preview
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="iframe-container">
                    <iframe id="documentPreview" style="width: 100%; height: 80vh; border: none;"></iframe>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTable with simple configuration
    const table = $('#documentsTable').DataTable({
        pageLength: 10,
        order: [[2, 'asc']], // Sort by wedding date
        responsive: true,
        language: {
            emptyTable: "No documents found",
            search: "Search:",
            lengthMenu: "Show _MENU_ entries"
        }
    });
});

function viewDocuments(userId) {
    // Store user ID in modal data
    $('#documentsModal').data('userId', userId);
    
    // Show loading state
    $('#documentsModal .modal-body').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading documents...</div>');
    $('#documentsModal').modal('show');
    
    // Load documents
    $.get('../api/get-user-documents.php', { 
        user_id: userId 
    })
    .done(function(response) {
        $('#documentsModal .modal-body').html(response);
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
    .fail(function(xhr) {
        $('#documentsModal .modal-body').html('<div class="alert alert-danger">Failed to load documents</div>');
        console.error('Error loading documents:', xhr.responseText);
    });
}

function approveDocument(docId) {
    Swal.fire({
        title: 'Approve Document?',
        text: 'This will mark the document as approved and notify the client.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, approve',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#28a745'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/approve-document.php', {
                document_id: docId,
                action: 'approve',
                remarks: ''
            })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Approved!',
                        text: 'Document has been approved and client has been notified.',
                        timer: 1500
                    }).then(() => {
                        // Refresh the documents list in modal
                        const userId = $('#documentsModal').data('userId');
                        viewDocuments(userId);
                        
                        // Reload the page instead of trying to update DataTable
                        window.location.reload();
                    });
                } else {
                    throw new Error(response.error);
                }
            })
            .fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.error || 'Failed to approve document.'
                });
            });
        }
    });
}

function rejectDocument(docId) {
    Swal.fire({
        title: 'Reject Document',
        html: `
            <div class="form-group">
                <label for="rejectionReason" class="text-left">Please provide a reason for rejection:</label>
                <textarea id="rejectionReason" class="form-control" rows="4" 
                    placeholder="Enter detailed reason for rejection..."></textarea>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Reject',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#dc3545',
        preConfirm: () => {
            const reason = document.getElementById('rejectionReason').value;
            if (!reason.trim()) {
                Swal.showValidationMessage('Please provide a reason for rejection');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/approve-document.php', {
                document_id: docId,
                action: 'reject',
                remarks: result.value
            })
            .done(function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejected!',
                        text: 'Document has been rejected and client has been notified.',
                        timer: 1500
                    }).then(() => {
                        // Refresh the documents list in modal
                        const userId = $('#documentsModal').data('userId');
                        viewDocuments(userId);
                        
                        // Reload the page instead of trying to update DataTable
                        window.location.reload();
                    });
                } else {
                    throw new Error(response.error);
                }
            })
            .fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.error || 'Failed to reject document.'
                });
            });
        }
    });
}

function previewDocument(docId) {
    $('#documentsModal').modal('hide');
    $('#documentPreview').attr('src', `../api/view-document.php?id=${docId}`);
    $('#previewModal').modal('show');
}

// Handle preview modal close
$('#previewModal').on('hidden.bs.modal', function () {
    // Show the documents modal again
    $('#documentsModal').modal('show');
    // Clear the iframe source
    $('#documentPreview').attr('src', '');
});
</script>

<style>
.documents-list .document-item {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    margin-bottom: 1rem;
}

.document-item .document-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.document-item .document-info {
    margin-bottom: 0.5rem;
}

.document-item .document-actions {
    display: flex;
    gap: 0.5rem;
}

.status-badge {
    padding: 0.25em 0.6em;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 0.25rem;
}

.status-pending {
    background-color: #ffc107;
    color: #000;
}

.status-approved {
    background-color: #28a745;
    color: #fff;
}

.status-rejected {
    background-color: #dc3545;
    color: #fff;
}

.remarks {
    font-size: 0.875rem;
    color: #dc3545;
    margin-top: 0.5rem;
}

.dataTables_wrapper .dataTables_processing {
    background: rgba(255,255,255,0.9);
    border: 1px solid #ddd;
    border-radius: 3px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.table-responsive {
    padding: 1rem;
}

.dataTables_wrapper .dataTables_length select {
    min-width: 60px;
}

.dataTables_wrapper .dataTables_filter input {
    min-width: 200px;
}
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    .modal-dialog {
        max-width: 90%;
        margin: 1.75rem auto;
    }

    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

.avatar-circle {
    width: 40px;
    height: 40px;
    background-color: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: #495057;
}

.card.shadow {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
}

.table > :not(caption) > * > * {
    padding: 1rem 1rem;
}

.progress {
    background-color: #e9ecef;
    border-radius: 0.5rem;
}

.badge {
    padding: 0.5em 1em;
    font-weight: 500;
}

.btn-sm {
    padding: 0.4rem 1rem;
    font-size: 0.875rem;
}

/* Modal Styles */
.modal-header {
    border-bottom: 1px solid #dee2e6;
    background-color: #f8f9fa;
}

.modal-title {
    display: flex;
    align-items: center;
    color: #2c3e50;
    font-weight: 500;
}

.documents-wrapper {
    min-height: 300px;
}

.document-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.document-card:hover {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transform: translateY(-1px);
}

.document-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.document-title {
    font-size: 1.1rem;
    font-weight: 500;
    color: #2c3e50;
    margin: 0;
}

.iframe-container {
    position: relative;
    background: #f8f9fa;
    border-radius: 4px;
    overflow: hidden;
}

.modal-footer {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

/* Loading Spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Document Status Badges */
.badge {
    padding: 0.5em 1em;
    font-weight: 500;
    text-transform: capitalize;
}

.badge.bg-success {
    background-color: #28a745 !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
}

/* Document Action Buttons */
.btn-group-sm > .btn, .btn-sm {
    padding: 0.4rem 0.8rem;
    font-size: 0.875rem;
    border-radius: 0.2rem;
}

.btn i {
    margin-right: 0.25rem;
}
</style> 