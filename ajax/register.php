<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

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

        // Insert new user
        $stmt = $db->prepare("INSERT INTO users (email, password, name, role, active) VALUES (?, ?, ?, 'client', 1)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if (!$stmt->execute([$email, $hashedPassword, $full_name])) {
            throw new Exception('Registration failed. Please try again.');
        }
        
        $response['success'] = true;
        $response['message'] = 'Registration successful! Please login.';
        
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}