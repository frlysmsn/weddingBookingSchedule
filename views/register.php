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
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        $result = $auth->register($name, $email, $password);
        if ($result['success']) {
            $success = $result['message'];
            // Redirect to login page after 2 seconds
            header("refresh:2;url=index.php?page=login");
        } else {
            $error = $result['message'];
        }
    }
}
?>

<div class="auth-container">
    <div class="auth-box">
        <h2>Register</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?page=register" class="auth-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>

            <div class="auth-links">
                <p>Already have an account? <a href="index.php?page=login">Login here</a></p>
            </div>
        </form>
    </div>
</div> 