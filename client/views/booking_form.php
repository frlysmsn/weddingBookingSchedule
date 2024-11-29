<?php
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$db = Database::getInstance()->getConnection();

// Check document approval status
$stmt = $db->prepare("
    SELECT 
        COUNT(*) as total_docs,
        SUM(CASE WHEN d.status = 'approved' THEN 1 ELSE 0 END) as approved_docs
    FROM documents d
    JOIN bookings b ON d.booking_id = b.id
    WHERE b.user_id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$doc_status = $stmt->fetch(PDO::FETCH_ASSOC);

$all_docs_approved = ($doc_status['total_docs'] > 0 && 
                     $doc_status['approved_docs'] == $doc_status['total_docs']);

// Progress tracker value (2 for registered users with pending docs, 3 for approved docs)
$progress = 1;
if ($doc_status['total_docs'] > 0) {
    $progress = 2;
    if ($all_docs_approved) {
        $progress = 3;
    }
}
?>

<!-- Progress Tracker -->
<div class="booking-progress mb-5">
    <div class="progress" style="height: 3px;">
        <div class="progress-bar" role="progressbar" 
             style="width: <?= ($progress * 25) ?>%;" 
             aria-valuenow="<?= ($progress * 25) ?>" 
             aria-valuemin="0" 
             aria-valuemax="100">
        </div>
    </div>
    <div class="step-indicators d-flex justify-content-between">
        <div class="step <?= $progress >= 1 ? 'active' : '' ?>">
            <div class="step-circle">1</div>
            <div class="step-text">Account Registration</div>
        </div>
        <div class="step <?= $progress >= 2 ? 'active' : '' ?>">
            <div class="step-circle">2</div>
            <div class="step-text">Requirements</div>
        </div>
        <div class="step <?= $progress >= 3 ? 'active' : '' ?>">
            <div class="step-circle">3</div>
            <div class="step-text">Wedding Details</div>
        </div>
        <div class="step <?= $progress >= 4 ? 'active' : '' ?>">
            <div class="step-circle">4</div>
            <div class="step-text">Confirmation</div>
        </div>
    </div>
</div>

<?php if (!$all_docs_approved): ?>
    <div class="alert alert-warning">
        <h5><i class="fas fa-exclamation-triangle"></i> Document Requirements</h5>
        <p>Please complete the document requirements before proceeding with the wedding details. 
           Current progress: <?= $doc_status['approved_docs'] ?>/<?= $doc_status['total_docs'] ?> documents approved.</p>
    </div>
<?php else: ?>
    <div class="booking-container">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4">Wedding Details</h3>
                
                <form id="bookingForm" class="needs-validation" novalidate>
                    <!-- Bride Information -->
                    <div class="form-section mb-4">
                        <h4 class="text-primary mb-3">Bride's Information</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="bride_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth *</label>
                                <input type="date" name="bride_dob" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Birthplace *</label>
                                <input type="text" name="bride_birthplace" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mother's Maiden Name *</label>
                                <input type="text" name="bride_mother" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Name *</label>
                                <input type="text" name="bride_father" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" name="bride_interview" required>
                                <label class="form-check-label">Already Interviewed for PRE-NUPTIAL *</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="bride_seminar" required>
                                <label class="form-check-label">Has Seminar for PRE-CANA *</label>
                            </div>
                        </div>
                    </div>

                    <!-- Groom Information -->
                    <div class="form-section mb-4">
                        <h4 class="text-primary mb-3">Groom's Information</h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="groom_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date of Birth *</label>
                                <input type="date" name="groom_dob" class="form-control" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Birthplace *</label>
                                <input type="text" name="groom_birthplace" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mother's Maiden Name *</label>
                                <input type="text" name="groom_mother" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father's Name *</label>
                                <input type="text" name="groom_father" class="form-control" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" name="groom_interview" required>
                                <label class="form-check-label">Already Interviewed for PRE-NUPTIAL *</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="groom_seminar" required>
                                <label class="form-check-label">Has Seminar for PRE-CANA *</label>
                            </div>
                        </div>
                    </div>

                    <!-- Wedding Date Selection -->
                    <div class="form-section mb-4">
                        <h4 class="text-primary mb-3">Select Wedding Date *</h4>
                        <div id="weddingCalendar"></div>
                        <input type="hidden" name="wedding_date" id="selected_date" required>
                        <div id="selectedDateDisplay" class="mt-3 alert alert-info d-none">
                            <i class="fas fa-calendar-check"></i> 
                            <span id="selectedDateText"></span>
                        </div>
                        <div id="dateValidationMessage" class="mt-2 text-danger small d-none">
                            <i class="fas fa-exclamation-circle"></i> Please select a wedding date
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Proceed to Confirmation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Make sure these are in your header -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php
    $stmt = $db->prepare("
        SELECT wedding_date 
        FROM bookings 
        WHERE wedding_date IS NOT NULL 
        AND status != 'cancelled'
    ");
    $stmt->execute();
    $reserved_dates = $stmt->fetchAll(PDO::FETCH_COLUMN);
    ?>
    
    const reservedDates = <?= json_encode($reserved_dates) ?>;
    const calendarEl = document.getElementById('weddingCalendar');
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        selectMirror: true,
        weekends: true,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth'
        },
        dateClick: function(info) {
            const clickedDate = info.dateStr;
            const selectedDisplay = document.getElementById('selectedDateDisplay');
            const validationMessage = document.getElementById('dateValidationMessage');
            
            // Check if date is in the past
            if (new Date(clickedDate) < new Date()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Date',
                    text: 'Please select a future date.',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            // Check if date is reserved
            if (reservedDates.includes(clickedDate)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Date Unavailable',
                    text: 'This date is already reserved. Please select another date.',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            // Update selection
            document.getElementById('selected_date').value = clickedDate;
            document.getElementById('selectedDateText').innerHTML = 
                new Date(clickedDate).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

            selectedDisplay.classList.remove('d-none');
            validationMessage.classList.add('d-none');

            // Clear previous selections and highlight new selection
            calendar.unselect();
            calendar.addEvent({
                start: clickedDate,
                end: clickedDate,
                display: 'background',
                backgroundColor: '#cfe2ff'
            });
        },
        events: reservedDates.map(date => ({
            start: date,
            display: 'background',
            backgroundColor: '#ffebee'
        }))
    });

    calendar.render();

    // Form validation
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        const selectedDate = document.getElementById('selected_date').value;
        const validationMessage = document.getElementById('dateValidationMessage');
        
        if (!selectedDate) {
            e.preventDefault();
            validationMessage.classList.remove('d-none');
            document.getElementById('weddingCalendar').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }
    });
});
</script>

<style>
#weddingCalendar {
    margin: 0 auto;
    max-width: 800px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.fc {
    font-family: inherit;
}

.fc .fc-toolbar-title {
    font-size: 1.25em;
    color: #4e73df;
}

.fc .fc-button-primary {
    background-color: #4e73df;
    border-color: #4e73df;
}

.fc .fc-button-primary:hover {
    background-color: #2e59d9;
    border-color: #2e59d9;
}

.fc-day-today {
    background-color: #e8f4ff !important;
}

.fc-day-past {
    background-color: #f8f9fa;
}

.fc td {
    border: 1px solid #dee2e6;
}

/* Weekend days */
.fc-day-sat, .fc-day-sun {
    background-color: #f8f9fa;
}

/* Available dates */
.fc-day-future:not(.fc-day-other) {
    background-color: #e8f5e9;
    cursor: pointer;
}

/* Hover effect on available dates */
.fc-day-future:not(.fc-day-other):hover {
    background-color: #c8e6c9;
}

/* Reserved dates */
.fc-daygrid-day.fc-day-future.reserved {
    background-color: #ffebee !important;
    cursor: not-allowed;
}

/* Selected date */
.fc-daygrid-day.selected {
    background-color: #cfe2ff !important;
}

/* Other month dates */
.fc-day-other {
    background-color: #f5f5f5;
}

#selectedDateDisplay {
    background-color: #cfe2ff;
    border-color: #b6d4fe;
    color: #084298;
}

.fc .fc-daygrid-day-number {
    font-weight: 500;
    color: #495057;
}

/* Today's date */
.fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
    background-color: #4e73df;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 4px;
}
</style> 