<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $document_id = $_POST['document_id'] ?? null;
    $status = $_POST['status'] ?? null;
    $remarks = $_POST['remarks'] ?? null;
    
    if (!$document_id || !$status) {
        throw new Exception('Missing required parameters');
    }
    
    $stmt = $db->prepare("
        UPDATE documents 
        SET status = ?,
            remarks = ?,
            reviewed_at = NOW(),
            reviewed_by = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $status,
        $remarks,
        $_SESSION['user_id'],
        $document_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Document status updated successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 