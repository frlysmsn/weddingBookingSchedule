<?php
$auth = new Authentication();

// If already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) {
        header('Location: index.php?page=admin-dashboard');
    } else {
        header('Location: index.php?page=client-dashboard');
    }
    exit;
}
?>

<head>
    <!-- Your other head elements -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><head>
    <!-- Add jQuery before SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
    
</head>

<div class="landing-container">
    <main class="main-content">
        <!-- Parish Information Section -->
        <section class="parish-info">
            <div class="parish-image">
                <img src="assets/images/parish.jpg" alt="St. Rita Parish" class="parish-photo">
            </div>
            <div class="parish-details">
                <h1><?= SITE_NAME ?></h1>
                <p class="parish-description">
                    Welcome to St. Rita Mission Station, where we help couples celebrate the sacred bond of marriage. 
                    Our beautiful church provides the perfect setting for your special day.
                </p>
                <div class="parish-contact">
                    <p><i class="fas fa-map-marker-alt"></i> 123 Church Street, Your City</p>
                    <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                    <p><i class="fas fa-envelope"></i> info@stritamission.com</p>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services-section" id="services">
            <div class="services-header">
                <h2>Our Services</h2>
                <p class="section-subtitle">Everything you need for your perfect church wedding</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="service-content">
                        <h3>Easy Scheduling</h3>
                        <p>Book your preferred wedding date through our online calendar system.</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Real-time availability</li>
                            <li><i class="fas fa-check"></i> Flexible date selection</li>
                            <li><i class="fas fa-check"></i> Instant confirmation</li>
                        </ul>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div class="service-content">
                        <h3>Document Management</h3>
                        <p>Easily upload and manage your wedding requirements online.</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Secure document storage</li>
                            <li><i class="fas fa-check"></i> Digital submissions</li>
                            <li><i class="fas fa-check"></i> Progress tracking</li>
                        </ul>
                    </div>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="service-content">
                        <h3>Real-time Updates</h3>
                        <p>Get instant notifications about your booking status.</p>
                        <ul class="service-features">
                            <li><i class="fas fa-check"></i> Status notifications</li>
                            <li><i class="fas fa-check"></i> Email updates</li>
                            <li><i class="fas fa-check"></i> Reminder alerts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <section class="map-section">
    <h2>Our Location</h2>
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3916.862359825314!2d124.5329199940939!3d10.97376018918724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3307f5b0d4a05795%3A0x11cc979bc4392d1c!2sSt.%20Rita%20Mission%20Station!5e0!3m2!1sen!2sph!4v1732900178475!5m2!1sen!2sph" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>
    </main>

    <!-- Login Sidebar -->
    <aside class="auth-sidebar">
        <div class="auth-box">
            <div class="auth-tabs">
                <button class="auth-tab-btn active" data-tab="login">Login</button>
                <button class="auth-tab-btn" data-tab="register">Register</button>
            </div>

            <!-- Login Form -->
            <div class="auth-tab-content" id="login-tab">
                <form id="loginForm" class="auth-form" method="POST">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                          <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                    </div>
                    
                    <div class="mb-3">
                        <a href="#" id="forgotPasswordLink" class="forgot-password-link">Forgot Password?</a>
                    </div>
                    <div class="auth-links">
                    <p><small>For admin login, please visit the <a href="admin/login.php">admin portal</a></small></p>
                    </div>
                </form>
            </div>

            <!-- Register Form -->
            <div class="auth-tab-content hidden" id="register-tab">
                                    <form method="POST" class="auth-form" id="registerForm">
                                    <div class="form-group">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="reg-email">Email Address</label>
                        <input type="email" id="reg-email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="reg-password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="reg-password" name="password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="reg-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2" style="display: none;">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                            <small class="strength-text text-muted"></small>
                            <div class="requirements text-muted">
                                <small>Password must contain:</small>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-circle fa-xs req-length"></i> At least 8 characters</li>
                                    <li><i class="fas fa-circle fa-xs req-uppercase"></i> One uppercase letter</li>
                                    <li><i class="fas fa-circle fa-xs req-number"></i> One number</li>
                                    <li><i class="fas fa-circle fa-xs req-special"></i> One special character</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="confirm-password">Confirm Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </aside>
</div>

<!-- Add this modal HTML before the closing </div> of landing-container -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Reset Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <div class="form-group">
                        <label for="reset-email">Enter your email address</label>
                        <input type="email" class="form-control" id="reset-email" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Switch between login and register tabs
    $('.auth-tab-btn').click(function() {
        const tab = $(this).data('tab');
        $('.auth-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.auth-tab-content').addClass('hidden');
        $(`#${tab}-tab`).removeClass('hidden');
    });

    // Handle registration form submission
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        // Check if passwords match
        const password = $('#reg-password').val();
        const confirmPassword = $('#confirm-password').val();
        
        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'Passwords do not match!'
            });
            return;
        }

        $.ajax({
            url: 'ajax/register.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message
                    }).then((result) => {
                        if (result.isConfirmed && response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message || 'An error occurred during registration.'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Registration error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong during registration. Please try again.'
                });
            }
        });
    });

    // Handle login form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'ajax/login.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.verified) {
                        window.location.href = response.redirect;
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                    
                    if (response.message === 'Please verify your email first.') {
                        setTimeout(function() {
                            window.location.href = 'index.php?page=verify';
                        }, 2000);
                    }
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

    // Forgot Password Link Click
    $('#forgotPasswordLink').click(function(e) {
        e.preventDefault();
        $('#forgotPasswordModal').modal({
            backdrop: 'static',
            keyboard: false
        });
        $('#forgotPasswordModal').modal('show');
    });

    // Handle Forgot Password Form Submit
    $('#modalForgotPasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'ajax/forgot_password.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#forgotPasswordModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message
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
                    title: 'Error!',
                    text: 'An error occurred. Please try again.'
                });
            }
        });
    });

    // Close modal button handler
    $('.close').click(function() {
        $('#forgotPasswordModal').modal('hide');
    });
});
</script>

<!-- Add this CSS -->
<style>
.forgot-password-link {
    color: #007bff;
    text-decoration: none;
    font-size: 0.9rem;
}

.forgot-password-link:hover {
    text-decoration: underline;
}

.modal-backdrop {
    display: none !important;
}

.modal {
    background: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: block;
}

.modal-dialog {
    margin: 10% auto;
    width: 90%;
    max-width: 500px;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
}

.close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.password-strength .progress-bar {
    transition: width 0.3s ease;
}
.password-strength .progress-bar.weak { background-color: #dc3545; }
.password-strength .progress-bar.medium { background-color: #ffc107; }
.password-strength .progress-bar.strong { background-color: #28a745; }
.requirements li { font-size: 0.8rem; }
.requirements .fa-circle { font-size: 0.5rem; margin-right: 5px; color: #dc3545; }
.requirements .valid .fa-circle { color: #28a745; }
</style>

<!-- Add these script tags at the bottom of the page, just before closing </body> tag -->
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Remove any existing backdrops
    $('.modal-backdrop').remove();
    
    $('#forgotPasswordLink').on('click', function(e) {
        e.preventDefault();
        // Remove any existing backdrops before showing modal
        $('.modal-backdrop').remove();
        var forgotPasswordModal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'), {
            backdrop: false // Disable Bootstrap's default backdrop
        });
        forgotPasswordModal.show();
    });

    // Close modal handlers
    $('.btn-close, .close').on('click', function() {
        var modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
        if (modal) {
            modal.hide();
            // Remove backdrop after hiding modal
            $('.modal-backdrop').remove();
        }
    });

    // Also handle escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            var modal = bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal'));
            if (modal) {
                modal.hide();
                $('.modal-backdrop').remove();
            }
        }
    });
});
</script>

<!-- Email Verification Modal -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verificationModalLabel">Email Verification Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Your email address has not been verified. Please enter the verification code sent to your email.</p>
                <form id="verificationForm" class="mt-3">
                    <div class="mb-3">
                        <label for="verification_code" class="form-label">Verification Code</label>
                        <input type="text" class="form-control" id="verification_code" required maxlength="6" pattern="[0-9]+" placeholder="Enter 6-digit code">
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Verify Email</button>
                        <button type="button" id="resendCode" class="btn btn-secondary">Resend Code</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Add this to your existing JavaScript
$(document).ready(function() {
    // Handle login form submission
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/login.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.needsVerification) {
                    // Show verification modal
                    $('#verificationModal').modal('show');
                } else if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
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

    // Handle verification form submission
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

    // Handle resend code button
    $('#resendCode').on('click', function() {
        $(this).prop('disabled', true);
        
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
            },
            complete: function() {
                $('#resendCode').prop('disabled', false);
            }
        });
    });
});
</script>

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('.toggle-password').click(function() {
        const targetId = $(this).data('target');
        const input = $(`#${targetId}`);
        const icon = $(this).find('i');
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength checker
    $('#reg-password').on('input', function() {
        const password = $(this).val();
        const strengthMeter = $('.password-strength');
        const progressBar = strengthMeter.find('.progress-bar');
        const strengthText = strengthMeter.find('.strength-text');
        
        // Show strength meter when user starts typing
        strengthMeter.show();
        
        // Check requirements
        const hasLength = password.length >= 8;
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*()\-_=+{};:,<.>]/.test(password);
        
        // Update requirement indicators
        $('.req-length').parent()[hasLength ? 'addClass' : 'removeClass']('valid');
        $('.req-uppercase').parent()[hasUpper ? 'addClass' : 'removeClass']('valid');
        $('.req-number').parent()[hasNumber ? 'addClass' : 'removeClass']('valid');
        $('.req-special').parent()[hasSpecial ? 'addClass' : 'removeClass']('valid');
        
        // Calculate strength
        let strength = 0;
        if (hasLength) strength += 25;
        if (hasUpper) strength += 25;
        if (hasNumber) strength += 25;
        if (hasSpecial) strength += 25;
        
        // Update progress bar
        progressBar.width(strength + '%');
        progressBar.removeClass('weak medium strong');
        
        if (strength <= 25) {
            progressBar.addClass('weak');
            strengthText.text('Weak');
        } else if (strength <= 75) {
            progressBar.addClass('medium');
            strengthText.text('Medium');
        } else {
            progressBar.addClass('strong');
            strengthText.text('Strong');
        }
    });

    // Confirm password validation
    $('#confirm-password').on('input', function() {
        const password = $('#reg-password').val();
        const confirmPassword = $(this).val();
        
        if (confirmPassword && password !== confirmPassword) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
});
</script>
