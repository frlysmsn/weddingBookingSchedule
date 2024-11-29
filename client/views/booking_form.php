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
       <b><p style="color: red;"> Note: Wait for the staff/admin to approve in order to proceed to the next step.</p></b>
           Current progress: <b> <?= $doc_status['approved_docs'] ?>/<?= $doc_status['total_docs'] ?></b> documents approved.</p>
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
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-danger me-2">&nbsp;</span>
                            <small>Booked Date</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-success me-2">&nbsp;</span>
                            <small>Available Date</small>
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
                                    <input type="email" class="form-control" name="email" required>
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
        // Initialize FullCalendar
        const calendar = new FullCalendar.Calendar(document.getElementById('weddingCalendar'), {
            initialView: 'dayGridMonth',
            selectable: true,
            selectConstraint: {
                start: new Date(), // Can't select dates before today
            },
            events: 'ajax/get_booked_dates.php', // Endpoint to get booked dates
            select: function(info) {
                const selectedDate = info.startStr;
                const dateInput = $('#wedding_date');
                dateInput.val(selectedDate);
                
                // Highlight the selected date
                $('.fc-day').removeClass('selected');
                $(info.dayEl).addClass('selected');
            },
            eventDidMount: function(info) {
                // Add tooltip showing it's booked
                if(info.event.title === 'Booked') {
                    $(info.el).tooltip({
                        title: 'This date is already booked',
                        placement: 'top'
                    });
                }
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
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Booking confirmed successfully',
                            confirmButtonColor: '#4e73df'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?page=booking_status';
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
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

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