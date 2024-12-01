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
        SELECT d.* 
        FROM documents d
        WHERE d.id = ?
    ");
    $stmt->execute([$document_id]);
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$document) {
        throw new Exception('Document not found');
    }
    
    // Get the absolute path to the file
    $file_path = realpath(__DIR__ . '/../' . $document['file_path']);
    
    // Verify file exists
    if (!$file_path || !file_exists($file_path)) {
        throw new Exception('File not found. Please contact administrator.');
    }
    
    // Verify file is within uploads directory (security check)
    $uploads_dir = realpath(__DIR__ . '/../uploads');
    if (strpos($file_path, $uploads_dir) !== 0) {
        throw new Exception('Invalid file path');
    }
    
    // Set proper headers for inline PDF viewing
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($document['file_path']) . '"');
    header('Cache-Control: public, must-revalidate, max-age=0');
    header('Pragma: public');
    header('X-Frame-Options: SAMEORIGIN');
    
    // Clear any output buffers
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // Read and output file
    readfile($file_path);
    exit;

} catch (Exception $e) {
    error_log("Document view error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
} 