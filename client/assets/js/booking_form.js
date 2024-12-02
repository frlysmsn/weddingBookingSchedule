document.getElementById('weddingBookingForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Get form data
    const formData = new FormData(this);
    
    // Submit the form
    fetch('ajax/confirm_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonText: 'View Bookings'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php?page=bookings';
                }
            });
        } else {
            throw new Error(data.message);
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message
        });
    });
}); 