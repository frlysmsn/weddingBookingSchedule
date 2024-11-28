<?php
require_once 'includes/Authentication.php';

$auth = new Authentication();
$error = '';
$success = '';

// If user is already logged in, redirect to appropriate dashboard
if ($auth->isLoggedIn()) {
    header('Location: index.php?page=' . ($auth->isAdmin() ? 'admin-dashboard' : 'client-dashboard'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($auth->login($email, $password)) {
        header('Location: index.php?page=' . ($auth->isAdmin() ? 'admin-dashboard' : 'client-dashboard'));
        exit;
    } else {
        $error = "Invalid email or password";
    }
}
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Login</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=login" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </div>

            <div class="auth-links">
                <p>Don't have an account? <a href="index.php?page=register">Register here</a></p>
                <p><a href="index.php?page=forgot-password">Forgot Password?</a></p>
            </div>
        </form>
    </div>
</div> 