<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => '', 'verified' => false];
    
    try {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            if (!$user['email_verified']) {
                $response['message'] = 'Please verify your email first.';
                $_SESSION['temp_user_id'] = $user['id'];
            } else if (!$user['active']) {
                $response['message'] = 'Your account has been disabled.';
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];
                
                $response['success'] = true;
                $response['verified'] = true;
                
                if ($user['role'] === 'admin') {
                    $response['redirect'] = 'admin/';
                } else {
                    $response['redirect'] = 'client/';
                }
            }
        } else {
            $response['message'] = 'Invalid email or password';
        }
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} 