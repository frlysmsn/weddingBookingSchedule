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
    
    $db = Database::getInstance()->getConnection();
    
    try {
        // First delete related records from bookings table
        $stmt = $db->prepare("DELETE FROM bookings WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Then delete the user
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to delete user']);
    }
} 
