<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

$db = Database::getInstance()->getConnection();

// Get user's active booking
$stmt = $db->prepare("
    SELECT * FROM bookings 
    WHERE user_id = ? 
    AND status != 'cancelled' 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$active_booking = $stmt->fetch(PDO::FETCH_ASSOC);

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
    WHERE d.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$uploaded_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group documents by type
$docs_by_type = [];
foreach($uploaded_docs as $doc) {
    $docs_by_type[$doc['document_type']] = $doc;
}
?>

<div class="dashboard-container">
    <div class="welcome-section">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
        <?php if($active_booking): ?>
            <p>You have an active wedding booking for <?= date('F d, Y', strtotime($active_booking['wedding_date'])) ?></p>
        <?php else: ?>
            <p>Start your wedding journey by booking a date!</p>
            <a href="index.php?page=booking_form" class="btn btn-primary">
                <i class="fas fa-calendar-plus"></i> Book Wedding Date
            </a>
        <?php endif; ?>
    </div>

    <div class="dashboard-grid">
        <!-- Document Status Card -->
        <div class="dashboard-card documents-status">
            <div class="card-header">
                <h3><i class="fas fa-file-alt"></i> Required Documents</h3>
                <a href="index.php?page=documents" class="btn btn-sm btn-primary">
                    Manage Documents
                </a>
            </div>
            
            <div class="progress-container">
                <?php
                $uploaded_count = count($docs_by_type);
                $total_docs = count($required_docs);
                $progress = ($total_docs > 0) ? ($uploaded_count / $total_docs) * 100 : 0;
                ?>
                <div class="progress">
                    <div class="progress-bar" 
                         role="progressbar" 
                         style="width: <?= $progress ?>%"
                         aria-valuenow="<?= $progress ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= $uploaded_count ?>/<?= $total_docs ?>
                    </div>
                </div>
            </div>

            <div class="documents-grid">
                <?php foreach($required_docs as $doc_type => $doc_info): ?>
                    <div class="document-item <?= isset($docs_by_type[$doc_type]) ? 'uploaded' : 'missing' ?>">
                        <div class="doc-icon">
                            <i class="fas <?= $doc_info['icon'] ?>"></i>
                        </div>
                        <div class="doc-info">
                            <h4><?= $doc_info['name'] ?></h4>
                            <?php if(isset($docs_by_type[$doc_type])): ?>
                                <span class="status uploaded">
                                    <i class="fas fa-check-circle"></i> Uploaded
                                </span>
                                <small class="upload-date">
                                    <?= date('M d, Y', strtotime($docs_by_type[$doc_type]['created_at'])) ?>
                                </small>
                            <?php else: ?>
                                <span class="status missing">
                                    <i class="fas fa-exclamation-circle"></i> Required
                                </span>
                                <a href="index.php?page=documents" class="upload-link">
                                    Upload now
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if($active_booking): ?>
        <!-- Booking Status Card -->
        <div class="dashboard-card booking-status">
            <div class="card-header">
                <h3><i class="fas fa-calendar-check"></i> Wedding Booking</h3>
            </div>
            <div class="booking-details">
                <div class="detail-item">
                    <label>Wedding Date:</label>
                    <span><?= date('F d, Y', strtotime($active_booking['wedding_date'])) ?></span>
                </div>
                <div class="detail-item">
                    <label>Time:</label>
                    <span><?= date('h:i A', strtotime($active_booking['preferred_time'])) ?></span>
                </div>
                <div class="detail-item">
                    <label>Status:</label>
                    <span class="badge badge-<?= $active_booking['status'] ?>">
                        <?= ucfirst($active_booking['status']) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.dashboard-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.welcome-section {
    margin-bottom: 2rem;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1.5rem;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.progress-container {
    margin-bottom: 1.5rem;
}

.progress {
    height: 1rem;
    background-color: #e9ecef;
    border-radius: 0.5rem;
    overflow: hidden;
}

.progress-bar {
    background-color: #007bff;
    color: white;
    text-align: center;
    line-height: 1rem;
    font-size: 0.75rem;
}

.documents-grid {
    display: grid;
    gap: 1rem;
}

.document-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 4px;
    background: #f8f9fa;
}

.document-item.uploaded {
    background: #e8f5e9;
}

.document-item.missing {
    background: #fff3e0;
}

.doc-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: white;
    color: #007bff;
}

.doc-info {
    flex: 1;
}

.doc-info h4 {
    margin: 0;
    font-size: 1rem;
}

.status {
    font-size: 0.8rem;
    display: block;
}

.status.uploaded {
    color: #28a745;
}

.status.missing {
    color: #dc3545;
}

.upload-date {
    color: #666;
    font-size: 0.8rem;
}

.upload-link {
    color: #007bff;
    font-size: 0.8rem;
    text-decoration: none;
}

.booking-details {
    display: grid;
    gap: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
}

.badge-pending {
    background: #ffc107;
    color: #000;
}

.badge-approved {
    background: #28a745;
    color: white;
}

.badge-rejected {
    background: #dc3545;
    color: white;
}
</style>

<script>
function viewBookingDetails(bookingId) {
    $.get('../api/get-booking-details.php', {
        booking_id: bookingId
    })
    .done(function(response) {
        Swal.fire({
            title: 'Booking Details',
            html: response,
            width: '600px'
        });
    });
}
</script> 