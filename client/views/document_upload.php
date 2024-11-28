<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch required documents list with descriptions
$required_docs = [
    'baptismal' => [
        'name' => 'Baptismal Certificate',
        'description' => 'Recent copy with annotation for marriage'
    ],
    'confirmation' => [
        'name' => 'Confirmation Certificate',
        'description' => 'Must be authenticated'
    ],
    'marriage_license' => [
        'name' => 'Marriage License',
        'description' => 'Valid government-issued license'
    ],
    'birth_certificate' => [
        'name' => 'Birth Certificate',
        'description' => 'PSA authenticated copy'
    ],
    'cenomar' => [
        'name' => 'CENOMAR',
        'description' => 'Certificate of No Marriage Record from PSA'
    ]
];

// Fetch user's uploaded documents
$stmt = $db->prepare("
    SELECT * FROM documents 
    WHERE user_id = ? 
    ORDER BY uploaded_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$uploaded_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group uploaded documents by type
$uploaded_docs_by_type = [];
foreach($uploaded_docs as $doc) {
    $uploaded_docs_by_type[$doc['document_type']] = $doc;
}
?>

<div class="documents-section">
    <div class="section-header">
        <h2>Required Documents</h2>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> Only PDF files are accepted. Maximum file size: 5MB
        </div>
    </div>
    
    <div class="document-list">
        <?php foreach($required_docs as $doc_key => $doc_info): ?>
            <div class="document-item">
                <div class="doc-info">
                    <h4><?= $doc_info['name'] ?></h4>
                    <p class="doc-description"><?= $doc_info['description'] ?></p>
                    <?php
                    $uploaded = isset($uploaded_docs_by_type[$doc_key]);
                    $status = $uploaded ? $uploaded_docs_by_type[$doc_key]['status'] : 'not_uploaded';
                    ?>
                    
                    <span class="status-badge status-<?= $status ?>">
                        <?= ucfirst(str_replace('_', ' ', $status)) ?>
                    </span>
                    
                    <?php if($uploaded): ?>
                        <small class="upload-date">
                            Uploaded: <?= date('M d, Y h:i A', strtotime($uploaded_docs_by_type[$doc_key]['uploaded_at'])) ?>
                        </small>
                    <?php endif; ?>
                </div>
                
                <div class="doc-actions">
                    <form class="upload-form" data-doc-type="<?= $doc_key ?>">
                        <input type="file" 
                               id="file_<?= $doc_key ?>" 
                               class="file-input" 
                               accept=".pdf"
                               data-doc-type="<?= $doc_key ?>" 
                               style="display: none;">
                               
                        <button type="button" 
                                onclick="triggerFileInput('<?= $doc_key ?>')" 
                                class="btn btn-primary">
                            <i class="fas fa-upload"></i> 
                            <?= $uploaded ? 'Update Document' : 'Upload PDF' ?>
                        </button>
                        
                        <?php if($uploaded): ?>
                            <button type="button"
                                    onclick="viewDocument('<?= $doc_key ?>')" 
                                    class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.documents-section {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-header {
    margin-bottom: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin: 1rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-info {
    background-color: #cce5ff;
    border: 1px solid #b8daff;
    color: #004085;
}

.document-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #eee;
    transition: background-color 0.3s;
}

.document-item:hover {
    background-color: #f8f9fa;
}

.doc-info {
    flex: 1;
}

.doc-info h4 {
    margin: 0 0 0.5rem 0;
    color: #2c3e50;
}

.doc-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.upload-date {
    display: block;
    color: #6c757d;
    font-size: 0.8rem;
    margin-top: 0.5rem;
}

.doc-actions {
    display: flex;
    gap: 0.5rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.85rem;
    display: inline-block;
}

.status-not_uploaded { background: #f8f9fa; color: #6c757d; }
.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }

.btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.btn i {
    font-size: 0.9rem;
}
</style>

<script>
function triggerFileInput(docType) {
    // Show file type reminder
    Swal.fire({
        title: 'Upload Document',
        text: 'Please select a PDF file to upload',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Select File',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`file_${docType}`).click();
        }
    });
}

$(document).ready(function() {
    // Handle file input change
    $('.file-input').change(function() {
        const file = this.files[0];
        const docType = $(this).data('doc-type');
        
        // Validate file type
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload only PDF files.'
            });
            this.value = ''; // Clear the file input
            return;
        }

        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Maximum file size is 5MB.'
            });
            this.value = '';
            return;
        }

        uploadDocument(file, docType);
    });
});

function uploadDocument(file, docType) {
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
}

function viewDocument(docType) {
    $.get('../api/get-document.php', { document_type: docType })
        .done(function(response) {
            if (response.url) {
                window.open(response.url, '_blank');
            }
        })
        .fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to retrieve document.'
            });
        });
}
</script> 