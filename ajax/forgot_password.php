<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';
require_once '../includes/Mail.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        
        if (!$email) {
            throw new Exception('Invalid email address.');
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception('If this email exists in our system, you will receive a password reset link.');
        }
        
        // Generate reset token and expiry
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Update user with reset token
        $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        if (!$stmt->execute([$token, $expires, $user['id']])) {
            throw new Exception('Failed to process reset request.');
        }
        
        // Send reset email
        $reset_link = dirname(SITE_URL) . '/index.php?page=reset_password&token=' . $token;
        $mailer = new Mail();
        
        try {
            if (!$mailer->sendPasswordReset($email, $reset_link)) {
                throw new Exception('Failed to send reset email. Please try again later.');
            }
        } catch (Exception $e) {
            error_log("Mail error: " . $e->getMessage());
            throw new Exception('Failed to send reset email. Please try again later.');
        }
        
        $response['success'] = true;
        $response['message'] = 'If this email exists in our system, you will receive a password reset link.';
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
} 