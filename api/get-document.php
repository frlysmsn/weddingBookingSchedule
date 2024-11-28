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
    
    $document_type = $_GET['document_type'] ?? '';
    if (empty($document_type)) {
        throw new Exception('Document type is required');
    }

    // Get document for user's active booking
    $stmt = $db->prepare("
        SELECT d.file_path 
        FROM documents d 
        JOIN bookings b ON d.booking_id = b.id 
        WHERE b.user_id = ? 
        AND d.document_type = ? 
        ORDER BY d.created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$_SESSION['user_id'], $document_type]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$document) {
        throw new Exception('Document not found');
    }

    if (!file_exists($document['file_path'])) {
        throw new Exception('Document file not found');
    }

    echo json_encode([
        'success' => true,
        'url' => str_replace('../', '', $document['file_path'])
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 