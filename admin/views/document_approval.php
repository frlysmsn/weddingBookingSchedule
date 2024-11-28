<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get users with pending documents
$stmt = $db->query("
    SELECT DISTINCT 
        u.id,
        u.name,
        u.email,
        b.wedding_date,
        (SELECT COUNT(*) FROM documents d2 WHERE d2.booking_id = b.id AND d2.status = 'pending') as pending_docs,
        (SELECT COUNT(*) FROM documents d3 WHERE d3.booking_id = b.id) as total_docs
    FROM users u
    JOIN bookings b ON u.id = b.user_id
    JOIN documents d ON d.booking_id = b.id
    WHERE d.status = 'pending'
    ORDER BY b.wedding_date ASC
");
$users_with_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Document Approval</h2>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="documentsTable">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Email</th>
                            <th>Wedding Date</th>
                            <th>Documents Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users_with_docs as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= date('M d, Y', strtotime($user['wedding_date'])) ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <?php 
                                        $percentage = ($user['total_docs'] - $user['pending_docs']) / $user['total_docs'] * 100;
                                        ?>
                                        <div class="progress-bar bg-success" 
                                             role="progressbar" 
                                             style="width: <?= $percentage ?>%"
                                             aria-valuenow="<?= $percentage ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= floor($percentage) ?>%
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <?= $user['total_docs'] - $user['pending_docs'] ?> of <?= $user['total_docs'] ?> approved
                                    </small>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="viewDocuments(<?= $user['id'] ?>)">
                                        <i class="fas fa-file-alt"></i> View Documents
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
            <div class="modal-header">
                <h5 class="modal-title">Client Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="client-info mb-4">
                    <!-- Client info will be loaded here -->
                </div>
                <div class="documents-list">
                    <!-- Documents will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
</style> 