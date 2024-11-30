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
        // Don't allow deactivating the last active admin
        if ($_POST['current_status'] == 1) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin' AND active = 1");
            $stmt->execute();
            $activeAdmins = $stmt->fetchColumn();
            
            if ($activeAdmins <= 1) {
                echo json_encode(['success' => false, 'message' => 'Cannot deactivate the last active administrator']);
                exit;
            }
        }
        
        $stmt = $db->prepare("UPDATE users SET active = NOT active WHERE id = ?");
        $stmt->execute([$userId]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
