<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get client ID from URL if viewing specific client
$client_id = isset($_GET['client_id']) ? $_GET['client_id'] : null;

// Fetch documents with client information
$query = "
    SELECT 
        d.*,
        b.user_id,
        u.name as client_name,
        b.wedding_date
    FROM documents d
    JOIN bookings b ON d.booking_id = b.id
    JOIN users u ON b.user_id = u.id
";

if($client_id) {
    $query .= " WHERE b.user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$client_id]);
} else {
    $stmt = $db->prepare($query);
    $stmt->execute();
}

$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container-fluid">
    <h2 class="mb-4">Client Documents</h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="documentsTable">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Wedding Date</th>
                            <th>Document Type</th>
                            <th>File</th>
                            <th>Upload Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($documents as $doc): ?>
                            <tr>
                                <td><?= htmlspecialchars($doc['client_name']) ?></td>
                                <td><?= date('M d, Y', strtotime($doc['wedding_date'])) ?></td>
                                <td>
                                    <?= ucfirst(str_replace('_', ' ', $doc['document_type'])) ?>
                                </td>
                                <td>
                                    <?php
                                    $filename = basename($doc['file_path']);
                                    echo htmlspecialchars($filename);
                                    ?>
                                </td>
                                <td><?= date('M d, Y h:i A', strtotime($doc['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" 
                                            onclick="viewDocument(<?= $doc['id'] ?>)">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-success" 
                                            onclick="approveDocument(<?= $doc['id'] ?>)">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                            onclick="rejectDocument(<?= $doc['id'] ?>)">
                                        <i class="fas fa-times"></i> Reject
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

<script>
$(document).ready(function() {
    $('#documentsTable').DataTable({
        order: [[4, 'desc']], // Sort by upload date by default
        responsive: true
    });
});

function viewDocument(docId) {
    window.open(`../api/view-document.php?id=${docId}`, '_blank');
}

function approveDocument(docId) {
    Swal.fire({
        title: 'Approve Document?',
        text: 'Are you sure you want to approve this document?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, approve it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/update-document-status.php', {
                document_id: docId,
                status: 'approved'
            })
            .done(function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Document Approved',
                    text: 'The document has been approved successfully.'
                }).then(() => {
                    location.reload();
                });
            })
            .fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to approve document'
                });
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
        confirmButtonText: 'Yes, reject it'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../api/update-document-status.php', {
                document_id: docId,
                status: 'rejected',
                remarks: result.value
            })
            .done(function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Document Rejected',
                    text: 'The document has been rejected successfully.'
                }).then(() => {
                    location.reload();
                });
            })
            .fail(function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to reject document'
                });
            });
        }
    });
}
</script> 