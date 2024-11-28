<?php
$error = '';
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

// Handle client login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($auth->login($email, $password)) {
        header('Location: client/');
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<div class="landing-container">
    <!-- Hero Section -->
    <section class="hero-section">
        <h1>Welcome to <?= SITE_NAME ?></h1>
        <p>Plan your perfect church wedding with us</p>
    </section>

    <!-- Login Section -->
    <section class="auth-section">
        <div class="auth-box">
            <h2>Client Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
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

                <div class="auth-links">
                    <p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
                    <p><small>For admin login, please visit the <a href="admin/login.php">admin portal</a></small></p>
                </div>
            </form>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <h2>Our Services</h2>
        <div class="feature-grid">
            <div class="feature-card">
                <i class="fas fa-calendar"></i>
                <h3>Easy Scheduling</h3>
                <p>Book your preferred wedding date through our online calendar system.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-file-upload"></i>
                <h3>Document Management</h3>
                <p>Easily upload and manage your wedding requirements online.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-bell"></i>
                <h3>Real-time Updates</h3>
                <p>Get instant notifications about your booking status.</p>
            </div>
        </div>
    </section>
</div>
