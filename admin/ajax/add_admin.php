<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Received POST request in add_admin.php");
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $db = Database::getInstance()->getConnection();
    
    try {
        error_log("Admin creation attempt - Name: $name, Email: $email");
        
        // Check if email exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit;
        }

        // Insert new admin
        $stmt = $db->prepare("INSERT INTO users (name, email, password, role, active, created_at) VALUES (?, ?, ?, 'admin', 1, CURRENT_TIMESTAMP)");
        $stmt->execute([$name, $email, $password]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
