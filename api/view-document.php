<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    $document_id = $_GET['id'] ?? null;
    if (!$document_id) {
        throw new Exception('Document ID is required');
    }
    
    // Get document details
    $stmt = $db->prepare("
        SELECT d.*, b.user_id 
        FROM documents d
        JOIN bookings b ON d.booking_id = b.id
        WHERE d.id = ?
    ");
    $stmt->execute([$document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        throw new Exception('Document not found');
    }
    
    // Check authorization
    if ($_SESSION['role'] !== 'admin' && $document['user_id'] !== $_SESSION['user_id']) {
        throw new Exception('Unauthorized access');
    }
    
    // Verify file exists
    if (!file_exists($document['file_path'])) {
        throw new Exception('File not found');
    }
    
    // Output file
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($document['file_path']) . '"');
    readfile($document['file_path']);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 