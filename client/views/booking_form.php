<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

require_once '../includes/db_connection.php';
$db = Database::getInstance()->getConnection();

// Required documents with descriptions
$required_docs = [
    'baptismal' => [
        'name' => 'Baptismal Certificate',
        'description' => 'Recent copy with annotation for marriage',
        'icon' => 'fa-church'
    ],
    'confirmation' => [
        'name' => 'Confirmation Certificate',
        'description' => 'Must be authenticated',
        'icon' => 'fa-scroll'
    ],
    'marriage_license' => [
        'name' => 'Marriage License',
        'description' => 'Valid government-issued license',
        'icon' => 'fa-file-contract'
    ],
    'birth_certificate' => [
        'name' => 'Birth Certificate',
        'description' => 'PSA authenticated copy',
        'icon' => 'fa-file-alt'
    ],
    'cenomar' => [
        'name' => 'CENOMAR',
        'description' => 'Certificate of No Marriage Record from PSA',
        'icon' => 'fa-file-signature'
    ]
];

// Get uploaded documents
$stmt = $db->prepare("
    SELECT d.* 
    FROM documents d 
    JOIN bookings b ON d.booking_id = b.id 
    WHERE b.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$uploaded_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group documents by type
$docs_by_type = [];
foreach($uploaded_docs as $doc) {
    $docs_by_type[$doc['document_type']] = $doc;
}

// Get document upload progress
$stmt = $db->prepare("
    SELECT 
        COUNT(DISTINCT d.document_type) as uploaded_docs,
        SUM(CASE WHEN d.status = 'approved' THEN 1 ELSE 0 END) as approved_docs
    FROM documents d
    JOIN bookings b ON d.booking_id = b.id
    WHERE b.user_id = ? 
    AND d.document_type IN ('baptismal', 'confirmation', 'birth_certificate', 'marriage_license')
");
$stmt->execute([$_SESSION['user_id']]);
$doc_progress = $stmt->fetch(PDO::FETCH_ASSOC);

$total_required_docs = 4;
$current_step = 1;
if ($doc_progress['uploaded_docs'] >= $total_required_docs) {
    $current_step = 2;
    if ($doc_progress['approved_docs'] >= $total_required_docs) {
        $current_step = 3;
    }
}
?>

<!-- Progress Tracker -->
<div class="progress-tracker">
    <div class="progress-step <?= $current_step >= 1 ? 'active' : '' ?> <?= $current_step > 1 ? 'completed' : '' ?>">
        <div class="step-icon">
            <i class="fas fa-file-upload"></i>
        </div>
        <div class="step-label">Upload Documents</div>
        <div class="step-progress"><?= $doc_progress['uploaded_docs'] ?>/<?= $total_required_docs ?></div>
    </div>
    <div class="progress-step <?= $current_step >= 2 ? 'active' : '' ?> <?= $current_step > 2 ? 'completed' : '' ?>">
        <div class="step-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="step-label">Document Approval</div>
        <div class="step-progress"><?= $doc_progress['approved_docs'] ?>/<?= $total_required_docs ?></div>
    </div>
    <div class="progress-step <?= $current_step >= 3 ? 'active' : '' ?>">
        <div class="step-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div class="step-label">Book Wedding Date</div>
    </div>
</div>

    <!-- Document Upload Section -->
    <div class="section document-section">
        <h3>Required Documents</h3>
        <div class="documents-grid">
            <?php foreach($required_docs as $doc_type => $doc_info): ?>
                <div class="document-card">
                    <div class="doc-icon">
                        <i class="fas <?= $doc_info['icon'] ?>"></i>
                    </div>
                    <div class="doc-info">
                        <h4><?= $doc_info['name'] ?></h4>
                        <p><?= $doc_info['description'] ?></p>
                        <?php if(isset($docs_by_type[$doc_type])): ?>
                            <div class="status <?= $docs_by_type[$doc_type]['status'] ?>">
                                <?= ucfirst($docs_by_type[$doc_type]['status']) ?>
                            </div>
                            <button onclick="uploadDocument('<?= $doc_type ?>')" class="btn btn-sm btn-primary">
                                <i class="fas fa-sync"></i> Update
                            </button>
                        <?php else: ?>
                            <button onclick="uploadDocument('<?= $doc_type ?>')" class="btn btn-sm btn-primary">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Booking Form Section -->
    <?php if($progress == 100): ?>
        <div class="section booking-form-section">
            <h3>Book Your Wedding Date</h3>
            <!-- Your existing booking form -->
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            Please upload and get approval for all required documents before booking your wedding date.
        </div>
    <?php endif; ?>
</div>

<style>
/* Add your existing styles plus these new ones */
.progress-tracker {
    display: flex;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.step {
    text-align: center;
    position: relative;
    flex: 1;
}

.step:not(:last-child):after {
    content: '';
    position: absolute;
    top: 25px;
    left: 60%;
    width: 80%;
    height: 2px;
    background: #ddd;
}

.step.completed:after {
    background: #28a745;
}

.step-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-size: 1.5rem;
    color: #6c757d;
}

.step.active .step-icon {
    background: #007bff;
    color: white;
}

.step.completed .step-icon {
    background: #28a745;
    color: white;
}

.step-label {
    font-size: 0.9rem;
    color: #6c757d;
}

.step.active .step-label {
    color: #007bff;
    font-weight: bold;
}

.step-progress {
    font-size: 0.8rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

/* Add your other existing styles */
</style>

<script>
// Add your existing JavaScript plus this function
function uploadDocument(docType) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf';
    
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload only PDF files.'
            });
            return;
        }
        
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Maximum file size is 5MB.'
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
                    text: 'Document uploaded successfully.'
                }).then(() => {
                    location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Failed to upload document.'
                });
            }
        });
    };
    
    input.click();
}
</script> 