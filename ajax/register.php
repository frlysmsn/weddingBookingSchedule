<?php
// Turn off output buffering
ob_start();

session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];

        // Validate inputs
        if (empty($email) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name)) {
            throw new Exception('All fields are required.');
        }

        if ($password !== $confirm_password) {
            throw new Exception('Passwords do not match.');
        }

        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters long.');
        }

        $db = Database::getInstance()->getConnection();
        
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            throw new Exception('Email already registered');
        }

        // Combine first and last names
        $full_name = trim($first_name . ' ' . $last_name);

        // Generate verification code
        $verification_code = sprintf("%06d", mt_rand(1, 999999));
        
        // Insert new user with verification code
        $stmt = $db->prepare("INSERT INTO users (email, password, name, role, active, verification_code) VALUES (?, ?, ?, 'client', 0, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if (!$stmt->execute([$email, $hashedPassword, $full_name, $verification_code])) {
            throw new Exception('Registration failed. Please try again.');
        }
        
        $user_id = $db->lastInsertId();
        
        error_log("User inserted successfully with ID: " . $user_id);
        
        // Send verification email
        $mailer = new Mail();
        try {
            error_log("Starting registration process for email: " . $email);
            error_log("Attempting to send verification email to: " . $email);
            if (!$mailer->sendVerificationCode($email, $verification_code)) {
                error_log("Failed to send verification code. Error: " . $mailer->getError());
                throw new Exception('Failed to send verification code: ' . $mailer->getError());
            }
            error_log("Verification email sent successfully to: " . $email);
        } catch (Exception $e) {
            error_log("Registration email error: " . $e->getMessage());
            throw $e;
        }
        
        // Store user_id temporarily for verification
        $_SESSION['temp_user_id'] = $user_id;
        
        $response['success'] = true;
        $response['message'] = 'Please check your email for verification code.';
        $response['redirect'] = 'index.php?page=verify';
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Registration error: " . $e->getMessage());
    }
    
    // Clear any output buffers
    ob_end_clean();

    // Send response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}