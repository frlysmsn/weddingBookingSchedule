// Confirmation button click handler
$('#confirmBookingBtn').click(function() {
    const formData = new FormData($('#weddingBookingForm')[0]);
    
    // Show loading state
    $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
    
    $.ajax({
        url: 'ajax/confirm_booking.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            const data = JSON.parse(response);
            
            $('#confirmationModal').modal('hide');
            
            if(data.success) {
                Swal.fire({
                    title: 'Booking Submitted!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-calendar-check fa-3x mb-3 text-success"></i>
                            <p class="mb-3">Your wedding booking has been submitted successfully!</p>
                            <p class="mb-3">Please wait for admin confirmation.</p>
                            <p class="mb-3">You will receive an email once your request is approved.</p>
                        </div>
                    `,
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error!', data.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to process your request.', 'error');
        },
        complete: function() {
            $('#confirmBookingBtn').prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i>Confirm Booking');
        }
    });
}); 