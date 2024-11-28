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
                <!-- Documents will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#documentsTable').DataTable({
        order: [[2, 'asc']], // Sort by wedding date
        pageLength: 10
    });
});

function viewDocuments(userId) {
    // Load documents into modal
    $.get('../api/get-user-documents.php', { user_id: userId })
        .done(function(response) {
            $('#documentsModal .modal-body').html(response);
            $('#documentsModal').modal('show');
        })
        .fail(function() {
            Swal.fire('Error!', 'Failed to load documents.', 'error');
        });
}

function approveDocument(docId) {
    Swal.fire({
        title: 'Approve Document?',
        text: 'Are you sure you want to approve this document?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, approve it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/approve-document.php', {
                document_id: docId,
                action: 'approve'
            })
            .done(function(response) {
                Swal.fire('Approved!', 'Document has been approved.', 'success')
                .then(() => location.reload());
            })
            .fail(function() {
                Swal.fire('Error!', 'Failed to approve document.', 'error');
            });
        }
    });
}

function rejectDocument(docId) {
    Swal.fire({
        title: 'Reject Document?',
        text: 'Please provide a reason for rejection:',
        input: 'text',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reject it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/approve-document.php', {
                document_id: docId,
                action: 'reject',
                remarks: result.value
            })
            .done(function(response) {
                Swal.fire('Rejected!', 'Document has been rejected.', 'success')
                .then(() => location.reload());
            })
            .fail(function() {
                Swal.fire('Error!', 'Failed to reject document.', 'error');
            });
        }
    });
}
</script> 