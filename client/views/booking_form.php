<?php
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

$db = Database::getInstance()->getConnection();

// Fetch user email from the database
$stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$userEmail = $user['email'] ?? ''; // Use null coalescing to handle potential null values

// Check for pending bookings first
$stmt = $db->prepare("SELECT status FROM bookings WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$lastBooking = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user has a pending booking
$hasPendingBooking = $lastBooking && $lastBooking['status'] === 'pending';

// Only check documents if no pending booking
if (!$hasPendingBooking) {
    // Check document approval status
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_docs,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_docs
        FROM documents 
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $doc_status = $stmt->fetch(PDO::FETCH_ASSOC);

    $all_docs_approved = ($doc_status['total_docs'] > 0 && 
                         $doc_status['approved_docs'] == $doc_status['total_docs']);

    // Progress tracker value
    $progress = 1;
    if ($doc_status['total_docs'] > 0) {
        $progress = 2;
        if ($all_docs_approved) {
            $progress = 3;
        }
    }
} else {
    // Set progress to 4 if there's a pending booking
    $progress = 4;
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

<?php if ($hasPendingBooking): ?>
    <div class="alert alert-info">
        <h5><i class="fas fa-clock"></i> Booking In Progress</h5>
        <p>You currently have a pending wedding booking. Please wait for admin approval before making another booking.</p>
        <a href="index.php?page=bookings" class="btn btn-primary">
            <i class="fas fa-eye"></i> View My Booking
        </a>
    </div>
<?php elseif (!$all_docs_approved): ?>
    <div class="alert alert-warning">
        <h5><i class="fas fa-exclamation-triangle"></i> Document Requirements</h5>
        <p>Please complete the document requirements before proceeding with the wedding details. 
        <b><p style="color: red;"> Note: Wait for the staff/admin to approve in order to proceed to the next step.</p></b>
        Current progress: <b><?= $doc_status['approved_docs'] ?>/<?= $doc_status['total_docs'] ?></b> documents approved.</p>
        <a href="index.php?page=documents" class="btn btn-primary">
            <i class="fas fa-file"></i> Manage Documents
        </a>
    </div>
<?php else: ?>
    <div class="row">
        <!-- Calendar Column -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Wedding Calendar</h5>
                </div>
                <div class="card-body">
                    <div id="weddingCalendar"></div>
                    <div class="calendar-legend mt-3">
                        <div class="legend-item">
                            <div class="legend-color available"></div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color fully-booked"></div>
                            <span>Fully Booked</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color partially-booked"></div>
                            <span>Partially Booked</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color sunday"></div>
                            <span>Sunday</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Form Column -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Wedding Booking Form</h5>
                </div>
                <div class="card-body">
                    <form id="weddingBookingForm">
                        <!-- Bride's Information -->
                        <h6 class="mb-3">Bride's Information</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" name="bride_fname" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" name="bride_mname">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" class="form-control" name="bride_lname" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth *</label>
                                    <input type="date" class="form-control" name="bride_dob" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Birthplace *</label>
                                    <input type="text" class="form-control" name="bride_birthplace" required>
                                </div>
                            </div>
                        </div>

                        <!-- Add these missing fields for Bride -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mother's Maiden Name *</label>
                                    <input type="text" class="form-control" name="bride_mother" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Father's Name *</label>
                                    <input type="text" class="form-control" name="bride_father" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PRE-NUPTIAL Interview *</label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bride_prenup" value="yes" required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bride_prenup" value="no" required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PRE-CANA Seminar *</label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bride_precana" value="yes" required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="bride_precana" value="no" required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Groom's Information -->
                        <h6 class="mb-3">Groom's Information</h6>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name *</label>
                                    <input type="text" class="form-control" name="groom_fname" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" class="form-control" name="groom_mname">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name *</label>
                                    <input type="text" class="form-control" name="groom_lname" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth *</label>
                                    <input type="date" class="form-control" name="groom_dob" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Birthplace *</label>
                                    <input type="text" class="form-control" name="groom_birthplace" required>
                                </div>
                            </div>
                        </div>

                        <!-- Add these missing fields for Groom -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mother's Maiden Name *</label>
                                    <input type="text" class="form-control" name="groom_mother" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Father's Name *</label>
                                    <input type="text" class="form-control" name="groom_father" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PRE-NUPTIAL Interview *</label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="groom_prenup" value="yes" required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="groom_prenup" value="no" required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>PRE-CANA Seminar *</label>
                                    <div class="mt-2">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="groom_precana" value="yes" required>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="groom_precana" value="no" required>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keep your existing Wedding Details section -->
                        <hr class="my-4">
                        <h6 class="mb-3">Wedding Details</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Preferred Wedding Date</label>
                                    <i><p>Note: Check the Wedding Calendar for available dates.</p></i>
                                    <input type="date" class="form-control" name="wedding_date" id="wedding_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Preferred Time</label>
                                    <select class="form-control" name="preferred_time" required>
                                        <option value="">Select Time Slot</option>
                                        <option value="08:00-09:00">8:00 AM - 9:00 AM</option>
                                        <option value="09:00-10:00">9:00 AM - 10:00 AM</option>
                                        <option value="13:00-14:00">1:00 PM - 2:00 PM</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="tel" class="form-control" name="contact_number" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userEmail) ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-success" id="proceedToConfirmation">
                                Proceed to Confirmation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Wedding Booking Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Couple's Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-user-couple me-2"></i>
                                Couple's Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Bride's Details -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-female me-2"></i>
                                        Bride's Details
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Complete Name:</th>
                                            <td><span id="confirm-bride-name"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth:</th>
                                            <td><span id="confirm-bride-dob"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Birthplace:</th>
                                            <td><span id="confirm-bride-birthplace"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Mother's Name:</th>
                                            <td><span id="confirm-bride-mother"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Father's Name:</th>
                                            <td><span id="confirm-bride-father"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <div class="bg-light p-2 rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="d-block"><strong>PRE-NUPTIAL Interview:</strong> 
                                                    <span class="badge bg-secondary" id="confirm-bride-prenup"></span>
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="d-block"><strong>PRE-CANA Seminar:</strong> 
                                                    <span class="badge bg-secondary" id="confirm-bride-precana"></span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Groom's Details -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2">
                                        <i class="fas fa-male me-2"></i>
                                        Groom's Details
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Complete Name:</th>
                                            <td><span id="confirm-groom-name"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth:</th>
                                            <td><span id="confirm-groom-dob"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Birthplace:</th>
                                            <td><span id="confirm-groom-birthplace"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Mother's Name:</th>
                                            <td><span id="confirm-groom-mother"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Father's Name:</th>
                                            <td><span id="confirm-groom-father"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-12">
                                    <div class="bg-light p-2 rounded">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="d-block"><strong>PRE-NUPTIAL Interview:</strong> 
                                                    <span class="badge bg-secondary" id="confirm-groom-prenup"></span>
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="d-block"><strong>PRE-CANA Seminar:</strong> 
                                                    <span class="badge bg-secondary" id="confirm-groom-precana"></span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wedding Details -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Wedding Schedule & Contact Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Wedding Date:</th>
                                            <td><span id="confirm-wedding-date" class="text-primary fw-bold"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Wedding Time:</th>
                                            <td><span id="confirm-wedding-time" class="text-primary fw-bold"></span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Contact Number:</th>
                                            <td><span id="confirm-contact"></span></td>
                                        </tr>
                                        <tr>
                                            <th>Email Address:</th>
                                            <td><span id="confirm-email"></span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="alert-heading">Please Review Your Details</h6>
                                <p class="mb-0">Carefully check all information before confirming. Once submitted, your booking will be reviewed by the admin for approval.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-edit me-2"></i>Edit Details
                    </button>
                    <button type="button" class="btn btn-success" id="confirmBookingBtn">
                        <i class="fas fa-check-circle me-2"></i>Confirm Booking
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        const calendarEl = document.getElementById('weddingCalendar');
        if (!calendarEl) return;

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            selectable: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            events: {
                url: 'ajax/get_booked_dates.php',
                method: 'GET',
                failure: function(error) {
                    console.error('Error details:', error);
                }
            },
            select: function(info) {
                const selectedDate = info.startStr;
                $('#wedding_date').val(selectedDate);
                
                // Clear previous selection
                $('.selected-date').removeClass('selected-date');
                $(info.dayEl).addClass('selected-date');
                
                // Fetch available times
                $.ajax({
                    url: 'ajax/get_available_times.php',
                    method: 'GET',
                    dataType: 'json',
                    data: { date: selectedDate },
                    success: function(response) {
                        console.log('Response:', response); // Debug log
                        
                        const timeSelect = $('select[name="preferred_time"]');
                        timeSelect.empty();
                        timeSelect.append('<option value="">Select Time Slot</option>');
                        
                        if (response.success) {
                            // Get the booked times array
                            const bookedTimes = response.booked_times || [];
                            console.log('Booked times:', bookedTimes); // Debug log
                            
                            // Define available time slots with database format
                            const timeSlots = {
                                '08:00:00': '8:00 AM - 9:00 AM',
                                '09:00:00': '9:00 AM - 10:00 AM',
                                '13:00:00': '1:00 PM - 2:00 PM'
                            };
                            
                            // Only add unbooked time slots to the dropdown
                            Object.entries(timeSlots).forEach(([value, label]) => {
                                if (!bookedTimes.includes(value)) {
                                    timeSelect.append(`<option value="${value}">${label}</option>`);
                                }
                            });
                        }
                        
                        timeSelect.prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', error); // Debug log
                        const timeSelect = $('select[name="preferred_time"]');
                        timeSelect.empty()
                            .append('<option value="">Error loading time slots</option>')
                            .prop('disabled', true);
                    }
                });
            }
        });
        
        calendar.render();

        // When "Proceed to Confirmation" is clicked
        $('#proceedToConfirmation').click(function(e) {
            e.preventDefault();
            
            // Check form validity
            if (!$('#weddingBookingForm')[0].checkValidity()) {
                $('#weddingBookingForm')[0].reportValidity();
                return;
            }

            // Update modal with form data
            updateConfirmationModal();
            $('#confirmationModal').modal('show');
        });

        // When "Confirm Booking" in modal is clicked
        $('#confirmBookingBtn').click(function(e) {
            e.preventDefault();
            
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            
            let formData = new FormData($('#weddingBookingForm')[0]);
            
            // Debug: Log form data
            console.log('Sending form data:', Object.fromEntries(formData));
            
            $.ajax({
                url: 'ajax/confirm_booking.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Success Response:', response);
                    
                    if (response && response.status === 'success') {
                        // Update progress tracker
                        $('.step').removeClass('active');
                        $('.step:last-child').addClass('active');
                        $('.progress-bar').css('width', '100%');
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Booking confirmed successfully',
                            confirmButtonColor: '#4e73df'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?page=bookings';
                            }
                        });
                    } else {
                        throw new Error(response.message || 'Invalid response format');
                    }
                },
                error: function(xhr, status, error) {
                    console.group('AJAX Error Details');
                    console.log('Status:', status);
                    console.log('Error:', error);
                    console.log('Response Text:', xhr.responseText);
                    console.log('URL:', this.url);
                    try {
                        console.log('Parsed Response:', JSON.parse(xhr.responseText));
                    } catch (e) {
                        console.log('Raw Response (not JSON):', xhr.responseText);
                    }
                    console.groupEnd();
                    
                    let errorMessage = 'Failed to connect to the server. Please try again.';
                    
                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                            if (response.debug) {
                                console.error('Debug Info:', response.debug);
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                            errorMessage = xhr.responseText;
                        }
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'System Error',
                        text: errorMessage,
                        confirmButtonColor: '#4e73df'
                    });
                },
                complete: function() {
                    $('#confirmBookingBtn').prop('disabled', false).html('Confirm Booking');
                }
            });
        });

        // Add form validation
        $('#weddingBookingForm').on('submit', function(e) {
            e.preventDefault();
            // Validate required fields
            let isValid = true;
            $(this).find('[required]').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill in all required fields.',
                    confirmButtonColor: '#4e73df'
                });
                return false;
            }
            
            return true;
        });

        function updateConfirmationModal() {
            // Combine names
            const brideName = [
                $('input[name="bride_fname"]').val(),
                $('input[name="bride_mname"]').val(),
                $('input[name="bride_lname"]').val()
            ].filter(Boolean).join(' ');

            const groomName = [
                $('input[name="groom_fname"]').val(),
                $('input[name="groom_mname"]').val(),
                $('input[name="groom_lname"]').val()
            ].filter(Boolean).join(' ');

            // Update modal fields
            $('#confirm-wedding-date').text(formatDate($('input[name="wedding_date"]').val()));
            $('#confirm-wedding-time').text($('select[name="preferred_time"]').val());
            $('#confirm-contact').text($('input[name="contact_number"]').val());
            $('#confirm-email').text($('input[name="email"]').val());

            // Bride details
            $('#confirm-bride-name').text(brideName);
            $('#confirm-bride-dob').text(formatDate($('input[name="bride_dob"]').val()));
            $('#confirm-bride-birthplace').text($('input[name="bride_birthplace"]').val());
            $('#confirm-bride-mother').text($('input[name="bride_mother"]').val());
            $('#confirm-bride-father').text($('input[name="bride_father"]').val());
            $('#confirm-bride-prenup').text($('input[name="bride_prenup"]:checked').val()?.toUpperCase() || 'NO');
            $('#confirm-bride-precana').text($('input[name="bride_precana"]:checked').val()?.toUpperCase() || 'NO');

            // Groom details
            $('#confirm-groom-name').text(groomName);
            $('#confirm-groom-dob').text(formatDate($('input[name="groom_dob"]').val()));
            $('#confirm-groom-birthplace').text($('input[name="groom_birthplace"]').val());
            $('#confirm-groom-mother').text($('input[name="groom_mother"]').val());
            $('#confirm-groom-father').text($('input[name="groom_father"]').val());
            $('#confirm-groom-prenup').text($('input[name="groom_prenup"]:checked').val()?.toUpperCase() || 'NO');
            $('#confirm-groom-precana').text($('input[name="groom_precana"]:checked').val()?.toUpperCase() || 'NO');
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            return new Date(dateString).toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
    });
    </script>
<?php endif; ?>

<!-- Make sure these are in your header -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<style>
/* Calendar container */
#weddingCalendar {
    margin: 0 auto;
    max-width: 100%;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    font-family: 'Arial', sans-serif;
}

/* Header toolbar styling */
.fc-header-toolbar {
    margin-bottom: 1em !important;
    padding: 0.8em;
    background: #f8f9fa;
    border-radius: 4px;
}

.fc-toolbar-title {
    font-size: 1.2rem !important;
    font-weight: 600;
}

/* Button styling */
.fc-button {
    padding: 8px 12px !important;
    font-size: 0.9rem !important;
    font-weight: 500 !important;
    text-transform: capitalize !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05) !important;
}

.fc-today-button {
    background-color: #2196F3 !important;
    border-color: #2196F3 !important;
}

.fc-prev-button, .fc-next-button {
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    color: #495057 !important;
}

/* Day header styling */
.fc-col-header-cell {
    background: #f8f9fa;
    padding: 12px 0 !important;
    font-size: 0.9rem;
    font-weight: 600;
}

/* Day cell styling */
.fc-daygrid-day {
    padding: 4px !important;
}

.fc-daygrid-day-number {
    font-size: 0.95rem !important;
    padding: 8px !important;
    font-weight: 500;
}

/* Today's date styling */
.fc-day-today .fc-daygrid-day-number {
    background: #2196F3;
    color: white;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 4px;
}

/* Event styling */
.fc-event {
    border-radius: 4px;
    font-size: 0.85rem;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .fc-toolbar-title {
        font-size: 1rem !important;
    }
    
    .fc-button {
        padding: 6px 10px !important;
        font-size: 0.8rem !important;
    }
    
    .fc-daygrid-day-number {
        font-size: 0.85rem !important;
    }
}

/* Legend styles */
.calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1rem;
    padding: 0.8rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.legend-item {
    display: flex;
    align-items: center;
    margin-right: 1rem;
    margin-bottom: 0.5rem;
}

.legend-color {
    width: 20px;
    height: 20px;
    margin-right: 0.5rem;
    border-radius: 4px;
}

/* Calendar date colors - matching with legend */
.legend-color.available { background-color: #ffffff; border: 1px solid #ddd; }
.legend-color.fully-booked { background-color: #ffcdd2; }
.legend-color.partially-booked { background-color: #fff3cd; }
.legend-color.sunday { background-color: #e3f2fd; }

/* Calendar date states */
.fc-day-future:not(.fc-day-sun):not(.booked-date) { background-color: #ffffff !important; }
.fc-day-today { background-color: #e8f5e9 !important; }
.fully-booked-date { background-color: #ffcdd2 !important; }
.partially-booked-date { background-color: #fff3cd !important; }
.fc-day-sun { background-color: #e3f2fd !important; }
.selected-date { border: 2px solid #1976d2 !important; }

/* Clean up calendar header */
.fc-header-toolbar {
    margin-bottom: 1em !important;
    padding: 0.5em;
    background: #f8f9fa;
    border-radius: 4px;
}

.fc-day-header {
    background: #f8f9fa;
    padding: 8px 0 !important;
}

.fc-day-number {
    padding: 8px !important;
}

.time-slot-chip {
    padding: 8px 16px;
    border-radius: 20px;
    background: rgb(33, 150, 243);
    color: white;
    font-size: 0.9rem;
    display: inline-block;
    margin: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.time-slot-chip:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

select option:disabled {
    background-color: #ffebee !important;
    color: #d32f2f !important;
    font-style: italic;
}

select option {
    padding: 8px;
}
</style>

<script src="assets/js/booking_form.js"></script> 