$(document).ready(function() {
    // Initialize both tables
    const tableConfig = {
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    };
    
    $('#pendingBookingsTable').DataTable(tableConfig);
    $('#completedBookingsTable').DataTable(tableConfig);

    // Event handlers using delegation
    $('#pendingBookingsTable, #completedBookingsTable').on('click', '.view-booking', function() {
        const bookingId = $(this).data('id');
        viewBookingDetails(bookingId);
    });

    // Approve/Reject only for pending table
    $('#pendingBookingsTable').on('click', '.approve-booking', function() {
        const bookingId = $(this).data('id');
        approveBooking(bookingId);
    });

    $('#pendingBookingsTable').on('click', '.reject-booking', function() {
        const bookingId = $(this).data('id');
        rejectBooking(bookingId);
    });

    // Delete for both tables
    $('#pendingBookingsTable, #completedBookingsTable').on('click', '.delete-booking', function() {
        const bookingId = $(this).data('id');
        deleteBooking(bookingId);
    });
});

function getStatusBadgeClass(status) {
    switch (status.toLowerCase()) {
        case 'pending':
            return 'warning';
        case 'approved':
            return 'success';
        case 'rejected':
            return 'danger';
        case 'cancelled':
            return 'secondary';
        default:
            return 'primary';
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function getProgressBarClass(percentage) {
    if (percentage >= 100) return 'success';
    if (percentage >= 50) return 'warning';
    return 'danger';
}

function viewBookingDetails(bookingId) {
    $.ajax({
        url: '../api/get-booking-details.php',
        method: 'GET',
        data: { booking_id: bookingId },
        dataType: 'json',
        success: function(response) {
            if (!response.success || !response.data) {
                Swal.fire('Error', response.error || 'Failed to load booking details', 'error');
                return;
            }

            const data = response.data;
            const detailsHtml = `
                <div class="booking-details p-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Couple's Information</h5>
                            
                            <!-- Bride's Details -->
                            <div class="mb-4">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-female me-2"></i>Bride's Details
                                </h6>
                                <div class="ms-4">
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Complete Name:</strong></div>
                                        <div class="col-8">${data.bride_name}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Date of Birth:</strong></div>
                                        <div class="col-8">${formatDate(data.bride_dob)}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Birthplace:</strong></div>
                                        <div class="col-8">${data.bride_birthplace}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Mother's Name:</strong></div>
                                        <div class="col-8">${data.bride_mother}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Father's Name:</strong></div>
                                        <div class="col-8">${data.bride_father}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>PRE-NUPTIAL Interview:</strong></div>
                                        <div class="col-8">
                                            <span class="badge bg-${data.bride_prenup === 'yes' ? 'secondary' : 'light border'}">${data.bride_prenup.toUpperCase()}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"><strong>PRE-CANA Seminar:</strong></div>
                                        <div class="col-8">
                                            <span class="badge bg-${data.bride_precana === 'yes' ? 'secondary' : 'light border'}">${data.bride_precana.toUpperCase()}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Groom's Details -->
                            <div>
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-male me-2"></i>Groom's Details
                                </h6>
                                <div class="ms-4">
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Complete Name:</strong></div>
                                        <div class="col-8">${data.groom_name}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Date of Birth:</strong></div>
                                        <div class="col-8">${formatDate(data.groom_dob)}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Birthplace:</strong></div>
                                        <div class="col-8">${data.groom_birthplace}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Mother's Name:</strong></div>
                                        <div class="col-8">${data.groom_mother}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>Father's Name:</strong></div>
                                        <div class="col-8">${data.groom_father}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-4"><strong>PRE-NUPTIAL Interview:</strong></div>
                                        <div class="col-8">
                                            <span class="badge bg-${data.groom_prenup === 'yes' ? 'secondary' : 'light border'}">${data.groom_prenup.toUpperCase()}</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"><strong>PRE-CANA Seminar:</strong></div>
                                        <div class="col-8">
                                            <span class="badge bg-${data.groom_precana === 'yes' ? 'secondary' : 'light border'}">${data.groom_precana.toUpperCase()}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wedding Schedule & Contact Information -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Wedding Schedule & Contact Information
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row mb-2">
                                        <div class="col-5"><strong>Wedding Date:</strong></div>
                                        <div class="col-7 text-primary">${formatDate(data.wedding_date)}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5"><strong>Wedding Time:</strong></div>
                                        <div class="col-7">${data.preferred_time}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row mb-2">
                                        <div class="col-5"><strong>Contact Number:</strong></div>
                                        <div class="col-7">${data.contact_number}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-5"><strong>Email Address:</strong></div>
                                        <div class="col-7">${data.client_email}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;

            Swal.fire({
                title: 'Wedding Booking Confirmation',
                html: detailsHtml,
                width: '800px',
                customClass: {
                    container: 'booking-details-modal',
                    title: 'text-primary'
                }
            });
        },
        error: function(xhr) {
            console.error('Request failed:', xhr.responseText);
            Swal.fire('Error', 'Failed to load booking details', 'error');
        }
    });
}

function approveBooking(bookingId) {
    Swal.fire({
        title: 'Approve Booking',
        text: 'Are you sure you want to approve this booking?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, approve it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/update_booking_status.php',
                method: 'POST',
                data: {
                    booking_id: bookingId,
                    status: 'approved'
                },
                success: function(response) {
                    try {
                        response = typeof response === 'string' ? JSON.parse(response) : response;
                        if (response.success) {
                            Swal.fire('Approved!', 'Booking has been approved.', 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error!', response.error || 'Failed to approve booking.', 'error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire('Error!', 'Invalid server response', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire('Error!', 'Failed to approve booking.', 'error');
                }
            });
        }
    });
}

function rejectBooking(bookingId) {
    Swal.fire({
        title: 'Reject Booking',
        text: 'Please provide a reason for rejection:',
        input: 'textarea',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Reject',
        inputValidator: (value) => {
            if (!value) {
                return 'You need to provide a reason!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/update_booking_status.php',
                method: 'POST',
                data: {
                    booking_id: bookingId,
                    status: 'rejected',
                    reason: result.value
                },
                success: function(response) {
                    try {
                        response = typeof response === 'string' ? JSON.parse(response) : response;
                        if (response.success) {
                            Swal.fire('Rejected!', 'Booking has been rejected.', 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error!', response.error || 'Failed to reject booking.', 'error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire('Error!', 'Invalid server response', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire('Error!', 'Failed to reject booking.', 'error');
                }
            });
        }
    });
}

function deleteBooking(bookingId) {
    Swal.fire({
        title: 'Delete Booking',
        text: 'Are you sure you want to delete this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'ajax/delete_booking.php',
                method: 'POST',
                data: {
                    booking_id: bookingId
                },
                success: function(response) {
                    try {
                        response = typeof response === 'string' ? JSON.parse(response) : response;
                        if (response.success) {
                            Swal.fire('Deleted!', 'Booking has been deleted.', 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('Error!', response.error || 'Failed to delete booking.', 'error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire('Error!', 'Invalid server response', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX Error:', xhr.responseText);
                    Swal.fire('Error!', 'Failed to delete booking.', 'error');
                }
            });
        }
    });
}