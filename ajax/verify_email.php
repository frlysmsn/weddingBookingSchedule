<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'redirect' => ''];
    
    try {
        if (!isset($_SESSION['temp_user_id'])) {
            throw new Exception('Invalid session. Please try registering again.');
        }
        
        $verification_code = trim($_POST['verification_code']);
        $user_id = $_SESSION['temp_user_id'];
        
        if (empty($verification_code)) {
            throw new Exception('Please enter the verification code.');
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND verification_code = ?");
        $stmt->execute([$user_id, $verification_code]);
        
        if ($user = $stmt->fetch()) {
            // Update user as verified and active
            $stmt = $db->prepare("UPDATE users SET email_verified = 1, active = 1, verification_code = NULL WHERE id = ?");
            if (!$stmt->execute([$user_id])) {
                throw new Exception('Failed to verify email. Please try again.');
            }
            
            // Clear temporary session
            unset($_SESSION['temp_user_id']);
            
            $response['success'] = true;
            $response['message'] = 'Email verified successfully! You can now login.';
            $response['redirect'] = 'index.php';
        } else {
            throw new Exception('Invalid verification code. Please try again.');
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
        error_log("Verification error: " . $e->getMessage());
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
