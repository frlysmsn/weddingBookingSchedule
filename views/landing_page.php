<?php
$auth = new Authentication();

// If already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    if ($auth->isAdmin()) {
        header('Location: admin/');
    } else {
        header('Location: client/');
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
                <form id="loginForm" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="login" class="btn btn-primary">Login</button>
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

                    <div class="form-group">
                        <label for="reg-password">Password</label>
                        <input type="password" id="reg-password" name="password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm_password" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </aside>
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
});
</script>
