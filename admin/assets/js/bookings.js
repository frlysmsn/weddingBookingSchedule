$(document).ready(function() {
    // Initialize DataTable
    $('#bookingsTable').DataTable({
        order: [[2, 'desc']], // Sort by wedding date
        responsive: true
    });

    // View Wedding Details
    $('.view-details').click(function() {
        const bookingId = $(this).data('id');
        
        $.ajax({
            url: 'ajax/get_wedding_details.php',
            type: 'POST',
            data: { booking_id: bookingId },
            success: function(response) {
                $('#weddingDetailsModal .modal-body').html(response);
            }
        });
    });

    // Approve Booking
    $('.approve-booking').click(function() {
        const bookingId = $(this).data('id');
        
        Swal.fire({
            title: 'Approve Wedding Booking?',
            text: "This will confirm the wedding date and details.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/update_booking_status.php',
                    type: 'POST',
                    data: { 
                        booking_id: bookingId,
                        status: 'approved'
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Approved!',
                                'The wedding booking has been approved.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    }
                });
            }
        });
    });

    // Reject Booking
    $('.reject-booking').click(function() {
        const bookingId = $(this).data('id');
        
        Swal.fire({
            title: 'Reject Wedding Booking?',
            text: "Please provide a reason for rejection:",
            input: 'textarea',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax/update_booking_status.php',
                    type: 'POST',
                    data: { 
                        booking_id: bookingId,
                        status: 'rejected',
                        reason: result.value
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Rejected!',
                                'The wedding booking has been rejected.',
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }
                    }
                });
            }
        });
    });
}); 