<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'verified' => false, 'needsVerification' => false];
    
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        if (empty($email) || empty($password)) {
            throw new Exception('Please fill in all fields');
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception('Invalid email or password');
        }
        
        if (password_verify($password, $user['password'])) {
            if (!$user['email_verified']) {
                $_SESSION['temp_user_id'] = $user['id'];
                $response['needsVerification'] = true;
                $response['message'] = 'Email not verified';
                echo json_encode($response);
                exit;
            }
            
            if (!$user['active']) {
                throw new Exception('Account is inactive. Please contact administrator.');
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];
            
            $response['success'] = true;
            $response['verified'] = true;
            $response['redirect'] = $user['role'] === 'admin' ? 'admin/' : 'client/';
        } else {
            throw new Exception('Invalid email or password');
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} 