<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Authentication.php';

$auth = new Authentication();

// Ensure only admins can access this area
if (!$auth->isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                $success = "Password updated successfully!";
            } else {
                $error = "New passwords do not match!";
            }
        } else {
            $error = "Current password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - <?= SITE_NAME ?></title>
    <link href='../assets/css/style.css' rel='stylesheet'>
    <link href='../assets/css/admin.css' rel='stylesheet'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css' rel='stylesheet'>
</head>
<body>
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <div class="admin-sidebar">
            <div class="sidebar-header">
                <h2><?= SITE_NAME ?></h2>
            </div>
            <nav class="sidebar-nav">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="bookings.php">
                    <i class="fas fa-calendar-check"></i> Bookings
                </a>
                <a href="users.php">
                    <i class="fas fa-users"></i> Users
                </a>
                <a href="settings.php" class="active">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="admin-main">
            <div class="admin-header">
                <h1>Settings</h1>
            </div>

            <div class="admin-content">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <!-- Change Password Section -->
                <div class="settings-section">
                    <h3>Change Password</h3>
                    <form method="POST" class="settings-form">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" id="new_password" name="new_password" 
                                   class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   class="form-control" required>
                        </div>

                        <button type="submit" name="update_password" class="btn btn-primary">
                            Update Password
                        </button>
                    </form>
                </div>

                <!-- System Settings Section -->
                <div class="settings-section">
                    <h3>System Settings</h3>
                    <div class="system-info">
                        <p><strong>System Version:</strong> 1.0.0</p>
                        <p><strong>PHP Version:</strong> <?= phpversion() ?></p>
                        <p><strong>Database:</strong> MySQL</p>
                        <p><strong>Server Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
                    </div>
                </div>

                <!-- Backup Section -->
                <div class="settings-section">
                    <h3>Database Backup</h3>
                    <button onclick="backupDatabase()" class="btn btn-success">
                        <i class="fas fa-download"></i> Create Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src='https://code.jquery.com/jquery-3.6.3.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css'></script>
    <script>
    function backupDatabase() {
        Swal.fire({
            title: 'Creating Backup',
            text: 'Please wait while we create your database backup...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                $.get('../api/backup-database.php')
                    .done(function(response) {
                        Swal.fire(
                            'Success!',
                            'Database backup created successfully.',
                            'success'
                        );
                    })
                    .fail(function() {
                        Swal.fire(
                            'Error!',
                            'Failed to create database backup.',
                            'error'
                        );
                    });
            }
        });
    }

    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        if (this.value !== document.getElementById('new_password').value) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
    </script>

    <style>
    .settings-section {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .settings-section h3 {
        margin-top: 0;
        margin-bottom: 1.5rem;
        color: #2c3e50;
    }

    .settings-form {
        max-width: 500px;
    }

    .system-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .system-info p {
        margin: 0.5rem 0;
    }

    .system-info strong {
        color: #2c3e50;
    }
    </style>
</body>
</html> 