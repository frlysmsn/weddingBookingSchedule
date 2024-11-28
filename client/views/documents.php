<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

require_once '../includes/db_connection.php';
$db = Database::getInstance()->getConnection();

// Required documents list
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

// Get user's uploaded documents
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
?>

<div class="documents-container">
    <div class="section-header">
        <h2>Required Documents</h2>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Note:</strong> Please upload all required documents in PDF format only. Maximum file size: 5MB
        </div>
    </div>

    <div class="documents-grid">
        <?php foreach($required_docs as $doc_type => $doc_info): ?>
            <div class="document-card">
                <div class="doc-info">
                    <h4><?= $doc_info['name'] ?></h4>
                    <p class="description"><?= $doc_info['description'] ?></p>
                    
                    <?php if(isset($docs_by_type[$doc_type])): ?>
                        <div class="upload-info">
                            <span class="upload-date">
                                Uploaded: <?= date('M d, Y h:i A', strtotime($docs_by_type[$doc_type]['created_at'])) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="doc-actions">
                    <form class="upload-form" data-doc-type="<?= $doc_type ?>">
                        <input type="file" 
                               id="file_<?= $doc_type ?>" 
                               class="file-input" 
                               accept=".pdf" 
                               style="display: none;">
                               
                        <button type="button" 
                                onclick="triggerFileInput('<?= $doc_type ?>')" 
                                class="btn btn-primary">
                            <i class="fas fa-upload"></i> 
                            <?= isset($docs_by_type[$doc_type]) ? 'Update Document' : 'Upload PDF' ?>
                        </button>
                        
                        <?php if(isset($docs_by_type[$doc_type])): ?>
                            <button type="button"
                                    onclick="viewDocument('<?= $doc_type ?>')" 
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
.documents-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.section-header {
    margin-bottom: 2rem;
}

.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert-info {
    background-color: #cce5ff;
    border: 1px solid #b8daff;
    color: #004085;
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
}

.doc-info h4 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.description {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.upload-info {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 1rem;
}

.doc-actions {
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn i {
    font-size: 0.9rem;
}
</style>

<script>
function triggerFileInput(docType) {
    document.getElementById(`file_${docType}`).click();
}

$(document).ready(function() {
    // Handle file input change
    $('.file-input').change(function() {
        const file = this.files[0];
        const docType = $(this).closest('form').data('doc-type');
        
        if (!file) return;

        // Validate file type
        if (file.type !== 'application/pdf') {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Type',
                text: 'Please upload only PDF files.'
            });
            this.value = '';
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Maximum file size is 5MB.'
            });
            this.value = '';
            return;
        }

        // Upload file
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
    });
});

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