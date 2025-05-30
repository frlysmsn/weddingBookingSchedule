<?php
if (!isset($_GET['token'])) {
    header('Location: index.php');
    exit; 
}
$token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .reset-password-container {
            max-width: 450px;
            margin: 80px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-password-container">
            <h2 class="text-center mb-4">Reset Password</h2>
            <form method="POST" id="resetPasswordForm" name="resetPasswordForm">
                <input type="hidden" id="token" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="new_password" 
                           name="new_password" 
                           required 
                           minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required>
                </div>
                
                <button type="submit" id="submitBtn" name="submitBtn" class="btn btn-primary w-100">Update Password</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();
                
                var newPassword = $('#new_password').val();
                var confirmPassword = $('#confirm_password').val();
                
                // Validate password length
                if (newPassword.length < 6) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Password must be at least 6 characters long!'
                    });
                    return;
                }
                
                // Validate password match
                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Passwords do not match!'
                    });
                    return;
                }
                
                // Disable submit button to prevent double submission
                $('#submitBtn').prop('disabled', true);
                
                $.ajax({
                    url: 'ajax/reset_password.php',
                    type: 'POST',
                    data: {
                        token: $('#token').val(),
                        new_password: newPassword,
                        confirm_password: confirmPassword
                    },
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
                            // Re-enable submit button on error
                            $('#submitBtn').prop('disabled', false);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Ajax error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred. Please try again.'
                        });
                        // Re-enable submit button on error
                        $('#submitBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
