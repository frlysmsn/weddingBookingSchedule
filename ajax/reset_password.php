<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];
    
    try {
        // Validate inputs
        $token = $_POST['token'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        
        if (empty($token) || empty($new_password)) {
            throw new Exception('Invalid request. Please try again.');
        }

        if (strlen($new_password) < 6) {
            throw new Exception('Password must be at least 6 characters long.');
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Check if token exists and hasn't expired
        $stmt = $db->prepare("
            SELECT id, email 
            FROM users 
            WHERE reset_token = ? 
            AND reset_expires > NOW()
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            throw new Exception('Invalid or expired reset token. Please request a new password reset.');
        }
        
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $stmt = $db->prepare("
            UPDATE users 
            SET password = ?, 
                reset_token = NULL, 
                reset_expires = NULL,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        if (!$stmt->execute([$hashed_password, $user['id']])) {
            throw new Exception('Failed to update password. Please try again.');
        }
        
        $response['success'] = true;
        $response['message'] = 'Password has been successfully updated. You can now login with your new password.';
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit;
} 