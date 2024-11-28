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

// Calculate progress
$total_docs = count($required_docs);
$uploaded_count = count($docs_by_type);
$approved_count = 0;
foreach($docs_by_type as $doc) {
    if($doc['status'] === 'approved') {
        $approved_count++;
    }
}
$progress = ($total_docs > 0) ? round(($approved_count / $total_docs) * 100) : 0;
?>

<div class="documents-container">
    <!-- Progress Section -->
    <div class="progress-section">
        <h2>Document Progress</h2>
        <div class="progress-stats">
            <div class="stat-item">
                <div class="stat-value"><?= $progress ?>%</div>
                <div class="stat-label">Complete</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $approved_count ?>/<?= $total_docs ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?= $total_docs - $uploaded_count ?></div>
                <div class="stat-label">Missing</div>
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width: <?= $progress ?>%"></div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="documents-grid">
        <?php foreach($required_docs as $doc_type => $doc_info): ?>
            <div class="document-card <?= isset($docs_by_type[$doc_type]) ? 'uploaded' : '' ?>">
                <div class="doc-icon">
                    <i class="fas <?= $doc_info['icon'] ?>"></i>
                </div>
                <div class="doc-info">
                    <h4><?= $doc_info['name'] ?></h4>
                    <p class="description"><?= $doc_info['description'] ?></p>
                    
                    <?php if(isset($docs_by_type[$doc_type])): ?>
                        <div class="doc-status <?= $docs_by_type[$doc_type]['status'] ?>">
                            <?php if($docs_by_type[$doc_type]['status'] === 'approved'): ?>
                                <i class="fas fa-check-circle"></i> Approved
                            <?php elseif($docs_by_type[$doc_type]['status'] === 'rejected'): ?>
                                <i class="fas fa-times-circle"></i> Rejected
                                <?php if($docs_by_type[$doc_type]['remarks']): ?>
                                    <div class="remarks">
                                        Reason: <?= htmlspecialchars($docs_by_type[$doc_type]['remarks']) ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <i class="fas fa-clock"></i> Pending Review
                            <?php endif; ?>
                        </div>
                        <div class="upload-info">
                            Uploaded: <?= date('M d, Y h:i A', strtotime($docs_by_type[$doc_type]['created_at'])) ?>
                        </div>
                        <div class="doc-actions">
                            <button onclick="viewDocument('<?= $doc_type ?>')" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button onclick="uploadDocument('<?= $doc_type ?>')" class="btn btn-sm btn-primary">
                                <i class="fas fa-upload"></i> Update
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="doc-status missing">
                            <i class="fas fa-exclamation-circle"></i> Required
                        </div>
                        <button onclick="uploadDocument('<?= $doc_type ?>')" class="btn btn-sm btn-primary">
                            <i class="fas fa-upload"></i> Upload Now
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.documents-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.progress-section {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.progress-stats {
    display: flex;
    justify-content: space-around;
    margin: 1.5rem 0;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.progress {
    height: 1rem;
    background: #e9ecef;
    border-radius: 0.5rem;
    overflow: hidden;
}

.progress-bar {
    background: #007bff;
    transition: width 0.3s ease;
}

.documents-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.document-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    gap: 1rem;
}

.doc-icon {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border-radius: 50%;
    font-size: 1.5rem;
    color: #007bff;
}

.doc-info {
    flex: 1;
}

.doc-info h4 {
    margin: 0 0 0.5rem 0;
}

.description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.doc-status {
    margin: 0.5rem 0;
    padding: 0.5rem;
    border-radius: 4px;
    font-size: 0.9rem;
}

.doc-status.approved {
    background: #d4edda;
    color: #155724;
}

.doc-status.rejected {
    background: #f8d7da;
    color: #721c24;
}

.doc-status.pending {
    background: #fff3cd;
    color: #856404;
}

.doc-status.missing {
    background: #f8f9fa;
    color: #666;
}

.remarks {
    font-size: 0.8rem;
    margin-top: 0.5rem;
    font-style: italic;
}

.upload-info {
    font-size: 0.8rem;
    color: #666;
    margin: 0.5rem 0;
}

.doc-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}

.btn {
    padding: 0.375rem 0.75rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-info {
    background: #17a2b8;
    color: white;
}
</style>

<script>
function uploadDocument(docType) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf';
    
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        // Validate file type
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload only PDF files.'
            });
            return;
        }
        
        // Validate file size (5MB)
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
        
        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while we upload your document.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
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

function viewDocument(docType) {
    $.get('../api/get-document.php', { document_type: docType })
        .done(function(response) {
            if (response.url) {
                window.open(response.url, '_blank');
            }
        })
        .fail(function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: xhr.responseJSON?.message || 'Failed to retrieve document.'
            });
        });
}
</script> 