<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Authentication.php';

$auth = new Authentication();
$error = '';

// If admin is already logged in, redirect to dashboard
if ($auth->isAdmin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if ($auth->loginAdmin($email, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid admin credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= SITE_NAME ?></title>
    <link href='../assets/css/style.css' rel='stylesheet'>
</head>
<body class="admin-login-page">
    <div class="auth-container">
        <div class="auth-box">
            <h2>Admin Login</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 