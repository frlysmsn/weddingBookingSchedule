<?php
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: ../index.php?page=login');
    exit;
}

$db = Database::getInstance()->getConnection();
$error = '';
$success = '';

// Fetch current user data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    
    try {
        if (!empty($current_password) && !empty($new_password)) {
            // Verify current password
            if (password_verify($current_password, $user['password'])) {
                // Update name, email, and password
                $stmt = $db->prepare("
                    UPDATE users 
                    SET name = ?, email = ?, password = ? 
                    WHERE id = ?
                ");
                $stmt->execute([
                    $name,
                    $email,
                    password_hash($new_password, PASSWORD_DEFAULT),
                    $_SESSION['user_id']
                ]);
            } else {
                throw new Exception("Current password is incorrect");
            }
        } else {
            // Update only name and email
            $stmt = $db->prepare("
                UPDATE users 
                SET name = ?, email = ? 
                WHERE id = ?
            ");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
        }
        
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $success = "Profile updated successfully!";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="profile-container">
    <h2>My Profile</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="profile-form">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="form-section">
            <h3>Change Password</h3>
            <p class="text-muted">Leave blank if you don't want to change your password</p>
            
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" 
                       class="form-control">
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" 
                       class="form-control">
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<style>
.profile-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-form .form-section {
    margin: 2rem 0;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.text-muted {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}
</style>