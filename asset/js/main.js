// Calendar initialization
document.addEventListener('DOMContentLoaded', function() {
    if(document.getElementById('calendar')) {
        const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
            initialView: 'dayGridMonth',
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            select: function(info) {
                handleDateSelection(info);
            },
            events: 'api/get-booked-dates.php'
        });
        calendar.render();
    }
});

// Form validation
$(document).ready(function() {
    if($('#bookingForm').length) {
        $('#bookingForm').parsley();
        
        // Initialize Dropzone
        Dropzone.autoDiscover = false;
        new Dropzone("#documentUpload", {
            url: "api/upload-documents.php",
            maxFilesize: 5, // MB
            acceptedFiles: ".pdf,.doc,.docx,.jpg,.jpeg,.png",
            addRemoveLinks: true,
            success: function(file, response) {
                console.log(response);
            }
        });
    }
});

// Handle date selection
function handleDateSelection(info) {
    Swal.fire({
        title: 'Confirm Booking',
        text: `Would you like to book for ${info.startStr}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, book it!',
        cancelButtonText: 'No, cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('api/book-date.php', {
                date: info.startStr
            })
            .done(function(response) {
                Swal.fire('Booked!', 'Your date has been booked.', 'success');
            })
            .fail(function() {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            });
        }
    });
}

function updateBookingStatus(bookingId, status) {
    $.post('api/update-booking-status.php', {
        booking_id: bookingId,
        status: status
    })
    .done(function(response) {
        Swal.fire('Success!', 'Booking status updated.', 'success').then(() => {
            location.reload();
        });
    })
    .fail(function() {
        Swal.fire('Error!', 'Failed to update booking status.', 'error');
    });
}
