<?php
// Check if user has a temporary ID
if (!isset($_SESSION['temp_user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<div class="verification-container">
    <div class="verification-box">
        <h2>Email Verification</h2>
        <p>Please enter the verification code sent to your email address.</p>
        
        <form id="verificationForm" class="verification-form" method="POST">
            <div class="form-group">
                <label for="verification_code">Verification Code</label>
                <input type="text" 
                       id="verification_code" 
                       name="verification_code" 
                       class="form-control" 
                       required 
                       maxlength="6" 
                       pattern="[0-9]+"
                       placeholder="Enter 6-digit code">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Verify Email</button>
                <button type="button" id="resendCode" class="btn btn-secondary mt-2">Resend Code</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Verify email code
    $('#verificationForm').on('submit', function(e) {
        e.preventDefault();
        const code = $('#verification_code').val();
        
        $.ajax({
            url: 'ajax/verify_email.php',
            type: 'POST',
            data: { verification_code: code },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });

    // Resend verification code
    $('#resendCode').on('click', function() {
        $.ajax({
            url: 'ajax/resend_verification_code.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: response.success ? 'success' : 'error',
                    title: response.success ? 'Code Sent!' : 'Error!',
                    text: response.message
                });
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to resend verification code.'
                });
            }
        });
    });
});
</script>

<style>
.verification-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.verification-box {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

.verification-box h2 {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #333;
}

.verification-box p {
    text-align: center;
    margin-bottom: 2rem;
    color: #666;
}

.verification-form .form-group {
    margin-bottom: 1.5rem;
}

.verification-form label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
}

.verification-form input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    text-align: center;
    letter-spacing: 0.2em;
}

.verification-form button {
    width: 100%;
    padding: 0.75rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.verification-form button:hover {
    background-color: #0056b3;
}
</style> 