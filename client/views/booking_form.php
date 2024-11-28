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

// Get available time slots
$timeSlots = [
    '08:00' => '8:00 AM',
    '10:00' => '10:00 AM',
    '14:00' => '2:00 PM',
    '16:00' => '4:00 PM'
];

// Check if date is selected
$selected_date = isset($_GET['date']) ? $_GET['date'] : '';

// If date is selected, check available slots
if ($selected_date) {
    $stmt = $db->prepare("
        SELECT preferred_time 
        FROM bookings 
        WHERE wedding_date = ? 
        AND status != 'cancelled'
    ");
    $stmt->execute([$selected_date]);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<div class="booking-container">
    <!-- Progress Tracker -->
    <div class="progress-tracker">
        <div class="step <?= $progress == 0 ? 'active' : ($progress > 0 ? 'completed' : '') ?>">
            <div class="step-icon">
                <i class="fas fa-file-upload"></i>
            </div>
            <div class="step-label">Upload Documents</div>
            <div class="step-progress"><?= $uploaded_count ?>/<?= $total_docs ?></div>
        </div>
        <div class="step <?= $progress == 100 ? 'active' : '' ?>">
            <div class="step-icon">
                <i class="fas fa-calendar-plus"></i>
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