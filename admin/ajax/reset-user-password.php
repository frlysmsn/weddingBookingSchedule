<?php
session_start();
require_once '../../includes/config.php';
require_once '../../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_POST['user_id'];
    $newPassword = bin2hex(random_bytes(8)); // Generate random password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $db = Database::getInstance()->getConnection();
    
    try {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
        
        echo json_encode(['success' => true, 'password' => $newPassword]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to reset password']);
    }
} 