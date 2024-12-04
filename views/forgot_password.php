<div class="forgot-password-container">
    <div class="forgot-password-box">
        <h2>Reset Password</h2>
        <p>Enter your email address to receive password reset instructions.</p>
        
        <form id="forgotPasswordForm" class="forgot-password-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       required 
                       placeholder="Enter your email">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Reset Password</button>
                <a href="index.php" class="btn btn-link">Back to Login</a>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#forgotPasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'ajax/forgot_password.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        allowOutsideClick: false
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            }
        });
    });
});
</script>

<style>
.forgot-password-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.forgot-password-box {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

.forgot-password-form .form-group {
    margin-bottom: 1.5rem;
}

.btn-link {
    display: block;
    text-align: center;
    margin-top: 1rem;
}
</style> 